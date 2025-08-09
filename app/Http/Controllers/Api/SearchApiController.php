<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchApiController extends Controller
{
    //
}
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Service;
use App\Models\Petshop;
use App\Models\UserSearch;

class SearchApiController extends Controller
{
    public function quickSearch(Request $request)
    {
        $query = $request->get('q', '');
        $limit = $request->get('limit', 8);
        
        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => [],
                'recent' => $this->getRecentSearches(),
                'popular' => $this->getPopularSearches()
            ]);
        }

        $suggestions = $this->getSuggestions($query, $limit);
        
        return response()->json([
            'suggestions' => $suggestions,
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

        // Sugestões de termos populares
        $popularTerms = UserSearch::where('query', 'LIKE', "%{$query}%")
            ->when($type !== 'all', function ($q) use ($type) {
                return $q->where('type', $type);
            })
            ->selectRaw('query, COUNT(*) as count')
            ->groupBy('query')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->pluck('query');

        foreach ($popularTerms as $term) {
            $suggestions->push([
                'text' => $term,
                'type' => 'term',
                'icon' => 'fas fa-search'
            ]);
        }

        // Sugestões de produtos
        if ($type === 'all' || $type === 'products') {
            $products = Product::active()
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get(['id', 'name', 'image']);

            foreach ($products as $product) {
                $suggestions->push([
                    'text' => $product->name,
                    'type' => 'product',
                    'icon' => 'fas fa-box',
                    'image' => $product->image_url,
                    'url' => route('products.show', $product->id)
                ]);
            }
        }

        // Sugestões de serviços
        if ($type === 'all' || $type === 'services') {
            $services = Service::active()
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get(['id', 'name', 'image']);

            foreach ($services as $service) {
                $suggestions->push([
                    'text' => $service->name,
                    'type' => 'service',
                    'icon' => 'fas fa-concierge-bell',
                    'image' => $service->image_url,
                    'url' => route('services.show', $service->id)
                ]);
            }
        }

        // Sugestões de petshops
        if ($type === 'all' || $type === 'petshops') {
            $petshops = Petshop::active()
                ->where('name', 'LIKE', "%{$query}%")
                ->limit(3)
                ->get(['id', 'name', 'logo', 'city']);

            foreach ($petshops as $petshop) {
                $suggestions->push([
                    'text' => $petshop->name,
                    'type' => 'petshop',
                    'icon' => 'fas fa-store',
                    'image' => $petshop->logo_url,
                    'subtitle' => $petshop->city,
                    'url' => route('petshops.show', $petshop->id)
                ]);
            }
        }

        return response()->json($suggestions->take(10)->values());
    }

    public function filters(Request $request)
    {
        $type = $request->get('type', 'all');
        
        $filters = [
            'categories' => [],
            'price_ranges' => [
                ['label' => 'Até R$ 50', 'value' => '0-50'],
                ['label' => 'R$ 50 - R$ 100', 'value' => '50-100'],
                ['label' => 'R$ 100 - R$ 200', 'value' => '100-200'],
                ['label' => 'R$ 200 - R$ 500', 'value' => '200-500'],
                ['label' => 'Acima de R$ 500', 'value' => '500+'],
            ],
            'ratings' => [
                ['label' => '4+ estrelas', 'value' => 4],
                ['label' => '3+ estrelas', 'value' => 3],
                ['label' => '2+ estrelas', 'value' => 2],
            ]
        ];

        if ($type === 'all' || $type === 'products') {
            $filters['categories'] = array_merge(
                $filters['categories'],
                Product::active()->distinct()->pluck('category')->filter()->sort()->values()->toArray()
            );
        }

        if ($type === 'all' || $type === 'services') {
            $filters['categories'] = array_merge(
                $filters['categories'],
                Service::active()->distinct()->pluck('category')->filter()->sort()->values()->toArray()
            );
        }

        if ($type === 'all' || $type === 'petshops') {
            $filters['locations'] = [
                'cities' => Petshop::active()->distinct()->pluck('city')->filter()->sort()->values(),
                'states' => Petshop::active()->distinct()->pluck('state')->filter()->sort()->values(),
            ];
        }

        $filters['categories'] = array_unique($filters['categories']);
        sort($filters['categories']);

        return response()->json($filters);
    }

    public function stats(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        if (empty($query)) {
            return response()->json([
                'total' => 0,
                'by_type' => []
            ]);
        }

        $stats = [
            'products' => 0,
            'services' => 0,
            'petshops' => 0
        ];

        if ($type === 'all' || $type === 'products') {
            $stats['products'] = Product::active()->search($query)->count();
        }

        if ($type === 'all' || $type === 'services') {
            $stats['services'] = Service::active()->search($query)->count();
        }

        if ($type === 'all' || $type === 'petshops') {
            $stats['petshops'] = Petshop::active()->search($query)->count();
        }

        return response()->json([
            'total' => array_sum($stats),
            'by_type' => $stats,
            'query' => $query
        ]);
    }

    private function getSuggestions($query, $limit)
    {
        $suggestions = collect();

        // Produtos
        $products = Product::active()
            ->quickSearch($query, $limit)
            ->get(['id', 'name', 'price', 'image'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name,
                    'type' => 'product',
                    'price' => 'R$ ' . number_format($item->price, 2, ',', '.'),
                    'image' => $item->image_url,
                    'url' => route('products.show', $item->id)
                ];
            });

        $suggestions = $suggestions->merge($products);

        // Serviços
        $services = Service::active()
            ->quickSearch($query, $limit)
            ->get(['id', 'name', 'price', 'image'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name,
                    'type' => 'service',
                    'price' => 'R$ ' . number_format($item->price, 2, ',', '.'),
                    'image' => $item->image_url,
                    'url' => route('services.show', $item->id)
                ];
            });

        $suggestions = $suggestions->merge($services);

        // Petshops
        $petshops = Petshop::active()
            ->quickSearch($query, $limit)
            ->get(['id', 'name', 'city', 'logo'])
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->name,
                    'type' => 'petshop',
                    'subtitle' => $item->city,
                    'image' => $item->logo_url,
                    'url' => route('petshops.show', $item->id)
                ];
            });

        $suggestions = $suggestions->merge($petshops);

        return $suggestions->take($limit);
    }

    private function getRecentSearches()
    {
        if (!auth()->check()) {
            return collect();
        }

        return UserSearch::getUserRecentSearches(auth()->id(), 5)
            ->map(function ($search) {
                return [
                    'text' => $search->query,
                    'type' => 'recent',
                    'icon' => 'fas fa-history'
                ];
            });
    }

    private function getPopularSearches()
    {
        return UserSearch::getPopularSearches(null, 5)
            ->map(function ($search) {
                return [
                    'text' => $search->query,
                    'type' => 'popular',
                    'icon' => 'fas fa-fire'
                ];
            });
    }
}
