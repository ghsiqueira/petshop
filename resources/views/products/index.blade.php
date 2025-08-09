{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">Produtos</h1>
        </div>
    </div>
    
    <!-- Filtros e Busca -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('products.index') }}" id="filtersForm">
                        <div class="row">
                            <!-- Campo de Busca -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Buscar produtos:</label>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Nome, descrição, marca...">
                            </div>
                            
                            <!-- Filtro por Categoria -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Categoria:</label>
                                <select class="form-select" name="category">
                                    <option value="">Todas</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Filtro por Marca -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Marca:</label>
                                <select class="form-select" name="brand">
                                    <option value="">Todas</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                            {{ $brand }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Ordenação -->
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Ordenar por:</label>
                                <select class="form-select" name="sort">
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nome A-Z</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nome Z-A</option>
                                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Menor preço</option>
                                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Maior preço</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mais recente</option>
                                </select>
                            </div>
                            
                            <!-- Botões -->
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <div class="btn-group w-100">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filtros Avançados (Colapsível) -->
                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                                    <i class="fas fa-filter me-1"></i>Filtros Avançados
                                </button>
                            </div>
                        </div>
                        
                        <div class="collapse mt-3" id="advancedFilters">
                            <div class="row">
                                <!-- Faixa de Preço -->
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Preço mínimo:</label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="min_price" 
                                           value="{{ request('min_price') }}" 
                                           placeholder="R$ 0,00" 
                                           step="0.01" 
                                           min="0">
                                </div>
                                
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Preço máximo:</label>
                                    <input type="number" 
                                           class="form-control" 
                                           name="max_price" 
                                           value="{{ request('max_price') }}" 
                                           placeholder="R$ 999,00" 
                                           step="0.01" 
                                           min="0">
                                </div>
                                
                                <!-- Filtros Especiais -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Filtros especiais:</label>
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="in_stock" 
                                               value="1" 
                                               {{ request('in_stock') ? 'checked' : '' }} 
                                               id="inStockFilter">
                                        <label class="form-check-label" for="inStockFilter">
                                            Apenas em estoque
                                        </label>
                                    </div>
                                    
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="featured" 
                                               value="1" 
                                               {{ request('featured') ? 'checked' : '' }} 
                                               id="featuredFilter">
                                        <label class="form-check-label" for="featuredFilter">
                                            Produtos em destaque
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações dos Resultados -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <p class="text-muted mb-0">
                    Mostrando {{ $products->count() }} de {{ $products->total() }} produto(s)
                </p>
                
                <!-- Filtros Ativos -->
                @if(request()->hasAny(['search', 'category', 'brand', 'min_price', 'max_price', 'in_stock', 'featured']))
                    <div class="active-filters">
                        <span class="text-muted me-2">Filtros ativos:</span>
                        @if(request('search'))
                            <span class="badge bg-primary me-1">Busca: {{ request('search') }}</span>
                        @endif
                        @if(request('category'))
                            <span class="badge bg-info me-1">Categoria: {{ request('category') }}</span>
                        @endif
                        @if(request('brand'))
                            <span class="badge bg-success me-1">Marca: {{ request('brand') }}</span>
                        @endif
                        @if(request('min_price'))
                            <span class="badge bg-warning me-1">Min: R$ {{ request('min_price') }}</span>
                        @endif
                        @if(request('max_price'))
                            <span class="badge bg-warning me-1">Max: R$ {{ request('max_price') }}</span>
                        @endif
                        @if(request('in_stock'))
                            <span class="badge bg-secondary me-1">Em estoque</span>
                        @endif
                        @if(request('featured'))
                            <span class="badge bg-dark me-1">Destaque</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Lista de Produtos -->
    <div class="row">
        @forelse($products as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 product-card">
                    <!-- Badges -->
                    @if(isset($product->featured) && $product->featured)
                        <div class="position-absolute top-0 start-0 m-2">
                            <span class="badge bg-warning">
                                <i class="fas fa-star me-1"></i>Destaque
                            </span>
                        </div>
                    @endif
                    
                    @if(isset($product->quantity) && $product->quantity <= 0)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-danger">Fora de estoque</span>
                        </div>
                    @elseif(isset($product->quantity) && $product->quantity <= 5)
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-warning">Últimas unidades</span>
                        </div>
                    @endif

                    <!-- Imagem -->
                    <div class="position-relative">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 class="card-img-top" 
                                 alt="{{ $product->name }}" 
                                 style="height: 200px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <i class="fas fa-box fa-3x text-muted"></i>
                            </div>
                        @endif
                        
                        <!-- Botão de Wishlist -->
                        @auth
                            <button class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 wishlist-btn" 
                                    data-product-id="{{ $product->id }}"
                                    style="border-radius: 50%; width: 35px; height: 35px;">
                                <i class="fas fa-heart {{ auth()->user()->hasInWishlist($product->id) ? 'text-danger' : 'text-muted' }}"></i>
                            </button>
                        @endauth
                    </div>

                    <div class="card-body d-flex flex-column">
                        <!-- Título -->
                        <h5 class="card-title">{{ $product->name }}</h5>
                        
                        <!-- Descrição -->
                        @if($product->description)
                            <p class="card-text text-muted small">
                                {{ Str::limit($product->description, 80) }}
                            </p>
                        @endif
                        
                        <!-- Informações do Produto -->
                        <div class="product-info mb-2">
                            @if($product->brand)
                                <small class="text-muted">
                                    <strong>Marca:</strong> {{ $product->brand }}
                                </small><br>
                            @endif
                            
                            @if($product->category)
                                <small class="text-muted">
                                    <strong>Categoria:</strong> {{ $product->category }}
                                </small><br>
                            @endif
                            
                            @if($product->petshop)
                                <small class="text-muted">
                                    <i class="fas fa-store me-1"></i>{{ $product->petshop->name }}
                                </small>
                            @endif
                        </div>

                        <!-- Preço -->
                        <div class="price-section mb-3 mt-auto">
                            <h4 class="text-primary mb-0">R$ {{ number_format($product->price, 2, ',', '.') }}</h4>
                        </div>

                        <!-- Botões -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>Ver Detalhes
                            </a>
                            
                            @auth
                                <button class="btn btn-primary add-to-cart-btn" 
                                        data-product-id="{{ $product->id }}"
                                        {{ (isset($product->quantity) && $product->quantity <= 0) ? 'disabled' : '' }}>
                                    <i class="fas fa-shopping-cart me-2"></i>
                                    {{ (isset($product->quantity) && $product->quantity <= 0) ? 'Indisponível' : 'Adicionar ao Carrinho' }}
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-2"></i>Fazer Login
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>Nenhum produto encontrado</h4>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'category', 'brand', 'min_price', 'max_price']))
                            Tente ajustar os filtros ou 
                            <a href="{{ route('products.index') }}">ver todos os produtos</a>.
                        @else
                            Não há produtos disponíveis no momento.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Paginação -->
    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @endif
</div>

<style>
.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e3e6f0;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.wishlist-btn {
    opacity: 0.8;
    transition: opacity 0.2s;
}

.wishlist-btn:hover {
    opacity: 1;
}

.active-filters .badge {
    font-size: 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form quando filtros mudarem
    const form = document.getElementById('filtersForm');
    const selects = form.querySelectorAll('select');
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    
    selects.forEach(select => {
        select.addEventListener('change', () => form.submit());
    });
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => form.submit());
    });
});
</script>
@endsection