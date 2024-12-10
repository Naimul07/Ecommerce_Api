<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    
/* 
  GET|HEAD        api/products ........................ products.index › ProductController@index  
  POST            api/products .............................. products.store › ProductController@store  
  GET|HEAD        api/products/{product} ...................... products.show › ProductController@show  
  PUT|PATCH       api/products/{product} ................... products.update › ProductController@update  
  DELETE          api/products/{product} .................... products.destroy › ProductController@destroy */


    public function index() : JsonResponse {
        $products = Product::all();
        return response()->json($products, 200);
    }
    public function store(Request $request):JsonResponse {
         

         $attribute = $request->validate([
            'name'=>['required'],
            'description' => ['nullable','string'],
            'subcategory_id'=>['required','exists:subcategories,id'],
            'price'=>['required','numeric','min:0'],
            'stock_quantity'=>['required','integer','min:0'],
        ]);
        
        $product = Product::create($attribute);
        // if($request->hasFile())
        // file management
        

        return response()->json($product,201); 
    }

    public function show($id): JsonResponse {
        $product = Product::findOrFail($id);
        return response()->json($product, 200);
    }

    public function update(Request $request,$id)
    {
        $product = Product::findOrFail($id);

        $attribute = $request->validate([
            'name'=>['required'],
            'description' => ['nullable','string'],
            'subcategory_id'=>['required','exists:subcategories,id'],
            'price'=>['required','numeric','min:0'],
            'stock_quantity'=>['required','integer','min:0'],
        ]);
        $product->update($attribute);
        return response()->json($product,201);
    }

    public function delete($id):JsonResponse{
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json([
            'message' => 'Product deleted successfully.'
        ]);
    }
}
