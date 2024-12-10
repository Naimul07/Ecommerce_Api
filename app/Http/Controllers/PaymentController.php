<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $stripeSecret = env('STRIPE_SECRET');

        $stripe = new \Stripe\StripeClient($stripeSecret);

        //Hard coded
        $YOUR_DOMAIN = env('FRONTEND_URL');

        $lineItems = [];
        $order = Order::with('orderItems')->findOrFail($request->id);
        dd($order);
        //need some work to do
        $checkout_session = $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Sample Product',
                    ],
                    'unit_amount' => 2000, // Amount in cents ($20.00)
                ],
                'quantity' => 1,
            ]],
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
