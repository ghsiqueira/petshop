<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'petshop_id',
        'created_by',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relacionamentos
    public function petshop()
    {
        return $this->belongsTo(Petshop::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Métodos de validação
    public function isValid()
    {
        // Verificar se está ativo
        if (!$this->is_active) {
            return false;
        }

        // Verificar data de início
        if ($this->starts_at && Carbon::now()->lt($this->starts_at)) {
            return false;
        }

        // Verificar data de expiração
        if ($this->expires_at && Carbon::now()->gt($this->expires_at)) {
            return false;
        }

        // Verificar limite de uso total
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canBeUsedBy($userId)
    {
        if (!$this->isValid()) {
            return false;
        }

        // Verificar limite por usuário
        $userUsages = $this->usages()->where('user_id', $userId)->count();
        
        if ($userUsages >= $this->usage_limit_per_user) {
            return false;
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        // Verificar valor mínimo
        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            return 0;
        }

        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
            
            // Aplicar desconto máximo se especificado
            if ($this->maximum_discount && $discount > $this->maximum_discount) {
                $discount = $this->maximum_discount;
            }
        } else { // fixed
            $discount = $this->value;
        }

        // O desconto não pode ser maior que o valor total
        return min($discount, $amount);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                    ->where(function($q) use ($now) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', $now);
                    })
                    ->where(function($q) use ($now) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', $now);
                    })
                    ->where(function($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }

    public function scopeForPetshop($query, $petshopId)
    {
        return $query->where('petshop_id', $petshopId);
    }

    public function scopeGlobal($query)
    {
        return $query->whereNull('petshop_id');
    }

    // Métodos auxiliares
    public function getDiscountText()
    {
        if ($this->type === 'percentage') {
            return $this->value . '% OFF';
        } else {
            return 'R$ ' . number_format($this->value, 2, ',', '.') . ' OFF';
        }
    }

    public function getRemainingUsages()
    {
        if (!$this->usage_limit) {
            return null; // Ilimitado
        }

        return max(0, $this->usage_limit - $this->used_count);
    }

    public function isExpiringSoon($days = 7)
    {
        if (!$this->expires_at) {
            return false;
        }

        return Carbon::now()->addDays($days)->gte($this->expires_at);
    }
}