<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Searchable;
use Carbon\Carbon;

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
        'free_delivery_minimum',
        // ============ NOVOS CAMPOS ADICIONADOS ============
        'business_hours',
        'slot_duration',
        'advance_booking_days',
        'allow_weekend_booking',
        'lunch_break_start',
        'lunch_break_end'
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
        'accepted_species' => 'array',
        // ============ NOVOS CASTS ADICIONADOS ============
        'business_hours' => 'array',
        'allow_weekend_booking' => 'boolean',
        'lunch_break_start' => 'datetime:H:i',
        'lunch_break_end' => 'datetime:H:i',
    ];

    protected $appends = [
        'logo_url',
        'full_address',
        'is_open_now'
    ];

    // ============ RELACIONAMENTOS EXISTENTES ============
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

    // ============ ACCESSORS EXISTENTES ============
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

    // ============ NOVOS MÉTODOS PARA HORÁRIOS DE FUNCIONAMENTO ============
    
    /**
     * Obter horários de funcionamento para agendamentos
     */
    public function getBusinessHours()
    {
        return $this->business_hours ?? $this->getDefaultBusinessHours();
    }

    /**
     * Horários padrão se não estiver configurado
     */
    public function getDefaultBusinessHours()
    {
        return [
            'monday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'tuesday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'wednesday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'thursday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'friday' => ['open' => '08:00', 'close' => '18:00', 'enabled' => true],
            'saturday' => ['open' => '08:00', 'close' => '16:00', 'enabled' => true],
            'sunday' => ['open' => '09:00', 'close' => '15:00', 'enabled' => false],
        ];
    }

    /**
     * Verificar se está aberto em um dia da semana
     */
    public function isOpenOnDay($dayOfWeek)
    {
        $businessHours = $this->getBusinessHours();
        $day = strtolower($dayOfWeek);
        
        return isset($businessHours[$day]) && $businessHours[$day]['enabled'];
    }

    /**
     * Obter horários de abertura/fechamento de um dia
     */
    public function getOpeningHours($dayOfWeek)
    {
        $businessHours = $this->getBusinessHours();
        $day = strtolower($dayOfWeek);
        
        if (!$this->isOpenOnDay($day)) {
            return null;
        }

        return [
            'open' => $businessHours[$day]['open'],
            'close' => $businessHours[$day]['close']
        ];
    }

    /**
     * Verificar se está no horário de almoço
     */
    public function isInLunchBreak($time)
    {
        if (!$this->lunch_break_start || !$this->lunch_break_end) {
            return false;
        }

        try {
            $lunchStart = Carbon::createFromFormat('H:i', $this->lunch_break_start);
            $lunchEnd = Carbon::createFromFormat('H:i', $this->lunch_break_end);
            $checkTime = Carbon::createFromFormat('H:i', $time);

            return $checkTime->between($lunchStart, $lunchEnd);
        } catch (\Exception $e) {
            return false;
        }
    }

    // ============ SCOPES EXISTENTES ============
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

    // ============ IMPLEMENTAÇÃO DO TRAIT SEARCHABLE ============
    protected function getSearchableFields(): array
    {
        return ['name', 'description', 'address', 'city', 'state'];
    }

    // ============ MÉTODOS AUXILIARES EXISTENTES ============
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