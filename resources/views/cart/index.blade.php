@extends('layouts.app')

@section('title', 'Carrinho de Compras')

@section('content')
<div class="container">
    <h1 class="mb-4">Carrinho de Compras</h1>
    
    @if(session()->has('cart') && count(session('cart')) > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Itens no Carrinho</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Preço</th>
                                        <th>Quantidade</th>
                                        <th>Subtotal</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $total = 0;
                                    @endphp
                                    @foreach(session('cart') as $id => $details)
                                        @php
                                            $subtotal = $details['price'] * $details['quantity'];
                                            $total += $subtotal;
                                        @endphp
                                        <tr id="product-row-{{ $id }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $details['image'] ? asset('storage/' . $details['image']) : 'https://via.placeholder.com/50x50' }}" 
                                                         class="rounded me-3" alt="{{ $details['name'] }}" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0">{{ $details['name'] }}</h6>
                                                        <small class="text-muted">{{ $details['petshop_name'] ?? '' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="product-price" data-price="{{ $details['price'] }}">
                                                    R$ {{ number_format($details['price'], 2, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <form class="quantity-form d-flex align-items-center" style="width: 120px;">
                                                    <input type="number" class="form-control form-control-sm quantity-input" 
                                                           data-product-id="{{ $id }}" 
                                                           value="{{ $details['quantity'] }}" 
                                                           min="1" max="99">
                                                </form>
                                            </td>
                                            <td>
                                                <span class="product-subtotal" id="subtotal-{{ $id }}">
                                                    R$ {{ number_format($subtotal, 2, ',', '.') }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('cart.remove') }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="product_id" value="{{ $id }}">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                            onclick="return confirm('Remover este item?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Cupom de Desconto -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Cupom de Desconto</h5>
                    </div>
                    <div class="card-body">
                        @auth
                            <div id="coupon-section">
                            @if(session('coupon'))
                                <!-- Cupom Aplicado -->
                                <div class="alert alert-success d-flex justify-content-between align-items-center mb-0" id="coupon-applied">
                                    <div>
                                        <strong>{{ session('coupon.code') }}</strong><br>
                                        <small>{{ session('coupon.name') }}</small><br>
                                        <span class="text-success">-R$ {{ number_format(session('coupon.discount'), 2, ',', '.') }}</span>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm" id="remove-coupon">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @else
                                <!-- Aplicar Cupom -->
                                <div id="coupon-form-section">
                                    <form id="coupon-form">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="coupon-code" 
                                                   placeholder="Código do cupom" style="text-transform: uppercase;">
                                            <button class="btn btn-outline-primary" type="submit" id="apply-coupon-btn">
                                                <i class="fas fa-tag me-1"></i>Aplicar
                                            </button>
                                        </div>
                                    </form>
                                    <div id="coupon-message" class="mt-2"></div>
                                </div>
                            @endif
                        </div>
                        @else
                            <p class="text-muted mb-0">
                                <a href="{{ route('login') }}">Faça login</a> para usar cupons de desconto.
                            </p>
                        @endauth
                    </div>
                </div>

                <!-- Resumo do Pedido -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumo do Pedido</h5>
                    </div>
                    <div class="card-body">
                        @php
                            $subtotal = $total;
                            $discount = session('coupon.discount', 0);
                            $finalTotal = $subtotal - $discount;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        
                        @if($discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Desconto ({{ session('coupon.code') }}):</span>
                                <span id="cart-discount">-R$ {{ number_format($discount, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="cart-total">R$ {{ number_format($finalTotal, 2, ',', '.') }}</strong>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('cart.checkout') }}" class="btn btn-primary">
                                <i class="fas fa-credit-card me-2"></i>Finalizar Compra
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-shopping-bag me-2"></i>Continuar Comprando
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger" 
                        onclick="return confirm('Tem certeza que deseja limpar o carrinho?')">
                    <i class="fas fa-trash me-2"></i>Limpar Carrinho
                </button>
            </form>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h3>Seu carrinho está vazio</h3>
                <p class="mb-4">Adicione produtos ao seu carrinho para continuar.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>Ver Produtos
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let couponData = @json(session('coupon')); // Armazenar dados do cupom do servidor
    
    // Função para renderizar a seção de cupom
    function renderCouponSection() {
        const couponSection = document.getElementById('coupon-section');
        if (!couponSection) return;
        
        if (couponData) {
            // Cupom aplicado
            couponSection.innerHTML = `
                <div class="alert alert-success d-flex justify-content-between align-items-center mb-0" id="coupon-applied">
                    <div>
                        <strong>${couponData.code}</strong><br>
                        <small>${couponData.name}</small><br>
                        <span class="text-success">-R$ ${formatMoney(couponData.discount)}</span>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="remove-coupon" title="Remover cupom">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        } else {
            // Formulário para aplicar cupom
            couponSection.innerHTML = `
                <div id="coupon-form-section">
                    <form id="coupon-form">
                        <div class="input-group">
                            <input type="text" class="form-control" id="coupon-code" 
                                   placeholder="Código do cupom" style="text-transform: uppercase;">
                            <button class="btn btn-outline-primary" type="submit" id="apply-coupon-btn">
                                <i class="fas fa-tag me-1"></i>Aplicar
                            </button>
                        </div>
                    </form>
                    <div id="coupon-message" class="mt-2"></div>
                </div>
            `;
        }
        
        // Reatachar eventos após renderizar
        attachCouponEvents();
    }
    
    // Função para anexar eventos
    function attachCouponEvents() {
        // Evento para aplicar cupom
        const couponForm = document.getElementById('coupon-form');
        if (couponForm) {
            couponForm.removeEventListener('submit', handleCouponSubmit); // Remove evento anterior
            couponForm.addEventListener('submit', handleCouponSubmit);
        }
        
        // Evento para remover cupom
        const removeCouponBtn = document.getElementById('remove-coupon');
        if (removeCouponBtn) {
            removeCouponBtn.removeEventListener('click', handleRemoveCoupon); // Remove evento anterior
            removeCouponBtn.addEventListener('click', handleRemoveCoupon);
        }
        
        // Converter código para maiúsculo
        const couponCodeInput = document.getElementById('coupon-code');
        if (couponCodeInput) {
            couponCodeInput.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }
    }
    
    // Handler para aplicar cupom
    function handleCouponSubmit(e) {
        e.preventDefault();
        
        const code = document.getElementById('coupon-code').value.trim();
        if (!code) return;
        
        const button = document.getElementById('apply-coupon-btn');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Aplicando...';
        button.disabled = true;
        
        fetch('/cart/coupon/apply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => response.json())
        .then(data => {
            const messageDiv = document.getElementById('coupon-message');
            
            if (data.success) {
                messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                
                // Atualizar dados do cupom localmente
                couponData = {
                    code: data.coupon.code,
                    name: data.coupon.name,
                    discount: data.coupon.discount
                };
                
                // Atualizar valores no resumo
                updateOrderSummary(data.totals);
                
                // Re-renderizar seção de cupom
                setTimeout(() => {
                    renderCouponSection();
                }, 1000);
                
            } else {
                messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                
                // Limpar mensagem após 5 segundos
                setTimeout(() => {
                    messageDiv.innerHTML = '';
                }, 5000);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            const messageDiv = document.getElementById('coupon-message');
            if (messageDiv) {
                messageDiv.innerHTML = '<div class="alert alert-danger">Erro ao aplicar cupom. Tente novamente.</div>';
                setTimeout(() => {
                    messageDiv.innerHTML = '';
                }, 5000);
            }
        })
        .finally(() => {
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    }
    
    // Handler para remover cupom
    function handleRemoveCoupon() {
        if (!confirm('Remover cupom de desconto?')) return;
        
        fetch('/cart/coupon/remove', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Limpar dados do cupom
                couponData = null;
                
                // Re-renderizar seção
                renderCouponSection();
                
                // Remover linha de desconto do resumo
                const discountRow = document.getElementById('discount-row');
                if (discountRow) {
                    discountRow.remove();
                }
                
                // Recalcular totais (recarregar é mais seguro)
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao remover cupom. Tente novamente.');
        });
    }
    
    // Função para atualizar resumo do pedido
    function updateOrderSummary(totals) {
        if (!totals) return;
        
        const subtotalElement = document.getElementById('cart-subtotal');
        const totalElement = document.getElementById('cart-total');
        
        if (subtotalElement) {
            subtotalElement.textContent = totals.subtotal_formatted;
        }
        
        if (totalElement) {
            totalElement.textContent = totals.total_formatted;
        }
        
        // Adicionar/atualizar linha de desconto
        let discountRow = document.getElementById('discount-row');
        if (totals.discount > 0) {
            if (!discountRow) {
                const totalRow = totalElement.closest('.d-flex');
                totalRow.insertAdjacentHTML('beforebegin', `
                    <div class="d-flex justify-content-between mb-2 text-success" id="discount-row">
                        <span>Desconto (${couponData.code}):</span>
                        <span id="cart-discount">-R$ ${formatMoney(totals.discount)}</span>
                    </div>
                `);
            } else {
                const discountElement = document.getElementById('cart-discount');
                if (discountElement) {
                    discountElement.textContent = `-R$ ${formatMoney(totals.discount)}`;
                }
            }
        }
    }
    
    // Função para formatar valor monetário
    function formatMoney(value) {
        return parseFloat(value).toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Observer para detectar mudanças no DOM
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            // Se a seção de cupom foi removida ou alterada, re-renderizar
            const couponSection = document.getElementById('coupon-section');
            if (couponSection && couponSection.innerHTML.trim() === '') {
                console.log('Seção de cupom vazia detectada, re-renderizando...');
                renderCouponSection();
            }
        });
    });
    
    // Observar mudanças no container do carrinho
    const cartContainer = document.querySelector('.container');
    if (cartContainer) {
        observer.observe(cartContainer, {
            childList: true,
            subtree: true,
            attributes: false
        });
    }
    
    // Verificar periodicamente se a seção ainda existe
    setInterval(function() {
        const couponSection = document.getElementById('coupon-section');
        if (couponSection) {
            // Se existe seção mas está vazia e deveria ter conteúdo
            if (couponSection.innerHTML.trim() === '' || 
                (!couponSection.querySelector('#coupon-form') && !couponSection.querySelector('#coupon-applied'))) {
                console.log('Seção de cupom perdida, restaurando...');
                renderCouponSection();
            }
        }
    }, 2000); // Verificar a cada 2 segundos
    
    // Inicializar
    renderCouponSection();
    
    // Código existente para atualizar quantidades...
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.getAttribute('data-product-id');
            const quantity = parseInt(this.value);
            
            if (quantity < 1) {
                this.value = 1;
                return;
            }
            
            // Encontrar o preço e calcular o subtotal
            const priceElement = document.querySelector(`#product-row-${productId} .product-price`);
            const price = parseFloat(priceElement.getAttribute('data-price'));
            const subtotal = price * quantity;
            
            // Atualizar o subtotal na interface
            const subtotalElement = document.getElementById(`subtotal-${productId}`);
            subtotalElement.textContent = `R$ ${formatCurrency(subtotal)}`;
            
            // Recalcular o total do carrinho
            updateCartTotal();
            
            // Enviar atualização para o servidor
            updateQuantityOnServer(productId, quantity);
        });
    });
});

function formatCurrency(value) {
    return value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function updateCartTotal() {
    let subtotal = 0;
    
    // Percorrer todos os subtotais
    document.querySelectorAll('.product-subtotal').forEach(element => {
        const subtotalText = element.textContent.replace('R$ ', '').replace('.', '').replace(',', '.');
        const subtotalValue = parseFloat(subtotalText);
        subtotal += subtotalValue;
    });
    
    // Atualizar o subtotal
    document.getElementById('cart-subtotal').textContent = `R$ ${formatCurrency(subtotal)}`;
    
    // Calcular desconto e total final
    const discountElement = document.getElementById('cart-discount');
    let discount = 0;
    
    if (discountElement) {
        const discountText = discountElement.textContent.replace('-R$ ', '').replace('.', '').replace(',', '.');
        discount = parseFloat(discountText);
    }
    
    const finalTotal = subtotal - discount;
    document.getElementById('cart-total').textContent = `R$ ${formatCurrency(finalTotal)}`;
}

function updateQuantityOnServer(productId, quantity) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity);
    formData.append('_method', 'PUT');
    formData.append('_token', csrfToken);
    
    fetch('{{ route('cart.update') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success && data.error) {
            alert(data.error);
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Erro ao atualizar carrinho:', error);
    });
}
</script>
@endsection