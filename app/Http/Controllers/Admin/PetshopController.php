<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Petshop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class PetshopController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }
    
    public function index(Request $request)
    {
        $query = Petshop::with('user');
        
        // Filtro de busca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Filtro por status
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }
        
        // Filtro por cidade
        if ($request->filled('city')) {
            $query->where('address', 'like', '%' . $request->city . '%');
        }
        
        // Filtro por data de criação
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filtro por quantidade de produtos
        if ($request->filled('min_products')) {
            $query->withCount('products')->having('products_count', '>=', $request->min_products);
        }
        
        // Ordenação
        $sortBy = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortOrder);
                break;
            case 'updated_at':
                $query->orderBy('updated_at', $sortOrder);
                break;
            case 'products_count':
                $query->withCount('products')->orderBy('products_count', $sortOrder);
                break;
            case 'services_count':
                $query->withCount('services')->orderBy('services_count', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
        
        $petshops = $query->withCount(['products', 'services', 'employees'])
                         ->paginate(10)
                         ->withQueryString();
        
        // Dados para os filtros
        $cities = Petshop::selectRaw("TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(address, ',', -2), ',', 1)) as city")
                        ->groupBy('city')
                        ->orderBy('city')
                        ->pluck('city')
                        ->filter()
                        ->unique()
                        ->values();
        
        return view('admin.petshops.index', compact('petshops', 'cities'));
    }
    
    public function create()
    {
        return view('admin.petshops.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'petshop_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'petshop_email' => 'required|string|email|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);
        
        // Criar o usuário
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Atribuir papel de petshop
        $role = Role::findByName('petshop');
        $user->assignRole($role);
        
        // Criar o petshop
        $petshop = new Petshop();
        $petshop->user_id = $user->id;
        $petshop->name = $request->petshop_name;
        $petshop->description = $request->description;
        $petshop->address = $request->address;
        $petshop->phone = $request->phone;
        $petshop->email = $request->petshop_email;
        $petshop->opening_hours = $request->opening_hours;
        $petshop->is_active = true;
        
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('petshops', 'public');
            $petshop->logo = $path;
        }
        
        $petshop->save();
        
        return redirect()->route('admin.petshops.index')
                      ->with('success', 'Pet Shop criado com sucesso!');
    }
    
    public function show(Petshop $petshop)
    {
        $petshop->load(['user', 'products', 'services', 'employees']);
        
        // Estatísticas básicas
        $stats = [
            'total_products' => $petshop->products()->count(),
            'active_products' => $petshop->products()->where('is_active', true)->count(),
            'total_services' => $petshop->services()->count(),
            'active_services' => $petshop->services()->where('is_active', true)->count(),
            'total_employees' => $petshop->employees()->count(),
        ];
        
        return view('admin.petshops.show', compact('petshop', 'stats'));
    }
    
    public function edit(Petshop $petshop)
    {
        return view('admin.petshops.edit', compact('petshop'));
    }
    
    public function update(Request $request, Petshop $petshop)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$petshop->user->id,
            'petshop_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'petshop_email' => 'required|string|email|max:255',
            'opening_hours' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);
        
        // Atualizar o usuário
        $user = $petshop->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        
        // Atualizar o petshop
        $petshop->name = $request->petshop_name;
        $petshop->description = $request->description;
        $petshop->address = $request->address;
        $petshop->phone = $request->phone;
        $petshop->email = $request->petshop_email;
        $petshop->opening_hours = $request->opening_hours;
        $petshop->is_active = $request->has('is_active');
        
        if ($request->hasFile('logo')) {
            // Excluir logo antigo se existir
            if ($petshop->logo) {
                Storage::disk('public')->delete($petshop->logo);
            }
            
            $path = $request->file('logo')->store('petshops', 'public');
            $petshop->logo = $path;
        }
        
        $petshop->save();
        
        return redirect()->route('admin.petshops.index')
                      ->with('success', 'Pet Shop atualizado com sucesso!');
    }
    
    public function destroy(Petshop $petshop)
    {
        // Verificar se o petshop tem produtos, serviços ou funcionários
        if ($petshop->products()->count() > 0 || $petshop->services()->count() > 0 || $petshop->employees()->count() > 0) {
            return back()->with('error', 'Não é possível excluir este Pet Shop pois ele possui produtos, serviços ou funcionários.');
        }
        
        // Excluir logo se existir
        if ($petshop->logo) {
            Storage::disk('public')->delete($petshop->logo);
        }
        
        // Excluir o petshop (mas não o usuário)
        $petshop->delete();
        
        return redirect()->route('admin.petshops.index')
                      ->with('success', 'Pet Shop excluído com sucesso!');
    }
    
    /**
     * Alternar status ativo/inativo do petshop
     */
    public function toggleStatus(Petshop $petshop)
    {
        $petshop->is_active = !$petshop->is_active;
        $petshop->save();
        
        $status = $petshop->is_active ? 'ativado' : 'desativado';
        
        return back()->with('success', "Pet Shop {$status} com sucesso!");
    }
    
    /**
     * Exportar dados dos petshops
     */
    public function export(Request $request)
    {
        $query = Petshop::with('user');
        
        // Aplicar os mesmos filtros da listagem
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('address', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }
        
        if ($request->filled('filter')) {
            switch ($request->filter) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }
        
        $petshops = $query->withCount(['products', 'services', 'employees'])->get();
        
        $filename = 'petshops_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($petshops) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho do CSV
            fputcsv($file, [
                'ID',
                'Nome do Pet Shop',
                'Proprietário',
                'Email do Proprietário',
                'Email do Pet Shop',
                'Telefone',
                'Endereço',
                'Horário de Funcionamento',
                'Status',
                'Total de Produtos',
                'Total de Serviços',
                'Total de Funcionários',
                'Data de Criação',
                'Última Atualização'
            ]);
            
            // Dados
            foreach ($petshops as $petshop) {
                fputcsv($file, [
                    $petshop->id,
                    $petshop->name,
                    $petshop->user->name,
                    $petshop->user->email,
                    $petshop->email,
                    $petshop->phone,
                    $petshop->address,
                    $petshop->opening_hours,
                    $petshop->is_active ? 'Ativo' : 'Inativo',
                    $petshop->products_count,
                    $petshop->services_count,
                    $petshop->employees_count,
                    $petshop->created_at->format('d/m/Y H:i'),
                    $petshop->updated_at->format('d/m/Y H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}