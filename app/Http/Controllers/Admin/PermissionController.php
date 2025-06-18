<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }
    
    public function index()
    {
        $permissions = Permission::all();
        return view('admin.permissions.index', compact('permissions'));
    }
    
    public function create()
    {
        return view('admin.permissions.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
        ]);
        
        Permission::create(['name' => $request->name]);
        
        return redirect()->route('admin.permissions.index')
                      ->with('success', 'Permissão criada com sucesso!');
    }
    
    public function show(Permission $permission)
    {
        return view('admin.permissions.show', compact('permission'));
    }
    
    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }
    
    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,'.$permission->id,
        ]);
        
        $permission->name = $request->name;
        $permission->save();
        
        return redirect()->route('admin.permissions.index')
                      ->with('success', 'Permissão atualizada com sucesso!');
    }
    
    public function destroy(Permission $permission)
    {
        if ($permission->roles()->count() > 0) {
            return back()->with('error', 'Não é possível excluir esta permissão pois ela está atribuída a papéis.');
        }
        
        $permission->delete();
        
        return redirect()->route('admin.permissions.index')
                      ->with('success', 'Permissão excluída com sucesso!');
    }
}