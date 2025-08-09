<div class="quick-filters-section py-4">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h5 class="mb-3 text-center">Busca Rápida</h5>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    <!-- Filtros por Tipo -->
                    <a href="{{ route('search.index', ['type' => 'products']) }}" 
                       class="btn btn-outline-primary">
                        <i class="fas fa-box me-2"></i>Produtos
                    </a>
                    
                    <a href="{{ route('search.index', ['type' => 'services']) }}" 
                       class="btn btn-outline-success">
                        <i class="fas fa-concierge-bell me-2"></i>Serviços
                    </a>
                    
                    <a href="{{ route('search.index', ['type' => 'petshops']) }}" 
                       class="btn btn-outline-info">
                        <i class="fas fa-store me-2"></i>Pet Shops
                    </a>
                    
                    <!-- Filtros Especiais -->
                    <a href="{{ route('search.index', ['featured' => '1']) }}" 
                       class="btn btn-outline-warning">
                        <i class="fas fa-star me-2"></i>Em Destaque
                    </a>
                    
                    <a href="{{ route('search.index', ['on_sale' => '1', 'type' => 'products']) }}" 
                       class="btn btn-outline-danger">
                        <i class="fas fa-tag me-2"></i>Promoções
                    </a>
                    
                    <a href="{{ route('search.index', ['min_rating' => '4']) }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-star me-2"></i>Bem Avaliados
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Categorias Populares -->
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="mb-3 text-center text-muted">Categorias Populares</h6>
                <div class="d-flex flex-wrap justify-content-center gap-2">
                    @php
                        $popularCategories = [
                            'Alimentação' => 'products',
                            'Banho e Tosa' => 'services',
                            'Brinquedos' => 'products',
                            'Veterinário' => 'services',
                            'Higiene e Cuidados' => 'products'
                        ];
                    @endphp
                    
                    @foreach($popularCategories as $category => $type)
                    <a href="{{ route('search.index', ['category' => $category, 'type' => $type]) }}" 
                       class="btn btn-sm btn-light border">
                        {{ $category }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.quick-filters-section {
    background-color: #f8f9fa;
    border-top: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
}

.quick-filters-section .btn {
    margin: 0.25rem;
    transition: all 0.2s ease;
}

.quick-filters-section .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .quick-filters-section .btn {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    
    .quick-filters-section .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>