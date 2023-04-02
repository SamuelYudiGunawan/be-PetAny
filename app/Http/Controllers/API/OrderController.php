<?php

namespace App\Http\Controllers\API;

use Midtrans\Snap;
use App\Models\User;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Petshop;
use App\Models\Product;
use Midtrans\Notification;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Http\Response;
use App\Models\BookAppoinment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function checkoutProduct(Request $request){
        $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);
    try {
        $product = Product::findOrFail($request->product_id);    
        $grossAmount = $product->price * $request->quantity;

        // Set your Merchant Server Key
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $orderId = 'PRODUCT_' . Carbon::now()->format('YmdHis') . '_' . Auth::user()->id;

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'product_id' => $request->product_id,
            'type' => 'product',
            'gross_amount' =>  $grossAmount, 
            'payment_url' => null,
            'order_id' => $orderId,
            'quantity' => $request->quantity,
        ]);

        $midtransParams = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $order->gross_amount,
            ],
        ];

        $midtransResponse = Snap::createTransaction($midtransParams);
        $midtransSnapToken = $midtransResponse->token;
        $paymentUrl = $midtransResponse->redirect_url;

        // Store the Midtrans token, transaction ID, and payment URL in the order
        $order->midtrans_token = $midtransSnapToken;
        $order->payment_url = $paymentUrl;
        $order->save();

        // Return the Midtrans Snap token to the client
        return response()->json([
            'data' => $product,
            'midtrans_token' => $midtransSnapToken, 
            'payment_url' => $paymentUrl
        ]);
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

            // Retrieve the order using the order ID provided in the notification
            $order = Order::where('order_id', $request->order_id)->first();

            // Construct the signature key using the order details and your merchant server key
            $signatureKey = $request->order_id . $request->status_code . $request->gross_amount . env('MIDTRANS_SERVER_KEY');
            $signatureKey = hash('SHA512', $signatureKey);

            // Verify the signature key
            if ($signatureKey != $request->signature_key) {
                return response()->json([
                    'status' => false,
                    'message' => 'Signature is Invalid',
                ], 400);
            }

            $order->transaction_id = $request->transaction_id;
            $order->status_code = $request->status_code;
            $order->json_data = json_encode($request->all());
            $order->signature_key = $request->signature_key;
            $order->payment_type = $request->payment_type;
            $order->transaction_status = $request->transaction_status;
            $order->save();

            $book_appoinment = BookAppoinment::where('order_id', $request->order_id)->first();
            $product = Product::where('order_id', $request->order_id)->first();
            if($request->order_id == $book_appoinment->order_id && $request->transaction_status == 'settlement') 
            {
                $doctor = User::where('id', $book_appoinment->doctor)->first();
                $user = User::where('id', $book_appoinment->user_id)->first();
                $notification = Notifications::create([
                    'user_id' => $book_appoinment->user_id,
                    'petshop_id' => $doctor->petshop_id,
                    'title' => 'New Book Appointment',
                    'body' => 'New book appointment by ' . $user->name . ' for shift ' . $book_appoinment->shift . ' please review it ASAP.',
                ]);
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
            } else if($request->order_id == $product->order_id && $request->transaction_status == 'settlement') {
                $user = User::where('id', $order->user_id)->first();
                $petshop = Petshop::where('id', $product->petshop_id)->with('user_id')  ->first();
                $notification = Notifications::create([
                    'user_id' => $petshop->user_id,
                    'petshop_id' => $product->petshop_id,
                    'title' => 'New Product Order',
                    'body' => 'New product order by ' . $user->name . ' product  ' . $product->name . ' please review it ASAP.',
                ]);
                $order->transaction_id = $request->transaction_id;
                $order->status_code = $request->status_code;
                $order->json_data = json_encode($request->all());
                $order->signature_key = $request->signature_key;
                $order->payment_type = $request->payment_type;
                $order->transaction_status = $request->transaction_status;
            }
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
            ], 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function acceptProduct($order_id){
        try {
        $order = Order::where('order_id', $order_id)->first();
        $product = Product::where('id', $order->product_id)->first();
        
        $order->update([
            'product_status' => 'pengemasan',
        ]);

        $product->update([
            'stock' => $product->stock -= $order->quantity,
        ]);

        $notification = Notifications::create([
            'user_id' => $order->user_id,
            'petshop_id' => $product->petshop_id,
            'title' => 'Product Order Accepted',
            'body' => 'Your product order ' . $product->name . ' is Accepted',
        ]);


        return response()->json([
            'message' => 'Product Order Accepted',
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function rejectProduct($order_id){
        try {
        $order = Order::where('order_id', $order_id)->first();
        $product = Product::where('id', $order->product_id)->first();

        $order->update([
            'product_status' => 'rejected',
        ]);

        $notification = Notifications::create([
            'user_id' => $order->user_id,
            'petshop_id' => $product->petshop_id,
            'title' => 'Product Order Rejected',
            'body' => 'Your product order ' . $product->name . ' is rejected',
        ]);


        return response()->json([
            'message' => 'Product Order Accepted',
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function sendProduct($order_id){
        try {
        $order = Order::where('order_id', $order_id)->first();
        $product = Product::where('id', $order->product_id)->first();
        
        $order->update([
            'product_status' => 'pengiriman',
        ]);

        $notification = Notifications::create([
            'user_id' => $order->user_id,
            'petshop_id' => $product->petshop_id,
            'title' => 'Product Order On Delivery',
            'body' => 'Your product order ' . $product->name . ' is on Delivery',
        ]);


        return response()->json([
            'message' => 'Product Order On Delivery',
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function finishProduct($order_id){
        try {
        $order = Order::where('order_id', $order_id)->first();
        $product = Product::where('id', $order->product_id)->first();
        
        $order->update([
            'product_status' => 'pesanan selesai',
        ]);

        $notification = Notifications::create([
            'user_id' => $order->user_id,
            'petshop_id' => $product->petshop_id,
            'title' => 'Product Order Finished',
            'body' => 'Product order ' . $product->name . ' is Finished',
        ]);

        return response()->json([
            'message' => 'Product Order On Delivery',
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function getAllOrder(){
        try{
            $data = Order::where('type', 'product')->where('transaction_status', 'settlement')->with('product')->get();
            $response = [];
    
            foreach($data as $d) {
                array_push($response, [
                    'data' => $d,
                ]);
            }
    
            return response()->json($response);
    
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    
    public function getWaitingConfirmations(){
        try{
            $data = Order::where('type', 'product')->where('transaction_status', 'settlement')->where('product_status', 'Menunggu Konfirmasi')->with('product')->get();
            $response = [];
            foreach($data as $d) {
                array_push($response, [
                    'data' => $d,
                ]);
            }
            return response()->json($response);
    
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    
    public function getPackagings(){
        try{
            $data = Order::where('type', 'product')->where('transaction_status', 'settlement')->where('product_status', 'Pengemasan')->with('product')->get();
            $response = [];
            foreach($data as $d) {
                array_push($response, [
                    'data' => $d,
                ]);
            }
            return response()->json($response);
    
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    
    public function getDeliveries(){
        try{
            $data = Order::where('type', 'product')->where('transaction_status', 'settlement')->where('product_status', 'Pengiriman')->with('product')->get();
            $response = [];
            foreach($data as $d) {
                array_push($response, [
                    'data' => $d,
                ]);
            }
            return response()->json($response);
    
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    
    public function getCompletedOrders(){
        try{
            $data = Order::where('type', 'product')->where('transaction_status', 'settlement')->where('product_status', 'Pesanan Selesai')->with('product')->get();
            $response = [];
            foreach($data as $d) {
                array_push($response, [
                    'data' => $d,
                ]);
            }
            return response()->json($response);
    
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }    
}
