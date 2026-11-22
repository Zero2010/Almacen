<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'rut', 'email', 'phone', 'address'];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
