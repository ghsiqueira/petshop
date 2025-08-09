<div class="rating-filter">
    <label class="form-label">{{ $label ?? 'Avaliação Mínima' }}</label>
    
    <div class="rating-options">
        @for($i = 5; $i >= 1; $i--)
        <div class="form-check">
            <input class="form-check-input" 
                   type="radio" 
                   name="{{ $name ?? 'min_rating' }}" 
                   id="{{ $id ?? 'rating' }}_{{ $i }}" 
                   value="{{ $i }}"
                   {{ (isset($value) && $value == $i) ? 'checked' : '' }}>
            <label class="form-check-label d-flex align-items-center" 
                   for="{{ $id ?? 'rating' }}_{{ $i }}">
                <div class="stars me-2">
                    @for($j = 1; $j <= 5; $j++)
                    <i class="fas fa-star {{ $j <= $i ? 'text-warning' : 'text-muted' }}"></i>
                    @endfor
                </div>
                <span>{{ $i }}+ estrela{{ $i > 1 ? 's' : '' }}</span>
                @if(isset($counts) && isset($counts[$i]))
                <span class="badge bg-secondary ms-auto">{{ $counts[$i] }}</span>
                @endif
            </label>
        </div>
        @endfor
        
        <!-- Opção para "Qualquer avaliação" -->
        <div class="form-check">
            <input class="form-check-input" 
                   type="radio" 
                   name="{{ $name ?? 'min_rating' }}" 
                   id="{{ $id ?? 'rating' }}_any" 
                   value=""
                   {{ !isset($value) || empty($value) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $id ?? 'rating' }}_any">
                <span>Qualquer avaliação</span>
            </label>
        </div>
    </div>
</div>