<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index');
    }
    
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);
        
        if (!$product->is_active) {
            return back()->with('error', 'Este produto não está disponível.');
        }
        
        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Estoque insuficiente.');
        }
        
        $cart = Session::get('cart', []);
        
        // Verificar se o produto já está no carrinho
        $found = false;
        foreach ($cart as $key => $item) {
            if ($item['product_id'] === $product->id) {
                $newQuantity = $cart[$key]['quantity'] + $request->quantity;
                
                // Verificar estoque para a nova quantidade
                if ($product->stock < $newQuantity) {
                    return back()->with('error', 'Estoque insuficiente para a quantidade solicitada.');
                }
                
                $cart[$key]['quantity'] = $newQuantity;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $cart[] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'petshop_name' => $product->petshop->name,
                'quantity' => $request->quantity
            ];
        }
        
        Session::put('cart', $cart);
        
        return back()->with('success', 'Produto adicionado ao carrinho!');
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $cart = Session::get('cart', []);
        
        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $request->product_id) {
                $product = Product::find($request->product_id);
                
                if ($product->stock < $request->quantity) {
                    // Em caso de AJAX, retornar resposta JSON
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Estoque insuficiente.'
                        ]);
                    }
                    
                    return back()->with('error', 'Estoque insuficiente.');
                }
                
                $cart[$key]['quantity'] = $request->quantity;
                break;
            }
        }
        
        Session::put('cart', $cart);
        
        // Se for uma requisição AJAX, retornar resposta JSON
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
        
        return back()->with('success', 'Carrinho atualizado!');
    }
    
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);
        
        $cart = Session::get('cart', []);
        
        foreach ($cart as $key => $item) {
            if ($item['product_id'] == $request->product_id) {
                unset($cart[$key]);
                break;
            }
        }
        
        Session::put('cart', $cart);
        
        return back()->with('success', 'Produto removido do carrinho!');
    }
    
    public function clear()
    {
        Session::forget(['cart', 'coupon']);
        
        return back()->with('success', 'Carrinho esvaziado!');
    }
    
    public function checkout()
    {
        if (!auth()->check()) {
            return redirect()->route('login')
                           ->with('error', 'Você precisa estar logado para finalizar a compra.');
        }
        
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')
                           ->with('error', 'Seu carrinho está vazio.');
        }
        
        $products = [];
        $subtotal = 0;
        
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $itemSubtotal = $product->price * $item['quantity'];
                $subtotal += $itemSubtotal;
                
                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'petshop' => $product->petshop->name,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal
                ];
            }
        }
        
        // Calcular desconto do cupom se aplicado
        $couponData = session('coupon');
        $discount = 0;
        
        if ($couponData) {
            $discount = $couponData['discount'];
        }
        
        $total = $subtotal - $discount;
        
        return view('cart.checkout', compact('products', 'subtotal', 'discount', 'total'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->code));
        $cart = session('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Seu carrinho está vazio.'
            ]);
        }

        // Buscar cupom
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Cupom não encontrado.'
            ]);
        }

        // Verificar se é válido
        if (!$coupon->canBeUsedBy(auth()->id())) {
            $message = 'Cupom inválido';
            
            if (!$coupon->isValid()) {
                if ($coupon->expires_at && \Carbon\Carbon::now()->gt($coupon->expires_at)) {
                    $message = 'Este cupom expirou.';
                } elseif (!$coupon->is_active) {
                    $message = 'Este cupom não está ativo.';
                } elseif ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                    $message = 'Este cupom atingiu o limite de uso.';
                }
            } else {
                $userUsages = $coupon->usages()->where('user_id', auth()->id())->count();
                if ($userUsages >= $coupon->usage_limit_per_user) {
                    $message = 'Você já utilizou este cupom o máximo de vezes permitido.';
                }
            }

            return response()->json([
                'success' => false,
                'message' => $message
            ]);
        }

        // Calcular total do carrinho
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Calcular desconto
        $discount = $coupon->calculateDiscount($subtotal);

        if ($discount == 0) {
            return response()->json([
                'success' => false,
                'message' => $coupon->minimum_amount 
                    ? 'Valor mínimo de R$ ' . number_format($coupon->minimum_amount, 2, ',', '.') . ' não atingido.'
                    : 'Cupom não pode ser aplicado a este pedido.'
            ]);
        }

        // Armazenar cupom na sessão
        session([
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount' => $discount,
                'type' => $coupon->type,
                'value' => $coupon->value,
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cupom aplicado com sucesso!',
            'coupon' => [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount' => $discount,
                'discount_formatted' => 'R$ ' . number_format($discount, 2, ',', '.'),
            ],
            'totals' => [
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $subtotal - $discount,
                'subtotal_formatted' => 'R$ ' . number_format($subtotal, 2, ',', '.'),
                'total_formatted' => 'R$ ' . number_format($subtotal - $discount, 2, ',', '.'),
            ]
        ]);
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        
        return response()->json([
            'success' => true,
            'message' => 'Cupom removido com sucesso!'
        ]);
    }
}