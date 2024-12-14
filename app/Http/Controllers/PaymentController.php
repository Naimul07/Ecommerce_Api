<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $attribute = $request->validate([
            'id'=>['required'],
        ]);
        $stripeSecret = env('STRIPE_SECRET');

        $stripe = new \Stripe\StripeClient($stripeSecret);

        //Hard coded
        $YOUR_DOMAIN = env('FRONTEND_URL');

        $lineItems = [];
        $order = Order::with('orderItems.product')->findOrFail($request->id);
        foreach ($order->order_items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd', // Update the currency as needed
                    'product_data' => [
                        'name' => $item->product->name,
                        // You can add other product details here, e.g., description or images
                    ],
                    'unit_amount' => $item->price * 100, // Stripe expects the amount in cents
                ],
                'quantity' => $item->quantity,
            ];
        }
        //need some work to do
        $checkout_session = $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'line_items' => $lineItems,
            'mode' => 'payment',
            'return_url' => $YOUR_DOMAIN . '/return?session_id={CHECKOUT_SESSION_ID}',
        ]);

        return response()->json([
            'clientSecret' => $checkout_session->client_secret, // Provide the session ID to the frontend
        ]);
    }
    public function status(Request $request)
    {
        $stripeSecret = env('STRIPE_SECRET');
        $stripe = new \Stripe\StripeClient($stripeSecret);
        try {
            $session = $stripe->checkout->sessions->retrieve($request->session_id);
            return response()->json([
                'message' => $session->status,
                'customer_email' => $session->customer_details->email,
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe API errors
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 400);
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json([
                'error' => true,
                'message' => 'An unexpected error occurred.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
