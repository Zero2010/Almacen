<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $fillable = [
        'user_id',
        'opened_at',
        'closed_at',
        'initial_amount',
        'final_amount',
        'actual_amount',
        'status', // open, closed
        'difference',
        'observations'
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'initial_amount' => 'integer',
        'final_amount' => 'integer',
        'actual_amount' => 'integer',
        'difference' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }
}
