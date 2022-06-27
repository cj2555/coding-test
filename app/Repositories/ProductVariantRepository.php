<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductVariant;

class ProductVariantRepository
{
   
    public static function add_product_variant(Product $product, $variant_data){
        
        foreach ($variant_data as $key1 => $value1) {
            $variant=$value1['option'];
            $variant_tags=$value1['tags'];
            foreach ($variant_tags as $key2 => $value2) {
                $insert_product_arr[]=[
                    'product_id'=>$product->id,
                    'variant_id'=>$variant,
                    'variant'=>strtolower($value2),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s')
                ];
               
            }
        }

        ProductVariant::insert($insert_product_arr);

    }


    public static function get_product_variants(Product $product){
        $product_variants=ProductVariant::
        where('product_id', $product->id)->get();

        $product_variant_option=[];
        foreach ($product_variants as $key => $value) {
            $product_variant_option[$value->variant_id]['option']=$value->variant_id;
            $product_variant_option[$value->variant_id]['tags'][]=$value->variant;
        }
        return array_values($product_variant_option);
    }

    public static function remove_all_product_variants(Product $product){
        ProductVariant::where('product_id', $product->id)->delete();
    }



}
