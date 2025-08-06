<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);
        
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id() && 
            !auth()->user()->hasRole('admin') && 
            !auth()->user()->hasRole('petshop')) {
            abort(403);
        }
        
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        // Regras de validação básicas
        $rules = [
            'payment_method' => 'required|in:credit_card,bank_slip,pix',
            'shipping_address' => 'required|string',
        ];
        
        // Adicionar regras para cartão de crédito quando este método for selecionado
        if ($request->payment_method === 'credit_card') {
            $rules['card_number'] = 'required|string|min:13|max:19';
            $rules['card_expiry'] = 'required|string';
            $rules['card_cvv'] = 'required|string|min:3|max:4';
            $rules['card_name'] = 'required|string';
        }
        
        $request->validate($rules);
        
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')
                        ->with('error', 'Seu carrinho está vazio.');
        }
        
        DB::beginTransaction();
        
        try {
            $subtotal = 0;
            
            // Calcular subtotal
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal += $product->price * $item['quantity'];
            }

            // Verificar se há cupom aplicado
            $couponData = session('coupon');
            $discount = 0;
            $coupon = null;
            
            if ($couponData) {
                $coupon = \App\Models\Coupon::find($couponData['id']);
                
                // Verificar se o cupom ainda é válido
                if ($coupon && $coupon->canBeUsedBy(auth()->id())) {
                    $discount = $coupon->calculateDiscount($subtotal);
                } else {
                    // Cupom inválido, remover da sessão
                    session()->forget('coupon');
                    throw new \Exception('O cupom aplicado não é mais válido.');
                }
            }

            $totalAmount = $subtotal - $discount;

            // Criar o pedido
            $order = new Order();
            $order->user_id = auth()->id();
            $order->subtotal = $subtotal;
            $order->coupon_discount = $discount;
            $order->total_amount = $totalAmount;
            $order->status = 'pending';
            $order->payment_method = $request->payment_method;
            $order->shipping_address = $request->shipping_address;
            
            // Salvar informações do cupom se aplicado
            if ($coupon && $discount > 0) {
                $order->coupon_id = $coupon->id;
                $order->coupon_code = $coupon->code;
            }
            
            // Armazenar informações do cartão de forma segura (ou apenas uma referência)
            // Em um sistema real, você não deve armazenar dados completos do cartão
            if ($request->payment_method === 'credit_card') {
                $order->payment_details = json_encode([
                    'card_last_four' => substr($request->card_number, -4),
                    'card_brand' => 'Visa',
                    'cardholder_name' => $request->card_name
                ]);
            } elseif ($request->payment_method === 'bank_slip') {
                $order->payment_details = json_encode([
                    'code' => 'BOLETO' . rand(1000000, 9999999),
                    'expiry_date' => Carbon::now()->addDays(7)->format('Y-m-d')
                ]);
            } elseif ($request->payment_method === 'pix') {
                $order->payment_details = json_encode([
                    'qr_code' => 'PIX' . rand(1000000, 9999999),
                    'key' => 'pix@petshop.com'
                ]);
            }
            
            $order->save();

            // Se houver cupom, registrar o uso
            if ($coupon && $discount > 0) {
                \App\Models\CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                    'discount_amount' => $discount,
                ]);

                // Incrementar contador de uso do cupom
                $coupon->increment('used_count');
            }
            
            // Criar os itens do pedido
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                // Verificar estoque
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("O produto '{$product->name}' não possui estoque suficiente.");
                }
                
                // Diminuir o estoque
                $product->stock -= $item['quantity'];
                $product->save();
                
                // Criar item do pedido
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $product->id;
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $product->price;
                $orderItem->save();
            }
            
            // Limpar carrinho e cupom
            Session::forget(['cart', 'coupon']);
            
            DB::commit();
            
            return redirect()->route('orders.show', $order)
                        ->with('success', 'Pedido realizado com sucesso!');
                        
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('cart.index')
                        ->with('error', $e->getMessage());
        }
    }
}