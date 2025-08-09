@php
    $itemType = $item->search_type ?? 'unknown';
    $itemUrl = '';
    $itemImage = '';
    $itemPrice = '';
    $itemRating = 0;
    $itemLocation = '';
    
    switch($itemType) {
        case 'product':
            $itemUrl = route('products.show', $item->id);
            $itemImage = $item->image_url;
            $itemPrice = 'R$ ' . number_format($item->final_price, 2, ',', '.');
            $itemRating = $item->avg_rating;
            $itemLocation = $item->petshop->name ?? '';
            break;
        case 'service':
            $itemUrl = route('services.show', $item->id);
            $itemImage = $item->image_url;
            $itemPrice = 'R$ ' . number_format($item->price, 2, ',', '.');
            $itemRating = $item->avg_rating;
            $itemLocation = $item->petshop->name ?? '';
            break;
        case 'petshop':
            $itemUrl = route('petshops.show', $item->id);
            $itemImage = $item->logo_url;
            $itemRating = $item->rating;
            $itemLocation = $item->city . ', ' . $item->state;
            break;
        default:
            // Tentar detectar o tipo pela classe do modelo
            if(get_class($item) === 'App\Models\Product') {
                $itemType = 'product';
                $itemUrl = route('products.show', $item->id);
                $itemImage = $item->image_url;
                $itemPrice = 'R$ ' . number_format($item->final_price, 2, ',', '.');
                $itemRating = $item->avg_rating;
                $itemLocation = $item->petshop->name ?? '';
            } elseif(get_class($item) === 'App\Models\Service') {
                $itemType = 'service';
                $itemUrl = route('services.show', $item->id);
                $itemImage = $item->image_url;
                $itemPrice = 'R$ ' . number_format($item->price, 2, ',', '.');
                $itemRating = $item->avg_rating;
                $itemLocation = $item->petshop->name ?? '';
            } elseif(get_class($item) === 'App\Models\Petshop') {
                $itemType = 'petshop';
                $itemUrl = route('petshops.show', $item->id);
                $itemImage = $item->logo_url;
                $itemRating = $item->rating;
                $itemLocation = $item->city . ', ' . $item->state;
            }
            break;
    }
@endphp

