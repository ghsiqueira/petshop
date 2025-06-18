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
            $totalAmount = 0;
            
            // Calcular valor total
            foreach ($cart as $item) {
                $product = Product::findOrFail($item['product_id']);
                $totalAmount += $product->price * $item['quantity'];
            }
            
            // Criar o pedido
            $order = new Order();
            $order->user_id = auth()->id();
            $order->total_amount = $totalAmount;
            $order->status = 'pending';
            $order->payment_method = $request->payment_method;
            $order->shipping_address = $request->shipping_address;
            
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
            
            // Limpar carrinho
            Session::forget('cart');
            
            DB::commit();
            
            return redirect()->route('orders.show', $order)
                           ->with('success', 'Pedido realizado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('cart.index')
                           ->with('error', 'Erro ao processar o pedido: ' . $e->getMessage());
        }
    }
}