<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

   /*  
    GET|HEAD        api/admin/category .................. category.index › CategoryController@index 
    POST            api/admin/category .................. category.store › CategoryController@store 
    GET|HEAD        api/admin/category/{category} ......... category.show › CategoryController@show 
    PUT|PATCH       api/admin/category/{category} ..... category.update › CategoryController@update 
    DELETE          api/admin/category/{category} ... category.destroy › CategoryController@destroy
    */
   
    public function index() : JsonResponse {
        $category = Category::all();
        return response()->json($category);
    }
    public function store(Request $request) : JsonResponse {
        $attribute = $request->validate([
            'name'=>['required'],
            'description'=>['string','nullable'],
        ]);
        $category = Category::create($attribute);
        return response()->json([
            'message'=>'category created',
            'category'=>$category,
        ]);
    }
    public function show($id): JsonResponse {
        $category = Category::findOrFail($id);
        return response()->json($category, 200);
    }
    public function update(Request $request,$id)
    {
        $category = Category::findOrFail($id);
        $attribute = $request->validate([
            'name'=>['required'],
            'description'=>['string','nullable'],
        ]);
        $category->update($attribute);
        return response()->json($category,201);
    }
    public function destroy($id) {
        $Category = Category::findOrFail($id);
        $Category->delete();
        return response()->json([
            'message'=> "category deleted successfully",
        ]);
    }
}
