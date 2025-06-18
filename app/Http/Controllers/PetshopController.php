<?php

namespace App\Http\Controllers;

use App\Models\Petshop;
use App\Models\Product;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetshopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('role:petshop')->except(['index', 'show']);
    }
    
    public function index()
    {
        $petshops = Petshop::where('is_active', true)
                     ->paginate(12);
        
        return view('petshops.index', compact('petshops'));
    }
    
    public function show(Petshop $petshop)
    {
        if (!$petshop->is_active) {
            abort(404);
        }
        
        $products = Product::where('petshop_id', $petshop->id)
                         ->where('is_active', true)
                         ->take(8)
                         ->get();
        
        $services = Service::where('petshop_id', $petshop->id)
                         ->where('is_active', true)
                         ->get();
        
        return view('petshops.show', compact('petshop', 'products', 'services'));
    }
    
    public function dashboard()
    {
        $petshop = auth()->user()->petshop;
        
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
    
    public function orders()
    {
        $petshop = auth()->user()->petshop;
        
        $orders = Order::whereHas('items.product', function ($query) use ($petshop) {
                      $query->where('petshop_id', $petshop->id);
                  })
                  ->with('items.product', 'user')
                  ->orderBy('created_at', 'desc')
                  ->paginate(10);
        
        return view('petshop.orders', compact('orders'));
    }
    
    public function appointments()
    {
        $petshop = auth()->user()->petshop;
        
        $appointments = Appointment::whereHas('service', function ($query) use ($petshop) {
                              $query->where('petshop_id', $petshop->id);
                          })
                          ->with(['pet', 'service', 'employee', 'user'])
                          ->orderBy('appointment_datetime')
                          ->paginate(10);
        
        return view('petshop.appointments', compact('appointments'));
    }
    
    public function update(Request $request)
    {
        $petshop = auth()->user()->petshop;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);
        
        $petshop->name = $request->name;
        $petshop->description = $request->description;
        $petshop->address = $request->address;
        $petshop->phone = $request->phone;
        $petshop->email = $request->email;
        
        if ($request->hasFile('logo')) {
            // Excluir logo antigo se existir
            if ($petshop->logo) {
                Storage::disk('public')->delete($petshop->logo);
            }
            
            $path = $request->file('logo')->store('petshops', 'public');
            $petshop->logo = $path;
        }
        
        $petshop->save();
        
        return back()->with('success', 'Informações do pet shop atualizadas com sucesso!');
    }
}