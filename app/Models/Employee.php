<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'petshop_id',
        'employee_number',
        'position',
        'department',
        'hire_date',
        'hourly_rate',
        'working_hours',
        'specializations',
        'is_active',
        'can_book_appointments',
        'phone',
        'emergency_contact'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'hourly_rate' => 'decimal:2',
        'working_hours' => 'array',
        'specializations' => 'array',
        'is_active' => 'boolean',
        'can_book_appointments' => 'boolean'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function petshop()
    {
        return $this->belongsTo(Petshop::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'employee_services')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCanBookAppointments($query)
    {
        return $query->where('can_book_appointments', true);
    }

    // Métodos auxiliares
    public function isAvailable($datetime)
    {
        if (!$this->is_active) {
            return false;
        }

        // Verificar se tem agendamentos no horário
        $hasAppointment = $this->appointments()
            ->where('appointment_datetime', $datetime)
            ->whereNotIn('status', ['cancelled', 'no_show'])
            ->exists();

        return !$hasAppointment;
    }

    public function getAppointmentsForDate($date)
    {
        return $this->appointments()
                    ->whereDate('appointment_datetime', $date)
                    ->whereNotIn('status', ['cancelled'])
                    ->orderBy('appointment_datetime')
                    ->get();
    }
}
