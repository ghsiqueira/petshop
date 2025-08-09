<form id="filtersForm">
    <!-- Filtro por Categoria -->
    @if(isset($filters['categories']) && count($filters['categories']['products']) > 0 || count($filters['categories']['services']) > 0)
    <div class="filter-group mb-4">
        <label class="form-label fw-bold">Categoria</label>
        <select class="form-select" name="category" id="categoryFilter">
            <option value="">Todas as categorias</option>
            @if($type == 'all' || $type == 'products')
                <optgroup label="Produtos">
                    @foreach($filters['categories']['products'] as $cat)
                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                    @endforeach
                </optgroup>
            @endif
            @if($type == 'all' || $type == 'services')
                <optgroup label="Serviços">
                    @foreach($filters['categories']['services'] as $cat)
                    <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                        {{ $cat }}
                    </option>
                    @endforeach
                </optgroup>
            @endif
        </select>
    </div>
    @endif

    <!-- Filtro por Preço -->
    @if($type != 'petshops')
    <div class="filter-group mb-4">
        <label class="form-label fw-bold">Faixa de Preço</label>
        
        <!-- Faixas Pré-definidas -->
        <div class="price-ranges mb-3">
            @foreach($filters['price_ranges'] as $range)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="price_range" 
                       id="price_{{ $loop->index }}" 
                       value="{{ isset($range['min']) ? $range['min'] : 0 }}-{{ isset($range['max']) ? $range['max'] : 999999 }}"
                       {{ ($minPrice == ($range['min'] ?? 0) && $maxPrice == ($range['max'] ?? 999999)) ? 'checked' : '' }}>
                <label class="form-check-label" for="price_{{ $loop->index }}">
                    {{ $range['label'] }}
                </label>
            </div>
            @endforeach
        </div>

        <!-- Valores Personalizados -->
        <div class="custom-price-range">
            <div class="row">
                <div class="col-6">
                    <label class="form-label small">Mínimo</label>
                    <input type="number" class="form-control form-control-sm" 
                           name="min_price" id="minPrice" 
                           value="{{ $minPrice }}" 
                           placeholder="R$ 0" min="0">
                </div>
                <div class="col-6">
                    <label class="form-label small">Máximo</label>
                    <input type="number" class="form-control form-control-sm" 
                           name="max_price" id="maxPrice" 
                           value="{{ $maxPrice }}" 
                           placeholder="R$ 999+" min="0">
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtro por Avaliação -->
    <div class="filter-group mb-4">
        <label class="form-label fw-bold">Avaliação</label>
        <div class="rating-filters">
            @for($i = 5; $i >= 1; $i--)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="min_rating" 
                       id="rating_{{ $i }}" value="{{ $i }}"
                       {{ $minRating == $i ? 'checked' : '' }}>
                <label class="form-check-label d-flex align-items-center" for="rating_{{ $i }}">
                    <div class="stars me-2">
                        @for($j = 1; $j <= 5; $j++)
                        <i class="fas fa-star {{ $j <= $i ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                    </div>
                    <span>{{ $i }}+ estrelas</span>
                </label>
            </div>
            @endfor
        </div>
    </div>

    <!-- Filtro por Localização (apenas para petshops) -->
    @if($type == 'all' || $type == 'petshops')
    <div class="filter-group mb-4">
        <label class="form-label fw-bold">Localização</label>
        
        <div class="mb-3">
            <label class="form-label small">Cidade</label>
            <select class="form-select" name="city" id="cityFilter">
                <option value="">Todas as cidades</option>
                @foreach($filters['cities'] as $cityOption)
                <option value="{{ $cityOption }}" {{ $city == $cityOption ? 'selected' : '' }}>
                    {{ $cityOption }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label small">Estado</label>
            <select class="form-select" name="state" id="stateFilter">
                <option value="">Todos os estados</option>
                @foreach($filters['states'] as $stateOption)
                <option value="{{ $stateOption }}" {{ $state == $stateOption ? 'selected' : '' }}>
                    {{ $stateOption }}
                </option>
                @endforeach
            </select>
        </div>
    </div>
    @endif

    <!-- Filtros Especiais -->
    <div class="filter-group mb-4">
        <label class="form-label fw-bold">Filtros Especiais</label>
        
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="featured" 
                   id="featuredFilter" value="1" {{ $featured ? 'checked' : '' }}>
            <label class="form-check-label" for="featuredFilter">
                <i class="fas fa-star text-warning me-1"></i>Em Destaque
            </label>
        </div>

        @if($type == 'all' || $type == 'products')
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="on_sale" 
                   id="onSaleFilter" value="1" {{ $onSale ? 'checked' : '' }}>
            <label class="form-check-label" for="onSaleFilter">
                <i class="fas fa-tag text-success me-1"></i>Em Promoção
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="in_stock" 
                   id="inStockFilter" value="1" {{ $inStock ? 'checked' : '' }}>
            <label class="form-check-label" for="inStockFilter">
                <i class="fas fa-check-circle text-info me-1"></i>Em Estoque
            </label>
        </div>
        @endif
    </div>

    <!-- Botões de Ação -->
    <div class="filter-actions">
        <button type="button" class="btn btn-primary w-100 mb-2" id="applyFilters">
            <i class="fas fa-search me-2"></i>Aplicar Filtros
        </button>
        <button type="button" class="btn btn-outline-secondary w-100" id="clearFilters">
            <i class="fas fa-times me-2"></i>Limpar Filtros
        </button>
    </div>
</form>