<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $fillable = [
        'number',
        'capacity',
        'zone', // interior, terraza, privado
        'status', // free, occupied, reserved, awaiting_payment
        'waiter_name'
    ];

    protected $casts = [
        'number' => 'integer',
        'capacity' => 'integer'
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
