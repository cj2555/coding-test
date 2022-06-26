<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class VariantRepository
{
   
    public static function get_variants_with_group(){

        $variants=  DB::table('variants')
        ->join('product_variants', 'variants.id', '=', 'product_variants.variant_id')
        ->select('variants.*', 'product_variants.variant')
        ->distinct()
        ->get();

        $variant_option=[];
        
        foreach ($variants as $variant) {
            $variant_option[$variant->title][] = $variant->variant;
        }

        return $variant_option;

    }   
 

}


