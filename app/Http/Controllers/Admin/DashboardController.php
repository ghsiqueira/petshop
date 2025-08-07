<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Petshop;
use App\Models\Coupon;
use App\Models\OrderItem;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        // Período para análise (padrão: últimos 30 dias)
        $period = $request->get('period', '30');
        $startDate = Carbon::now()->subDays($period);
        $endDate = Carbon::now();

        // === CARDS DE ESTATÍSTICAS ===
        $totalUsers = User::count();
        $totalPetshops = Petshop::count();
        $totalProducts = Product::count();
        $totalOrders = Order::count();

        // Novos usuários no período
        $newUsers = User::where('created_at', '>=', $startDate)->count();
        $newUsersGrowth = $this->calculateGrowth($newUsers, $period, 'users');

        // Receita total e do período (status válidos: paid, shipped, delivered)
        $totalRevenue = Order::whereIn('status', ['paid', 'shipped', 'delivered'])->sum('total_amount');
        $periodRevenue = Order::whereIn('status', ['paid', 'shipped', 'delivered'])
                             ->where('created_at', '>=', $startDate)
                             ->sum('total_amount');
        $revenueGrowth = $this->calculateGrowth($periodRevenue, $period, 'revenue');

        // Pedidos do período
        $periodOrders = Order::where('created_at', '>=', $startDate)->count();
        $ordersGrowth = $this->calculateGrowth($periodOrders, $period, 'orders');

        // Cupons usados no período
        $couponsUsed = CouponUsage::where('created_at', '>=', $startDate)->count();
        $couponsGrowth = $this->calculateGrowth($couponsUsed, $period, 'coupons');

        // === GRÁFICOS ===

        // 1. Vendas por dia (últimos X dias)
        $salesByDay = $this->getSalesByDay($period);

        // 2. Produtos mais vendidos
        $topProducts = $this->getTopProducts(10);

        // 3. Receita por mês (últimos 12 meses)
        $monthlyRevenue = $this->getMonthlyRevenue();

        // 4. Usuários por tipo
        $usersByRole = $this->getUsersByRole();

        // 5. Top Pet Shops por vendas
        $topPetshops = $this->getTopPetshops(10);

        // 6. Cupons mais utilizados
        $topCoupons = $this->getTopCoupons(10);

        // === LISTAS RECENTES ===
        $recentOrders = Order::with(['user', 'items.product'])
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        $recentUsers = User::orderBy('created_at', 'desc')
                          ->take(5)
                          ->get();

        return view('admin.dashboard.index', compact(
            // Cards
            'totalUsers', 'totalPetshops', 'totalProducts', 'totalOrders',
            'newUsers', 'newUsersGrowth', 'totalRevenue', 'periodRevenue', 'revenueGrowth',
            'periodOrders', 'ordersGrowth', 'couponsUsed', 'couponsGrowth',
            
            // Gráficos
            'salesByDay', 'topProducts', 'monthlyRevenue', 'usersByRole', 'topPetshops', 'topCoupons',
            
            // Listas
            'recentOrders', 'recentUsers',
            
            // Filtros
            'period'
        ));
    }

    private function calculateGrowth($currentValue, $period, $type)
    {
        $previousPeriod = Carbon::now()->subDays($period * 2);
        $currentPeriodStart = Carbon::now()->subDays($period);
        
        $previousValue = 0;
        
        switch ($type) {
            case 'users':
                $previousValue = User::where('created_at', '>=', $previousPeriod)
                                   ->where('created_at', '<', $currentPeriodStart)
                                   ->count();
                break;
            case 'revenue':
                $previousValue = Order::where('status', '!=', 'cancelled')
                                     ->where('created_at', '>=', $previousPeriod)
                                     ->where('created_at', '<', $currentPeriodStart)
                                     ->sum('total_amount');
                break;
            case 'orders':
                $previousValue = Order::where('created_at', '>=', $previousPeriod)
                                     ->where('created_at', '<', $currentPeriodStart)
                                     ->count();
                break;
            case 'coupons':
                $previousValue = CouponUsage::where('created_at', '>=', $previousPeriod)
                                           ->where('created_at', '<', $currentPeriodStart)
                                           ->count();
                break;
        }

        if ($previousValue == 0) {
            return $currentValue > 0 ? 100 : 0;
        }

        return round((($currentValue - $previousValue) / $previousValue) * 100, 1);
    }

    private function getSalesByDay($days)
    {
        $salesData = Order::whereIn('status', ['paid', 'shipped', 'delivered'])
                         ->where('created_at', '>=', Carbon::now()->subDays($days))
                         ->select(
                             DB::raw('DATE(created_at) as date'),
                             DB::raw('COUNT(*) as orders'),
                             DB::raw('SUM(total_amount) as revenue')
                         )
                         ->groupBy('date')
                         ->orderBy('date')
                         ->get();

        $labels = [];
        $ordersData = [];
        $revenueData = [];

        // Preencher com zeros os dias sem vendas
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::now()->subDays($i)->format('d/m');
            
            $dayData = $salesData->firstWhere('date', $date);
            $ordersData[] = $dayData ? $dayData->orders : 0;
            $revenueData[] = $dayData ? floatval($dayData->revenue) : 0;
        }

        return [
            'labels' => $labels,
            'orders' => $ordersData,
            'revenue' => $revenueData
        ];
    }

    private function getTopProducts($limit)
    {
        return OrderItem::select('product_id')
                       ->selectRaw('SUM(quantity) as total_sold')
                       ->selectRaw('SUM(quantity * price) as total_revenue')
                       ->with(['product' => function($query) {
                           $query->select('id', 'name', 'image');
                       }])
                       ->groupBy('product_id')
                       ->orderBy('total_sold', 'desc')
                       ->take($limit)
                       ->get()
                       ->map(function($item) {
                           return [
                               'name' => $item->product->name ?? 'Produto Removido',
                               'image' => $item->product->image ?? null,
                               'sold' => $item->total_sold,
                               'revenue' => $item->total_revenue
                           ];
                       });
    }

    private function getMonthlyRevenue()
    {
        $monthlyData = Order::whereIn('status', ['paid', 'shipped', 'delivered'])
                           ->where('created_at', '>=', Carbon::now()->subYear())
                           ->select(
                               DB::raw('YEAR(created_at) as year'),
                               DB::raw('MONTH(created_at) as month'),
                               DB::raw('SUM(total_amount) as revenue'),
                               DB::raw('COUNT(*) as orders')
                           )
                           ->groupBy('year', 'month')
                           ->orderBy('year')
                           ->orderBy('month')
                           ->get();

        $labels = [];
        $revenueData = [];
        $ordersData = [];

        // Últimos 12 meses
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;
            
            $labels[] = $date->format('M/Y');
            
            $monthData = $monthlyData->where('year', $year)->where('month', $month)->first();
            $revenueData[] = $monthData ? floatval($monthData->revenue) : 0;
            $ordersData[] = $monthData ? $monthData->orders : 0;
        }

        return [
            'labels' => $labels,
            'revenue' => $revenueData,
            'orders' => $ordersData
        ];
    }

    private function getUsersByRole()
    {
        $users = DB::table('users')
                   ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                   ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                   ->select('roles.name', DB::raw('COUNT(*) as count'))
                   ->groupBy('roles.name')
                   ->get();

        $labels = [];
        $data = [];

        foreach ($users as $user) {
            $labels[] = ucfirst($user->name);
            $data[] = $user->count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getTopPetshops($limit)
    {
        return DB::table('petshops')
                 ->leftJoin('products', 'petshops.id', '=', 'products.petshop_id')
                 ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                 ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                 ->select(
                     'petshops.name',
                     'petshops.id',
                     DB::raw('COALESCE(SUM(order_items.quantity * order_items.price), 0) as total_revenue'),
                     DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_products_sold')
                 )
                 ->where(function($query) {
                     $query->whereIn('orders.status', ['paid', 'shipped', 'delivered'])
                           ->orWhereNull('orders.status');
                 })
                 ->groupBy('petshops.id', 'petshops.name')
                 ->orderBy('total_revenue', 'desc')
                 ->take($limit)
                 ->get();
    }

    private function getTopCoupons($limit)
    {
        return Coupon::select('coupons.code', 'coupons.name', 'coupons.used_count')
                     ->selectRaw('COALESCE(SUM(coupon_usages.discount_amount), 0) as total_discount')
                     ->leftJoin('coupon_usages', 'coupons.id', '=', 'coupon_usages.coupon_id')
                     ->groupBy('coupons.id', 'coupons.code', 'coupons.name', 'coupons.used_count')
                     ->orderBy('coupons.used_count', 'desc')
                     ->take($limit)
                     ->get();
    }
}