<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'species',
        'breed',
        'gender',
        'birth_date',
        'weight',
        'color',
        'microchip_number',
        'photo',
        'photos',
        'medical_notes',
        'allergies',
        'medications',
        'emergency_contact',
        'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'photos' => 'array',
        'allergies' => 'array',
        'medications' => 'array'
    ];

    protected $appends = [
        'photo_url',
        'age',
        'age_formatted'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    // Accessors
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        
        return asset('img/no-pet-image.jpg');
    }

    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }

        return $this->birth_date->diffInMonths(now());
    }

    public function getAgeFormattedAttribute()
    {
        $ageInMonths = $this->age;
        
        if ($ageInMonths === null) {
            return 'Idade não informada';
        }

        if ($ageInMonths < 12) {
            return $ageInMonths . ' ' . ($ageInMonths == 1 ? 'mês' : 'meses');
        }

        $years = floor($ageInMonths / 12);
        $months = $ageInMonths % 12;

        if ($months == 0) {
            return $years . ' ' . ($years == 1 ? 'ano' : 'anos');
        }

        return $years . ' ' . ($years == 1 ? 'ano' : 'anos') . ' e ' . 
               $months . ' ' . ($months == 1 ? 'mês' : 'meses');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySpecies($query, $species)
    {
        return $query->where('species', $species);
    }

    public function scopeByBreed($query, $breed)
    {
        return $query->where('breed', $breed);
    }

    // Métodos auxiliares
    public function canReceiveService($serviceId)
    {
        // Implementar lógica de validação se o pet pode receber determinado serviço
        // Ex: verificar espécie aceita, alergias, etc.
        return $this->is_active;
    }

    public function getLastAppointment()
    {
        return $this->appointments()
                    ->where('status', 'completed')
                    ->orderBy('appointment_datetime', 'desc')
                    ->first();
    }

    public function getUpcomingAppointments()
    {
        return $this->appointments()
                    ->where('appointment_datetime', '>', now())
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('appointment_datetime')
                    ->get();
    }
}
