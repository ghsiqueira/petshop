<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;

class Petshop extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'address',
        'city',
        'state',
        'zip_code',
        'phone',
        'email',
        'website',
        'logo',
        'images',
        'opening_hours',
        'rating',
        'total_reviews',
        'is_active',
        'search_keywords',
        'amenities',
        'accepted_species',
        'featured',
        'latitude',
        'longitude',
        'delivery_radius',
        'minimum_order_value',
        'delivery_fee',
        'free_delivery_minimum'
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'delivery_radius' => 'decimal:2',
        'minimum_order_value' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'free_delivery_minimum' => 'decimal:2',
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'opening_hours' => 'array',
        'images' => 'array',
        'amenities' => 'array',
        'accepted_species' => 'array'
    ];

    protected $appends = [
        'logo_url',
        'full_address',
        'is_open_now'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function appointments()
    {
        return $this->hasManyThrough(Appointment::class, Service::class);
    }

    public function orders()
    {
        return $this->hasManyThrough(Order::class, Product::class, 'petshop_id', 'id', 'id', 'id')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->where('order_items.product_id', 'products.id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    // Accessors
    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        
        return asset('img/default-petshop-logo.png');
    }

    public function getFullAddressAttribute()
    {
        $parts = array_filter([$this->address, $this->city, $this->state, $this->zip_code]);
        return implode(', ', $parts);
    }

    public function getIsOpenNowAttribute()
    {
        if (!$this->opening_hours) {
            return true; // Se não tem horário definido, considera sempre aberto
        }

        $now = now();
        $dayOfWeek = strtolower($now->format('l')); // monday, tuesday, etc.
        
        if (!isset($this->opening_hours[$dayOfWeek])) {
            return false;
        }

        $todayHours = $this->opening_hours[$dayOfWeek];
        
        if ($todayHours === 'closed' || !isset($todayHours['open']) || !isset($todayHours['close'])) {
            return false;
        }

        $currentTime = $now->format('H:i');
        return $currentTime >= $todayHours['open'] && $currentTime <= $todayHours['close'];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeNearby($query, $latitude, $longitude, $radius = 50)
    {
        return $query->whereRaw(
            '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) < ?',
            [$latitude, $longitude, $latitude, $radius]
        );
    }

    public function scopeWithDelivery($query)
    {
        return $query->whereNotNull('delivery_radius')
                    ->where('delivery_radius', '>', 0);
    }

    // Implementação do Trait Searchable
    protected function getSearchableFields(): array
    {
        return ['name', 'description', 'address', 'city', 'state'];
    }

    // Métodos auxiliares
    public function updateRating()
    {
        $reviews = $this->reviews()->where('status', 'approved');
        $this->rating = $reviews->avg('rating') ?? 0;
        $this->total_reviews = $reviews->count();
        $this->save();
    }

    public function isDeliveryAvailable($customerLatitude = null, $customerLongitude = null)
    {
        if (!$this->delivery_radius || $this->delivery_radius <= 0) {
            return false;
        }

        if (!$customerLatitude || !$customerLongitude || !$this->latitude || !$this->longitude) {
            return true; // Se não temos coordenadas, assume que delivery está disponível
        }

        $distance = $this->calculateDistance(
            $this->latitude, 
            $this->longitude, 
            $customerLatitude, 
            $customerLongitude
        );

        return $distance <= $this->delivery_radius;
    }

    public function calculateDeliveryFee($orderValue = 0)
    {
        if ($this->free_delivery_minimum && $orderValue >= $this->free_delivery_minimum) {
            return 0;
        }

        return $this->delivery_fee ?? 0;
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function hasStock($productId, $quantity = 1)
    {
        $product = $this->products()->find($productId);
        return $product && $product->canPurchase($quantity);
    }

    public function getActiveProductsCount()
    {
        return $this->products()->active()->count();
    }

    public function getActiveServicesCount()
    {
        return $this->services()->active()->count();
    }
}