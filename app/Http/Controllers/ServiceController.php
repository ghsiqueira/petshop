<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
        $this->middleware('role:petshop')->except(['index', 'show']);
    }
    
    public function index()
    {
        // Verificar se é uma solicitação de petshop
        if (request()->is('petshop/services*')) {
            $petshop = auth()->user()->petshop;
            $services = Service::where('petshop_id', $petshop->id)
                             ->when(request('search'), function($query) {
                                 return $query->where('name', 'like', '%' . request('search') . '%');
                             })
                             ->when(request('filter') == 'active', function($query) {
                                 return $query->where('is_active', true);
                             })
                             ->when(request('filter') == 'inactive', function($query) {
                                 return $query->where('is_active', false);
                             })
                             ->orderBy('created_at', 'desc')
                             ->paginate(10);
                             
            return view('petshop.services.index', compact('services'));
        }
        
        // Visualização pública
        $services = Service::with('petshop')
                         ->where('is_active', true)
                         ->paginate(12);
        
        return view('services.index', compact('services'));
    }
    
    public function show(Service $service) {
        // 1. Verificar se o usuário é dono do pet shop
        $isOwner = auth()->check() && 
                   auth()->user()->hasRole('petshop') && 
                   auth()->user()->petshop->id == $service->petshop_id;
        
        // 2. Se for uma solicitação administrativa, use view administrativa
        if (request()->is('petshop/services*')) {
            // Verificar permissão
            if (!$isOwner) {
                abort(403);
            }
            return view('petshop.services.show', compact('service'));
        }
        
        // 3. Para solicitação pública, verificar atividade
        if (!$service->is_active) {
            abort(404);
        }
        
        return view('services.show', compact('service', 'isOwner'));
    }
    
    public function create()
    {
        return view('petshop.services.create');
    }
    
    public function store(Request $request)
    {
        $petshop = auth()->user()->petshop;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);
        
        $service = new Service();
        $service->petshop_id = $petshop->id;
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->duration_minutes = $request->duration_minutes;
        $service->is_active = true;
        $service->save();
        
        return redirect()->route('petshop.services.index')
                       ->with('success', 'Serviço criado com sucesso!');
    }
    
    public function edit(Service $service)
    {
        $petshop = auth()->user()->petshop;
        
        if ($service->petshop_id !== $petshop->id) {
            abort(403);
        }
        
        return view('petshop.services.edit', compact('service'));
    }
    
    public function update(Request $request, Service $service)
    {
        $petshop = auth()->user()->petshop;
        
        if ($service->petshop_id !== $petshop->id) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
        ]);
        
        $service->name = $request->name;
        $service->description = $request->description;
        $service->price = $request->price;
        $service->duration_minutes = $request->duration_minutes;
        $service->is_active = $request->has('is_active');
        $service->save();
        
        return redirect()->route('petshop.services.index')
                      ->with('success', 'Serviço atualizado com sucesso!');
    }
    
    public function destroy(Service $service)
    {
        $petshop = auth()->user()->petshop;
        
        if ($service->petshop_id !== $petshop->id) {
            abort(403);
        }
        
        // Verificar se o serviço tem agendamentos
        if ($service->appointments()->exists()) {
            return back()->with('error', 'Não é possível excluir este serviço pois ele possui agendamentos.');
        }
        
        $service->delete();
        
        return redirect()->route('petshop.services.index')
                      ->with('success', 'Serviço excluído com sucesso!');
    }

    public function toggleStatus(Service $service)
    {
        $petshop = auth()->user()->petshop;
        
        if ($service->petshop_id !== $petshop->id) {
            abort(403);
        }
        
        $service->is_active = !$service->is_active;
        $service->save();
        
        return back()->with('success', 'Status do serviço atualizado com sucesso!');
    }
}