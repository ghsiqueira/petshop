@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Início</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produtos</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-5">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('img/no-image.jpg') }}" class="img-fluid rounded" alt="{{ $product->name }}">
        </div>
        
        <div class="col-md-7">
            <!-- Título com botão de wishlist -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-grow-1">
                    <h1>{{ $product->name }}</h1>
                </div>
                
                @auth
                    <button class="btn btn-outline-danger wishlist-btn ms-3" 
                            data-product-id="{{ $product->id }}"
                            data-bs-toggle="tooltip" 
                            data-bs-placement="left" 
                            title="{{ auth()->user()->hasInWishlist($product->id) ? 'Remover da lista de desejos' : 'Adicionar à lista de desejos' }}">
                        <i class="fas fa-heart {{ auth()->user()->hasInWishlist($product->id) ? 'text-danger' : '' }} me-2"></i>
                        <span class="wishlist-text d-none d-md-inline">
                            {{ auth()->user()->hasInWishlist($product->id) ? 'Remover dos Favoritos' : 'Adicionar aos Favoritos' }}
                        </span>
                    </button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-danger ms-3">
                        <i class="fas fa-heart me-2"></i>
                        <span class="d-none d-md-inline">Adicionar aos Favoritos</span>
                    </a>
                @endauth
            </div>
            
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('petshops.show', $product->petshop->id) }}" class="text-decoration-none">
                    <span class="text-muted">{{ $product->petshop->name }}</span>
                </a>
                
                @if($product->reviews->count() > 0)
                    <div class="ms-3">
                        @php
                            $avgRating = $product->reviews->avg('rating');
                        @endphp
                        
                        <div class="d-flex align-items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($avgRating))
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                            <span class="ms-1 text-muted">({{ $product->reviews->count() }})</span>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="mb-3">
                <h3 class="text-primary">R$ {{ number_format($product->price, 2, ',', '.') }}</h3>
            </div>
            
            <div class="mb-4">
                <p>{{ $product->description }}</p>
            </div>
            
            <div class="mb-4">
                <p>
                    <strong>Disponibilidade:</strong>
                    @if($product->stock > 0)
                        <span class="text-success">Em estoque ({{ $product->stock }} unidades)</span>
                    @else
                        <span class="text-danger">Indisponível</span>
                    @endif
                </p>
            </div>
            
            @if($product->stock > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-3">
                        <div class="col-auto">
                            <div class="input-group">
                                <label class="input-group-text" for="quantity">Quantidade</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" max="{{ $product->stock }}">
                            </div>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-cart-plus me-2"></i>Adicionar ao Carrinho
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
    
    <!-- Seção de avaliações -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Avaliações</h3>
            
            @if($reviews->count() > 0)
                <div class="mb-4">
                    @foreach($reviews as $review)
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h5 class="card-title mb-0">{{ $review->user->name }}</h5>
                                        <small class="text-muted">{{ $review->created_at->format('d/m/Y') }}</small>
                                    </div>
                                    <div>
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <p class="card-text">{{ $review->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>Este produto ainda não possui avaliações.</p>
            @endif
            
            @auth
                @if(auth()->user()->hasRole('client'))
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Deixe sua avaliação</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('products.reviews.store', $product->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="rating" class="form-label">Avaliação</label>
                                    <div class="rating-stars mb-3">
                                        <div class="d-flex">
                                            @for($i = 5; $i >= 1; $i--)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" required>
                                                    <label class="form-check-label" for="rating{{ $i }}">{{ $i }} <i class="far fa-star"></i></label>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="comment" class="form-label">Comentário (opcional)</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Enviar Avaliação</button>
                            </form>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-info">
                    <a href="{{ route('login') }}">Entre</a> ou <a href="{{ route('register') }}">Cadastre-se</a> para deixar uma avaliação.
                </div>
            @endauth
        </div>
    </div>
    
    <!-- Produtos relacionados -->
    @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Produtos Relacionados</h3>
                
                <div class="row">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="col-md-3 mb-4">
                            <div class="card h-100 position-relative">
                                <!-- Botão wishlist nos produtos relacionados -->
                                @auth
                                    <button class="btn btn-outline-danger btn-sm position-absolute wishlist-btn" 
                                            style="top: 10px; right: 10px; z-index: 10; border-radius: 50%; width: 35px; height: 35px; padding: 0;"
                                            data-product-id="{{ $relatedProduct->id }}"
                                            data-bs-toggle="tooltip" 
                                            data-bs-placement="left" 
                                            title="{{ auth()->user()->hasInWishlist($relatedProduct->id) ? 'Remover da lista de desejos' : 'Adicionar à lista de desejos' }}">
                                        <i class="fas fa-heart {{ auth()->user()->hasInWishlist($relatedProduct->id) ? 'text-danger' : '' }}"></i>
                                    </button>
                                @endauth

                                <img src="{{ $relatedProduct->image ? asset('storage/' . $relatedProduct->image) : asset('img/no-image.jpg') }}" 
                                     class="card-img-top" 
                                     alt="{{ $relatedProduct->name }}" 
                                     style="height: 200px; object-fit: cover;">
                                
                                <div class="card-body">
                                    <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                                    <p class="card-text text-primary fw-bold">R$ {{ number_format($relatedProduct->price, 2, ',', '.') }}</p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <a href="{{ route('products.show', $relatedProduct->id) }}" class="btn btn-outline-primary w-100">Ver Detalhes</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script para destacar estrelas na avaliação
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            const rating = parseInt(this.value);
            
            ratingInputs.forEach((input, index) => {
                const starLabel = input.nextElementSibling;
                const starIcon = starLabel.querySelector('i');
                
                if (parseInt(input.value) <= rating) {
                    starIcon.classList.remove('far');
                    starIcon.classList.add('fas');
                } else {
                    starIcon.classList.remove('fas');
                    starIcon.classList.add('far');
                }
            });
        });
    });
    
    // Script para botões de wishlist
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const icon = this.querySelector('i');
            const text = this.querySelector('.wishlist-text');
            
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
                    // Alternar aparência do botão
                    if (data.is_in_wishlist) {
                        icon.classList.add('text-danger');
                        if (text) {
                            text.textContent = 'Remover dos Favoritos';
                        }
                        this.title = 'Remover da lista de desejos';
                    } else {
                        icon.classList.remove('text-danger');
                        if (text) {
                            text.textContent = 'Adicionar aos Favoritos';
                        }
                        this.title = 'Adicionar à lista de desejos';
                    }
                    
                    // Atualizar contador na navegação
                    const wishlistCounter = document.getElementById('wishlist-counter');
                    if (wishlistCounter) {
                        wishlistCounter.textContent = data.wishlist_count || '0';
                        
                        if (data.wishlist_count === 0) {
                            wishlistCounter.style.display = 'none';
                        } else {
                            wishlistCounter.style.display = 'inline';
                        }
                    }
                    
                    // Mostrar mensagem de sucesso
                    showAlert(data.message, 'success');
                } else {
                    showAlert(data.message || 'Erro ao processar solicitação', 'error');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('Erro ao processar solicitação', 'error');
            })
            .finally(() => {
                this.disabled = false;
            });
        });
    });
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1055; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 3000);
}
</script>
@endsection