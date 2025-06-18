@extends('layouts.app')

@section('title', 'Finalizar Compra')

@section('content')
<div class="container">
    <h1 class="mb-4">Finalizar Compra</h1>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Detalhes do Pedido</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col">Produto</th>
                                    <th scope="col" width="100">Preço</th>
                                    <th scope="col" width="80">Qtd</th>
                                    <th scope="col" width="120">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $product['image'] ? asset('storage/' . $product['image']) : asset('img/no-image.jpg') }}" alt="{{ $product['name'] }}" class="img-thumbnail me-3" width="60">
                                                <div>
                                                    <h6 class="mb-0">{{ $product['name'] }}</h6>
                                                    <small class="text-muted">{{ $product['petshop'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>R$ {{ number_format($product['price'], 2, ',', '.') }}</td>
                                        <td>{{ $product['quantity'] }}</td>
                                        <td>R$ {{ number_format($product['subtotal'], 2, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="bg-light">
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="fw-bold">R$ {{ number_format($total, 2, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Informações de Pagamento</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Método de Pagamento*</label>
                            <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method" required>
                                <option value="" selected disabled>Selecione...</option>
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
                                <option value="bank_slip" {{ old('payment_method') == 'bank_slip' ? 'selected' : '' }}>Boleto Bancário</option>
                                <option value="pix" {{ old('payment_method') == 'pix' ? 'selected' : '' }}>Pix</option>
                            </select>
                            @error('payment_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3" id="credit_card_details" style="display: none;">
                            <div class="mb-3">
                                <label for="card_number" class="form-label">Número do Cartão*</label>
                                <input type="text" class="form-control @error('card_number') is-invalid @enderror" 
                                      id="card_number" name="card_number" placeholder="0000 0000 0000 0000"
                                      value="{{ old('card_number') }}">
                                @error('card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="card_expiry" class="form-label">Data de Validade*</label>
                                    <input type="text" class="form-control @error('card_expiry') is-invalid @enderror" 
                                          id="card_expiry" name="card_expiry" placeholder="MM/AA"
                                          value="{{ old('card_expiry') }}">
                                    @error('card_expiry')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="card_cvv" class="form-label">CVV*</label>
                                    <input type="text" class="form-control @error('card_cvv') is-invalid @enderror" 
                                          id="card_cvv" name="card_cvv" placeholder="123"
                                          value="{{ old('card_cvv') }}">
                                    @error('card_cvv')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="card_name" class="form-label">Nome no Cartão*</label>
                                <input type="text" class="form-control @error('card_name') is-invalid @enderror" 
                                      id="card_name" name="card_name"
                                      value="{{ old('card_name') }}">
                                @error('card_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">Endereço de Entrega*</label>
                            <textarea class="form-control @error('shipping_address') is-invalid @enderror" id="shipping_address" name="shipping_address" rows="3" required>{{ old('shipping_address', auth()->user()->address) }}</textarea>
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary">Voltar ao Carrinho</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>Confirmar Pedido
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const creditCardDetails = document.getElementById('credit_card_details');
        const creditCardInputs = creditCardDetails.querySelectorAll('input');
        
        // Função para alternar a obrigatoriedade dos campos de cartão
        function toggleRequiredFields(isRequired) {
            creditCardInputs.forEach(input => {
                input.required = isRequired;
            });
        }
        
        paymentMethodSelect.addEventListener('change', function() {
            if (this.value === 'credit_card') {
                creditCardDetails.style.display = 'block';
                toggleRequiredFields(true);
            } else {
                creditCardDetails.style.display = 'none';
                toggleRequiredFields(false);
            }
        });
        
        // Verificar no carregamento inicial
        if (paymentMethodSelect.value === 'credit_card') {
            creditCardDetails.style.display = 'block';
            toggleRequiredFields(true);
        } else {
            toggleRequiredFields(false);
        }
        
        // Validação adicional no envio do formulário
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            if (paymentMethodSelect.value === 'credit_card') {
                let isValid = true;
                
                creditCardInputs.forEach(input => {
                    if (!input.value.trim()) {
                        isValid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos do cartão de crédito.');
                }
            }
        });
        
        // Formatação básica dos campos de cartão
        if (document.getElementById('card_number')) {
            document.getElementById('card_number').addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 16) value = value.slice(0, 16);
                
                // Adicionar espaços a cada 4 dígitos
                let formattedValue = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) {
                        formattedValue += ' ';
                    }
                    formattedValue += value[i];
                }
                
                this.value = formattedValue;
            });
        }
        
        if (document.getElementById('card_expiry')) {
            document.getElementById('card_expiry').addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 4) value = value.slice(0, 4);
                
                // Formato MM/AA
                if (value.length > 2) {
                    this.value = value.slice(0, 2) + '/' + value.slice(2);
                } else {
                    this.value = value;
                }
            });
        }
        
        if (document.getElementById('card_cvv')) {
            document.getElementById('card_cvv').addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 4) value = value.slice(0, 4);
                this.value = value;
            });
        }
    });
</script>
@endsection