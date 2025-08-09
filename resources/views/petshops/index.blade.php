@extends('layouts.app')

@section('title', 'Pet Shops')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-store me-2"></i>Pet Shops</h1>
        <small class="text-muted">{{ $petshops->total() }} pet shops encontrados</small>
    </div>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('petshops.index') }}" method="GET" id="filterForm">
                <div class="row g-3">
                    <!-- Busca por nome -->
                    <div class="col-md-6 col-lg-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" 
                               class="form-control" 
                               id="search" 
                               name="search" 
                               placeholder="Nome, descrição..." 
                               value="{{ request('search') }}">
                    </div>
                    
                    <!-- Filtro por cidade -->
                    <div class="col-md-6 col-lg-3">
                        <label for="city" class="form-label">Cidade</label>
                        <select name="city" id="city" class="form-select">
                            <option value="">Todas as cidades</option>
                            @foreach($cities as $city)
                                <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                    {{ $city }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtro por tipo de serviço -->
                    <div class="col-md-6 col-lg-3">
                        <label for="service_type" class="form-label">Tipo de Serviço</label>
                        <select name="service_type" id="service_type" class="form-select">
                            <option value="">Todos os serviços</option>
                            @foreach($serviceTypes as $serviceType)
                                <option value="{{ $serviceType }}" {{ request('service_type') == $serviceType ? 'selected' : '' }}>
                                    {{ $serviceType }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filtro por categoria de produtos -->
                    <div class="col-md-6 col-lg-3">
                        <label for="product_category" class="form-label">Categoria de Produtos</label>
                        <select name="product_category" id="product_category" class="form-select">
                            <option value="">Todas as categorias</option>
                            @foreach($productCategories as $key => $category)
                                <option value="{{ $key }}" {{ request('product_category') == $key ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <!-- Ordenação -->
                    <div class="col-md-6 col-lg-3">
                        <label for="sort" class="form-label">Ordenar por</label>
                        <select name="sort" id="sort" class="form-select">
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nome A-Z</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mais recentes</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Melhor avaliados</option>
                        </select>
                    </div>
                    
                    <!-- Botões de ação -->
                    <div class="col-md-6 col-lg-3 d-flex align-items-end">
                        <div class="btn-group w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('petshops.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Resultados -->
    @if($petshops->count() > 0)
        <div class="row">
            @foreach($petshops as $petshop)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <!-- Logo do petshop -->
                        <div class="position-relative">
                            <img src="{{ $petshop->logo ? asset('storage/' . $petshop->logo) : asset('img/no-logo.jpg') }}" 
                                 class="card-img-top" 
                                 alt="{{ $petshop->name }}" 
                                 style="height: 200px; object-fit: cover;">
                            
                            <!-- Badge com número de produtos/serviços -->
                            <div class="position-absolute top-0 end-0 m-2">
                                @if($petshop->products_count > 0)
                                    <span class="badge bg-primary me-1">
                                        <i class="fas fa-box me-1"></i>{{ $petshop->products_count }}
                                    </span>
                                @endif
                                @if($petshop->services_count > 0)
                                    <span class="badge bg-success">
                                        <i class="fas fa-cut me-1"></i>{{ $petshop->services_count }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $petshop->name }}</h5>
                            <p class="card-text text-muted flex-grow-1">
                                {{ Str::limit($petshop->description, 100) }}
                            </p>
                            
                            <!-- Informações de contato -->
                            <div class="mb-3">
                                <small class="text-muted d-block">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ Str::limit($petshop->address, 50) }}
                                </small>
                                @if($petshop->phone)
                                    <small class="text-muted d-block">
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $petshop->phone }}
                                    </small>
                                @endif
                                @if($petshop->opening_hours)
                                    <small class="text-muted d-block">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $petshop->opening_hours }}
                                    </small>
                                @endif
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white border-top-0">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                <a href="{{ route('petshops.show', $petshop->id) }}" 
                                   class="btn btn-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i>Visitar
                                </a>
                                @if($petshop->products_count > 0)
                                    <a href="{{ route('petshops.show', $petshop->id) }}#products" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-shopping-cart me-1"></i>Produtos
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Paginação -->
        <div class="d-flex justify-content-center mt-4">
            {{ $petshops->links() }}
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <h4>Nenhum pet shop encontrado</h4>
                    <p class="mb-0">Tente ajustar os filtros ou remover algumas restrições de busca.</p>
                    <a href="{{ route('petshops.index') }}" class="btn btn-primary mt-3">
                        Ver todos os pet shops
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Auto-submit no change dos selects para melhor UX
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('#city, #service_type, #product_category, #sort');
    
    selects.forEach(select => {
        select.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
    
    // Submit no enter do campo de busca
    document.getElementById('search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('filterForm').submit();
        }
    });
});
</script>
@endpush
@endsection