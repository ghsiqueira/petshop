<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'subtotal',
        'tax_amount',
        'delivery_fee',
        'discount_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'delivery_address',
        'delivery_city',
        'delivery_state',
        'delivery_zip_code',
        'delivery_notes',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime'
    ];

    protected $appends = [
        'full_delivery_address',
        'status_label'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Accessors
    public function getFullDeliveryAddressAttribute()
    {
        $parts = array_filter([
            $this->delivery_address,
            $this->delivery_city,
            $this->delivery_state,
            $this->delivery_zip_code
        ]);
        return implode(', ', $parts);
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Pendente',
            'processing' => 'Processando',
            'shipped' => 'Enviado',
            'delivered' => 'Entregue',
            'cancelled' => 'Cancelado'
        ];

        return $labels[$this->status] ?? 'Desconhecido';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Métodos auxiliares
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function cancel($reason = null)
    {
        $this->status = 'cancelled';
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        $this->save();

        // Repor estoque dos produtos
        foreach ($this->items as $item) {
            if ($item->product) {
                $item->product->increaseStock($item->quantity);
            }
        }
    }

    public function markAsDelivered()
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }
}
