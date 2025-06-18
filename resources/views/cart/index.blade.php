@extends('layouts.app')

@section('title', 'Carrinho de Compras')

@section('content')
<div class="container">
    <h1 class="mb-4">Carrinho de Compras</h1>
    
    @if(count($products) > 0)
        <div class="card mb-4">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0" scope="col" width="100">Produto</th>
                                <th class="border-0" scope="col">Descrição</th>
                                <th class="border-0" scope="col" width="120">Preço</th>
                                <th class="border-0" scope="col" width="120">Quantidade</th>
                                <th class="border-0" scope="col" width="120">Subtotal</th>
                                <th class="border-0" scope="col" width="80">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                <tr id="product-row-{{ $product['id'] }}">
                                    <td>
                                        <img src="{{ $product['image'] ? asset('storage/' . $product['image']) : asset('img/no-image.jpg') }}" alt="{{ $product['name'] }}" class="img-thumbnail" width="80">
                                    </td>
                                    <td>
                                        <h6>{{ $product['name'] }}</h6>
                                        <small class="text-muted">{{ $product['petshop'] }}</small>
                                    </td>
                                    <td>
                                        <span class="product-price" data-price="{{ $product['price'] }}">
                                            R$ {{ number_format($product['price'], 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <form class="quantity-form">
                                            @csrf
                                            <div class="input-group input-group-sm">
                                                <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                                <input type="number" name="quantity" class="form-control form-control-sm quantity-input" 
                                                    value="{{ $product['quantity'] }}" min="1" 
                                                    data-product-id="{{ $product['id'] }}"
                                                    style="max-width: 60px; text-align: center;">
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <span class="product-subtotal" id="subtotal-{{ $product['id'] }}">
                                            R$ {{ number_format($product['subtotal'], 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('cart.remove') }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            
                            <tr class="bg-light">
                                <td colspan="4" class="text-end fw-bold">Total:</td>
                                <td colspan="2" class="fw-bold">
                                    <span id="cart-total">R$ {{ number_format($total, 2, ',', '.') }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between">
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Limpar Carrinho</button>
            </form>
            
            <div>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary me-2">Continuar Comprando</a>
                <a href="{{ route('cart.checkout') }}" class="btn btn-primary">Finalizar Compra</a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h3>Seu carrinho está vazio</h3>
                <p class="mb-4">Adicione produtos ao seu carrinho para continuar.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">Ver Produtos</a>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Selecionar todos os inputs de quantidade
        const quantityInputs = document.querySelectorAll('.quantity-input');
        
        // Para cada input de quantidade
        quantityInputs.forEach(input => {
            // Adicionar evento para detectar mudanças
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
                
                // Enviar atualização para o servidor automaticamente
                updateQuantityOnServer(productId, quantity);
            });
        });
        
        // Função para formatar valor como moeda
        function formatCurrency(value) {
            return value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Função para atualizar o total do carrinho
        function updateCartTotal() {
            let total = 0;
            
            // Percorrer todos os subtotais
            document.querySelectorAll('.product-subtotal').forEach(element => {
                // Extrair o valor numérico do texto (R$ X.XXX,XX)
                const subtotalText = element.textContent.replace('R$ ', '').replace('.', '').replace(',', '.');
                const subtotal = parseFloat(subtotalText);
                total += subtotal;
            });
            
            // Atualizar o total na interface
            document.getElementById('cart-total').textContent = `R$ ${formatCurrency(total)}`;
        }
        
        // Função para enviar a atualização para o servidor
        function updateQuantityOnServer(productId, quantity) {
            // Obter o token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Criar dados do formulário
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            formData.append('_method', 'PUT');
            formData.append('_token', csrfToken);
            
            // Enviar requisição para atualizar o carrinho
            fetch('{{ route('cart.update') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mostrar mensagem de sucesso (opcional)
                    // alert('Carrinho atualizado!');
                } else if (data.error) {
                    alert(data.error);
                    // Recarregar a página para obter os valores corretos
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Erro ao atualizar carrinho:', error);
            });
        }
        
        // Prevenir o envio do formulário ao clicar no botão de atualizar
        document.querySelectorAll('.quantity-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const input = this.querySelector('.quantity-input');
                const productId = input.getAttribute('data-product-id');
                const quantity = parseInt(input.value);
                
                updateQuantityOnServer(productId, quantity);
            });
        });
    });
</script>
@endsection