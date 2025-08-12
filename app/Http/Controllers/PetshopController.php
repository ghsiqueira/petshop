<?php

namespace App\Http\Controllers;

use App\Models\Petshop;
use App\Models\Product;
use App\Models\Service;
use App\Models\Appointment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class PetshopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('role:petshop')->except(['index', 'show']);
    }
    
    public function index(Request $request)
    {
        $query = Petshop::where('is_active', true);
        
        // Filtro de busca por nome
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%');
            });
        }
        
        // Filtro por cidade/região (extraído do endereço)
        if ($request->filled('city')) {
            $query->where('address', 'like', '%' . $request->city . '%');
        }
        
        // Filtro por tipo de serviço oferecido
        if ($request->filled('service_type')) {
            $query->whereHas('services', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->service_type . '%')
                  ->where('is_active', true);
            });
        }
        
        // Filtro por categoria de produtos
        if ($request->filled('product_category')) {
            $query->whereHas('products', function($q) use ($request) {
                $q->where('category', $request->product_category)
                  ->where('is_active', true);
            });
        }
        
        // Ordenação
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rating':
                // Se você tiver sistema de avaliações, pode implementar aqui
                $query->orderBy('name', $sortOrder);
                break;
            default:
                $query->orderBy('name', 'asc');
        }
        
        $petshops = $query->withCount(['products' => function($q) {
                            $q->where('is_active', true);
                        }, 'services' => function($q) {
                            $q->where('is_active', true);
                        }])
                        ->paginate(12)
                        ->withQueryString();
        
        // Dados para os filtros
        $cities = Petshop::where('is_active', true)
                        ->selectRaw("TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, ',', -2), ',', 1)) as city")
                        ->groupBy('city')
                        ->orderBy('city')
                        ->pluck('city')
                        ->filter()
                        ->unique()
                        ->values();
        
        $serviceTypes = Service::where('is_active', true)
                              ->distinct()
                              ->orderBy('name')
                              ->pluck('name');
        
        $productCategories = [
            'food' => 'Alimentação',
            'toys' => 'Brinquedos',
            'accessories' => 'Acessórios',
            'health' => 'Saúde',
            'hygiene' => 'Higiene'
        ];
        
        return view('petshops.index', compact('petshops', 'cities', 'serviceTypes', 'productCategories'));
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
                          ->with('service', 'pet', 'user')
                          ->orderBy('appointment_datetime', 'desc')
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
            'email' => 'required|string|email|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);
        
        // Atualizar campos básicos
        $petshop->name = $request->name;
        $petshop->description = $request->description;
        $petshop->address = $request->address;
        $petshop->phone = $request->phone;
        $petshop->email = $request->email;
        
        // Verificar se a coluna opening_hours existe antes de tentar atualizar
        if (Schema::hasColumn('petshops', 'opening_hours')) {
            $petshop->opening_hours = $request->opening_hours;
        }
        
        // Tratamento do upload da logo
        if ($request->hasFile('logo')) {
            try {
                // Excluir logo antigo se existir
                if ($petshop->logo && Storage::disk('public')->exists($petshop->logo)) {
                    Storage::disk('public')->delete($petshop->logo);
                }
                
                // Fazer upload da nova logo
                $path = $request->file('logo')->store('petshops', 'public');
                $petshop->logo = $path;
                
            } catch (\Exception $e) {
                return redirect()->back()
                            ->with('error', 'Erro ao fazer upload da logo: ' . $e->getMessage())
                            ->withInput();
            }
        }
        
        try {
            $petshop->save();
            
            return redirect()->route('petshop.dashboard')
                            ->with('success', 'Informações atualizadas com sucesso!');
                            
        } catch (\Exception $e) {
            return redirect()->back()
                        ->with('error', 'Erro ao salvar as informações: ' . $e->getMessage())
                        ->withInput();
        }
    }
}