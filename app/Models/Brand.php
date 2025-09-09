<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'slug', 'image'];

    // A brand has many products
    public function products()
    {
        return $this->hasMany(Products::class, 'brand_id');
    }
}
