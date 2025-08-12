<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Petshop;
use App\Models\Pet;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Redirecionar para o dashboard analytics específico de cada papel
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('petshop')) {
            return redirect()->route('petshop.dashboard');
        } elseif ($user->hasRole('employee')) {
            return redirect()->route('analytics.employee');
        } else {
            return redirect()->route('analytics.client');
        }
    }

    /**
     * Dashboard para clientes (fallback caso não use analytics)
     */
    public function clientDashboard()
    {
        $user = Auth::user();
        
        // Obter os próximos agendamentos do cliente
        $upcomingAppointments = Appointment::where('user_id', $user->id)
            ->whereDate('appointment_datetime', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->with(['pet', 'service', 'service.petshop'])
            ->orderBy('appointment_datetime')
            ->take(3)
            ->get();
        
        // Obter os pedidos recentes do cliente
        $recentOrders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        return view('dashboard.client', compact('upcomingAppointments', 'recentOrders'));
    }

    /**
     * Dashboard para funcionários (fallback caso não use analytics)
     */
    public function employeeDashboard()
    {
        $user = Auth::user();
        $employee = $user->employee;
        
        if (!$employee) {
            return redirect()->route('home')->with('error', 'Funcionário não encontrado.');
        }
        
        $today = now()->format('Y-m-d');
        $todayAppointments = Appointment::where('employee_id', $employee->id)
            ->whereDate('appointment_datetime', $today)
            ->with(['pet', 'service', 'user'])
            ->orderBy('appointment_datetime')
            ->get();
        
        $upcomingAppointments = Appointment::where('employee_id', $employee->id)
            ->whereDate('appointment_datetime', '>', $today)
            ->where('status', '!=', 'cancelled')
            ->orderBy('appointment_datetime')
            ->take(5)
            ->get();
        
        return view('employee.dashboard', compact('employee', 'todayAppointments', 'upcomingAppointments'));
    }

    /**
     * Dashboard para petshops (fallback caso não use analytics)
     */
    public function petshopDashboard()
    {
        $user = Auth::user();
        $petshop = $user->petshop;
        
        if (!$petshop) {
            return redirect()->route('home')
                ->with('error', 'Seu perfil de petshop ainda não foi configurado. Entre em contato com o administrador.');
        }
        
        $productCount = Product::where('petshop_id', $petshop->id)->count();
        $serviceCount = Service::where('petshop_id', $petshop->id)->count();
        $employeeCount = $petshop->employees()->count();
        
        $pendingAppointments = Appointment::whereHas('service', function ($query) use ($petshop) {
            $query->where('petshop_id', $petshop->id);
        })
        ->where('status', 'pending')
        ->count();
        
        $recentOrders = Order::whereHas('items.product', function ($query) use ($petshop) {
            $query->where('petshop_id', $petshop->id);
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
        
        return view('petshop.dashboard', compact(
            'petshop', 
            'productCount', 
            'serviceCount', 
            'employeeCount', 
            'pendingAppointments', 
            'recentOrders'
        ));
    }
}