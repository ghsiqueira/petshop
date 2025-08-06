@extends('layouts.app')

@section('title', 'Criar Cupom')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('coupons.index') }}">Cupons</a></li>
            <li class="breadcrumb-item active">Criar Cupom</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus me-2"></i>Criar Novo Cupom</h1>
        <a href="{{ route('coupons.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informações do Cupom</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('coupons.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="code" class="form-label">Código do Cupom *</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       id="code" name="code" value="{{ old('code') }}" 
                                       placeholder="Ex: BLACKFRIDAY10" style="text-transform: uppercase;">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Use apenas letras, números e alguns símbolos. Será convertido para maiúsculo.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome Amigável *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Ex: Black Friday 2024">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="2" 
                                      placeholder="Descrição opcional do cupom">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="type" class="form-label">Tipo de Desconto *</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type">
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Porcentagem (%)</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Valor Fixo (R$)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="value" class="form-label">Valor do Desconto *</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="value-symbol">%</span>
                                    <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                           id="value" name="value" value="{{ old('value') }}" 
                                           step="0.01" min="0" placeholder="10">
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="minimum_amount" class="form-label">Valor Mínimo</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                           id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount') }}" 
                                           step="0.01" min="0" placeholder="0.00">
                                    @error('minimum_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Valor mínimo do pedido para usar o cupom</div>
                            </div>
                        </div>

                        <div class="row mb-3" id="maximum-discount-row" style="display: none;">
                            <div class="col-md-4">
                                <label for="maximum_discount" class="form-label">Desconto Máximo</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" class="form-control @error('maximum_discount') is-invalid @enderror" 
                                           id="maximum_discount" name="maximum_discount" value="{{ old('maximum_discount') }}" 
                                           step="0.01" min="0" placeholder="0.00">
                                    @error('maximum_discount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">Limite máximo de desconto (apenas para porcentagem)</div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="usage_limit" class="form-label">Limite Total de Uso</label>
                                <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                       id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" 
                                       min="1" placeholder="Ilimitado">
                                @error('usage_limit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deixe vazio para uso ilimitado</div>
                            </div>
                            <div class="col-md-4">
                                <label for="usage_limit_per_user" class="form-label">Limite por Usuário *</label>
                                <input type="number" class="form-control @error('usage_limit_per_user') is-invalid @enderror" 
                                       id="usage_limit_per_user" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', 1) }}" 
                                       min="1">
                                @error('usage_limit_per_user')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="starts_at" class="form-label">Data de Início</label>
                                <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" 
                                       id="starts_at" name="starts_at" value="{{ old('starts_at') }}">
                                @error('starts_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deixe vazio para ativar imediatamente</div>
                            </div>
                            <div class="col-md-6">
                                <label for="expires_at" class="form-label">Data de Expiração</label>
                                <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                       id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                @error('expires_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Deixe vazio para nunca expirar</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Ativar cupom imediatamente
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Criar Cupom
                            </button>
                            <a href="{{ route('coupons.index') }}" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Dicas para Cupons</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-lightbulb text-warning me-2"></i>
                            <strong>Códigos memoráveis:</strong> Use códigos fáceis como NATAL2024, FRETE10
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-percent text-info me-2"></i>
                            <strong>Desconto em %:</strong> Ideal para vendas sazonais (10%, 15%, 20%)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-dollar-sign text-success me-2"></i>
                            <strong>Valor fixo:</strong> Melhor para primeira compra (R$ 10 OFF)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar text-primary me-2"></i>
                            <strong>Urgência:</strong> Defina data de expiração para criar urgência
                        </li>
                        <li>
                            <i class="fas fa-users text-secondary me-2"></i>
                            <strong>Limite de uso:</strong> Controle a quantidade para evitar abusos
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const valueSymbol = document.getElementById('value-symbol');
    const maxDiscountRow = document.getElementById('maximum-discount-row');
    
    function updateDiscountType() {
        if (typeSelect.value === 'percentage') {
            valueSymbol.textContent = '%';
            maxDiscountRow.style.display = 'block';
        } else {
            valueSymbol.textContent = 'R$';
            maxDiscountRow.style.display = 'none';
        }
    }
    
    typeSelect.addEventListener('change', updateDiscountType);
    updateDiscountType(); // Executar na carga da página
    
    // Converter código para maiúsculo
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
    });
});
</script>
@endsection