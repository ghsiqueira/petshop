<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'total_amount', 'status', 'payment_method', 'shipping_address','payment_details','coupon_id','coupon_code','coupon_discount','subtotal',
    ];

    protected $casts = [
        // ... casts existentes ...
        'coupon_discount' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class);
    }
}