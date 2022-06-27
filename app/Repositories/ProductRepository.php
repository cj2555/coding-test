<?php

namespace App\Repositories;

use App\Models\Product;
use Carbon\Carbon;

class ProductRepository
{
   
    public static function filter_products($filter_options){

        $search = isset($filter_options['title']) ? $filter_options['title'] : null;
        $variant = isset($filter_options['variant']) ? $filter_options['variant'] : null;
        $price_from = isset($filter_options['price_from']) ? $filter_options['price_from'] : null;
        $price_to = isset($filter_options['price_to']) ? $filter_options['price_to'] : null;
        $date = isset($filter_options['date']) ? Carbon::parse($filter_options['date'])->format('Y-m-d') : null;
        // return $date;

        $data=Product::
        when($search, function($query) use ($search){
            $query->where('title', 'like', '%'.$search.'%');
        })
        ->when($date, function($query) use ($date){
            $query->whereDate('created_at', $date);
        })
        ->when($variant, function($query) use ($variant){
            $query->whereHas('productVariants', function($query) use ($variant){
                $query->where('variant', 'like', '%'.$variant.'%');
            });
        });
       
        

        if(request()->query('price_from')){
            $data->whereHas('productVariantPrices', function($query) use ($price_from){
                $query->where('price', '>=', $price_from);
            });
            
        }

        else if(request()->query('price_to')){
            $data->whereHas('productVariantPrices', function($query) use ($price_to){
                $query->where('price', '<=', $price_to);
            });
            
        }
        else{
            $data->with(['productVariantPrices'=>function($query){
                $query->with('productVariantOne')->with('productVariantTwo')->with('productVariantThree');
            }]);
        }

        return $data->paginate(5)->appends(request()->query());

    }


    public static function create_product($product_data){
        
        $new_product=new Product();
        $new_product->title=$product_data['title'];
        $new_product->description=$product_data['description'];
        $new_product->sku=$product_data['sku'];
        $new_product->save();

        return $new_product;
    }

    public static function update_product($product, $product_data){
        
        $product->title=$product_data['title'];
        $product->description=$product_data['description'];
        $product->sku=$product_data['sku'];
        $product->save();

        return $product;
    }



}
