<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $products = [];
        $total = 0;
        
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;
                
                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'petshop' => $product->petshop->name,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal
                ];
            }
        }
        
        return view('cart.index', compact('products', 'total'));
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
                $cart[$key]['quantity'] += $request->quantity;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $cart[] = [
                'product_id' => $product->id,
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
                        return response()->json(['error' => 'Estoque insuficiente.']);
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
        Session::forget('cart');
        
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
        $total = 0;
        
        foreach ($cart as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $subtotal = $product->price * $item['quantity'];
                $total += $subtotal;
                
                $products[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'petshop' => $product->petshop->name,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal
                ];
            }
        }
        
        return view('cart.checkout', compact('products', 'total'));
    }
}