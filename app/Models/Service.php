<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'petshop_id', 'name', 'description', 'price', 'duration_minutes', 'is_active'
    ];

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
    
    public function getDurationAttribute($value)
    {
        return $value ?: 'Não especificada';
    }
}