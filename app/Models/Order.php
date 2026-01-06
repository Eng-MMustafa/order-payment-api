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
        'shipping_address',
        'billing_address',
        'total_amount',
        'status',
        'notes'
    ];

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for the order.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Check if the order has any payments.
     */
    public function hasPayments()
    {
        return $this->payments()->count() > 0;
    }

    /**
     * Check if the order can be processed for payment.
     */
    public function canProcessPayment()
    {
        return $this->status === 'confirmed';
    }
}
