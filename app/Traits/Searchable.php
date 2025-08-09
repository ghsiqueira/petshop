<?php
// app/Traits/Searchable.php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait Searchable
{
    /**
     * Scope para busca avançada com múltiplos termos
     */
    public function scopeSearch(Builder $query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        // Limpar e preparar termos de busca
        $searchTerms = $this->prepareSearchTerms($search);
        
        return $query->where(function ($query) use ($searchTerms, $search) {
            // Busca exata (maior prioridade)
            $this->addExactMatchConditions($query, $search);
            
            // Busca por termos individuais
            foreach ($searchTerms as $term) {
                $this->addTermMatchConditions($query, $term);
            }
            
            // Busca em campos de relacionamento se aplicável
            $this->addRelationshipSearchConditions($query, $searchTerms);
        });
    }

    /**
     * Scope para filtro por categoria
     */
    public function scopeFilterByCategory(Builder $query, $category)
    {
        if (empty($category)) {
            return $query;
        }
        
        return $query->where('category', $category);
    }

    /**
     * Scope para filtro por faixa de preço
     */
    public function scopeFilterByPriceRange(Builder $query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', $minPrice);
        }
        
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', $maxPrice);
        }
        
        return $query;
    }

    /**
     * Scope para filtro por avaliação mínima
     */
    public function scopeFilterByRating(Builder $query, $minRating = null)
    {
        if ($minRating !== null && $minRating !== '') {
            if ($this->hasSearchAttribute('avg_rating')) {
                $query->where('avg_rating', '>=', $minRating);
            } elseif ($this->hasSearchAttribute('rating')) {
                $query->where('rating', '>=', $minRating);
            }
        }
        
        return $query;
    }

    /**
     * Scope para filtro por localização
     */
    public function scopeFilterByLocation(Builder $query, $city = null, $state = null)
    {
        if ($city) {
            $query->where('city', 'LIKE', "%{$city}%");
        }
        
        if ($state) {
            $query->where('state', 'LIKE', "%{$state}%");
        }
        
        return $query;
    }

    /**
     * Scope para itens em destaque
     */
    public function scopeFeatured(Builder $query)
    {
        if ($this->hasSearchAttribute('featured')) {
            return $query->where('featured', true);
        }
        
        return $query;
    }

    /**
     * Scope para itens ativos
     */
    public function scopeActive(Builder $query)
    {
        if ($this->hasSearchAttribute('is_active')) {
            return $query->where('is_active', true);
        } elseif ($this->hasSearchAttribute('active')) {
            return $query->where('active', true);
        }
        
        return $query;
    }

    /**
     * Scope para ordenação avançada
     */
    public function scopeSortBy(Builder $query, $sort = 'relevance')
    {
        switch ($sort) {
            case 'price_asc':
                return $query->orderBy('price', 'asc');
                
            case 'price_desc':
                return $query->orderBy('price', 'desc');
                
            case 'rating':
                $ratingField = $this->hasSearchAttribute('avg_rating') ? 'avg_rating' : 'rating';
                return $query->orderBy($ratingField, 'desc');
                
            case 'newest':
                return $query->orderBy('created_at', 'desc');
                
            case 'oldest':
                return $query->orderBy('created_at', 'asc');
                
            case 'featured':
                if ($this->hasSearchAttribute('featured')) {
                    $query->orderBy('featured', 'desc');
                }
                $ratingField = $this->hasSearchAttribute('avg_rating') ? 'avg_rating' : 'rating';
                return $query->orderBy($ratingField, 'desc');
                
            case 'name':
                return $query->orderBy('name', 'asc');
                
            case 'popularity':
                if ($this->hasSearchAttribute('total_reviews')) {
                    return $query->orderBy('total_reviews', 'desc');
                }
                return $query->orderBy('created_at', 'desc');
                
            default: // 'relevance'
                return $this->applySortByRelevance($query);
        }
    }

    /**
     * Scope para busca por tags
     */
    public function scopeFilterByTags(Builder $query, $tags)
    {
        if (empty($tags) || !$this->hasSearchAttribute('tags')) {
            return $query;
        }

        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        foreach ($tags as $tag) {
            $query->whereJsonContains('tags', trim($tag));
        }

        return $query;
    }

    /**
     * Scope para busca por espécies aceitas (para petshops)
     */
    public function scopeFilterBySpecies(Builder $query, $species)
    {
        if (empty($species) || !$this->hasSearchAttribute('accepted_species')) {
            return $query;
        }

        return $query->whereJsonContains('accepted_species', $species);
    }

    /**
     * Busca próxima por coordenadas geográficas
     */
    public function scopeNearby(Builder $query, $latitude, $longitude, $radius = 50)
    {
        if (!$this->hasSearchAttribute('latitude') || !$this->hasSearchAttribute('longitude')) {
            return $query;
        }

        return $query->selectRaw(
            '*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
            [$latitude, $longitude, $latitude]
        )->having('distance', '<', $radius)
         ->orderBy('distance');
    }

    /**
     * Preparar termos de busca
     */
    protected function prepareSearchTerms($search)
    {
        // Remover caracteres especiais e dividir por espaços
        $search = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $search);
        $terms = array_filter(explode(' ', $search));
        
        // Remover termos muito curtos
        return array_filter($terms, function($term) {
            return strlen(trim($term)) >= 2;
        });
    }

    /**
     * Adicionar condições de busca exata
     */
    protected function addExactMatchConditions(Builder $query, $search)
    {
        $searchableFields = $this->getSearchableFields();
        
        foreach ($searchableFields as $field) {
            $query->orWhere($field, 'LIKE', "%{$search}%");
        }
        
        // Buscar em keywords se existir
        if ($this->hasSearchAttribute('search_keywords')) {
            $query->orWhere('search_keywords', 'LIKE', "%{$search}%");
        }
    }

    /**
     * Adicionar condições de busca por termo
     */
    protected function addTermMatchConditions(Builder $query, $term)
    {
        $query->orWhere(function ($q) use ($term) {
            $searchableFields = $this->getSearchableFields();
            
            foreach ($searchableFields as $field) {
                $q->orWhere($field, 'LIKE', "%{$term}%");
            }
            
            // Buscar em tags se existir
            if ($this->hasSearchAttribute('tags')) {
                $q->orWhereJsonContains('tags', $term);
            }
            
            // Buscar em keywords se existir
            if ($this->hasSearchAttribute('search_keywords')) {
                $q->orWhere('search_keywords', 'LIKE', "%{$term}%");
            }
        });
    }

    /**
     * Adicionar condições de busca em relacionamentos
     */
    protected function addRelationshipSearchConditions(Builder $query, array $searchTerms)
    {
        // Para produtos: buscar no petshop
        if (method_exists($this, 'petshop')) {
            foreach ($searchTerms as $term) {
                $query->orWhereHas('petshop', function ($q) use ($term) {
                    $q->where('name', 'LIKE', "%{$term}%")
                      ->orWhere('city', 'LIKE', "%{$term}%")
                      ->orWhere('state', 'LIKE', "%{$term}%");
                });
            }
        }

        // Para petshops: buscar no usuário
        if (method_exists($this, 'user')) {
            foreach ($searchTerms as $term) {
                $query->orWhereHas('user', function ($q) use ($term) {
                    $q->where('name', 'LIKE', "%{$term}%");
                });
            }
        }
    }

    /**
     * Aplicar ordenação por relevância
     */
    protected function applySortByRelevance(Builder $query)
    {
        $orderClauses = [];
        
        if ($this->hasSearchAttribute('featured')) {
            $orderClauses[] = 'featured DESC';
        }
        
        $ratingField = $this->hasSearchAttribute('avg_rating') ? 'avg_rating' : 'rating';
        if ($this->hasSearchAttribute($ratingField)) {
            $orderClauses[] = $ratingField . ' DESC';
        }
        
        if ($this->hasSearchAttribute('total_reviews')) {
            $orderClauses[] = 'total_reviews DESC';
        }
        
        $orderClauses[] = 'created_at DESC';
        
        return $query->orderByRaw(implode(', ', $orderClauses));
    }

    /**
     * Verificar se o modelo tem um atributo específico
     * MUDADO DE protected PARA public para resolver o erro
     */
    public function hasSearchAttribute($attribute)
    {
        return in_array($attribute, $this->getFillable()) ||
               in_array($attribute, $this->getGuarded()) ||
               empty($this->getGuarded());
    }

    /**
     * Obter campos de busca rápida para autocomplete
     */
    public function scopeQuickSearch(Builder $query, $term, $limit = 10)
    {
        if (empty($term)) {
            return $query->limit(0);
        }

        $searchableFields = $this->getSearchableFields();
        $mainField = $searchableFields[0] ?? 'name';

        return $query->where($mainField, 'LIKE', "%{$term}%")
                    ->limit($limit);
    }

    /**
     * Busca com destaque de termos encontrados
     */
    public function scopeSearchWithHighlight(Builder $query, $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->search($search);
    }

    /**
     * Filtro por disponibilidade (para produtos)
     */
    public function scopeInStock(Builder $query)
    {
        if ($this->hasSearchAttribute('quantity')) {
            return $query->where('quantity', '>', 0);
        }
        
        return $query;
    }

    /**
     * Filtro por promoções
     */
    public function scopeOnSale(Builder $query)
    {
        if ($this->hasSearchAttribute('discount_percentage')) {
            return $query->where('discount_percentage', '>', 0)
                        ->where(function ($q) {
                            $q->whereNull('discount_start_date')
                              ->orWhere('discount_start_date', '<=', now());
                        })
                        ->where(function ($q) {
                            $q->whereNull('discount_end_date')
                              ->orWhere('discount_end_date', '>=', now());
                        });
        }
        
        return $query;
    }

    /**
     * Define quais campos são pesquisáveis
     * Deve ser implementado em cada model que usa este trait
     */
    abstract protected function getSearchableFields(): array;
}