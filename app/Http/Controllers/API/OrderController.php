<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {

            // Set your Merchant Server Key
            Config::$serverKey = 'SB-Mid-server-yUWEa26RmN6-m79R4pQIJ8yG';
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = false;
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = true;
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = true;

            // Validate the request data
            $validatedData = $request->validate([
                'product_id' => 'sometimes|nullable|exists:products,id',
                'book_appointment_id' => 'sometimes|nullable|exists:book_appointments,id',
                'type' => 'required|in:product,book_appointment',
            ]);

            if ($validatedData['type'] === 'product') {
                $product = Product::findOrFail($validatedData['product_id']);
                $grossAmount = $product->price;
            } else {
                $grossAmount = 15000;
            }

            // Create a new order
            $order = Order::create([
                'user_id' => Auth::user()->id,
                'product_id' => $validatedData['product_id'] ?? null,
                'book_appoinment_id' => $validatedData['book_appointment_id'] ?? null,
                'type' => $validatedData['type'],
                'status' => ($validatedData['type'] == 'product') ? 'waiting_confirmation' : null,
                'gross_amount' =>  $grossAmount,
            ]);

            // Create a Midtrans transaction
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $order->id,
                    'gross_amount' => $order->gross_amount,
                ],
            ];
            $midtransSnapToken = Snap::getSnapToken($midtransParams);

            // Store the Midtrans token and transaction ID in the order
            $order->midtrans_token = $midtransSnapToken;
            $order->transaction_id = null; // The transaction ID will be set later
            $order->save();

            // Return the Midtrans Snap token to the client
            return response()->json(['token' => $midtransSnapToken]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
}