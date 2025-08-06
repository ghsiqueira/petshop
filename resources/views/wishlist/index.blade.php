@extends('layouts.app')

@section('title', 'Minha Lista de Desejos')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-heart text-danger me-2"></i>
                    Minha Lista de Desejos
                </h1>
                <span class="badge bg-primary">{{ $wishlistItems->total() }} {{ $wishlistItems->total() == 1 ? 'item' : 'itens' }}</span>
            </div>
        </div>
    </div>

    @if($wishlistItems->count() > 0)
        <div class="row">
            @foreach($wishlistItems as $wishlistItem)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative">
                            <img src="{{ $wishlistItem->product->image ? asset('storage/' . $wishlistItem->product->image) : 'https://via.placeholder.com/300x200?text=Sem+Imagem' }}" 
                                 class="card-img-top" 
                                 alt="{{ $wishlistItem->product->name }}"
                                 style="height: 200px; object-fit: cover;">
                            
                            <!-- Botão de remover da wishlist -->
                            <button class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 wishlist-btn" 
                                    data-product-id="{{ $wishlistItem->product->id }}"
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="left" 
                                    title="Remover da lista de desejos">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $wishlistItem->product->name }}</h5>
                            <p class="card-text text-muted small flex-grow-1">
                                {{ Str::limit($wishlistItem->product->description, 80) }}
                            </p>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="h5 mb-0 text-primary">R$ {{ number_format($wishlistItem->product->price, 2, ',', '.') }}</span>
                                    
                                    @if($wishlistItem->product->stock > 0)
                                        <span class="badge bg-success">Em estoque</span>
                                    @else
                                        <span class="badge bg-danger">Indisponível</span>
                                    @endif
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('products.show', $wishlistItem->product) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i> Ver Detalhes
                                    </a>
                                    
                                    @if($wishlistItem->product->stock > 0)
                                        <form action="{{ route('cart.add', $wishlistItem->product) }}" method="POST" class="d-inline">
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
                        
                        <div class="card-footer text-muted small">
                            Adicionado em {{ $wishlistItem->created_at->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginação -->
        @if($wishlistItems->hasPages())
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                    {{ $wishlistItems->links() }}
                </div>
            </div>
        @endif
    @else
        <!-- Estado vazio -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-heart-broken fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">Sua lista de desejos está vazia</h3>
                        <p class="text-muted mb-4">
                            Explore nossos produtos e adicione seus favoritos à lista de desejos!
                        </p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>
                            Explorar Produtos
                        </a>
                    </div>
                </div>
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
                    // Se foi removido da wishlist, remover o card da tela
                    if (!data.is_in_wishlist) {
                        const card = this.closest('.col-lg-3, .col-md-4, .col-sm-6');
                        card.style.transition = 'opacity 0.3s ease-out';
                        card.style.opacity = '0';
                        
                        setTimeout(() => {
                            card.remove();
                            
                            // Verificar se não há mais itens
                            const remainingCards = document.querySelectorAll('.wishlist-btn').length - 1;
                            if (remainingCards === 0) {
                                location.reload(); // Recarregar para mostrar estado vazio
                            }
                        }, 300);
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
    // Criar um toast/alerta simples
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Remover automaticamente após 3 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 3000);
}
</script>
@endsection