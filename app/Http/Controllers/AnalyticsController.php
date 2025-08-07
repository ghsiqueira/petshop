<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard Analytics para Petshop
     */
    public function petshopDashboard()
    {
        if (!auth()->user()->hasRole('petshop')) {
            abort(403, 'Acesso negado');
        }
        
        $petshop = auth()->user()->petshop;
        
        if (!$petshop) {
            return redirect()->route('home')->with('error', 'Petshop não encontrado.');
        }

        // Métricas principais (últimos 30 dias)
        $lastMonth = Carbon::now()->subMonth();
        
        // Receita total (últimos 30 dias)
        $monthlyRevenue = Order::whereHas('items.product', function ($query) use ($petshop) {
                $query->where('petshop_id', $petshop->id);
            })
            ->where('created_at', '>=', $lastMonth)
            ->where('status', 'delivered')
            ->sum('total_amount');

        // Crescimento mensal
        $previousMonth = Order::whereHas('items.product', function ($query) use ($petshop) {
                $query->where('petshop_id', $petshop->id);
            })
            ->whereBetween('created_at', [Carbon::now()->subMonths(2), $lastMonth])
            ->where('status', 'delivered')
            ->sum('total_amount');

        $growthPercentage = $previousMonth > 0 ? 
            (($monthlyRevenue - $previousMonth) / $previousMonth) * 100 : 100;

        // Produtos mais vendidos (últimos 30 dias)
        $topProducts = OrderItem::whereHas('order', function ($query) use ($lastMonth) {
                $query->where('created_at', '>=', $lastMonth)
                      ->where('status', 'delivered');
            })
            ->whereHas('product', function ($query) use ($petshop) {
                $query->where('petshop_id', $petshop->id);
            })
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(price * quantity) as total_revenue'))
            ->with('product')
            ->groupBy('product_id')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // Serviços mais agendados (últimos 30 dias)
        $topServices = Appointment::whereHas('service', function ($query) use ($petshop) {
                $query->where('petshop_id', $petshop->id);
            })
            ->where('created_at', '>=', $lastMonth)
            ->whereIn('status', ['confirmed', 'completed'])
            ->select('service_id', DB::raw('COUNT(*) as total_appointments'))
            ->with('service')
            ->groupBy('service_id')
            ->orderBy('total_appointments', 'desc')
            ->take(5)
            ->get();

        // Dados para gráfico de vendas (últimos 6 meses)
        $salesChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthSales = Order::whereHas('items.product', function ($query) use ($petshop) {
                    $query->where('petshop_id', $petshop->id);
                })
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', 'delivered')
                ->sum('total_amount');
            
            $salesChart[] = [
                'month' => $monthStart->format('M Y'),
                'sales' => (float)$monthSales
            ];
        }

        // Avaliações médias - Versão simplificada
        $productIds = Product::where('petshop_id', $petshop->id)->pluck('id');
        $avgProductRating = Review::where('reviewable_type', Product::class)
            ->whereIn('reviewable_id', $productIds)
            ->avg('rating') ?? 0;

        $serviceIds = Service::where('petshop_id', $petshop->id)->pluck('id');
        $avgServiceRating = Review::where('reviewable_type', Service::class)
            ->whereIn('reviewable_id', $serviceIds)
            ->avg('rating') ?? 0;

        // Clientes únicos (últimos 30 dias)
        $uniqueCustomers = Order::whereHas('items.product', function ($query) use ($petshop) {
                $query->where('petshop_id', $petshop->id);
            })
            ->where('created_at', '>=', $lastMonth)
            ->distinct('user_id')
            ->count('user_id');

        // Agendamentos por status
        $appointmentStats = Appointment::whereHas('service', function ($query) use ($petshop) {
                $query->where('petshop_id', $petshop->id);
            })
            ->where('created_at', '>=', $lastMonth)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Estatísticas gerais
        $totalProducts = Product::where('petshop_id', $petshop->id)->count();
        $totalServices = Service::where('petshop_id', $petshop->id)->count();
        $totalEmployees = $petshop->employees()->count();

        return view('analytics.petshop-dashboard', compact(
            'petshop',
            'monthlyRevenue',
            'growthPercentage',
            'topProducts',
            'topServices',
            'salesChart',
            'avgProductRating',
            'avgServiceRating',
            'uniqueCustomers',
            'appointmentStats',
            'totalProducts',
            'totalServices',
            'totalEmployees'
        ));
    }

    /**
     * Dashboard Analytics para Funcionário
     */
    public function employeeDashboard()
    {
        if (!auth()->user()->hasRole('employee')) {
            abort(403, 'Acesso negado');
        }
        
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return redirect()->route('home')->with('error', 'Funcionário não encontrado.');
        }

        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Agendamentos de hoje
        $todayAppointments = Appointment::where('employee_id', $employee->id)
            ->whereDate('appointment_datetime', $today)
            ->with(['pet', 'service', 'user'])
            ->orderBy('appointment_datetime')
            ->get();

        // Próximos agendamentos (próximos 7 dias)
        $upcomingAppointments = Appointment::where('employee_id', $employee->id)
            ->whereBetween('appointment_datetime', [Carbon::now(), Carbon::now()->addDays(7)])
            ->where('status', '!=', 'cancelled')
            ->with(['pet', 'service', 'user'])
            ->orderBy('appointment_datetime')
            ->take(10)
            ->get();

        // Estatísticas do mês
        $monthlyStats = [
            'total_appointments' => Appointment::where('employee_id', $employee->id)
                ->where('created_at', '>=', $thisMonth)
                ->count(),
            'completed_appointments' => Appointment::where('employee_id', $employee->id)
                ->where('created_at', '>=', $thisMonth)
                ->where('status', 'completed')
                ->count(),
            'cancelled_appointments' => Appointment::where('employee_id', $employee->id)
                ->where('created_at', '>=', $thisMonth)
                ->where('status', 'cancelled')
                ->count()
        ];

        // Avaliação média dos serviços - Versão simplificada
        $completedAppointments = Appointment::where('employee_id', $employee->id)
            ->where('status', 'completed')
            ->pluck('service_id')
            ->unique();
            
        $avgRating = 0;
        if ($completedAppointments->isNotEmpty()) {
            $avgRating = Review::where('reviewable_type', Service::class)
                ->whereIn('reviewable_id', $completedAppointments)
                ->avg('rating') ?? 0;
        }

        // Serviços mais realizados
        $topServices = Appointment::where('employee_id', $employee->id)
            ->where('created_at', '>=', $thisMonth)
            ->whereIn('status', ['confirmed', 'completed'])
            ->select('service_id', DB::raw('COUNT(*) as count'))
            ->with('service')
            ->groupBy('service_id')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

        // Gráfico de agendamentos da semana
        $weeklyChart = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->startOfWeek()->addDays($i);
            $count = Appointment::where('employee_id', $employee->id)
                ->whereDate('appointment_datetime', $date)
                ->count();
            
            $weeklyChart[] = [
                'day' => $date->format('D'),
                'date' => $date->format('d/m'),
                'count' => $count
            ];
        }

        return view('analytics.employee-dashboard', compact(
            'employee',
            'todayAppointments',
            'upcomingAppointments',
            'monthlyStats',
            'avgRating',
            'topServices',
            'weeklyChart'
        ));
    }

    /**
     * Dashboard Analytics para Cliente
     */
    public function clientDashboard()
    {
        if (!auth()->user()->hasRole('client')) {
            abort(403, 'Acesso negado');
        }
        
        $user = auth()->user();
        $thisYear = Carbon::now()->startOfYear();

        // Próximos agendamentos
        $upcomingAppointments = Appointment::where('user_id', $user->id)
            ->whereDate('appointment_datetime', '>=', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->with(['pet', 'service', 'service.petshop'])
            ->orderBy('appointment_datetime')
            ->take(5)
            ->get();

        // Pedidos recentes
        $recentOrders = Order::where('user_id', $user->id)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Estatísticas anuais
        $yearlyStats = [
            'total_spent' => Order::where('user_id', $user->id)
                ->where('created_at', '>=', $thisYear)
                ->where('status', 'delivered')
                ->sum('total_amount'),
            'total_orders' => Order::where('user_id', $user->id)
                ->where('created_at', '>=', $thisYear)
                ->count(),
            'total_appointments' => Appointment::where('user_id', $user->id)
                ->where('created_at', '>=', $thisYear)
                ->count()
        ];

        // Gastos mensais (últimos 6 meses)
        $spendingChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthSpent = Order::where('user_id', $user->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', 'delivered')
                ->sum('total_amount');
            
            $spendingChart[] = [
                'month' => $monthStart->format('M Y'),
                'amount' => (float)$monthSpent
            ];
        }

        // Pets favoritos (mais agendamentos)
        $favoritePets = Appointment::where('user_id', $user->id)
            ->select('pet_id', DB::raw('COUNT(*) as appointments_count'))
            ->with('pet')
            ->groupBy('pet_id')
            ->orderBy('appointments_count', 'desc')
            ->take(3)
            ->get();

        return view('analytics.client-dashboard', compact(
            'upcomingAppointments',
            'recentOrders',
            'yearlyStats',
            'spendingChart',
            'favoritePets'
        ));
    }

    /**
     * API endpoint para dados de gráficos
     */
    public function chartData(Request $request, $type)
    {
        switch ($type) {
            case 'petshop-sales':
                return $this->getPetshopSalesData($request);
            case 'employee-weekly':
                return $this->getEmployeeWeeklyData($request);
            case 'client-spending':
                return $this->getClientSpendingData($request);
            default:
                return response()->json(['error' => 'Invalid chart type'], 400);
        }
    }

    private function getPetshopSalesData($request)
    {
        $petshop = auth()->user()->petshop;
        $months = $request->get('months', 6);
        
        $salesData = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthSales = Order::whereHas('items.product', function ($query) use ($petshop) {
                    $query->where('petshop_id', $petshop->id);
                })
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', 'delivered')
                ->sum('total_amount');
            
            $salesData[] = [
                'month' => $monthStart->format('M Y'),
                'sales' => (float)$monthSales
            ];
        }
        
        return response()->json($salesData);
    }

    private function getEmployeeWeeklyData($request)
    {
        $employee = auth()->user()->employee;
        
        $weeklyData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->startOfWeek()->addDays($i);
            $count = Appointment::where('employee_id', $employee->id)
                ->whereDate('appointment_datetime', $date)
                ->count();
            
            $weeklyData[] = [
                'day' => $date->format('D'),
                'count' => $count
            ];
        }
        
        return response()->json($weeklyData);
    }

    private function getClientSpendingData($request)
    {
        $user = auth()->user();
        $months = $request->get('months', 6);
        
        $spendingData = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $monthSpent = Order::where('user_id', $user->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('status', 'delivered')
                ->sum('total_amount');
            
            $spendingData[] = [
                'month' => $monthStart->format('M Y'),
                'amount' => (float)$monthSpent
            ];
        }
        
        return response()->json($spendingData);
    }
}