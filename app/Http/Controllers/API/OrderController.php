<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Product;
use Midtrans\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderPayment;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {

            // Set your Merchant Server Key
            Config::$serverKey = env('MIDTRANS_SERVER_KEY');
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
                $orderId = 'PRODUCT_' . Carbon::now()->format('YmdHis') . '_' . Auth::user()->id;
            } else {
                $grossAmount = 15000;
                $orderId = 'BOOK_' . Carbon::now()->format('YmdHis') . '_' . Auth::user()->id;

            }

            $order = Order::create([
                'user_id' => Auth::user()->id,
                'product_id' => $validatedData['product_id'] ?? null,
                'book_appoinment_id' => $validatedData['book_appointment_id'] ?? null,
                'type' => $validatedData['type'],
                'product_status' => ($validatedData['type'] == 'product') ? 'waiting_confirmation' : null,
                'gross_amount' =>  $grossAmount,
                'payment_url' => null,
                'order_id' => $orderId,
            ]);
            
            $midtransParams = [
                'transaction_details' => [
                    'order_id' => $orderId,
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
            
            // $orderPayment = OrderPayment::where('order_detail_id', $request->order_id)->first();

            // if (!$orderPayment) {
            //     $orderPayment = new OrderPayment([
            //         'order_detail_id' => $request->order_id,
            //         'transaction_id' => $request->transaction_id,
            //         'status' => $request->transaction_status,
            //         'status_code' => $request->status_code,
            //         'payment_type' => $request->payment_type,
            //         'payment_amount' => $request->gross_amount,
            //         'json_data' => json_encode($request->all()),
            //     ]);
            //     $orderPayment->save();

            //     return response()->json([
            //         'status' => true,
            //         'message' => 'Transaction has been created',
            //     ], 201);
            // }

            // Retrieve the order using the order ID provided in the notification
            $order = Order::where('order_id', $request->order_id)->first();

            $serverKey = "SB-Mid-server-yUWEa26RmN6-m79R4pQIJ8yG";

            // Construct the signature key using the order details and your merchant server key
            $signatureKey = $request->order_id . $request->status_code . $request->gross_amount . $serverKey;
            $signatureKey = hash('SHA512', $signatureKey);

            // Verify the signature key
            if ($signatureKey != $request->signature_key) {
                return response()->json([
                    'status' => false,
                    'message' => 'Signature is Invalid',
                ], 400);
            }
            // $order = Order::where('order_id', $request->order_id)->first();


            $order->transaction_id = $request->transaction_id;
            $order->status_code = $request->status_code;
            $order->json_data = json_encode($request->all());
            $order->signature_key = $request->signature_key;
            $order->payment_type = $request->payment_type;
            $order->transaction_status = $request->transaction_status;
            // if ($request->transaction_status == 'settlement') {
            //     $order->transaction_status = 'paid';
            // }
            // if ($request->transaction_status == 'cancel' || $request->transaction_status == 'expire' || $request->transaction_status == 'deny') {
            //     $order->transaction_status = 'error';
            // }
            $order->save();


            // $order->update([
            //     'transaction_id' => $request->transaction_id,
            //     'status_code' => $request->status_code,
            //     'json_data' => 'test',
            //     'signature_key' => $request->signature_key,
            //     'payment_type' => $request->payment_type,
            //     'transaction_status' => $request->transaction_status,
            // ]);
            // $order->status_code = $request->status_code;

            // $orderPayment->save();
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
            ], 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 200);
        }
    }
}
