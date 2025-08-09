<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Search Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para o sistema de busca avançada
    |
    */

    'enabled' => env('SEARCH_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Search Types
    |--------------------------------------------------------------------------
    |
    | Tipos de busca disponíveis no sistema
    |
    */
    'types' => [
        'all' => [
            'label' => 'Tudo',
            'icon' => 'fas fa-search',
            'models' => ['Product', 'Service', 'Petshop']
        ],
        'products' => [
            'label' => 'Produtos',
            'icon' => 'fas fa-box',
            'models' => ['Product']
        ],
        'services' => [
            'label' => 'Serviços',
            'icon' => 'fas fa-concierge-bell',
            'models' => ['Service']
        ],
        'petshops' => [
            'label' => 'Pet Shops',
            'icon' => 'fas fa-store',
            'models' => ['Petshop']
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Settings
    |--------------------------------------------------------------------------
    |
    | Configurações gerais de busca
    |
    */
    'settings' => [
        'min_query_length' => 2,
        'max_suggestions' => 15,
        'autocomplete_delay' => 300,
        'results_per_page' => 12,
        'max_recent_searches' => 10,
        'popular_searches_days' => 30,
        'cache_duration' => 3600, // 1 hora
        'enable_search_history' => true,
        'enable_analytics' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Price Ranges
    |--------------------------------------------------------------------------
    |
    | Faixas de preço predefinidas para filtros
    |
    */
    'price_ranges' => [
        ['label' => 'Até R$ 25', 'min' => null, 'max' => 25],
        ['label' => 'R$ 25 - R$ 50', 'min' => 25, 'max' => 50],
        ['label' => 'R$ 50 - R$ 100', 'min' => 50, 'max' => 100],
        ['label' => 'R$ 100 - R$ 200', 'min' => 100, 'max' => 200],
        ['label' => 'R$ 200 - R$ 500', 'min' => 200, 'max' => 500],
        ['label' => 'R$ 500 - R$ 1000', 'min' => 500, 'max' => 1000],
        ['label' => 'Acima de R$ 1000', 'min' => 1000, 'max' => null],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sort Options
    |--------------------------------------------------------------------------
    |
    | Opções de ordenação disponíveis
    |
    */
    'sort_options' => [
        'relevance' => [
            'label' => 'Relevância',
            'icon' => 'fas fa-star'
        ],
        'price_asc' => [
            'label' => 'Menor preço',
            'icon' => 'fas fa-sort-amount-up'
        ],
        'price_desc' => [
            'label' => 'Maior preço',
            'icon' => 'fas fa-sort-amount-down'
        ],
        'rating' => [
            'label' => 'Melhor avaliação',
            'icon' => 'fas fa-star'
        ],
        'newest' => [
            'label' => 'Mais recente',
            'icon' => 'fas fa-clock'
        ],
        'oldest' => [
            'label' => 'Mais antigo',
            'icon' => 'fas fa-history'
        ],
        'name' => [
            'label' => 'Nome A-Z',
            'icon' => 'fas fa-sort-alpha-down'
        ],
        'featured' => [
            'label' => 'Em destaque',
            'icon' => 'fas fa-star'
        ],
        'popularity' => [
            'label' => 'Mais popular',
            'icon' => 'fas fa-fire'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    |
    | Categorias padrão do sistema
    |
    */
    'categories' => [
        'products' => [
            'Alimentação',
            'Brinquedos',
            'Higiene e Cuidados',
            'Roupas e Acessórios',
            'Camas e Casas',
            'Coleiras e Guias',
            'Medicamentos',
            'Petiscos',
            'Aquarismo',
            'Jardinagem'
        ],
        'services' => [
            'Banho e Tosa',
            'Veterinário',
            'Hospedagem',
            'Adestramento',
            'Transporte',
            'Fisioterapia',
            'Estética',
            'Nutrição',
            'Vacinação',
            'Consultas'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Filters
    |--------------------------------------------------------------------------
    |
    | Configurações dos filtros de busca
    |
    */
    'filters' => [
        'enabled' => [
            'category',
            'price_range',
            'rating',
            'location',
            'featured',
            'on_sale',
            'in_stock',
            'tags',
            'species' // Para petshops
        ],
        'location' => [
            'enable_radius_search' => true,
            'default_radius' => 50, // km
            'max_radius' => 200
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Search Analytics
    |--------------------------------------------------------------------------
    |
    | Configurações para analytics de busca
    |
    */
    'analytics' => [
        'track_searches' => true,
        'track_no_results' => true,
        'track_clicks' => true,
        'retention_days' => 90
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Configurações de cache para busca
    |
    */
    'cache' => [
        'enabled' => env('SEARCH_CACHE_ENABLED', true),
        'ttl' => env('SEARCH_CACHE_TTL', 3600),
        'prefix' => 'search:',
        'tags' => ['search', 'filters', 'suggestions']
    ]
];