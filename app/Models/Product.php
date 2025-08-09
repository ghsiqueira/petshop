<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;

class Product extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'petshop_id',
        'name',
        'description',
        'price',
        'cost_price',
        'quantity',
        'category',
        'brand',
        'sku',
        'image',
        'images',
        'weight',
        'dimensions',
        'minimum_stock',
        'is_active',
        'search_keywords',
        'avg_rating',
        'total_reviews',
        'featured',
        'tags',
        'discount_percentage',
        'discount_start_date',
        'discount_end_date'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'avg_rating' => 'decimal:2',
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'tags' => 'array',
        'images' => 'array',
        'dimensions' => 'array',
        'discount_percentage' => 'decimal:2',
        'discount_start_date' => 'date',
        'discount_end_date' => 'date'
    ];

    protected $appends = [
        'image_url',
        'final_price',
        'is_on_sale',
        'stock_status'
    ];

    // Relacionamentos
    public function petshop()
    {
        return $this->belongsTo(Petshop::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Accessors
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        
        return asset('img/no-product-image.jpg');
    }

    public function getFinalPriceAttribute()
    {
        if ($this->is_on_sale) {
            return $this->price - ($this->price * $this->discount_percentage / 100);
        }
        
        return $this->price;
    }

    public function getIsOnSaleAttribute()
    {
        if (!$this->discount_percentage || $this->discount_percentage <= 0) {
            return false;
        }

        $now = now();
        
        if ($this->discount_start_date && $now < $this->discount_start_date) {
            return false;
        }
        
        if ($this->discount_end_date && $now > $this->discount_end_date) {
            return false;
        }
        
        return true;
    }

    public function getStockStatusAttribute()
    {
        if ($this->quantity <= 0) {
            return 'out_of_stock';
        }
        
        if ($this->quantity <= $this->minimum_stock) {
            return 'low_stock';
        }
        
        return 'in_stock';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'minimum_stock');
    }

    public function scopeOnSale($query)
    {
        return $query->where('discount_percentage', '>', 0)
                    ->where(function ($q) {
                        $q->whereNull('discount_start_date')
                          ->orWhere('discount_start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('discount_end_date')
                          ->orWhere('discount_end_date', '>=', now());
                    });
    }

    // Implementação do Trait Searchable
    protected function getSearchableFields(): array
    {
        return ['name', 'description', 'category', 'brand', 'sku'];
    }

    // Métodos auxiliares
    public function updateRating()
    {
        $reviews = $this->reviews()->where('status', 'approved');
        $this->avg_rating = $reviews->avg('rating') ?? 0;
        $this->total_reviews = $reviews->count();
        $this->save();
    }

    public function canPurchase($quantity = 1)
    {
        return $this->is_active && $this->quantity >= $quantity;
    }

    public function decreaseStock($quantity)
    {
        if ($this->quantity >= $quantity) {
            $this->quantity -= $quantity;
            $this->save();
            return true;
        }
        
        return false;
    }

    public function increaseStock($quantity)
    {
        $this->quantity += $quantity;
        $this->save();
    }
}
