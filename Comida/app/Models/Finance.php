<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finance extends Model
{
    protected $fillable = [
        'cash_register_id',
        'type', // income, expense
        'category', // sales, purchase, salary, utilities, merger, other
        'amount',
        'date',
        'description',
        'payment_method',
        'document_path'
    ];

    protected $casts = [
        'amount' => 'integer',
        'date' => 'date'
    ];

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }
}
