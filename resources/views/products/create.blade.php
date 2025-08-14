@extends('layouts.app')

@section('title', 'Novo Produto')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('petshop.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('petshop.products.index') }}">Produtos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Novo Produto</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus me-2 text-primary"></i>Novo Produto</h1>
        <a href="{{ route('petshop.products.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Voltar
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Erro na validação:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('petshop.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Informações Básicas -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informações do Produto
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label">Nome do Produto*</label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       placeholder="Ex: Ração Premium para Cães"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Categoria*</label>
                                <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                    <option value="" disabled selected>Selecione uma categoria</option>
                                    <option value="racao" {{ old('category') == 'racao' ? 'selected' : '' }}>Ração</option>
                                    <option value="petiscos" {{ old('category') == 'petiscos' ? 'selected' : '' }}>Petiscos</option>
                                    <option value="brinquedos" {{ old('category') == 'brinquedos' ? 'selected' : '' }}>Brinquedos</option>
                                    <option value="higiene" {{ old('category') == 'higiene' ? 'selected' : '' }}>Higiene</option>
                                    <option value="acessorios" {{ old('category') == 'acessorios' ? 'selected' : '' }}>Acessórios</option>
                                    <option value="medicamentos" {{ old('category') == 'medicamentos' ? 'selected' : '' }}>Medicamentos</option>
                                    <option value="outros" {{ old('category') == 'outros' ? 'selected' : '' }}>Outros</option>
                                </select>
                                @error('category')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="brand" class="form-label">Marca</label>
                                <input type="text" 
                                       class="form-control @error('brand') is-invalid @enderror" 
                                       id="brand" 
                                       name="brand" 
                                       value="{{ old('brand') }}" 
                                       placeholder="Ex: Royal Canin">
                                @error('brand')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Descrição*</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          placeholder="Descreva detalhadamente o produto..."
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preço e Estoque -->
                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-dollar-sign me-2"></i>
                            Preço e Estoque
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Preço*</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" 
                                           name="price" 
                                           value="{{ old('price') }}" 
                                           step="0.01" 
                                           min="0"
                                           placeholder="0,00"
                                           required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="stock_quantity" class="form-label">Quantidade em Estoque*</label>
                                <input type="number" 
                                       class="form-control @error('stock_quantity') is-invalid @enderror" 
                                       id="stock_quantity" 
                                       name="stock_quantity" 
                                       value="{{ old('stock_quantity', 0) }}" 
                                       min="0"
                                       required>
                                @error('stock_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="minimum_stock" class="form-label">Estoque Mínimo</label>
                                <input type="number" 
                                       class="form-control @error('minimum_stock') is-invalid @enderror" 
                                       id="minimum_stock" 
                                       name="minimum_stock" 
                                       value="{{ old('minimum_stock', 5) }}" 
                                       min="0">
                                @error('minimum_stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Alerta quando estoque estiver baixo</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Imagem do Produto -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-image me-2"></i>
                            Imagem do Produto
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="image-upload-area" id="imageUploadArea">
                            <div class="upload-placeholder" id="uploadPlaceholder">
                                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Clique para adicionar uma imagem</p>
                                <small class="text-muted">JPG, PNG ou GIF (máx. 2MB)</small>
                            </div>
                            <img id="imagePreview" class="img-fluid rounded d-none" alt="Preview">
                        </div>
                        
                        <input type="file" 
                               class="form-control d-none @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        
                        @error('image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Status e Configurações -->
                <div class="card mt-4">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Configurações
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>Produto Ativo</strong>
                                <br><small class="text-muted">Visível para os clientes</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="featured" 
                                   name="featured" 
                                   {{ old('featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="featured">
                                <strong>Produto em Destaque</strong>
                                <br><small class="text-muted">Aparece na página inicial</small>
                            </label>
                        </div>

                        <div class="mb-3">
                            <label for="weight" class="form-label">Peso (kg)</label>
                            <input type="number" 
                                   class="form-control @error('weight') is-invalid @enderror" 
                                   id="weight" 
                                   name="weight" 
                                   value="{{ old('weight') }}" 
                                   step="0.01" 
                                   min="0"
                                   placeholder="0.00">
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU/Código</label>
                            <input type="text" 
                                   class="form-control @error('sku') is-invalid @enderror" 
                                   id="sku" 
                                   name="sku" 
                                   value="{{ old('sku') }}" 
                                   placeholder="Ex: RAC001">
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botões de Ação -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('petshop.products.index') }}" class="btn btn-outline-secondary me-md-2">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Salvar Produto
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.image-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 2rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.image-upload-area:hover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.upload-placeholder {
    text-align: center;
}

#imagePreview {
    max-height: 200px;
    border: 1px solid #ddd;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Upload de imagem
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');

    imageUploadArea.addEventListener('click', function() {
        imageInput.click();
    });

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Verificar tamanho do arquivo (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Arquivo muito grande! Máximo 2MB.');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.classList.remove('d-none');
                uploadPlaceholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Gerar SKU automaticamente baseado no nome
    const nameInput = document.getElementById('name');
    const skuInput = document.getElementById('sku');
    
    nameInput.addEventListener('blur', function() {
        if (!skuInput.value && this.value) {
            const sku = this.value
                .toUpperCase()
                .replace(/[^A-Z0-9]/g, '')
                .substring(0, 6) + Math.floor(Math.random() * 100);
            skuInput.value = sku;
        }
    });

    // Loading no submit
    document.querySelector('form').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Salvando...';
        submitBtn.disabled = true;
    });

    // Máscara para preço
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('input', function() {
        let value = this.value.replace(/[^\d.,]/g, '');
        this.value = value;
    });
});
</script>
@endpush