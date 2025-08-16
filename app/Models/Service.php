<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;

class Service extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'petshop_id',
        'name',
        'description',
        'price',
        'category',
        'duration_minutes',
        'image',
        'images',
        'requirements',
        'is_active',
        'search_keywords',
        'avg_rating',
        'total_reviews',
        'featured',
        'tags',
        'max_pets_per_session',
        'requires_appointment',
        'advance_booking_days',
        'cancellation_hours',
        // CAMPOS JÁ EXISTENTES NA MIGRAÇÃO
        'available_hours',
        'use_petshop_hours',
        'available_days',
        'buffer_time'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'avg_rating' => 'decimal:2',
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'requires_appointment' => 'boolean',
        'tags' => 'array',
        'images' => 'array',
        'requirements' => 'array',
        // CASTS PARA CAMPOS JÁ EXISTENTES
        'available_hours' => 'array',
        'use_petshop_hours' => 'boolean',
        'available_days' => 'array'
    ];

    protected $appends = [
        'image_url',
        'duration_formatted'
    ];

    // Relacionamentos
    public function petshop()
    {
        return $this->belongsTo(Petshop::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_services')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return asset('img/no-service-image.jpg');
    }

    public function getDurationFormattedAttribute()
    {
        if (!$this->duration_minutes) {
            return 'Duração não especificada';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0 && $minutes > 0) {
            return "{$hours}h {$minutes}min";
        } elseif ($hours > 0) {
            return "{$hours}h";
        } else {
            return "{$minutes}min";
        }
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequiresAppointment($query)
    {
        return $query->where('requires_appointment', true);
    }

    // Implementação do Trait Searchable
    protected function getSearchableFields(): array
    {
        return ['name', 'description', 'category'];
    }

    // ============ NOVOS MÉTODOS PARA DISPONIBILIDADE ============

    /**
     * Verificar se o serviço está disponível em um dia da semana
     */
    public function isAvailableOnDay($dayOfWeek)
    {
        // Se usa horário do petshop, verificar disponibilidade no petshop
        if ($this->use_petshop_hours || !$this->available_days) {
            return $this->petshop->isOpenOnDay($dayOfWeek);
        }

        // Se tem dias específicos configurados
        $day = strtolower($dayOfWeek);
        return in_array($day, $this->available_days ?? []);
    }

    /**
     * Obter horários específicos do serviço para um dia
     */
    public function getServiceHours($dayOfWeek)
    {
        // Se usa horário do petshop
        if ($this->use_petshop_hours || !$this->available_hours) {
            return $this->petshop->getOpeningHours($dayOfWeek);
        }

        // Se tem horários customizados
        $day = strtolower($dayOfWeek);
        if (!$this->isAvailableOnDay($day)) {
            return null;
        }

        return $this->available_hours[$day] ?? null;
    }

    /**
     * Configurar disponibilidade padrão (usa horários do petshop)
     */
    public function setDefaultAvailability()
    {
        $this->update([
            'use_petshop_hours' => true,
            'available_hours' => null,
            'available_days' => null,
            'buffer_time' => 0
        ]);
    }

    /**
     * Configurar horários customizados para o serviço
     */
    public function setCustomAvailability($hours, $days = null)
    {
        $this->update([
            'use_petshop_hours' => false,
            'available_hours' => $hours,
            'available_days' => $days,
        ]);
    }

    // Métodos auxiliares existentes
    public function updateRating()
    {
        $reviews = $this->reviews()->where('status', 'approved');
        $this->avg_rating = $reviews->avg('rating') ?? 0;
        $this->total_reviews = $reviews->count();
        $this->save();
    }

    public function isAvailableForBooking($date = null)
    {
        if (!$this->is_active || !$this->requires_appointment) {
            return false;
        }

        if ($date && $this->advance_booking_days) {
            $minDate = now()->addDays($this->advance_booking_days);
            return $date >= $minDate;
        }

        return true;
    }

    public function getAvailableSlots($date, $employeeId = null)
    {
        // Implementar lógica de horários disponíveis
        $appointments = $this->appointments()
            ->whereDate('appointment_datetime', $date)
            ->when($employeeId, function ($query) use ($employeeId) {
                return $query->where('employee_id', $employeeId);
            })
            ->get();

        // Retornar slots disponíveis baseado na duração do serviço
        // Esta é uma implementação básica - pode ser expandida
        return collect();
    }
}