class SearchFilters {
    constructor() {
        this.filtersForm = document.getElementById('filtersForm');
        this.searchForm = document.getElementById('searchForm');
        this.debounceTimer = null;
        
        this.init();
    }
    
    init() {
        this.bindFilterEvents();
        this.initializePriceRanges();
        this.syncFilters();
    }
    
    bindFilterEvents() {
        if (!this.filtersForm) return;
        
        // Filtros de categoria, cidade, estado
        const selectFilters = this.filtersForm.querySelectorAll('select');
        selectFilters.forEach(select => {
            select.addEventListener('change', () => {
                this.applyFilters();
            });
        });
        
        // Filtros de rating
        const ratingFilters = this.filtersForm.querySelectorAll('input[name="min_rating"]');
        ratingFilters.forEach(radio => {
            radio.addEventListener('change', () => {
                this.applyFilters();
            });
        });
        
        // Checkboxes especiais
        const checkboxFilters = this.filtersForm.querySelectorAll('input[type="checkbox"]');
        checkboxFilters.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                this.applyFilters();
            });
        });
        
        // Faixas de preço predefinidas
        const priceRangeRadios = this.filtersForm.querySelectorAll('input[name="price_range"]');
        priceRangeRadios.forEach(radio => {
            radio.addEventListener('change', (e) => {
                this.handlePriceRangeChange(e.target.value);
            });
        });
        
        // Inputs de preço customizados
        const priceInputs = this.filtersForm.querySelectorAll('#minPrice, #maxPrice');
        priceInputs.forEach(input => {
            input.addEventListener('input', () => {
                this.clearPriceRangeRadios();
                this.debouncedApplyFilters();
            });
        });
        
        // Botões de ação
        const applyBtn = document.getElementById('applyFilters');
        if (applyBtn) {
            applyBtn.addEventListener('click', () => {
                this.applyFilters();
            });
        }
        
        const clearBtn = document.getElementById('clearFilters');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.clearFilters();
            });
        }
    }
    
    initializePriceRanges() {
        // Botões de faixa de preço predefinidas
        const priceRangeBtns = document.querySelectorAll('.price-range-btn');
        priceRangeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const min = btn.dataset.min;
                const max = btn.dataset.max;
                
                const minInput = document.getElementById('minPrice');
                const maxInput = document.getElementById('maxPrice');
                
                if (minInput) minInput.value = min !== '0' ? min : '';
                if (maxInput) maxInput.value = max !== '999999' ? max : '';
                
                this.clearPriceRangeRadios();
                this.applyFilters();
            });
        });
    }
    
    handlePriceRangeChange(value) {
        const [min, max] = value.split('-');
        
        const minInput = document.getElementById('minPrice');
        const maxInput = document.getElementById('maxPrice');
        
        if (minInput) {
            minInput.value = min !== '0' ? min : '';
        }
        
        if (maxInput) {
            maxInput.value = max !== '999999' ? max : '';
        }
        
        this.applyFilters();
    }
    
    clearPriceRangeRadios() {
        const priceRangeRadios = this.filtersForm.querySelectorAll('input[name="price_range"]');
        priceRangeRadios.forEach(radio => {
            radio.checked = false;
        });
    }
    
    debouncedApplyFilters() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.applyFilters();
        }, 500);
    }
    
    applyFilters() {
        if (!this.filtersForm || !this.searchForm) return;
        
        const formData = new FormData(this.filtersForm);
        
        // Sincronizar com o formulário principal
        formData.forEach((value, key) => {
            const hiddenInput = this.searchForm.querySelector(`input[name="${key}"]`);
            if (hiddenInput) {
                hiddenInput.value = value;
            }
        });
        
        // Submeter busca
        if (window.advancedSearch) {
            window.advancedSearch.performSearch();
        } else {
            this.searchForm.submit();
        }
    }
    
    clearFilters() {
        if (!this.filtersForm) return;
        
        // Reset do form de filtros
        this.filtersForm.reset();
        
        // Limpar inputs hidden do form principal
        if (this.searchForm) {
            const hiddenInputs = this.searchForm.querySelectorAll('input[type="hidden"]');
            hiddenInputs.forEach(input => {
                if (input.name !== 'q' && input.name !== 'type') {
                    input.value = '';
                }
            });
        }
        
        // Limpar filtros rápidos
        const quickFilters = document.querySelectorAll('.quick-filter.active');
        quickFilters.forEach(filter => {
            filter.classList.remove('active');
        });
        
        this.applyFilters();
    }
    
    syncFilters() {
        // Sincronizar filtros com valores da URL
        const urlParams = new URLSearchParams(window.location.search);
        
        if (!this.filtersForm) return;
        
        urlParams.forEach((value, key) => {
            const input = this.filtersForm.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = value === '1' || value === 'true';
                } else if (input.type === 'radio') {
                    input.checked = input.value === value;
                } else {
                    input.value = value;
                }
            }
        });
        
        // Verificar se há uma faixa de preço que corresponde aos valores atuais
        const minPrice = urlParams.get('min_price');
        const maxPrice = urlParams.get('max_price');
        
        if (minPrice || maxPrice) {
            const priceRangeRadios = this.filtersForm.querySelectorAll('input[name="price_range"]');
            priceRangeRadios.forEach(radio => {
                const [rangeMin, rangeMax] = radio.value.split('-');
                if (
                    (minPrice || '0') === rangeMin && 
                    (maxPrice || '999999') === rangeMax
                ) {
                    radio.checked = true;
                }
            });
        }
    }
    
    getActiveFiltersCount() {
        if (!this.filtersForm) return 0;
        
        let count = 0;
        const formData = new FormData(this.filtersForm);
        
        formData.forEach((value) => {
            if (value && value !== '') {
                count++;
            }
        });
        
        return count;
    }
    
    updateFiltersBadge() {
        const count = this.getActiveFiltersCount();
        const badge = document.querySelector('.filters-badge');
        
        if (badge) {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }
    }
}

// Inicializar filtros
document.addEventListener('DOMContentLoaded', function() {
    window.searchFilters = new SearchFilters();
});
