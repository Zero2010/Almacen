<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'tracking_code',
        'current_stage',
        'client_name',
        'client_phone',
        'client_email',
        'type', // delivery, pickup, in_person
        'table_number',
        'address',
        'region_id',
        'commune_id',
        'status', // pending, preparing, ready, delivering, completed, cancelled
        'subtotal',
        'delivery_fee',
        'discount',
        'total',
        'payment_method', // cash, transfer, transbank
        'invoice_type', // none, boleta, factura
        'document_number', // SII folio
        'notes',
        'estimated_delivery_time',
        'table_id',
        'waiter_id',
        'waiter_name',
        'party_size',
        'opened_at',
        'closed_at'
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'delivery_fee' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
        'estimated_delivery_time' => 'datetime',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function waiter()
    {
        return $this->belongsTo(Waiter::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Status helpers
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPreparing(): bool
    {
        return $this->status === 'preparing';
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function isDelivering(): bool
    {
        return $this->status === 'delivering';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
