<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'species', 'breed', 'birth_date', 'gender', 'medical_information', 'photo'
    ];

    protected $casts = [
        'birth_date' => 'datetime',
    ];

    /**
     * Retorna a idade formatada do pet em anos e meses
     */
    public function getFormattedAgeAttribute()
    {
        if (!$this->birth_date) {
            return 'Não informada';
        }
        
        try {
            $now = Carbon::now();
            $birthDate = $this->birth_date;
            
            // Se a data de nascimento for no futuro, retorne uma mensagem apropriada
            if ($birthDate->gt($now)) {
                return 'Data inválida';
            }
            
            // Cálculo total de meses
            $totalMonths = $birthDate->diffInMonths($now);
            
            // Calcula anos e meses restantes
            $years = floor($totalMonths / 12);
            $months = $totalMonths % 12;
            
            // Formata a string de idade
            if ($years > 0) {
                $ageString = $years . ' ' . ($years == 1 ? 'ano' : 'anos');
                
                if ($months > 0) {
                    $ageString .= ' e ' . $months . ' ' . ($months == 1 ? 'mês' : 'meses');
                }
            } else {
                $ageString = $months . ' ' . ($months == 1 ? 'mês' : 'meses');
            }
            
            return $ageString;
            
        } catch (\Exception $e) {
            // Em caso de erro, retorna uma mensagem genérica
            return 'Idade indisponível';
        }
    }

    /**
     * Método para depuração do cálculo de idade
     */
    public function getDebugAgeAttribute()
    {
        if (!$this->birth_date) {
            return 'Sem data de nascimento';
        }
        
        $now = Carbon::now();
        $birthDate = $this->birth_date;
        
        return [
            'birth_date' => $birthDate->format('Y-m-d'),
            'now' => $now->format('Y-m-d'),
            'total_months' => $birthDate->diffInMonths($now),
            'years' => floor($birthDate->diffInMonths($now) / 12),
            'months' => $birthDate->diffInMonths($now) % 12,
        ];
    }

    // Relacionamentos existentes
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}