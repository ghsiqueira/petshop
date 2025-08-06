@extends('layouts.app')

@section('title', 'Produtos')

@section('content')
<div class="container">
    <h1 class="mb-4">Produtos</h1>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <form action="{{ route('products.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Buscar produtos..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-primary">Buscar</button>
            </form>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown">
                        Categorias
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('products.index') }}">Todas</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'food']) }}">Alimentação</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'toys']) }}">Brinquedos</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'accessories']) }}">Acessórios</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['category' => 'health']) }}">Saúde</a></li>
                    </ul>
                </div>
                
                <div class="dropdown ms-2">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-bs-toggle="dropdown">
                        Ordenar
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('products.index', ['sort' => 'newest']) }}">Mais recentes</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['sort' => 'price_asc']) }}">Menor preço</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['sort' => 'price_desc']) }}">Maior preço</a></li>
                        <li><a class="dropdown-item" href="{{ route('products.index', ['sort' => 'name_asc']) }}">Nome (A-Z)</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        @forelse($products as $product)
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm position-relative">
                    <!-- Botão Wishlist -->
                    @auth
                        <button class="btn btn-outline-danger btn-sm position-absolute wishlist-btn" 
                                style="top: 10px; right: 10px; z-index: 10; border-radius: 50%; width: 40px; height: 40px; padding: 0;"
                                data-product-id="{{ $product->id }}"
                                data-bs-toggle="tooltip" 
                                data-bs-placement="left" 
                                title="{{ auth()->user()->hasInWishlist($product->id) ? 'Remover da lista de desejos' : 'Adicionar à lista de desejos' }}">
                            <i class="fas fa-heart {{ auth()->user()->hasInWishlist($product->id) ? 'text-danger' : '' }}"></i>
                        </button>
                    @endauth

                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x200?text=Sem+Imagem' }}" 
                         class="card-img-top" 
                         alt="{{ $product->name }}"
                         style="height: 200px; object-fit: cover;">
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted small flex-grow-1">
                            {{ Str::limit($product->description, 80) }}
                        </p>
                        
                        <!-- Categoria -->
                        <div class="mb-2">
                            <span class="badge bg-secondary">
                                {{ ucfirst($product->category) }}
                            </span>
                        </div>
                        
                        <!-- Preço e Estoque -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h5 mb-0 text-primary">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            @if($product->stock > 0)
                                <span class="badge bg-success">Em estoque</span>
                            @else
                                <span class="badge bg-danger">Indisponível</span>
                            @endif
                        </div>
                        
                        <!-- Reviews -->
                        @if($product->reviews->count() > 0)
                            <div class="mb-2">
                                <div class="d-flex align-items-center">
                                    @php
                                        $avgRating = $product->reviews->avg('rating');
                                    @endphp
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $avgRating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <small class="text-muted ms-2">
                                        ({{ number_format($avgRating, 1) }} - {{ $product->reviews->count() }} {{ $product->reviews->count() == 1 ? 'avaliação' : 'avaliações' }})
                                    </small>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Botões -->
                        <div class="mt-auto">
                            <div class="d-grid gap-2">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i> Ver Detalhes
                                </a>
                                
                                @if($product->stock > 0)
                                    <form action="{{ route('cart.add', $product) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-cart-plus me-1"></i> Adicionar ao Carrinho
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                        <h3>Nenhum produto encontrado</h3>
                        <p class="text-muted mb-4">Tente ajustar os filtros de busca ou navegue pelas categorias.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">Ver todos os produtos</a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Paginação -->
    @if($products->hasPages())
        <div class="row">
            <div class="col-12 d-flex justify-content-center">
                {{ $products->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar cliques nos botões de wishlist
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');
            
            // Desabilitar botão temporariamente
            this.disabled = true;
            
            fetch(`/wishlist/${productId}/toggle`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Alternar aparência do ícone
                    if (data.is_in_wishlist) {
                        icon.classList.add('text-danger');
                        this.title = 'Remover da lista de desejos';
                    } else {
                        icon.classList.remove('text-danger');
                        this.title = 'Adicionar à lista de desejos';
                    }
                    
                    // Atualizar contador na navegação se existir
                    const wishlistCounter = document.getElementById('wishlist-counter');
                    if (wishlistCounter) {
                        wishlistCounter.textContent = data.wishlist_count || '0';
                        
                        // Se não há mais itens, ocultar o badge
                        if (data.wishlist_count === 0) {
                            wishlistCounter.style.display = 'none';
                        } else {
                            wishlistCounter.style.display = 'inline';
                        }
                    }
                    
                    // Mostrar toast de sucesso
                    showToast(data.message, 'success');
                } else {
                    showToast(data.message || 'Erro ao processar solicitação', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast('Erro ao processar solicitação', 'error');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});

function showToast(message, type) {
    // Criar um toast simples
    const toastDiv = document.createElement('div');
    toastDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    toastDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1055; min-width: 300px;';
    toastDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toastDiv);
    
    // Remover automaticamente após 3 segundos
    setTimeout(() => {
        if (toastDiv.parentNode) {
            toastDiv.parentNode.removeChild(toastDiv);
        }
    }, 3000);
}
</script>
@endsection