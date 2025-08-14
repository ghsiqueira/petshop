<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->where('is_active', true)->with('petshop');
        
        // Filtro por busca/pesquisa
        if ($request->filled('search') || $request->filled('q')) {
            $searchTerm = $request->get('search') ?: $request->get('q');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                
                // Adicionar busca por marca apenas se a coluna existir
                if (Schema::hasColumn('products', 'brand')) {
                    $q->orWhere('brand', 'LIKE', "%{$searchTerm}%");
                }
            });
        }
        
        // Filtro por categoria
        if ($request->filled('category')) {
            $query->where('category', $request->get('category'));
        }
        
        // Filtro por faixa de preço
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }
        
        // Filtro por marca (apenas se a coluna existir)
        if ($request->filled('brand') && Schema::hasColumn('products', 'brand')) {
            $query->where('brand', $request->get('brand'));
        }
        
        // Filtro por produtos em estoque (apenas se a coluna existir)
        if ($request->filled('in_stock') && $request->get('in_stock') && Schema::hasColumn('products', 'quantity')) {
            $query->where('quantity', '>', 0);
        }
        
        // Filtro por produtos em promoção (apenas se a coluna existir)
        if ($request->filled('on_sale') && $request->get('on_sale') && Schema::hasColumn('products', 'discount_percentage')) {
            $query->where('discount_percentage', '>', 0);
        }
        
        // Filtro por produtos em destaque (apenas se a coluna existir)
        if ($request->filled('featured') && $request->get('featured') && Schema::hasColumn('products', 'featured')) {
            $query->where('featured', true);
        }
        
        // Ordenação
        $sort = $request->get('sort', 'name_asc');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'rating':
                // Se existir coluna avg_rating
                if (Schema::hasColumn('products', 'avg_rating')) {
                    $query->orderBy('avg_rating', 'desc');
                } else {
                    $query->orderBy('created_at', 'desc');
                }
                break;
            default:
                $query->orderBy('name', 'asc');
        }
        
        // Paginação
        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);
        
        // Manter parâmetros da query na paginação
        $products->appends($request->query());
        
        // Obter dados para os filtros (verificando se as colunas existem)
        $categories = Product::where('is_active', true)
                           ->distinct()
                           ->pluck('category')
                           ->filter()
                           ->sort()
                           ->values();
        
        // Apenas buscar marcas se a coluna existir
        $brands = collect();
        if (Schema::hasColumn('products', 'brand')) {
            $brands = Product::where('is_active', true)
                            ->distinct()
                            ->pluck('brand')
                            ->filter()
                            ->sort()
                            ->values();
        }
        
        // Verificar quais colunas existem para passar para a view
        $availableColumns = [
            'brand' => Schema::hasColumn('products', 'brand'),
            'quantity' => Schema::hasColumn('products', 'quantity'),
            'featured' => Schema::hasColumn('products', 'featured'),
            'discount_percentage' => Schema::hasColumn('products', 'discount_percentage'),
            'avg_rating' => Schema::hasColumn('products', 'avg_rating'),
        ];
        
        return view('products.index', compact('products', 'categories', 'brands', 'availableColumns'));
    }
    
    public function show(Product $product)
    {
        if (!$product->is_active) {
            abort(404, 'Produto não encontrado');
        }
        
        // Carregar relacionamentos básicos
        $product->load(['petshop']);
        
        // Carregar reviews com usuários - tratamento para caso a tabela não exista
        try {
            $reviews = $product->reviews()->with('user')->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            // Se a tabela reviews não existir ou houver erro, criar collection vazia
            $reviews = collect();
        }
        
        // Produtos relacionados (mesma categoria)
        $relatedProducts = Product::where('category', $product->category)
                            ->where('id', '!=', $product->id)
                            ->where('is_active', true)
                            ->take(4)
                            ->get();
        
        return view('products.show', compact('product', 'relatedProducts', 'reviews'));
    }
    
    // Métodos para petshop (se necessário)
    public function create()
    {
        return view('products.create');
    }
    
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|in:racao,petiscos,brinquedos,higiene,acessorios,medicamentos,outros',
            'image' => 'nullable|image|max:2048'
        ];
        
        // Adicionar validação para campos opcionais
        if (Schema::hasColumn('products', 'brand')) {
            $rules['brand'] = 'nullable|string|max:100';
        }
        
        // Campo stock é obrigatório
        $rules['stock_quantity'] = 'required|integer|min:0';
        
        if (Schema::hasColumn('products', 'minimum_stock')) {
            $rules['minimum_stock'] = 'nullable|integer|min:0';
        }
        
        if (Schema::hasColumn('products', 'sku')) {
            $rules['sku'] = 'nullable|string|max:100';
        }
        
        if (Schema::hasColumn('products', 'weight')) {
            $rules['weight'] = 'nullable|numeric|min:0';
        }
        
        $validated = $request->validate($rules);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        // Adicionar petshop_id
        if (auth()->user() && auth()->user()->petshop) {
            $validated['petshop_id'] = auth()->user()->petshop->id;
        }
        
        // Mapear campos do formulário para o banco (OBRIGATÓRIO)
        $validated['stock'] = $validated['stock_quantity'];
        unset($validated['stock_quantity']);
        
        $validated['is_active'] = $request->has('is_active');
        
        if ($request->has('featured') && Schema::hasColumn('products', 'featured')) {
            $validated['featured'] = true;
        }
        
        try {
            Product::create($validated);
            
            return redirect()->route('petshop.products.index')
                            ->with('success', 'Produto criado com sucesso!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Erro ao criar produto: ' . $e->getMessage()])->withInput();
        }
    }
    
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }
    
    public function update(Request $request, Product $product)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|max:2048'
        ];
        
        // Adicionar validação para campos opcionais se existirem
        if (Schema::hasColumn('products', 'brand')) {
            $rules['brand'] = 'nullable|string|max:100';
        }
        
        if (Schema::hasColumn('products', 'quantity')) {
            $rules['quantity'] = 'required|integer|min:0';
        }
        
        $validated = $request->validate($rules);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        $product->update($validated);
        
        return redirect()->route('products.index')
                        ->with('success', 'Produto atualizado com sucesso!');
    }
    
    public function destroy(Product $product)
    {
        $product->delete();
        
        return redirect()->route('products.index')
                        ->with('success', 'Produto excluído com sucesso!');
    }
}