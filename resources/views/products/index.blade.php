@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{url()->current()}}"
         method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control"
                    value="{{request()->query('title')}}">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">

                        <option value="" disabled selected>Select Variant</option>
                        @foreach ($variant_option as $title => $variants)
                            <optgroup label="{{$title}}">
                                @foreach ($variants as $variant)
                                    <option value="{{$variant}}"
                                    @if(request()->query('variant') == $variant)
                                        selected
                                    @endif
                                    >{{$variant}}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                  
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control"
                        value="{{request()->query('price_from')}}">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control"
                        value="{{request()->query('price_to')}}">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control"
                    value="{{request()->query('date')}}">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>


                    @foreach($products as $key=>$product)
                      
                    <tr>
                        <td> {{$product->id}} </td>
                        <td>{{$product->title}}
                       
                             <br> Created at : 
                                <?php
                                    $created_at = strtotime($product->created_at);
                                    $now = time();
                                    $difference_in_seconds = $now - $created_at;
                                    $difference_in_hours = $difference_in_seconds / 3600;
                                    echo round($difference_in_hours)." hours ago";
                                ?>
                            </td>
                        <td>
                            {{$product->description}}
                        </td>
                        <td>
                               
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" 
                                id="variant-{{$product->id}}">
                            @foreach($product->productVariantPrices as $product_variant_price)

                                <dt class="col-sm-3 pb-0">
                                    {{$product_variant_price->productVariantOne ? $product_variant_price->productVariantOne->variant : ''}}
                                    /{{$product_variant_price->productVariantTwo ? $product_variant_price->productVariantTwo->variant : ''}}
                                    /{{$product_variant_price->productVariantThree ? $product_variant_price->productVariantThree->variant : ''}}
                                    
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price : {{ number_format($product_variant_price->price, 2) }}</dt>

                                        <d class="col-sm-8 pb-0">InStock : {{ number_format($product_variant_price->stock, 0) }}</d>
                                    </dl>
                                </dd>
                            @endforeach

                            </dl>
                            <button onclick="$('#variant-{{$product->id}}').height('auto')" class="btn btn-sm btn-link">
                            Show more</button>
                            
                            
                                
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit', $product->id) }}" class="btn btn-primary">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    <!-- //pagination -->
                    <!-- {{ $products->links("pagination::bootstrap-4") }} -->
                    <!-- {!! $products->render() !!} -->

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} items</p>
                </div>
                <div class="col-md-2">
                {{ $products->links("pagination::bootstrap-4") }}

                </div>
                  


            </div>
        </div>
    </div>

@endsection
