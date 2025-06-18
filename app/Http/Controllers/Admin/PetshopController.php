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
    
    public function index()
    {
        $petshops = Petshop::with('user')->paginate(10);
        return view('admin.petshops.index', compact('petshops'));
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
        return view('admin.petshops.show', compact('petshop'));
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
}