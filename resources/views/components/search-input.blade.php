<div class="search-input-wrapper">
    <div class="input-group">
        <input type="text" 
               class="form-control search-autocomplete" 
               name="{{ $name ?? 'q' }}"
               id="{{ $id ?? 'searchInput' }}"
               value="{{ $value ?? '' }}" 
               placeholder="{{ $placeholder ?? 'Digite sua busca...' }}"
               autocomplete="off"
               data-type="{{ $type ?? 'all' }}">
        
        <button class="btn btn-primary" type="submit">
            <i class="fas fa-search"></i>
        </button>
    </div>
    
    <!-- Dropdown de sugestões -->
    <div class="search-suggestions-dropdown" id="{{ $id ?? 'searchInput' }}_suggestions">
        <!-- Conteúdo carregado via AJAX -->
    </div>
</div>