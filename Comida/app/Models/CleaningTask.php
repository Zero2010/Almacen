<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CleaningTask extends Model
{
    protected $fillable = [
        'name',
        'zone',
        'frequency', // daily, weekly, monthly
        'last_completed_at',
        'completed_by_id',
        'next_due_date'
    ];

    protected $casts = [
        'last_completed_at' => 'date',
        'next_due_date' => 'date',
        'completed_by_id' => 'integer'
    ];

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by_id');
    }
}
