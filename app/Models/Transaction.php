<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'total_price',
        'delivery_fee',
        'delivery_name',
        'delivery_phone',
        'delivery_address',
        'delivery_latitude',
        'delivery_longitude',
        'status',
        'shipping_proof',
        'shipping_proof_uploaded_at',
        'snap_token',
        'midtrans_order_id',
        'payment_type',
        'paid_at',
        'paid_status_at',
        'customer_cancel_deadline',
        'user_user_id',
        'cart_cart_id',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'delivery_fee' => 'decimal:2',
            'delivery_latitude' => 'decimal:8',
            'delivery_longitude' => 'decimal:8',
            'paid_at' => 'datetime',
            'paid_status_at' => 'datetime',
            'shipping_proof_uploaded_at' => 'datetime',
            'customer_cancel_deadline' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_user_id', 'user_id');
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_cart_id', 'cart_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Get transaction items.
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transaction_transaction_id', 'transaction_id');
    }

    
    public function ratings()
    {
        return $this->hasMany(Rating::class, 'transaction_transaction_id', 'transaction_id');
    }

    /**
     * Get grand total (items + delivery).
     */
    public function grandTotal(): int
    {
        return (int) $this->total_price + (int) $this->delivery_fee;

    }
    /**
     * Check if the transaction is paid.
     */
    public function isPaid(): bool
    {
        return in_array($this->status, ['paid', 'processing', 'shipping', 'shipped', 'delivered']);
    }

    /**
     * Check if customer can still cancel this order.
     * Only within 5 minutes of order creation AND before shipping.
     */
    public function canCustomerCancel(): bool
    {
        if (in_array($this->status, ['shipping', 'shipped', 'delivered', 'cancelled'])) {
            return false;
        }

        if ($this->customer_cancel_deadline) {
            return now()->lt($this->customer_cancel_deadline);
        }

        return false;
    }

    /**
     * Check if order can be cancelled at all (by admin).
     */
    public function canBeCancelled(): bool
    {
        return !in_array($this->status, ['shipping', 'shipped', 'delivered', 'cancelled']);
    }

    /**
     * Check if shipping proof is overdue (6 hours after payment without proof).
     */
    public function isShippingProofOverdue(): bool
    {
        if ($this->status !== 'paid') {
            return false;
        }

        if ($this->shipping_proof) {
            return false;
        }

        if (!$this->paid_status_at) {
            return false;
        }

        return now()->gt($this->paid_status_at->addHours(6));
    }

    /**
     * Check if shipping proof has been uploaded.
     */
    public function hasShippingProof(): bool
    {
        return !empty($this->shipping_proof);
    }

    /**
     * Get remaining time for shipping proof deadline.
     */
    public function shippingProofDeadline()
    {
        if ($this->paid_status_at) {
            return $this->paid_status_at->addHours(6);
        }
        return null;
    }

    /**
     * Get a human-readable status label.
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Awaiting Payment',
            'paid' => 'Paid',
            'shipping' => 'Shipping',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get status badge CSS classes.
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-700',
            'paid' => 'bg-blue-100 text-blue-700',
            'shipping' => 'bg-indigo-100 text-indigo-700',
            'delivered' => 'bg-green-100 text-green-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }


}
