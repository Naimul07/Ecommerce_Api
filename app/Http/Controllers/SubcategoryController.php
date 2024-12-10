<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubcategoryController extends Controller
{
    //
    public function index() : JsonResponse {
        $subcategory= Subcategory::all();
        return response()->json($subcategory);
    }
    public function store(Request $request) : JsonResponse {
        $attribute = $request->validate([
            'name'=>['required'],
            'description'=>['string','nullable'],
            'category_id' =>['required'],
        ]);
        $subcategory = Subcategory::create($attribute);
        return response()->json([
            'message'=>'subcategory created',
            'subcategory'=>$subcategory,
        ]);
    }
    public function show($id): JsonResponse {
        $subcategory = Subcategory::findOrFail($id);
        return response()->json($subcategory, 200);
    }
    public function update(Request $request,$id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $attribute = $request->validate([
            'name'=>['required'],
            'description'=>['string','nullable'],
            'category_id' =>['required'],

        ]);
        $subcategory->update($attribute);
        return response()->json($subcategory,201);
    }
    public function destroy($id) {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();
        return response()->json([
            'message'=> "subcategory deleted successfully",
        ]);
    }
}
