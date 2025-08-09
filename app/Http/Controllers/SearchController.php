<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    //
}
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Service;
use App\Models\Petshop;
use App\Models\Pet;
use App\Models\UserSearch;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        $category = $request->get('category');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $minRating = $request->get('min_rating');
        $city = $request->get('city');
        $state = $request->get('state');
        $sort = $request->get('sort', 'relevance');
        $perPage = (int) $request->get('per_page', 12);
        $tags = $request->get('tags');
        $inStock = $request->get('in_stock');
        $onSale = $request->get('on_sale');
        $featured = $request->get('featured');

        $results = collect();
        $totalResults = 0;
        
        if (!empty($query) || $this->hasFilters($request)) {
            $results = $this->performSearch($query, $type, [
                'category' => $category,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'min_rating' => $minRating,
                'city' => $city,
                'state' => $state,
                'sort' => $sort,
                'per_page' => $perPage,
                'tags' => $tags,
                'in_stock' => $inStock,
                'on_sale' => $onSale,
                'featured' => $featured
            ]);
            
            $totalResults = $results instanceof LengthAwarePaginator ? $results->total() : $results->count();
            
            // Registrar a pesquisa
            if (!empty($query)) {
                $this->recordSearch($query, $type, $request->all(), $totalResults);
            }
        }

        $filters = $this->getAvailableFilters();
        $recentSearches = $this->getRecentSearches();
        $popularSearches = $this->getPopularSearches($type);

        return view('search.index', compact(
            'query', 'type', 'results', 'totalResults', 'filters', 
            'recentSearches', 'popularSearches', 'category', 'minPrice', 
            'maxPrice', 'minRating', 'city', 'state', 'sort', 'tags',
            'inStock', 'onSale', 'featured'
        ));
    }

    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        
        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => [],
                'recent' => $this->getRecentSearches(),
                'popular' => $this->getPopularSearches($type)
            ]);
        }

        $suggestions = collect();

        // Buscar em produtos
        if ($type === 'all' || $type === 'products') {
            $products = Product::active()
                ->quickSearch($query, 5)
                ->get(['id', 'name', 'category', 'price', 'image'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->name,
                        'type' => 'product',
                        'category' => $item->category,
                        'price' => 'R$ ' . number_format($item->price, 2, ',', '.'),
                        'image' => $item->image_url,
                        'url' => route('products.show', $item->id)
                    ];
                });
            $suggestions = $suggestions->merge($products);
        }

        // Buscar em serviços
        if ($type === 'all' || $type === 'services') {
            $services = Service::active()
                ->quickSearch($query, 5)
                ->get(['id', 'name', 'category', 'price', 'image'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->name,
                        'type' => 'service',
                        'category' => $item->category,
                        'price' => 'R$ ' . number_format($item->price, 2, ',', '.'),
                        'image' => $item->image_url,
                        'url' => route('services.show', $item->id)
                    ];
                });
            $suggestions = $suggestions->merge($services);
        }

        // Buscar em petshops
        if ($type === 'all' || $type === 'petshops') {
            $petshops = Petshop::active()
                ->quickSearch($query, 5)
                ->get(['id', 'name', 'city', 'state', 'logo'])
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'text' => $item->name,
                        'type' => 'petshop',
                        'category' => $item->city . ', ' . $item->state,
                        'image' => $item->logo_url,
                        'url' => route('petshops.show', $item->id)
                    ];
                });
            $suggestions = $suggestions->merge($petshops);
        }

        return response()->json([
            'suggestions' => $suggestions->take(15)->values(),
            'query' => $query
        ]);
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = collect();

        // Buscar termos populares similares
        $popularTerms = UserSearch::where('query', 'LIKE', "%{$query}%")
            ->when($type !== 'all', function ($q) use ($type) {
                return $q->where('type', $type);
            })
            ->selectRaw('query, COUNT(*) as count')
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->pluck('query');

        $suggestions = $suggestions->merge($popularTerms->map(function ($term) {
            return [
                'text' => $term,
                'type' => 'search_term'
            ];
        }));

        // Buscar categorias
        if ($type === 'all' || $type === 'products') {
            $productCategories = Product::where('category', 'LIKE', "%{$query}%")
                ->distinct()
                ->pluck('category')
                ->take(3);
            
            $suggestions = $suggestions->merge($productCategories->map(function ($category) {
                return [
                    'text' => $category,
                    'type' => 'category'
                ];
            }));
        }

        if ($type === 'all' || $type === 'services') {
            $serviceCategories = Service::where('category', 'LIKE', "%{$query}%")
                ->distinct()
                ->pluck('category')
                ->take(3);
            
            $suggestions = $suggestions->merge($serviceCategories->map(function ($category) {
                return [
                    'text' => $category,
                    'type' => 'category'
                ];
            }));
        }

        return response()->json($suggestions->take(10)->values());
    }

    private function performSearch($query, $type, $filters)
    {
        switch ($type) {
            case 'products':
                return $this->searchProducts($query, $filters);
            case 'services':
                return $this->searchServices($query, $filters);
            case 'petshops':
                return $this->searchPetshops($query, $filters);
            default: // 'all'
                return $this->searchAll($query, $filters);
        }
    }

    private function searchProducts($query, $filters)
    {
        $products = Product::query()
            ->active()
            ->search($query)
            ->filterByCategory($filters['category'])
            ->filterByPriceRange($filters['min_price'], $filters['max_price'])
            ->filterByRating($filters['min_rating'])
            ->filterByTags($filters['tags'])
            ->when($filters['in_stock'], function ($q) {
                return $q->inStock();
            })
            ->when($filters['on_sale'], function ($q) {
                return $q->onSale();
            })
            ->when($filters['featured'], function ($q) {
                return $q->featured();
            })
            ->with('petshop')
            ->sortBy($filters['sort']);

        return $products->paginate($filters['per_page'])->withQueryString();
    }

    private function searchServices($query, $filters)
    {
        $services = Service::query()
            ->active()
            ->search($query)
            ->filterByCategory($filters['category'])
            ->filterByPriceRange($filters['min_price'], $filters['max_price'])
            ->filterByRating($filters['min_rating'])
            ->filterByTags($filters['tags'])
            ->when($filters['featured'], function ($q) {
                return $q->featured();
            })
            ->with('petshop')
            ->sortBy($filters['sort']);

        return $services->paginate($filters['per_page'])->withQueryString();
    }

    private function searchPetshops($query, $filters)
    {
        $petshops = Petshop::query()
            ->active()
            ->search($query)
            ->filterByLocation($filters['city'], $filters['state'])
            ->filterByRating($filters['min_rating'])
            ->when($filters['featured'], function ($q) {
                return $q->featured();
            })
            ->sortBy($filters['sort']);

        return $petshops->paginate($filters['per_page'])->withQueryString();
    }

    private function searchAll($query, $filters)
    {
        $allResults = collect();
        
        // Buscar produtos (limitado)
        $products = $this->searchProducts($query, array_merge($filters, ['per_page' => 100]))
            ->getCollection()
            ->take(20)
            ->map(function ($item) {
                $item->search_type = 'product';
                return $item;
            });
        
        // Buscar serviços (limitado)
        $services = $this->searchServices($query, array_merge($filters, ['per_page' => 100]))
            ->getCollection()
            ->take(20)
            ->map(function ($item) {
                $item->search_type = 'service';
                return $item;
            });
        
        // Buscar petshops (limitado)
        $petshops = $this->searchPetshops($query, array_merge($filters, ['per_page' => 100]))
            ->getCollection()
            ->take(10)
            ->map(function ($item) {
                $item->search_type = 'petshop';
                return $item;
            });
        
        // Combinar e ordenar resultados
        $allResults = $products->merge($services)->merge($petshops);
        
        // Aplicar ordenação geral se necessário
        $allResults = $this->sortMixedResults($allResults, $filters['sort']);
        
        // Simular paginação
        $currentPage = request()->get('page', 1);
        $perPage = $filters['per_page'];
        
        $paginatedResults = new LengthAwarePaginator(
            $allResults->forPage($currentPage, $perPage),
            $allResults->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );

        return $paginatedResults;
    }

    private function sortMixedResults($results, $sort)
    {
        switch ($sort) {
            case 'price_asc':
                return $results->sortBy('price');
            case 'price_desc':
                return $results->sortByDesc('price');
            case 'rating':
                return $results->sortByDesc(function ($item) {
                    return $item->avg_rating ?? $item->rating ?? 0;
                });
            case 'newest':
                return $results->sortByDesc('created_at');
            case 'name':
                return $results->sortBy('name');
            default:
                // Ordenar por tipo e relevância
                return $results->sortByDesc(function ($item) {
                    $score = 0;
                    if (isset($item->featured) && $item->featured) $score += 1000;
                    $score += ($item->avg_rating ?? $item->rating ?? 0) * 100;
                    return $score;
                });
        }
    }

    private function recordSearch($query, $type, $filters, $resultsCount)
    {
        if (empty($query)) return;

        $cleanFilters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        UserSearch::recordSearch($query, $type, $cleanFilters, $resultsCount);
    }

    private function hasFilters($request)
    {
        $filterFields = [
            'category', 'min_price', 'max_price', 'min_rating', 
            'city', 'state', 'tags', 'in_stock', 'on_sale', 'featured'
        ];
        
        foreach ($filterFields as $field) {
            if ($request->filled($field)) {
                return true;
            }
        }
        
        return false;
    }

    private function getAvailableFilters()
    {
        return [
            'categories' => [
                'products' => Product::active()->distinct()->pluck('category')->filter()->sort()->values(),
                'services' => Service::active()->distinct()->pluck('category')->filter()->sort()->values(),
            ],
            'price_ranges' => [
                ['label' => 'Até R$ 50', 'max' => 50],
                ['label' => 'R$ 50 - R$ 100', 'min' => 50, 'max' => 100],
                ['label' => 'R$ 100 - R$ 200', 'min' => 100, 'max' => 200],
                ['label' => 'R$ 200 - R$ 500', 'min' => 200, 'max' => 500],
                ['label' => 'Acima de R$ 500', 'min' => 500],
            ],
            'rating_options' => [
                ['label' => '4+ estrelas', 'value' => 4],
                ['label' => '3+ estrelas', 'value' => 3],
                ['label' => '2+ estrelas', 'value' => 2],
                ['label' => '1+ estrela', 'value' => 1],
            ],
            'cities' => Petshop::active()->distinct()->pluck('city')->filter()->sort()->values(),
            'states' => Petshop::active()->distinct()->pluck('state')->filter()->sort()->values(),
            'sort_options' => [
                ['label' => 'Relevância', 'value' => 'relevance'],
                ['label' => 'Menor preço', 'value' => 'price_asc'],
                ['label' => 'Maior preço', 'value' => 'price_desc'],
                ['label' => 'Melhor avaliação', 'value' => 'rating'],
                ['label' => 'Mais recente', 'value' => 'newest'],
                ['label' => 'Nome A-Z', 'value' => 'name'],
                ['label' => 'Em destaque', 'value' => 'featured'],
            ]
        ];
    }

    private function getRecentSearches()
    {
        if (!auth()->check()) {
            return collect();
        }

        return UserSearch::getUserRecentSearches(auth()->id(), 5);
    }

    private function getPopularSearches($type = null)
    {
        return UserSearch::getPopularSearches($type, 8);
    }
}
