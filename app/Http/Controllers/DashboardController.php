<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;         // Adicione esta linha
use App\Models\Petshop;      // Adicione esta linha
use App\Models\Pet;          // Adicione esta linha
use App\Models\Order;        // Adicione esta linha
use App\Models\Product;      // Adicione esta linha 
use App\Models\Service;      // Adicione esta linha
use App\Models\Employee;     // Adicione esta linha
use App\Models\Appointment;  // Adicione esta linha

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            $userCount = User::count();
            $petshopCount = Petshop::count();
            $petCount = Pet::count();
            $orderCount = Order::count();
            
            $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
            $recentPetshops = Petshop::with('user')->orderBy('created_at', 'desc')->take(5)->get();
            
            return view('dashboard.admin', compact(
                'userCount', 
                'petshopCount', 
                'petCount', 
                'orderCount', 
                'recentUsers', 
                'recentPetshops'
            ));
        } elseif ($user->hasRole('petshop')) {
            $petshop = $user->petshop;
            
            // Verificar se o petshop existe para este usuário
            if (!$petshop) {
                // Redirecionar para uma página informando que precisa ser configurado um petshop
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
        } elseif ($user->hasRole('employee')) {
            $employee = $user->employee;
            
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
        } else {
            // Cliente
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
    }
}