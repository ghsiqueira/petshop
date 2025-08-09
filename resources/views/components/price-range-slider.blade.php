<div class="price-range-slider">
    <label class="form-label">{{ $label ?? 'Faixa de Preço' }}</label>
    
    <div class="price-inputs mb-3">
        <div class="row">
            <div class="col-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">R$</span>
                    <input type="number" 
                           class="form-control" 
                           name="{{ $minName ?? 'min_price' }}"
                           id="{{ $minId ?? 'minPrice' }}"
                           value="{{ $minValue ?? '' }}"
                           placeholder="Mín"
                           min="0">
                </div>
            </div>
            <div class="col-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text">R$</span>
                    <input type="number" 
                           class="form-control" 
                           name="{{ $maxName ?? 'max_price' }}"
                           id="{{ $maxId ?? 'maxPrice' }}"
                           value="{{ $maxValue ?? '' }}"
                           placeholder="Máx"
                           min="0">
                </div>
            </div>
        </div>
    </div>

    <!-- Slider visual (opcional, pode ser implementado com JavaScript) -->
    <div class="price-slider" 
         data-min="{{ $min ?? 0 }}" 
         data-max="{{ $max ?? 1000 }}"
         data-current-min="{{ $minValue ?? $min ?? 0 }}"
         data-current-max="{{ $maxValue ?? $max ?? 1000 }}">
        <!-- Implementar com JavaScript se necessário -->
    </div>

    <!-- Faixas de preço predefinidas -->
    @if(isset($ranges) && is_array($ranges))
    <div class="predefined-ranges mt-2">
        @foreach($ranges as $range)
        <button type="button" 
                class="btn btn-sm btn-outline-secondary me-1 mb-1 price-range-btn"
                data-min="{{ $range['min'] ?? 0 }}"
                data-max="{{ $range['max'] ?? 999999 }}">
            {{ $range['label'] }}
        </button>
        @endforeach
    </div>
    @endif
</div>