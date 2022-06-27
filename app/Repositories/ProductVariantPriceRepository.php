<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;

class ProductVariantPriceRepository
{
   
    public static function add_product_variant_price(Product $product, $variant_price_data){
        
        
        foreach ($variant_price_data as $key => $value) {
           $title=$value['title'];
           $price=$value['price'];
           $stock=$value['stock'];
           $title_arr=array_filter(explode('/',$title));

              $product_variant_price[$key]=[
                'product_id'=>$product->id,
                'price'=>$price,
                'stock'=>$stock,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
              ];

              $colmun_name=[
                0=>'product_variant_one',
                1=>'product_variant_two',
                2=>'product_variant_three',
              ];
              
              for($i=0;$i<=2;$i++){

                if(!isset($title_arr[$i])){
                    continue;
                }
                $product_variant_id=ProductVariant::where([
                  'product_id'=>$product->id,
                  'variant'=>$title_arr[$i]
                ])->first()->id;
                $product_variant_price[$key][$colmun_name[$i]]=$product_variant_id;
              }

        }
        // return array_values($product_variant_price);
        ProductVariantPrice::insert(array_values($product_variant_price));    

    }

    public static function get_product_variant_prices(Product $product){

      return  ProductVariantPrice::
          leftJoin('product_variants', 'product_variant_prices.product_variant_one', '=', 'product_variants.id')
        ->leftJoin('product_variants as product_variant_two', 'product_variant_prices.product_variant_two', '=', 'product_variant_two.id')
        ->leftJoin('product_variants as product_variant_three', 'product_variant_prices.product_variant_three', '=', 'product_variant_three.id')
        ->where('product_variant_prices.product_id', $product->id)
        ->selectRaw('
            product_variant_prices.price as price,
            product_variant_prices.stock as stock,
            concat( ifnull(product_variants.variant,""), "/",  ifnull(product_variant_two.variant,""), "/", ifnull(product_variant_three.variant, "")) as title
            ')
        ->get();

            

    }


    public static function remove_all_product_variant_prices(Product $product){
        ProductVariantPrice::where('product_id', $product->id)->delete();
    }



}
