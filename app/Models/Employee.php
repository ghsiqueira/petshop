<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'petshop_id', 'position', 'bio'
    ];

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
}