<div class="col-lg-4 col-md-6 mb-4">
    <div class="card search-result-card h-100">
        <!-- Badge do Tipo -->
        <div class="result-type-badge">
            @switch($itemType)
                @case('product')
                    <span class="badge bg-primary">
                        <i class="fas fa-box me-1"></i>Produto
                    </span>
                    @break
                @case('service')
                    <span class="badge bg-success">
                        <i class="fas fa-concierge-bell me-1"></i>Serviço
                    </span>
                    @break
                @case('petshop')
                    <span class="badge bg-info">
                        <i class="fas fa-store me-1"></i>Pet Shop
                    </span>
                    @break
            @endswitch
        </div>

        <!-- Badges Especiais -->
        <div class="result-badges">
            @if(isset($item->featured) && $item->featured)
                <span class="badge bg-warning text-dark">
                    <i class="fas fa-star me-1"></i>Destaque
                </span>
            @endif
            
            @if($itemType == 'product' && isset($item->is_on_sale) && $item->is_on_sale)
                <span class="badge bg-danger">
                    <i class="fas fa-tag me-1"></i>Promoção
                </span>
            @endif
        </div>

        <!-- Imagem -->
        <div class="result-image">
            <a href="{{ $itemUrl }}">
                <img src="{{ $itemImage }}" 
                     class="card-img-top" 
                     alt="{{ $item->name }}"
                     loading="lazy">
            </a>
        </div>

        <div class="card-body d-flex flex-column">
            <!-- Título -->
            <h5 class="card-title">
                <a href="{{ $itemUrl }}" class="text-decoration-none">
                    {{ $item->name }}
                </a>
            </h5>

            <!-- Descrição -->
            @if($item->description)
            <p class="card-text text-muted small">
                {{ Str::limit($item->description, 100) }}
            </p>
            @endif

            <!-- Categoria -->
            @if(isset($item->category) && $item->category)
            <div class="mb-2">
                <span class="badge bg-light text-dark">{{ $item->category }}</span>
            </div>
            @endif

            <!-- Avaliação -->
            @if($itemRating > 0)
            <div class="rating-display mb-2">
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= $itemRating ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                </div>
                <span class="rating-text ms-2">
                    {{ number_format($itemRating, 1) }}
                    @if(isset($item->total_reviews) && $item->total_reviews > 0)
                        ({{ $item->total_reviews }} {{ $item->total_reviews == 1 ? 'avaliação' : 'avaliações' }})
                    @endif
                </span>
            </div>
            @endif

            <!-- Localização -->
            @if($itemLocation)
            <div class="location-info mb-2">
                <i class="fas fa-map-marker-alt text-muted me-1"></i>
                <span class="text-muted small">{{ $itemLocation }}</span>
            </div>
            @endif

            <!-- Informações Específicas do Produto -->
            @if($itemType == 'product')
                <!-- Estoque -->
                @if(isset($item->stock_status))
                <div class="stock-status mb-2">
                    @switch($item->stock_status)
                        @case('in_stock')
                            <span class="text-success small">
                                <i class="fas fa-check-circle me-1"></i>Em estoque
                            </span>
                            @break
                        @case('low_stock')
                            <span class="text-warning small">
                                <i class="fas fa-exclamation-triangle me-1"></i>Últimas unidades
                            </span>
                            @break
                        @case('out_of_stock')
                            <span class="text-danger small">
                                <i class="fas fa-times-circle me-1"></i>Fora de estoque
                            </span>
                            @break
                    @endswitch
                </div>
                @endif

                <!-- Preço com Desconto -->
                @if(isset($item->is_on_sale) && $item->is_on_sale)
                <div class="price-display">
                    <span class="original-price text-muted text-decoration-line-through me-2">
                        R$ {{ number_format($item->price, 2, ',', '.') }}
                    </span>
                    <span class="sale-price text-danger fw-bold">
                        {{ $itemPrice }}
                    </span>
                    @if(isset($item->discount_percentage))
                    <span class="discount-badge badge bg-danger ms-2">
                        -{{ $item->discount_percentage }}%
                    </span>
                    @endif
                </div>
                @else
                <div class="price-display">
                    <span class="current-price fw-bold">{{ $itemPrice }}</span>
                </div>
                @endif
            @endif

            <!-- Informações Específicas do Serviço -->
            @if($itemType == 'service')
                <div class="service-info mb-2">
                    @if(isset($item->duration_formatted))
                    <span class="text-muted small">
                        <i class="fas fa-clock me-1"></i>{{ $item->duration_formatted }}
                    </span>
                    @endif
                </div>
                <div class="price-display">
                    <span class="current-price fw-bold">{{ $itemPrice }}</span>
                </div>
            @endif

            <!-- Informações Específicas do Petshop -->
            @if($itemType == 'petshop')
                @if(isset($item->is_open_now))
                <div class="opening-status mb-2">
                    @if($item->is_open_now)
                        <span class="text-success small">
                            <i class="fas fa-circle me-1"></i>Aberto agora
                        </span>
                    @else
                        <span class="text-danger small">
                            <i class="fas fa-circle me-1"></i>Fechado
                        </span>
                    @endif
                </div>
                @endif

                <!-- Serviços/Produtos disponíveis -->
                <div class="petshop-stats small text-muted">
                    @if(method_exists($item, 'getActiveProductsCount'))
                        {{ $item->getActiveProductsCount() }} produtos •
                    @endif
                    @if(method_exists($item, 'getActiveServicesCount'))
                        {{ $item->getActiveServicesCount() }} serviços
                    @endif
                </div>
            @endif

            <!-- Botões de Ação -->
            <div class="mt-auto pt-3">
                <div class="d-flex gap-2">
                    <a href="{{ $itemUrl }}" class="btn btn-primary flex-fill">
                        @switch($itemType)
                            @case('product')
                                <i class="fas fa-eye me-1"></i>Ver Produto
                                @break
                            @case('service')
                                <i class="fas fa-calendar me-1"></i>Agendar
                                @break
                            @case('petshop')
                                <i class="fas fa-store me-1"></i>Visitar
                                @break
                            @default
                                <i class="fas fa-eye me-1"></i>Ver Detalhes
                        @endswitch
                    </a>

                    @if($itemType == 'product')
                        @auth
                        <button class="btn btn-outline-primary" 
                                onclick="addToCart({{ $item->id }})"
                                {{ isset($item->stock_status) && $item->stock_status == 'out_of_stock' ? 'disabled' : '' }}>
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                        @endauth
                    @endif

                    <!-- Botão de Favoritos -->
                    @auth
                    <button class="btn btn-outline-secondary favorite-btn" 
                            data-item-id="{{ $item->id }}" 
                            data-item-type="{{ $itemType }}">
                        <i class="far fa-heart"></i>
                    </button>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>