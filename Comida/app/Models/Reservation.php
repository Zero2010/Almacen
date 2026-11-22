<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'table_id',
        'customer_name',
        'customer_phone',
        'party_size',
        'reserved_at',
        'status', // pending, confirmed, cancelled, completed
        'notes'
    ];

    protected $casts = [
        'table_id' => 'integer',
        'party_size' => 'integer',
        'reserved_at' => 'datetime'
    ];

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
