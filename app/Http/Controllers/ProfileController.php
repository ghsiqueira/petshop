<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar página de perfil do usuário
     */
    public function show()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'profileStats' => $this->getProfileStats($user),
        ];

        // Dados específicos por role
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('client')) {
                $data['pets'] = method_exists($user, 'pets') ? $user->pets()->get() : collect();
                $data['orders'] = method_exists($user, 'orders') ? $user->orders()->latest()->take(5)->get() : collect();
                $data['appointments'] = method_exists($user, 'appointments') ? $user->appointments()->latest()->take(5)->get() : collect();
            } elseif ($user->hasRole('petshop')) {
                $data['petshop'] = method_exists($user, 'petshop') ? $user->petshop : null;
            } elseif ($user->hasRole('employee')) {
                $data['employee'] = method_exists($user, 'employee') ? $user->employee : null;
            } elseif ($user->hasRole('admin')) {
                $data['systemStats'] = $this->getSystemStats();
                $data['recentUsers'] = User::latest()->take(10)->get();
            }
        }

        return view('profile.show', $data);
    }

    /**
     * Mostrar formulário de edição
     */
    public function edit()
    {
        $user = Auth::user();
        
        $data = ['user' => $user];

        // Adicionar dados específicos por role
        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('petshop')) {
                $data['petshop'] = method_exists($user, 'petshop') ? $user->petshop : null;
            } elseif ($user->hasRole('employee')) {
                $data['employee'] = method_exists($user, 'employee') ? $user->employee : null;
            }
        }

        return view('profile.edit', $data);
    }

    /**
     * Atualizar perfil do usuário
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validação básica
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|max:5120', // 5MB = 5120KB
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        DB::beginTransaction();
        
        try {
            // Verificar a senha atual se fornecida
            if ($request->filled('current_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'A senha atual está incorreta.']);
                }
            }
            
            // Atualizar dados básicos
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->address = $request->address;
            
            // Atualizar senha se fornecida
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            // Upload de imagem de perfil
            if ($request->hasFile('profile_picture')) {
                // Excluir imagem antiga se existir
                if ($user->profile_picture) {
                    Storage::disk('public')->delete($user->profile_picture);
                }
                
                $path = $request->file('profile_picture')->store('profile_pictures', 'public');
                $user->profile_picture = $path;
            }
            
            $user->save();
            
            DB::commit();

            return redirect()->route('profile.show')
                ->with('success', 'Perfil atualizado com sucesso!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erro ao atualizar perfil: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }

    /**
     * Upload de foto de perfil via AJAX
     */
    public function uploadPhoto(Request $request)
    {
        // Validação
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120' // 5MB = 5120KB
        ]);

        $user = Auth::user();
        
        try {
            $file = $request->file('photo');
            
            // Verificar se o arquivo foi enviado corretamente
            if (!$file || !$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Arquivo inválido ou não foi enviado corretamente.'
                ], 400);
            }
            
            // Verificar se as pastas existem
            $publicPath = storage_path('app/public');
            $profilePath = storage_path('app/public/profile-photos');
            
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }
            
            if (!file_exists($profilePath)) {
                mkdir($profilePath, 0755, true);
            }
            
            // Gerar nome único para o arquivo
            $fileName = $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Salvar arquivo
            $storedPath = $file->storeAs('profile-photos', $fileName, 'public');
            
            // Verificar se o arquivo foi salvo
            if (!$storedPath || !Storage::disk('public')->exists($storedPath)) {
                throw new \Exception('Falha ao salvar o arquivo no storage.');
            }

            // Backup da foto anterior
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            // Atualizar usuário
            $user->update(['profile_picture' => $storedPath]);

            return response()->json([
                'success' => true,
                'message' => 'Foto atualizada com sucesso!',
                'photo_url' => Storage::url($storedPath)
            ]);

        } catch (\Exception $e) {
            Log::error('Erro no upload da foto: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload da foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deletar foto de perfil
     */
    public function deletePhoto()
    {
        $user = Auth::user();
        
        if (!$user->profile_picture) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma foto para deletar.'
            ]);
        }

        try {
            // Deletar arquivo
            Storage::disk('public')->delete($user->profile_picture);
            
            // Atualizar usuário
            $user->update(['profile_picture' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Foto removida com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao deletar foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Exportar dados do perfil
     */
    public function export($format = 'json')
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user->toArray(),
            'profile_stats' => $this->getProfileStats($user),
            'exported_at' => now()->toISOString(),
        ];

        switch ($format) {
            case 'json':
                return response()->json($data);
                
            case 'csv':
                return $this->exportToCsv($data);
                
            default:
                return response()->json(['error' => 'Formato não suportado'], 400);
        }
    }

    /**
     * Métodos auxiliares privados
     */
    private function getProfileStats($user)
    {
        $stats = [
            'profile_completion' => $this->calculateProfileCompletion($user),
            'member_since' => $user->created_at->diffForHumans(),
            'last_login' => $user->updated_at ? $user->updated_at->diffForHumans() : 'Nunca',
        ];

        if (method_exists($user, 'hasRole')) {
            if ($user->hasRole('client')) {
                $stats['total_pets'] = method_exists($user, 'pets') ? $user->pets()->count() : 0;
                $stats['total_orders'] = method_exists($user, 'orders') ? $user->orders()->count() : 0;
                $stats['total_appointments'] = method_exists($user, 'appointments') ? $user->appointments()->count() : 0;
            } elseif ($user->hasRole('petshop') && method_exists($user, 'petshop')) {
                $petshop = $user->petshop;
                if ($petshop) {
                    $stats['total_products'] = method_exists($petshop, 'products') ? $petshop->products()->count() : 0;
                    $stats['total_services'] = method_exists($petshop, 'services') ? $petshop->services()->count() : 0;
                    $stats['total_employees'] = method_exists($petshop, 'employees') ? $petshop->employees()->count() : 0;
                    $stats['average_rating'] = $petshop->average_rating ?? 0;
                }
            }
        }

        return $stats;
    }

    private function calculateProfileCompletion($user)
    {
        $fields = ['name', 'email', 'phone', 'profile_picture'];
        $completed = 0;
        
        foreach ($fields as $field) {
            if (!empty($user->$field)) {
                $completed++;
            }
        }
        
        return round(($completed / count($fields)) * 100);
    }

    private function getSystemStats()
    {
        return [
            'total_users' => User::count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
        ];
    }

    private function exportToCsv($data)
    {
        $filename = 'profile_' . auth()->id() . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho
            fputcsv($file, ['Campo', 'Valor']);
            
            // Dados do usuário
            foreach ($data['user'] as $key => $value) {
                if (!is_array($value) && !is_object($value)) {
                    fputcsv($file, [$key, $value]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}