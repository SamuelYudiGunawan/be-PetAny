<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Product;
use Midtrans\Notification;
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

            $validatedData = $request->validate([
                'product_id' => 'sometimes|nullable|exists:products,id',
                'book_appointment_id' => 'sometimes|nullable|exists:book_appointments,id',
                'type' => 'required|in:product,book_appointment',
                'quantity' => 'nullable|integer|min:1',
            ]);

            if ($validatedData['type'] === 'product') {
                $product = Product::findOrFail($validatedData['product_id']);
                $grossAmount = $product->price * $validatedData['quantity'];
            } else {
                $grossAmount = 15000;
            }

            $order = Order::create([
                'user_id' => Auth::user()->id,
                'product_id' => $validatedData['product_id'] ?? null,
                'book_appoinment_id' => $validatedData['book_appointment_id'] ?? null,
                'type' => $validatedData['type'],
                'product_status' => ($validatedData['type'] == 'product') ? 'waiting_confirmation' : null,
                'gross_amount' =>  $grossAmount,
                'payment_url' => null,
            ]);
            
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $order->id,
                    'gross_amount' => $order->gross_amount,
                ],
            ];
            // $midtransSnapToken = Snap::getSnapToken($midtransParams);

            // // Store the Midtrans token and transaction ID in the order
            // $order->midtrans_token = $midtransSnapToken;
            // $order->transaction_id = null; // The transaction ID will be set later
            // $order->save();
            
            // // Get the payment URL using the Snap::createTransactionUrl() method
            // $paymentUrl = Snap::createTransactionUrl($midtransSnapToken);

            // // Save the payment URL to the order
            // $order->payment_url = $paymentUrl;
            // $order->save();

            $midtransResponse = Snap::createTransaction($midtransParams);
            $midtransSnapToken = $midtransResponse->token;
            $paymentUrl = $midtransResponse->redirect_url;

            // Store the Midtrans token, transaction ID, and payment URL in the order
            $order->midtrans_token = $midtransSnapToken;
            $order->payment_url = $paymentUrl;
            $order->save();

            // Return the Midtrans Snap token to the client
            return response()->json(['data' => ['midtrans_token' => $midtransSnapToken, 'payment_url' => $paymentUrl]]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function handleMidtransNotification(Request $request)
    {
        try {
            $json = $request->getContent();
            $data = json_decode($json);
    
            // Retrieve the order using the order ID provided in the notification
            $order = Order::findOrFail($data->order_id);

            // Construct the signature key using the order details and your merchant server key
            $signatureKey = hash('sha512', $order->order_id . $order->status_code . $order->gross_amount . Config::$serverKey);

            // Verify the signature key
            if ($signatureKey !== $data->signature_key) {
                Log::error('Invalid signature key');
                return response()->json([
                    'error' => 'Invalid signature key'
                ], 400);
            }

            $order->transaction_id = $data->transaction_id;
            $order->transaction_status = $data->transaction_status; 
            $order->status_code = $data->status_code;
            $order->json_data = $data;
            $order->signarute_key = $signatureKey;
            $order->payment_type = $data->payment_type;
            $order->save();
    
            return response()->json(['data' => $data], 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
}
