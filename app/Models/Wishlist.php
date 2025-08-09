<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Métodos auxiliares
    public static function toggle($userId, $productId)
    {
        $wishlistItem = static::where('user_id', $userId)
                             ->where('product_id', $productId)
                             ->first();

        if ($wishlistItem) {
            $wishlistItem->delete();
            return false; // Removido
        } else {
            static::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
            return true; // Adicionado
        }
    }

    public static function isInWishlist($userId, $productId)
    {
        return static::where('user_id', $userId)
                    ->where('product_id', $productId)
                    ->exists();
    }
}