<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'petshop_id', 'name', 'description', 'price', 'stock', 'image', 'is_active'
    ];

    public function petshop()
    {
        return $this->belongsTo(Petshop::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists');
    }

    // Método para contar quantas vezes foi favoritado
    public function wishlistCount()
    {
        return $this->wishlists()->count();
    }
}