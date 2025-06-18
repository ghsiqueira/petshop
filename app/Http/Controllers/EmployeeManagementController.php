<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EmployeeManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:petshop');
    }
    
    public function index()
    {
        $petshop = auth()->user()->petshop;
        $employees = Employee::where('petshop_id', $petshop->id)
                        ->with('user')
                        ->paginate(10); // Mudança aqui: get() -> paginate(10)
        
        return view('petshop.employees.index', compact('employees'));
    }
    
    public function create()
    {
        return view('petshop.employees.create');
    }
    
    public function store(Request $request)
    {
        $petshop = auth()->user()->petshop;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'position' => 'required|string|max:255',
            'bio' => 'nullable|string',
        ]);
        
        // Criar o usuário
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Atribuir papel de funcionário
        $role = Role::findByName('employee');
        $user->assignRole($role);
        
        // Criar o funcionário
        $employee = new Employee();
        $employee->user_id = $user->id;
        $employee->petshop_id = $petshop->id;
        $employee->position = $request->position;
        $employee->bio = $request->bio;
        $employee->save();
        
        return redirect()->route('petshop.employees.index')
                       ->with('success', 'Funcionário adicionado com sucesso!');
    }
    
    public function show(Employee $employee)
    {
        $petshop = auth()->user()->petshop;
        
        if ($employee->petshop_id !== $petshop->id) {
            abort(403);
        }
        
        return view('petshop.employees.show', compact('employee'));
    }
    
    public function edit(Employee $employee)
    {
        $petshop = auth()->user()->petshop;
        
        if ($employee->petshop_id !== $petshop->id) {
            abort(403);
        }
        
        return view('petshop.employees.edit', compact('employee'));
    }
    
    public function update(Request $request, Employee $employee)
    {
        $petshop = auth()->user()->petshop;
        
        if ($employee->petshop_id !== $petshop->id) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'bio' => 'nullable|string',
        ]);
        
        // Atualizar o usuário
        $user = $employee->user;
        $user->name = $request->name;
        $user->save();
        
        // Atualizar o funcionário
        $employee->position = $request->position;
        $employee->bio = $request->bio;
        $employee->save();
        
        return redirect()->route('petshop.employees.index')
                       ->with('success', 'Funcionário atualizado com sucesso!');
    }
    
    public function destroy(Employee $employee)
    {
        $petshop = auth()->user()->petshop;
        
        if ($employee->petshop_id !== $petshop->id) {
            abort(403);
        }
        
        // Verificar se o funcionário tem agendamentos
        if ($employee->appointments()->exists()) {
            return back()->with('error', 'Não é possível excluir este funcionário pois ele possui agendamentos.');
        }
        
        // Excluir o funcionário (mas não o usuário)
        $employee->delete();
        
        return redirect()->route('petshop.employees.index')
                       ->with('success', 'Funcionário removido com sucesso!');
    }
}