<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Petshop; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }
    
    public function index(Request $request)
    {
        $query = User::with('roles');
        
        // Filtragem por papel
        if ($request->has('role') && $request->role) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        // Pesquisa
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }
    
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        
        $roles = Role::whereIn('id', $request->roles)->get();
        $user->syncRoles($roles);
        
        // Verifique se o papel 'petshop' foi atribuído
        if ($user->hasRole('petshop') && !$user->petshop) {
            // Crie um petshop para o usuário
            $petshop = new Petshop();
            $petshop->user_id = $user->id;
            $petshop->name = "PetShop de " . $user->name;
            $petshop->description = "Descrição do petshop";
            $petshop->address = "Endereço a definir";
            $petshop->phone = "(00) 0000-0000";
            $petshop->email = $user->email;
            $petshop->is_active = true;
            $petshop->save();
        }
        
        return redirect()->route('admin.users.index')
                      ->with('success', 'Usuário criado com sucesso!');
    }
    
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);
        
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();
        
        // Verificar se tinha papel de petshop antes
        $hadPetshopRole = $user->hasRole('petshop');
        
        // Atualizar papéis
        $roles = Role::whereIn('id', $request->roles)->get();
        $user->syncRoles($roles);
        
        // Verificar se agora tem papel de petshop
        $hasPetshopRole = $user->hasRole('petshop');
        
        // Se recebeu o papel de petshop e não tinha antes, criar o petshop
        if ($hasPetshopRole && !$hadPetshopRole && !$user->petshop) {
            $petshop = new Petshop();
            $petshop->user_id = $user->id;
            $petshop->name = "PetShop de " . $user->name;
            $petshop->description = "Descrição do petshop";
            $petshop->address = "Endereço a definir";
            $petshop->phone = "(00) 0000-0000";
            $petshop->email = $user->email;
            $petshop->is_active = true;
            $petshop->save();
        }
        
        return redirect()->route('admin.users.index')
                      ->with('success', 'Usuário atualizado com sucesso!');
    }
    
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }
    
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }
    
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Você não pode excluir seu próprio usuário.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
                      ->with('success', 'Usuário excluído com sucesso!');
    }
}