<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewable_type',
        'reviewable_id',
        'appointment_id',
        'rating',
        'title',
        'comment',
        'status',
        'helpful_votes',
        'images'
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_votes' => 'integer',
        'images' => 'array'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewable()
    {
        return $this->morphTo();
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeHighRated($query, $minRating = 4)
    {
        return $query->where('rating', '>=', $minRating);
    }

    // Métodos auxiliares
    public function approve()
    {
        $this->status = 'approved';
        $this->save();

        // Atualizar rating do item avaliado
        if (method_exists($this->reviewable, 'updateRating')) {
            $this->reviewable->updateRating();
        }
    }

    public function reject()
    {
        $this->status = 'rejected';
        $this->save();
    }
}
