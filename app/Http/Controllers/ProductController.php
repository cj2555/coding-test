<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use App\Repositories\ProductImageRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ProductVariantPriceRepository;
use App\Repositories\ProductVariantRepository;
use App\Repositories\VariantRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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


        $validator=Validator::make($request->all(), [
            'title' => 'required|max:255',
            'description' => 'required',
            'sku' => 'required|unique:products,sku'
        ]);

        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()->first()], 422);
        }

        $product_data=[
            'title'=>$request->title,
            'description'=>$request->description,
            'sku'=>$request->sku,
        ];


        //add product
        $product=ProductRepository::create_product($product_data);


        //add product images
        if($request->has('files') && count($request->files)>0){

            ProductImageRepository::add_product_images($product, $request->file('files'));
            
        }
        
        
        //add product variants
        if($request->has('product_variant') && $request->has_variant=='true'){
            ProductVariantRepository::add_product_variant($product, json_decode($request->product_variant, true));

            //add variant prices
            if($request->has('product_variant_prices')){
                $product_variant_prices=json_decode($request->product_variant_prices, true);
                if(count($product_variant_prices)>0){
                      ProductVariantPriceRepository::add_product_variant_price($product, $product_variant_prices);
                }
            }
        }
        

        return response()->json(['success'=>true], 201);

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
        $product->product_images=ProductImageRepository::get_product_images($product);
        $product->product_variants=ProductVariantRepository::get_product_variants($product);
        $product->product_variant_prices=ProductVariantPriceRepository::get_product_variant_prices($product);

        return view('products.edit')
            ->with('product', $product)
            ->with('variants', $variants);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validator=Validator::make($request->all(), [
            'id'=>'required',
            'title' => 'required|max:255',
            'description' => 'required',
            'sku' => 'required|unique:products,sku,'.$request->id,
        ]);


        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()->first()], 422);
        }

        $product_data=[
            'title'=>$request->title,
            'description'=>$request->description,
            'sku'=>$request->sku,
        ];

        $product=Product::find($request->id);
        $product=ProductRepository::update_product($product, $product_data);


        if($request->has_file_changed && $request->has_file_changed=='true'){
            ProductImageRepository::add_product_images($product, $request->file('files'));
       
        }


        return response()->json(['success'=>true], 200);
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

    public function delete_product_image(Request $request){
        $validator=Validator::make($request->all(), [
            'id'=>'required',
        ]);

        if($validator->fails()){
            return response()->json(['message'=>$validator->errors()->first()], 422);
        }

        $product_image=ProductImage::find($request->id);
        @unlink(public_path($product_image->file_path));
        $product_image->delete();
        return response()->json(['success'=>true], 200);
    }
}
