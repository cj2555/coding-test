<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
   
    public static function filter_products($filter_options){

        $search = isset($filter_options['search']) ? $filter_options['search'] : null;
        $variant = isset($filter_options['variant']) ? $filter_options['variant'] : null;
        $price_from = isset($filter_options['price_from']) ? $filter_options['price_from'] : null;
        $price_to = isset($filter_options['price_to']) ? $filter_options['price_to'] : null;
        $date = isset($filter_options['date']) ? $filter_options['date'] : null;


        $data=Product::
        when($search, function($query) use ($search){
            $query->where('title', 'like', '%'.$search.'%');
        })
        ->when($variant, function($query) use ($variant){
            $query->whereHas('productVariants', function($query) use ($variant){
                $query->where('variant', 'like', '%'.$variant.'%');
            });
        })
       
        ->when($date, function($query) use ($date){
            $query->whereDate('created_at', $date);
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
            $data->with(['productVariantPrices']);
        }

        return $data->paginate(5);



    }


}
