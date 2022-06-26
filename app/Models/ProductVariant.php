<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    public function variantObj(){ 
        return $this->belongsTo(Variant::class, 'variant_id');
    }
}
