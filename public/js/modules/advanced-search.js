class AdvancedSearch {
    constructor() {
        this.searchForm = document.getElementById('searchForm');
        this.searchQuery = document.getElementById('searchQuery');
        this.searchType = document.getElementById('searchType');
        this.sortSelect = document.getElementById('sortSelect');
        this.loadingOverlay = document.getElementById('searchLoading');
        this.quickFilters = document.querySelectorAll('.quick-filter');
        
        this.debounceTimer = null;
        this.currentRequest = null;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.initializeQuickFilters();
        this.restoreActiveFilters();
    }
    
    bindEvents() {
        // Submissão do formulário
        if (this.searchForm) {
            this.searchForm.addEventListener('submit', (e) => {
                this.handleSearch(e);
            });
        }
        
        // Mudança no tipo de busca
        if (this.searchType) {
            this.searchType.addEventListener('change', () => {
                this.updateSearchPlaceholder();
                this.performSearch();
            });
        }
        
        // Mudança na ordenação
        if (this.sortSelect) {
            this.sortSelect.addEventListener('change', () => {
                this.updateSort();
            });
        }
        
        // Filtros rápidos
        this.quickFilters.forEach(filter => {
            filter.addEventListener('click', (e) => {
                this.toggleQuickFilter(e.target);
            });
        });
        
        // Teclas especiais no campo de busca
        if (this.searchQuery) {
            this.searchQuery.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.performSearch();
                }
            });
        }
        
        // Limpar filtros
        const clearFiltersBtn = document.getElementById('clearFilters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                this.clearAllFilters();
            });
        }
    }
    
    handleSearch(e) {
        e.preventDefault();
        this.performSearch();
    }
    
    performSearch() {
        if (this.currentRequest) {
            this.currentRequest.abort();
        }
        
        this.showLoading();
        
        const formData = new FormData(this.searchForm);
        const searchParams = new URLSearchParams(formData);
        
        // Atualizar URL sem recarregar a página
        const newUrl = `${window.location.pathname}?${searchParams.toString()}`;
        window.history.pushState({}, '', newUrl);
        
        // Realizar busca via AJAX ou recarregar página
        if (this.shouldUseAjax()) {
            this.performAjaxSearch(searchParams);
        } else {
            window.location.href = newUrl;
        }
    }
    
    shouldUseAjax() {
        // Usar AJAX se estivermos na página de busca
        return window.location.pathname.includes('/search');
    }
    
    performAjaxSearch(searchParams) {
        this.currentRequest = fetch(`/search?${searchParams.toString()}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            this.updateResults(data);
            this.hideLoading();
        })
        .catch(error => {
            if (error.name !== 'AbortError') {
                console.error('Erro na busca:', error);
                this.hideLoading();
            }
        });
    }
    
    updateResults(data) {
        // Atualizar contadores
        const resultsInfo = document.querySelector('.search-results-info h4');
        if (resultsInfo && data.query) {
            resultsInfo.innerHTML = `Resultados para: <span class="text-primary">"${data.query}"</span>`;
        }
        
        const resultsCount = document.querySelector('.search-results-info p');
        if (resultsCount) {
            resultsCount.textContent = `${data.totalResults.toLocaleString()} resultado(s) encontrado(s)`;
        }
        
        // Atualizar grid de resultados
        const resultsContainer = document.getElementById('searchResults');
        if (resultsContainer && data.resultsHtml) {
            resultsContainer.innerHTML = data.resultsHtml;
        }
        
        // Atualizar paginação
        const paginationContainer = document.querySelector('.d-flex.justify-content-center');
        if (paginationContainer && data.paginationHtml) {
            paginationContainer.innerHTML = data.paginationHtml;
        }
    }
    
    updateSort() {
        const sortValue = this.sortSelect.value;
        const sortInput = document.querySelector('input[name="sort"]');
        if (sortInput) {
            sortInput.value = sortValue;
        }
        this.performSearch();
    }
    
    toggleQuickFilter(filterButton) {
        const filterName = filterButton.dataset.filter;
        const filterValue = filterButton.dataset.value;
        
        filterButton.classList.toggle('active');
        
        const hiddenInput = document.querySelector(`input[name="${filterName}"]`);
        if (hiddenInput) {
            if (filterButton.classList.contains('active')) {
                hiddenInput.value = filterValue;
            } else {
                hiddenInput.value = '';
            }
        }
        
        this.performSearch();
    }
    
    initializeQuickFilters() {
        this.quickFilters.forEach(filter => {
            const filterName = filter.dataset.filter;
            const filterValue = filter.dataset.value;
            const currentValue = new URLSearchParams(window.location.search).get(filterName);
            
            if (currentValue === filterValue) {
                filter.classList.add('active');
            }
        });
    }
    
    restoreActiveFilters() {
        const urlParams = new URLSearchParams(window.location.search);
        
        // Restaurar filtros no formulário
        urlParams.forEach((value, key) => {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    input.checked = input.value === value;
                } else {
                    input.value = value;
                }
            }
        });
    }
    
    clearAllFilters() {
        // Limpar formulário principal
        const formInputs = this.searchForm.querySelectorAll('input[type="hidden"]');
        formInputs.forEach(input => {
            if (input.name !== 'q' && input.name !== 'type') {
                input.value = '';
            }
        });
        
        // Limpar filtros laterais
        const filtersForm = document.getElementById('filtersForm');
        if (filtersForm) {
            filtersForm.reset();
        }
        
        // Desativar filtros rápidos
        this.quickFilters.forEach(filter => {
            filter.classList.remove('active');
        });
        
        this.performSearch();
    }
    
    updateSearchPlaceholder() {
        const placeholders = {
            'all': 'O que você está procurando?',
            'products': 'Buscar produtos...',
            'services': 'Buscar serviços...',
            'petshops': 'Buscar pet shops...'
        };
        
        if (this.searchQuery) {
            this.searchQuery.placeholder = placeholders[this.searchType.value] || placeholders['all'];
        }
    }
    
    showLoading() {
        if (this.loadingOverlay) {
            this.loadingOverlay.classList.remove('d-none');
        }
    }
    
    hideLoading() {
        if (this.loadingOverlay) {
            this.loadingOverlay.classList.add('d-none');
        }
    }
}

// Funções globais para uso nos templates
function updateSort(sortValue) {
    const sortInput = document.querySelector('input[name="sort"]');
    if (sortInput) {
        sortInput.value = sortValue;
    }
    
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.submit();
    }
}

function removeFilter(paramName) {
    const input = document.querySelector(`input[name="${paramName}"]`);
    if (input) {
        input.value = '';
    }
    
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.submit();
    }
}

function clearAllFilters() {
    if (window.advancedSearch) {
        window.advancedSearch.clearAllFilters();
    }
}

function addToCart(productId) {
    fetch('/api/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Mostrar notificação de sucesso
            showNotification('Produto adicionado ao carrinho!', 'success');
            
            // Atualizar contador do carrinho se existir
            updateCartCounter();
        } else {
            showNotification(data.message || 'Erro ao adicionar produto', 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showNotification('Erro ao adicionar produto ao carrinho', 'error');
    });
}

function showNotification(message, type = 'info') {
    // Implementar sistema de notificações
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Remover após 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function updateCartCounter() {
    // Atualizar contador do carrinho no header
    fetch('/api/cart/count')
        .then(response => response.json())
        .then(data => {
            const cartCounter = document.querySelector('.cart-counter');
            if (cartCounter) {
                cartCounter.textContent = data.count;
                cartCounter.classList.toggle('d-none', data.count === 0);
            }
        });
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    window.advancedSearch = new AdvancedSearch();
});
