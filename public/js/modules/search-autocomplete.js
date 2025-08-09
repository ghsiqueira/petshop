// public/js/modules/search-autocomplete.js

class SearchAutocomplete {
    constructor(inputElement, options = {}) {
        this.input = inputElement;
        this.options = {
            minLength: 2,
            maxResults: 10,
            delay: 300,
            showImages: true,
            showCategories: true,
            ...options
        };
        
        this.suggestionsContainer = null;
        this.currentFocus = -1;
        this.debounceTimer = null;
        this.currentRequest = null;
        
        this.init();
    }
    
    init() {
        this.createSuggestionsContainer();
        this.bindEvents();
    }
    
    createSuggestionsContainer() {
        this.suggestionsContainer = document.createElement('div');
        this.suggestionsContainer.className = 'search-suggestions';
        this.suggestionsContainer.id = this.input.id + '_suggestions';
        
        const wrapper = this.input.closest('.search-input-container') || this.input.parentNode;
        wrapper.style.position = 'relative';
        wrapper.appendChild(this.suggestionsContainer);
    }
    
    bindEvents() {
        // Input events
        this.input.addEventListener('input', (e) => {
            this.handleInput(e);
        });
        
        this.input.addEventListener('keydown', (e) => {
            this.handleKeydown(e);
        });
        
        this.input.addEventListener('focus', () => {
            this.handleFocus();
        });
        
        this.input.addEventListener('blur', () => {
            // Delay para permitir clique nas sugestões
            setTimeout(() => this.hideSuggestions(), 150);
        });
        
        // Clique fora para fechar
        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target) && !this.suggestionsContainer.contains(e.target)) {
                this.hideSuggestions();
            }
        });
    }
    
    handleInput(e) {
        const query = e.target.value.trim();
        
        clearTimeout(this.debounceTimer);
        
        if (query.length < this.options.minLength) {
            this.hideSuggestions();
            return;
        }
        
        this.debounceTimer = setTimeout(() => {
            this.fetchSuggestions(query);
        }, this.options.delay);
    }
    
    handleKeydown(e) {
        const suggestions = this.suggestionsContainer.querySelectorAll('.suggestion-item');
        
        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.currentFocus = Math.min(this.currentFocus + 1, suggestions.length - 1);
                this.updateFocus(suggestions);
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                this.currentFocus = Math.max(this.currentFocus - 1, -1);
                this.updateFocus(suggestions);
                break;
                
            case 'Enter':
                e.preventDefault();
                if (this.currentFocus >= 0 && suggestions[this.currentFocus]) {
                    this.selectSuggestion(suggestions[this.currentFocus]);
                } else {
                    this.submitSearch();
                }
                break;
                
            case 'Escape':
                this.hideSuggestions();
                this.input.blur();
                break;
        }
    }
    
    handleFocus() {
        const query = this.input.value.trim();
        if (query.length >= this.options.minLength) {
            this.fetchSuggestions(query);
        } else {
            this.showRecentAndPopular();
        }
    }
    
    fetchSuggestions(query) {
        if (this.currentRequest) {
            this.currentRequest.abort();
        }
        
        const type = document.getElementById('searchType')?.value || 'all';
        
        this.currentRequest = fetch(`/api/search/suggestions?q=${encodeURIComponent(query)}&type=${type}&limit=${this.options.maxResults}`)
            .then(response => response.json())
            .then(data => {
                this.displaySuggestions(data.suggestions || [], query);
            })
            .catch(error => {
                if (error.name !== 'AbortError') {
                    console.error('Erro ao buscar sugestões:', error);
                }
            });
    }
    
    showRecentAndPopular() {
        fetch('/api/search/quick-search?limit=5')
            .then(response => response.json())
            .then(data => {
                this.displayQuickSuggestions(data);
            })
            .catch(error => {
                console.error('Erro ao carregar sugestões rápidas:', error);
            });
    }
    
    displaySuggestions(suggestions, query) {
        this.suggestionsContainer.innerHTML = '';
        
        if (suggestions.length === 0) {
            this.hideSuggestions();
            return;
        }
        
        // Agrupar por tipo
        const grouped = this.groupSuggestionsByType(suggestions);
        
        Object.keys(grouped).forEach(type => {
            if (grouped[type].length > 0) {
                this.addSectionHeader(this.getTypeLabel(type));
                grouped[type].forEach(suggestion => {
                    this.addSuggestionItem(suggestion, query);
                });
            }
        });
        
        this.showSuggestions();
        this.currentFocus = -1;
    }
    
    displayQuickSuggestions(data) {
        this.suggestionsContainer.innerHTML = '';
        
        if (data.recent && data.recent.length > 0) {
            this.addSectionHeader('Buscas Recentes');
            data.recent.forEach(item => {
                this.addQuickSuggestionItem(item);
            });
        }
        
        if (data.popular && data.popular.length > 0) {
            this.addSectionHeader('Buscas Populares');
            data.popular.forEach(item => {
                this.addQuickSuggestionItem(item);
            });
        }
        
        if (this.suggestionsContainer.innerHTML) {
            this.showSuggestions();
        }
    }
    
    groupSuggestionsByType(suggestions) {
        return suggestions.reduce((acc, suggestion) => {
            const type = suggestion.type || 'term';
            if (!acc[type]) acc[type] = [];
            acc[type].push(suggestion);
            return acc;
        }, {});
    }
    
    getTypeLabel(type) {
        const labels = {
            'product': 'Produtos',
            'service': 'Serviços',
            'petshop': 'Pet Shops',
            'category': 'Categorias',
            'term': 'Sugestões'
        };
        return labels[type] || 'Sugestões';
    }
    
    addSectionHeader(title) {
        const header = document.createElement('div');
        header.className = 'suggestion-section-header';
        header.textContent = title;
        this.suggestionsContainer.appendChild(header);
    }
    
    addSuggestionItem(suggestion, query) {
        const item = document.createElement('div');
        item.className = 'suggestion-item';
        item.dataset.url = suggestion.url || '';
        item.dataset.text = suggestion.text || '';
        
        let content = '';
        
        // Ícone ou imagem
        if (suggestion.image && this.options.showImages) {
            content += `<img src="${suggestion.image}" alt="" class="suggestion-image">`;
        } else if (suggestion.icon) {
            content += `<i class="${suggestion.icon} suggestion-icon"></i>`;
        } else {
            content += `<i class="fas fa-search suggestion-icon"></i>`;
        }
        
        // Conteúdo principal
        content += '<div class="suggestion-content">';
        content += `<div class="suggestion-title">${this.highlightMatch(suggestion.text, query)}</div>`;
        
        if (suggestion.category || suggestion.subtitle) {
            content += `<div class="suggestion-subtitle">${suggestion.category || suggestion.subtitle}</div>`;
        }
        
        if (suggestion.price) {
            content += `<div class="suggestion-price text-primary fw-bold">${suggestion.price}</div>`;
        }
        
        content += '</div>';
        
        item.innerHTML = content;
        
        item.addEventListener('click', () => {
            this.selectSuggestion(item);
        });
        
        this.suggestionsContainer.appendChild(item);
    }
    
    addQuickSuggestionItem(item) {
        const suggestionItem = document.createElement('div');
        suggestionItem.className = 'suggestion-item';
        suggestionItem.dataset.text = item.text;
        
        suggestionItem.innerHTML = `
            <i class="${item.icon || 'fas fa-search'} suggestion-icon"></i>
            <div class="suggestion-content">
                <div class="suggestion-title">${item.text}</div>
            </div>
        `;
        
        suggestionItem.addEventListener('click', () => {
            this.selectSuggestion(suggestionItem);
        });
        
        this.suggestionsContainer.appendChild(suggestionItem);
    }
    
    highlightMatch(text, query) {
        if (!query) return text;
        
        const regex = new RegExp(`(${this.escapeRegex(query)})`, 'gi');
        return text.replace(regex, '<mark>$1</mark>');
    }
    
    escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }
    
    updateFocus(suggestions) {
        suggestions.forEach((item, index) => {
            item.classList.toggle('active', index === this.currentFocus);
        });
        
        // Scroll para manter o item visível
        if (this.currentFocus >= 0 && suggestions[this.currentFocus]) {
            suggestions[this.currentFocus].scrollIntoView({
                block: 'nearest',
                behavior: 'smooth'
            });
        }
    }
    
    selectSuggestion(item) {
        const url = item.dataset.url;
        const text = item.dataset.text;
        
        if (url) {
            window.location.href = url;
        } else if (text) {
            this.input.value = text;
            this.submitSearch();
        }
        
        this.hideSuggestions();
    }
    
    submitSearch() {
        const form = this.input.closest('form');
        if (form) {
            form.submit();
        }
    }
    
    showSuggestions() {
        this.suggestionsContainer.classList.add('show');
    }
    
    hideSuggestions() {
        this.suggestionsContainer.classList.remove('show');
        this.currentFocus = -1;
    }
}

// Inicializar autocomplete em todos os campos de busca
document.addEventListener('DOMContentLoaded', function() {
    const searchInputs = document.querySelectorAll('.search-input, .search-autocomplete');
    
    searchInputs.forEach(input => {
        new SearchAutocomplete(input, {
            minLength: 2,
            maxResults: 10,
            delay: 300
        });
    });
});