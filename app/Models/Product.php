<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    public function productVarients()
    {
        return $this->hasMany(ProductVarient::class, 'product_id');
    }

    public function productVariantPrices()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_id');
    }

}
