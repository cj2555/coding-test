<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;

class ProductImageRepository
{
   
    public static function add_product_images(Product $product, $files){
        foreach($files as $file){
            $file_name=$file->getClientOriginalName();
            $store_path='images/product';
            $file->move(public_path($store_path), $file_name);
            $file_path=$store_path.'/'.$file_name;

            $insert_product_arr[]=[
                'product_id'=>$product->id,
                'file_path'=>$file_path,
                'created_at'=>date('Y-m-d H:i:s'),
                'updated_at'=>date('Y-m-d H:i:s')
            ];

        }
        ProductImage::insert($insert_product_arr);
    }
         

    public static function get_product_images(Product $product){
        $product_images=ProductImage::where('product_id', $product->id)->get();
        return $product_images;
    }   

    public static function delete_product_images(Product $product){
        $product_images=ProductImage::where('product_id', $product->id)->get();

        //unlink
        foreach ($product_images as $key => $value) {
            unlink(public_path($value->file_path));
        }
        
        ProductImage::where('product_id', $product->id)->delete();
    }

}
