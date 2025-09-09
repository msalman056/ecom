<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'parent_id',
    ];

    // A category has many products
    public function products()
    {
        return $this->hasMany(Products::class, 'category_id');
    }
}
