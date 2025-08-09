<div class="search-bar-wrapper">
    <form method="GET" action="{{ route('search.index') }}" id="searchForm">
        <div class="search-bar-container">
            <div class="input-group input-group-lg">
                <!-- Seletor de Tipo -->
                <select class="form-select search-type-select" name="type" id="searchType">
                    <option value="all" {{ $type == 'all' ? 'selected' : '' }}>Tudo</option>
                    <option value="products" {{ $type == 'products' ? 'selected' : '' }}>Produtos</option>
                    <option value="services" {{ $type == 'services' ? 'selected' : '' }}>Serviços</option>
                    <option value="petshops" {{ $type == 'petshops' ? 'selected' : '' }}>Pet Shops</option>
                </select>

                <!-- Campo de Busca -->
                <div class="search-input-container">
                    <input type="text" 
                           class="form-control search-input" 
                           name="q" 
                           id="searchQuery"
                           value="{{ $query }}" 
                           placeholder="O que você está procurando?"
                           autocomplete="off">
                    
                    <!-- Dropdown de Sugestões -->
                    <div class="search-suggestions" id="searchSuggestions">
                        <!-- Sugestões serão carregadas via AJAX -->
                    </div>
                </div>

                <!-- Botão de Busca -->
                <button class="btn btn-primary search-btn" type="submit">
                    <i class="fas fa-search"></i>
                    <span class="d-none d-md-inline ms-2">Buscar</span>
                </button>
            </div>
        </div>

        <!-- Filtros Rápidos -->
        <div class="quick-filters mt-3">
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary quick-filter" 
                        data-filter="featured" data-value="1">
                    <i class="fas fa-star me-1"></i>Em Destaque
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary quick-filter" 
                        data-filter="on_sale" data-value="1">
                    <i class="fas fa-tag me-1"></i>Em Promoção
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary quick-filter" 
                        data-filter="in_stock" data-value="1">
                    <i class="fas fa-check-circle me-1"></i>Em Estoque
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary quick-filter" 
                        data-filter="min_rating" data-value="4">
                    <i class="fas fa-star me-1"></i>4+ Estrelas
                </button>
            </div>
        </div>

        <!-- Campos Hidden para Filtros -->
        <input type="hidden" name="category" value="{{ $category }}">
        <input type="hidden" name="min_price" value="{{ $minPrice }}">
        <input type="hidden" name="max_price" value="{{ $maxPrice }}">
        <input type="hidden" name="min_rating" value="{{ $minRating }}">
        <input type="hidden" name="city" value="{{ $city }}">
        <input type="hidden" name="state" value="{{ $state }}">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="featured" value="{{ $featured }}">
        <input type="hidden" name="on_sale" value="{{ $onSale }}">
        <input type="hidden" name="in_stock" value="{{ $inStock }}">
    </form>
</div>