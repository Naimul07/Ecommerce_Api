<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

/*     
  GET|HEAD        api/order ............................ order.index › OrderController@index 
  POST            api/order ............................ order.store › OrderController@store 
  GET|HEAD        api/order/{order} ...................... order.show › OrderController@show 
  PUT|PATCH       api/order/{order} .................. order.update › OrderController@update 
  DELETE          api/order/{order} ................ order.destroy › OrderController@destroy 

 */

    public function index() {

        //need to implement logic only unpaid orders
        $orders = Order::with('orderItems.product')->where('user_id', Auth::id())->latest()->get();
        return response()->json($orders, 200);

    }
    public function store(Request $request) {
        $attribute = $request->validate([
            'order_items'=> ['required','array','min:1'],
            'order_items.*.product_id'=>['required','exists:products,id'],
            'order_items.*.quantity'=>['required','integer','min:1']
            
        ]);
        
        DB::beginTransaction();

        try{
            $totalPrice = 0;
            foreach ($attribute['order_items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                // dd($product);
                /* if ($item['quantity'] > $product->stock_quantity) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                } */
               $totalPrice += $product->price* $item['quantity'];
                
            }
            // dd($totalPrice);
            $order = Order::create([
                'user_id'=> Auth::id(),
                'total_price'=>$totalPrice,
                'status'=>'pending',
            ]);

            foreach ($attribute['order_items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                OrderItem::create([
                    'order_id'=>$order->id,
                    'product_id'=>$product->id,
                    'quantity'=> $item['quantity'],
                    'price'=>$product->price,
                ]);
            }

            DB::commit();
            return response()->json([
                'message'=>"order is created Successfully",
                'order'=>$order,
            ]);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Order creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    

    }
    public function show($id) {
       $order = Order::with('orderItems.product')->findOrFail($id);
       return response()->json($order);

    }
    public function update(Request $request,$id)
    {
        
    }
    public function destroy($id) {
       $order = Order::findOrFail($id);
       if($order->status ==='pending')
       {
        $order->delete();
        return response()->json([
            'message'=>"order is deleted"
        ]);
       }
       return response()->json([
        'message'=>"you are not authorized to delete"
    ],403);
    }

    public function store1(Request $request) {
        $attribute = $request->validate([
            'order_items'=> ['required','array','min:1'],
            'order_items.*.product_id'=>['required','exists:products,id'],
            'order_items.*.quantity'=>['required','integer','min:1']
            
        ]);

        DB::beginTransaction();

        try{
            $totalPrice = 0;
            
            DB::commit();
            return response()->json([
                'message'=>"order is created Successfully",
                
            ]);
        }
        catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'message' => 'Order creation failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    

    }
}
