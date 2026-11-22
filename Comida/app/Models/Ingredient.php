<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'stock',
        'minimum_stock',
        'cost_per_unit',
        'supplier_id',
        'lot_number',
        'expiration_date'
    ];

    protected $casts = [
        'stock' => 'double',
        'minimum_stock' => 'double',
        'cost_per_unit' => 'integer',
        'expiration_date' => 'date'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'recipes')
                    ->withPivot('quantity_required')
                    ->withTimestamps();
    }
}
