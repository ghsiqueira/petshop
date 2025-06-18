<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('role:petshop')->except(['index', 'show']);
    }

    public function index()
    {
        $products = Product::with('petshop')
                          ->where('is_active', true)
                          ->paginate(12);
        
        return view('products.index', compact('products'));
    }

    public function show(Product $product)
    {
        $relatedProducts = Product::where('petshop_id', $product->petshop_id)
                                 ->where('id', '!=', $product->id)
                                 ->take(4)
                                 ->get();
        
        $reviews = $product->reviews()->with('user')->get();
        
        return view('products.show', compact('product', 'relatedProducts', 'reviews'));
    }

    public function create()
    {
        return view('petshop.products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $product = new Product();
        $product->petshop_id = auth()->user()->petshop->id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }
        
        $product->save();
        
        return redirect()->route('petshop.products.index')
                       ->with('success', 'Produto criado com sucesso!');
    }

    public function edit(Product $product)
    {
        if ($product->petshop_id !== auth()->user()->petshop->id) {
            abort(403);
        }
        
        return view('petshop.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if ($product->petshop_id !== auth()->user()->petshop->id) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->is_active = $request->has('is_active');
        
        if ($request->hasFile('image')) {
            // Excluir imagem antiga se existir
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }
        
        $product->save();
        
        return redirect()->route('petshop.products.index')
                       ->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        if ($product->petshop_id !== auth()->user()->petshop->id) {
            abort(403);
        }
        
        // Excluir imagem se existir
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        return redirect()->route('petshop.products.index')
                       ->with('success', 'Produto excluído com sucesso!');
    }
}