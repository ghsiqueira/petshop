<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\UserSearch;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar configurações de busca
        $this->mergeConfigFrom(
            __DIR__.'/../../config/search.php', 'search'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Compartilhar dados de busca com todas as views
        View::composer('*', function ($view) {
            // Buscas populares globais para sugestões
            $popularSearches = cache()->remember('popular_searches_global', 3600, function () {
                try {
                    return UserSearch::getPopularSearches(null, 5);
                } catch (\Exception $e) {
                    // Se a tabela ainda não existe, retornar coleção vazia
                    return collect();
                }
            });
            
            $view->with('globalPopularSearches', $popularSearches);
        });

        // Composer específico para views de busca
        View::composer(['search.*', 'components.header-search'], function ($view) {
            $searchTypes = config('search.types', []);
            $priceRanges = config('search.price_ranges', []);
            $sortOptions = config('search.sort_options', []);
            
            $view->with([
                'searchTypes' => $searchTypes,
                'searchPriceRanges' => $priceRanges,
                'searchSortOptions' => $sortOptions
            ]);
        });

        // Registrar macros para busca se necessário
        $this->registerSearchMacros();
    }

    /**
     * Registrar macros úteis para busca
     */
    protected function registerSearchMacros()
    {
        // Macro para gerar URL de busca com filtros
        \Illuminate\Http\Request::macro('searchUrl', function (array $filters = []) {
            $currentFilters = $this->only([
                'q', 'type', 'category', 'min_price', 'max_price', 
                'min_rating', 'city', 'state', 'sort', 'featured', 
                'on_sale', 'in_stock', 'tags'
            ]);
            
            $mergedFilters = array_merge($currentFilters, $filters);
            
            // Remover filtros vazios
            $mergedFilters = array_filter($mergedFilters, function ($value) {
                return $value !== null && $value !== '';
            });
            
            return route('search.index', $mergedFilters);
        });

        // Macro para verificar se um filtro está ativo
        \Illuminate\Http\Request::macro('hasFilter', function (string $filter, $value = null) {
            if ($value === null) {
                return $this->filled($filter);
            }
            
            return $this->get($filter) == $value;
        });
    }
}