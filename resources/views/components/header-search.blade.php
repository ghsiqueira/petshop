<div class="header-search-wrapper">
    <form method="GET" action="{{ route('search.index') }}" class="header-search-form">
        <div class="input-group">
            <input type="text" 
                   class="form-control search-autocomplete" 
                   name="q"
                   id="headerSearch"
                   placeholder="Buscar produtos, serviços ou pet shops..."
                   autocomplete="off"
                   value="{{ request('q') }}">
            
            <input type="hidden" name="type" value="all">
            
            <button class="btn btn-primary" type="submit">
                <i class="fas fa-search"></i>
                <span class="d-none d-sm-inline ms-1">Buscar</span>
            </button>
        </div>
        
        <!-- Dropdown de sugestões será criado dinamicamente pelo JavaScript -->
    </form>
</div>

<style>
.header-search-wrapper {
    position: relative;
    max-width: 500px;
    width: 100%;
}

.header-search-form .form-control {
    border-right: none;
}

.header-search-form .btn {
    border-left: none;
}

@media (max-width: 768px) {
    .header-search-wrapper {
        max-width: 100%;
        margin: 0.5rem 0;
    }
}
</style>