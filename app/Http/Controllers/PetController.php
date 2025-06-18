<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:client');
    }

    public function index()
{
    $pets = Pet::where('user_id', auth()->id())->get();
    
    // Adicionar informação sobre agendamentos para cada pet
    foreach($pets as $pet) {
        $pet->hasAppointments = $pet->appointments()->exists();
    }
    
    return view('pets.index', compact('pets'));
}


    public function create()
    {
        return view('pets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|in:dog,cat,bird,reptile,rodent,other',
            'breed' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female,unknown',
            'birth_date' => 'nullable|date',
            'medical_information' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);
        
        $pet = new Pet();
        $pet->user_id = auth()->id();
        $pet->name = $request->name;
        $pet->species = $request->species;
        $pet->breed = $request->breed;
        $pet->gender = $request->gender;
        $pet->medical_information = $request->medical_information;
        
        // Tratamento da data de nascimento
        if ($request->filled('birth_date')) {
            try {
                $pet->birth_date = Carbon::parse($request->birth_date);
            } catch (\Exception $e) {
                $pet->birth_date = null;
            }
        } else {
            $pet->birth_date = null;
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pets', 'public');
            $pet->photo = $path;
        }
        
        $pet->save();
        
        return redirect()->route('pets.index')
                       ->with('success', 'Pet adicionado com sucesso!');
    }

    public function show(Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }
        
        $appointments = $pet->appointments()->with(['service', 'service.petshop', 'employee.user'])->get();
        
        return view('pets.show', compact('pet', 'appointments'));
    }

    public function edit(Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }
        
        return view('pets.edit', compact('pet'));
    }

    public function update(Request $request, Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string|in:dog,cat,bird,reptile,rodent,other',
            'breed' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female,unknown',
            'birth_date' => 'nullable|date',
            'medical_information' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
            'remove_photo' => 'nullable|boolean',
        ]);
        
        $pet->name = $request->name;
        $pet->species = $request->species;
        $pet->breed = $request->breed;
        $pet->gender = $request->gender;
        $pet->medical_information = $request->medical_information;
        
        // Tratamento da data de nascimento
        if ($request->filled('birth_date')) {
            try {
                $pet->birth_date = Carbon::parse($request->birth_date);
            } catch (\Exception $e) {
                $pet->birth_date = null;
            }
        } else {
            $pet->birth_date = null;
        }
        
        // Verificar se o usuário quer remover a foto atual
        if ($request->has('remove_photo') && $request->remove_photo) {
            // Excluir foto antiga se existir
            if ($pet->photo) {
                Storage::disk('public')->delete($pet->photo);
                $pet->photo = null; // Limpar o campo photo
            }
        }
        // Verificar se uma nova foto foi enviada (e o usuário não quer remover a atual)
        elseif ($request->hasFile('photo')) {
            // Excluir foto antiga se existir
            if ($pet->photo) {
                Storage::disk('public')->delete($pet->photo);
            }
            
            $path = $request->file('photo')->store('pets', 'public');
            $pet->photo = $path;
        }
        
        $pet->save();
        
        return redirect()->route('pets.index')
                    ->with('success', 'Pet atualizado com sucesso!');
    }

    public function destroy(Pet $pet)
    {
        if ($pet->user_id !== auth()->id()) {
            abort(403);
        }
        
        $hasAppointments = $pet->appointments()->exists();
        
        if ($hasAppointments && !request()->has('force_delete')) {
            return back()->with([
                'warning' => 'Este pet possui agendamentos associados. Se continuar, todos os agendamentos serão excluídos junto com o pet.',
                'pet_id' => $pet->id
            ])->withInput(); // Isso ajuda a manter a entrada do formulário
        }
        
        // Usar transação para garantir integridade
        DB::beginTransaction();
        
        try {
            // Excluir primeiro todos os agendamentos relacionados
            $pet->appointments()->delete();
            
            // Excluir foto se existir
            if ($pet->photo) {
                Storage::disk('public')->delete($pet->photo);
            }
            
            // Agora exclui o pet
            $pet->delete();
            
            DB::commit();
            
            return redirect()->route('pets.index')
                        ->with('success', 'Pet excluído com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', 'Erro ao excluir o pet: ' . $e->getMessage());
        }
    }
}