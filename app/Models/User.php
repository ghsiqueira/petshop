<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'zip_code',
        'birth_date',
        'profile_picture',
        'email_verified_at',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relacionamentos
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }

    public function petshop()
    {
        return $this->hasOne(Petshop::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function searches()
    {
        return $this->hasMany(UserSearch::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')
                    ->withTimestamps();
    }

    // Métodos de Wishlist
    public function hasInWishlist($productId)
    {
        if (!auth()->check()) {
            return false;
        }

        return $this->wishlists()
                    ->where('product_id', $productId)
                    ->exists();
    }

    public function addToWishlist($productId)
    {
        if (!$this->hasInWishlist($productId)) {
            return $this->wishlists()->create([
                'product_id' => $productId
            ]);
        }
        
        return false;
    }

    public function removeFromWishlist($productId)
    {
        return $this->wishlists()
                    ->where('product_id', $productId)
                    ->delete();
    }

    public function toggleWishlist($productId)
    {
        if ($this->hasInWishlist($productId)) {
            $this->removeFromWishlist($productId);
            return false; // Removido
        } else {
            $this->addToWishlist($productId);
            return true; // Adicionado
        }
    }

    // Métodos auxiliares
    public function getProfilePictureUrlAttribute()
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        
        return asset('img/default-user.png');
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([$this->address, $this->city, $this->state, $this->zip_code]);
        return implode(', ', $parts);
    }

    public function hasActivePetshop()
    {
        return $this->petshop && $this->petshop->is_active;
    }

    // Método para carrinho (se necessário)
    public function getCartItemsCount()
    {
        return $this->cartItems()->sum('quantity');
    }

    public function getWishlistCount()
    {
        return $this->wishlists()->count();
    }
}