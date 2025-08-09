@extends('layouts.app')

@section('title', 'Buscar - ' . ($query ? $query : 'Pesquisa Avançada'))

@section('head')
<link href="{{ asset('css/modules/search-system.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar de Filtros -->
        <div class="col-lg-3 col-md-4">
            <div class="search-filters-sidebar">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filtros
                            <button class="btn btn-sm btn-outline-secondary ms-auto" id="clearFilters">
                                Limpar
                            </button>
                        </h5>
                    </div>
                    <div class="card-body">
                        @include('search.components.filters')
                    </div>
                </div>

                <!-- Buscas Recentes -->
                @if($recentSearches->isNotEmpty() && auth()->check())
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-history me-2"></i>Buscas Recentes
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($recentSearches as $recent)
                        <a href="{{ route('search.index', ['q' => $recent->query, 'type' => $recent->type]) }}" 
                           class="d-block text-decoration-none mb-2 recent-search-item">
                            <small class="text-muted">{{ $recent->query }}</small>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Buscas Populares -->
                @if($popularSearches->isNotEmpty())
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-fire me-2"></i>Trending
                        </h6>
                    </div>
                    <div class="card-body">
                        @foreach($popularSearches as $popular)
                        <a href="{{ route('search.index', ['q' => $popular->query]) }}" 
                           class="d-block text-decoration-none mb-2 popular-search-item">
                            <small>{{ $popular->query }}</small>
                            <span class="badge bg-secondary ms-1">{{ $popular->search_count }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Área Principal de Resultados -->
        <div class="col-lg-9 col-md-8">
            <!-- Barra de Busca Superior -->
            <div class="search-header mb-4">
                @include('search.components.search-bar')
            </div>

            <!-- Informações dos Resultados -->
            <div class="search-results-info d-flex justify-content-between align-items-center mb-4">
                <div>
                    @if($query)
                        <h4>Resultados para: <span class="text-primary">"{{ $query }}"</span></h4>
                        <p class="text-muted mb-0">{{ number_format($totalResults) }} resultado(s) encontrado(s)</p>
                    @else
                        <h4>Explore nossos produtos e serviços</h4>
                        <p class="text-muted mb-0">Use os filtros para encontrar o que procura</p>
                    @endif
                </div>

                <!-- Ordenação -->
                @if($totalResults > 0)
                <div class="search-sort">
                    <select class="form-select" id="sortSelect" onchange="updateSort(this.value)">
                        <option value="relevance" {{ $sort == 'relevance' ? 'selected' : '' }}>Relevância</option>
                        <option value="price_asc" {{ $sort == 'price_asc' ? 'selected' : '' }}>Menor preço</option>
                        <option value="price_desc" {{ $sort == 'price_desc' ? 'selected' : '' }}>Maior preço</option>
                        <option value="rating" {{ $sort == 'rating' ? 'selected' : '' }}>Melhor avaliação</option>
                        <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Mais recente</option>
                        <option value="name" {{ $sort == 'name' ? 'selected' : '' }}>Nome A-Z</option>
                        <option value="featured" {{ $sort == 'featured' ? 'selected' : '' }}>Em destaque</option>
                    </select>
                </div>
                @endif
            </div>

            <!-- Filtros Ativos (Tags) -->
            @php
                $activeFilters = collect();
                if($category) $activeFilters->push(['label' => 'Categoria: ' . $category, 'param' => 'category']);
                if($minPrice) $activeFilters->push(['label' => 'Preço mín: R$ ' . $minPrice, 'param' => 'min_price']);
                if($maxPrice) $activeFilters->push(['label' => 'Preço máx: R$ ' . $maxPrice, 'param' => 'max_price']);
                if($minRating) $activeFilters->push(['label' => 'Avaliação: ' . $minRating . '+', 'param' => 'min_rating']);
                if($city) $activeFilters->push(['label' => 'Cidade: ' . $city, 'param' => 'city']);
                if($state) $activeFilters->push(['label' => 'Estado: ' . $state, 'param' => 'state']);
            @endphp

            @if($activeFilters->isNotEmpty())
            <div class="active-filters mb-3">
                <span class="me-2">Filtros ativos:</span>
                @foreach($activeFilters as $filter)
                <span class="badge bg-primary me-1">
                    {{ $filter['label'] }}
                    <button type="button" class="btn-close btn-close-white ms-1" 
                            onclick="removeFilter('{{ $filter['param'] }}')" 
                            aria-label="Remover filtro"></button>
                </span>
                @endforeach
            </div>
            @endif

            <!-- Resultados -->
            <div class="search-results">
                @if($totalResults > 0)
                    <div class="row" id="searchResults">
                        @foreach($results as $item)
                            @include('search.components.result-item', ['item' => $item])
                        @endforeach
                    </div>

                    <!-- Paginação -->
                    @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="d-flex justify-content-center mt-4">
                            {{ $results->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <!-- Estado Vazio -->
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        @if($query)
                            <h5>Nenhum resultado encontrado</h5>
                            <p class="text-muted">Tente ajustar sua busca ou remover alguns filtros</p>
                            <button class="btn btn-outline-primary" onclick="clearAllFilters()">
                                <i class="fas fa-filter me-2"></i>Limpar todos os filtros
                            </button>
                        @else
                            <h5>Digite algo para começar sua busca</h5>
                            <p class="text-muted">Explore produtos, serviços e petshops</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="searchLoading" class="search-loading d-none">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/modules/advanced-search.js') }}"></script>
<script src="{{ asset('js/modules/search-autocomplete.js') }}"></script>
<script src="{{ asset('js/modules/search-filters.js') }}"></script>
@endsection