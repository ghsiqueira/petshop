<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
        'service_id',
        'employee_id',
        'appointment_datetime',
        'duration_minutes',
        'status',
        'notes',
        'special_instructions',
        'price',
        'cancelled_at',
        'cancellation_reason',
        'completed_at',
        'no_show'
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
        'price' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'no_show' => 'boolean'
    ];

    protected $appends = [
        'status_label',
        'can_be_cancelled'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'scheduled' => 'Agendado',
            'confirmed' => 'Confirmado',
            'in_progress' => 'Em Andamento',
            'completed' => 'Concluído',
            'cancelled' => 'Cancelado',
            'no_show' => 'Não Compareceu'
        ];

        return $labels[$this->status] ?? 'Desconhecido';
    }

    public function getCanBeCancelledAttribute()
    {
        if (in_array($this->status, ['cancelled', 'completed', 'no_show'])) {
            return false;
        }

        // Não pode cancelar se faltam menos de 2 horas
        $minimumCancellationTime = now()->addHours(2);
        return $this->appointment_datetime > $minimumCancellationTime;
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('appointment_datetime', '>', now())
                    ->whereNotIn('status', ['cancelled', 'no_show']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('appointment_datetime', today());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'scheduled');
    }

    // Métodos auxiliares
    public function cancel($reason = null)
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->save();
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    public function markAsNoShow()
    {
        $this->status = 'no_show';
        $this->no_show = true;
        $this->save();
    }
}
