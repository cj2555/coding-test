<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Repositories\ProductRepository;
use App\Repositories\VariantRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
  
        $variants=VariantRepository::get_variants_with_group();
        
        $products=ProductRepository::filter_products(request()->query());
 
        return view('products.index')
            ->with('products', $products)
            ->with('variant_option', $variants);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // return response()->json($request->all());


        $new_product=new Product();
        $new_product->title=$request->title;
        $new_product->description=$request->description;
        $new_product->sku=$request->sku;
        $new_product->save();


        
        foreach ($request->product_variant as $key1 => $value1) {
            $variant=$value1['option'];
            $variant_tags=$value1['tags'];
            foreach ($variant_tags as $key2 => $value2) {
                $insert_product_arr[]=[
                    'product_id'=>$new_product->id,
                    'variant_id'=>$variant,
                    'variant'=>strtolower($value2),
                    'created_at'=>date('Y-m-d H:i:s'),
                    'updated_at'=>date('Y-m-d H:i:s')
                ];
               
            }
        }

        ProductVariant::insert($insert_product_arr);

        foreach ($request->product_variant_prices as $key => $value) {
           $title=$value['title'];
           $price=$value['price'];
           $stock=$value['stock'];
           $title_arr=array_filter(explode('/',$title));

              $product_variant_price[$key]=[
                'product_id'=>$new_product->id,
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
                $product_variant_id=ProductVariant::where([
                  'product_id'=>$new_product->id,
                  'variant'=>$title_arr[$i]
                ])->first()->id;
                // return  [$product_variant_id,$new_product->id,$title_arr[$i]];
                $product_variant_price[$key][$colmun_name[$i]]=$product_variant_id;
              }

        }
        ProductVariantPrice::insert($product_variant_price);    

        return response()->json(array_values($product_variant_price));

    }




        // return 'ok';


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
