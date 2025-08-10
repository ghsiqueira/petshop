<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Petshop;
use App\Models\Product;
use App\Models\Service;
use App\Models\Order;
use App\Models\Appointment;
use App\Models\Coupon;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        // Período selecionado (padrão: últimos 30 dias)
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        // Métricas principais
        $totalUsers = User::count();
        $totalPetshops = Petshop::count();
        $totalProducts = Product::count();
        $totalServices = Service::count();

        // Métricas do período
        $monthlyRevenue = Order::whereBetween('created_at', $dateRange)
            ->whereIn('status', ['paid', 'shipped', 'delivered'])
            ->sum('total_amount');

        $monthlyOrders = Order::whereBetween('created_at', $dateRange)
            ->count();

        $couponsUsed = Order::whereBetween('created_at', $dateRange)
            ->whereNotNull('coupon_id')
            ->count();

        // Dados para gráficos
        $salesChart = $this->getSalesChartData($dateRange);
        $usersChart = $this->getUsersChartData();
        $monthlyChart = $this->getMonthlyChartData();

        // Top produtos
        $topProducts = $this->getTopProducts();

        // Top pet shops
        $topPetshops = $this->getTopPetshops();

        // Pedidos recentes
        $recentOrders = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalUsers',
            'totalPetshops', 
            'totalProducts',
            'totalServices',
            'monthlyRevenue',
            'monthlyOrders',
            'couponsUsed',
            'salesChart',
            'usersChart',
            'monthlyChart',
            'topProducts',
            'topPetshops',
            'recentOrders',
            'period'
        ));
    }

    /**
     * Obter intervalo de datas baseado no período
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();
        
        switch ($period) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            case 'week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()];
            case 'month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
            case 'year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear()];
            default:
                return [$now->copy()->subDays(30), $now];
        }
    }

    /**
     * Dados do gráfico de vendas diárias
     */
    private function getSalesChartData($dateRange)
    {
        $startDate = $dateRange[0];
        $endDate = $dateRange[1];
        $data = [];

        // Gerar dados para cada dia no intervalo
        $current = $startDate->copy();
        while ($current <= $endDate) {
            $dayStart = $current->copy()->startOfDay();
            $dayEnd = $current->copy()->endOfDay();

            $revenue = Order::whereBetween('created_at', [$dayStart, $dayEnd])
                ->whereIn('status', ['paid', 'shipped', 'delivered'])
                ->sum('total_amount');

            $orders = Order::whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();

            $data[] = [
                'day' => $current->format('d/m'),
                'date' => $current->format('Y-m-d'),
                'revenue' => floatval($revenue),
                'orders' => $orders
            ];

            $current->addDay();
        }

        return $data;
    }

    /**
     * Dados do gráfico de usuários por tipo
     */
    private function getUsersChartData()
    {
        $data = [];

        // Contar usuários por role
        $roles = [
            'client' => ['name' => 'Clientes', 'color' => '#4e73df'],
            'petshop' => ['name' => 'Pet Shops', 'color' => '#1cc88a'],
            'employee' => ['name' => 'Funcionários', 'color' => '#36b9cc'],
            'admin' => ['name' => 'Admins', 'color' => '#f6c23e']
        ];

        foreach ($roles as $role => $config) {
            $count = User::role($role)->count();
            
            if ($count > 0) {
                $data[] = [
                    'type' => $config['name'],
                    'count' => $count,
                    'color' => $config['color']
                ];
            }
        }

        // Se não houver dados específicos por role, usar contagem geral
        if (empty($data)) {
            $totalUsers = User::count();
            $data[] = [
                'type' => 'Usuários',
                'count' => $totalUsers,
                'color' => '#4e73df'
            ];
        }

        return $data;
    }

    /**
     * Dados do gráfico de receita mensal
     */
    private function getMonthlyChartData()
    {
        $data = [];
        $currentMonth = Carbon::now();

        // Últimos 12 meses
        for ($i = 11; $i >= 0; $i--) {
            $month = $currentMonth->copy()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $revenue = Order::whereBetween('created_at', [$monthStart, $monthEnd])
                ->whereIn('status', ['paid', 'shipped', 'delivered'])
                ->sum('total_amount');

            $orders = Order::whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $data[] = [
                'month' => $month->format('M'),
                'full_month' => $month->format('M/Y'),
                'revenue' => floatval($revenue),
                'orders' => $orders
            ];
        }

        return $data;
    }

    /**
     * Top 10 produtos mais vendidos
     */
    private function getTopProducts()
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereIn('orders.status', ['paid', 'shipped', 'delivered'])
            ->select(
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->name,
                    'total_sold' => $item->total_sold,
                    'total_revenue' => $item->total_revenue
                ];
            });
    }

    /**
     * Top pet shops por receita
     */
    private function getTopPetshops()
    {
        return DB::table('petshops')
            ->leftJoin('products', 'petshops.id', '=', 'products.petshop_id')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereIn('orders.status', ['paid', 'shipped', 'delivered']);
            })
            ->select(
                'petshops.name',
                DB::raw('COUNT(DISTINCT orders.id) as total_sales'),
                DB::raw('COALESCE(SUM(order_items.quantity * order_items.price), 0) as revenue')
            )
            ->groupBy('petshops.id', 'petshops.name')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'name' => $item->name,
                    'total_sales' => $item->total_sales,
                    'revenue' => $item->revenue
                ];
            });
    }

    /**
     * Exportar dados do dashboard (integração com ExportController)
     */
    public function export($format)
    {
        // Redirecionar para o ExportController
        return redirect()->route('export.admin.dashboard', $format);
    }

    /**
     * API endpoint para dados em tempo real
     */
    public function chartData(Request $request, $type)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        switch ($type) {
            case 'sales':
                return response()->json($this->getSalesChartData($dateRange));
            
            case 'users':
                return response()->json($this->getUsersChartData());
            
            case 'monthly':
                return response()->json($this->getMonthlyChartData());
            
            case 'products':
                return response()->json($this->getTopProducts());
            
            case 'petshops':
                return response()->json($this->getTopPetshops());
            
            default:
                return response()->json(['error' => 'Tipo de dados não encontrado'], 404);
        }
    }

    /**
     * Atualizar dados em tempo real via AJAX
     */
    public function refreshData(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        $data = [
            'totalUsers' => User::count(),
            'totalPetshops' => Petshop::count(),
            'monthlyRevenue' => Order::whereBetween('created_at', $dateRange)
                ->whereIn('status', ['paid', 'shipped', 'delivered'])
                ->sum('total_amount'),
            'monthlyOrders' => Order::whereBetween('created_at', $dateRange)->count(),
            'couponsUsed' => Order::whereBetween('created_at', $dateRange)
                ->whereNotNull('coupon_id')
                ->count(),
            'lastUpdate' => now()->format('d/m/Y H:i:s')
        ];

        return response()->json($data);
    }

    /**
     * Estatísticas detalhadas para modal ou página separada
     */
    public function detailedStats(Request $request)
    {
        $period = $request->get('period', 'month');
        $dateRange = $this->getDateRange($period);

        return response()->json([
            'period' => $period,
            'date_range' => [
                'start' => $dateRange[0]->format('d/m/Y'),
                'end' => $dateRange[1]->format('d/m/Y')
            ],
            'metrics' => [
                'total_users' => User::count(),
                'new_users' => User::whereBetween('created_at', $dateRange)->count(),
                'total_petshops' => Petshop::count(),
                'active_petshops' => Petshop::where('is_active', true)->count(),
                'total_orders' => Order::count(),
                'period_orders' => Order::whereBetween('created_at', $dateRange)->count(),
                'total_revenue' => Order::whereIn('status', ['paid', 'shipped', 'delivered'])->sum('total_amount'),
                'period_revenue' => Order::whereBetween('created_at', $dateRange)
                    ->whereIn('status', ['paid', 'shipped', 'delivered'])
                    ->sum('total_amount'),
                'avg_order_value' => Order::whereBetween('created_at', $dateRange)
                    ->whereIn('status', ['paid', 'shipped', 'delivered'])
                    ->avg('total_amount'),
                'top_selling_day' => $this->getTopSellingDay($dateRange),
                'growth_metrics' => $this->getGrowthMetrics($dateRange)
            ]
        ]);
    }

    /**
     * Obter dia com mais vendas no período
     */
    private function getTopSellingDay($dateRange)
    {
        $topDay = Order::whereBetween('created_at', $dateRange)
            ->whereIn('status', ['paid', 'shipped', 'delivered'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('date')
            ->orderBy('revenue', 'desc')
            ->first();

        return $topDay ? [
            'date' => Carbon::parse($topDay->date)->format('d/m/Y'),
            'revenue' => $topDay->revenue
        ] : null;
    }

    /**
     * Calcular métricas de crescimento
     */
    private function getGrowthMetrics($dateRange)
    {
        $days = $dateRange[0]->diffInDays($dateRange[1]);
        $previousStart = $dateRange[0]->copy()->subDays($days);
        $previousEnd = $dateRange[0]->copy()->subDay();

        $currentRevenue = Order::whereBetween('created_at', $dateRange)
            ->whereIn('status', ['paid', 'shipped', 'delivered'])
            ->sum('total_amount');

        $previousRevenue = Order::whereBetween('created_at', [$previousStart, $previousEnd])
            ->whereIn('status', ['paid', 'shipped', 'delivered'])
            ->sum('total_amount');

        $currentOrders = Order::whereBetween('created_at', $dateRange)->count();
        $previousOrders = Order::whereBetween('created_at', [$previousStart, $previousEnd])->count();

        return [
            'revenue_growth' => $previousRevenue > 0 
                ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 
                : 0,
            'orders_growth' => $previousOrders > 0 
                ? (($currentOrders - $previousOrders) / $previousOrders) * 100 
                : 0
        ];
    }
}