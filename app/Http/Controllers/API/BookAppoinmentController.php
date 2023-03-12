<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Midtrans\Snap;
use App\Models\Pet;
use App\Models\User;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Petshop;
use App\Models\Notifications;
use Illuminate\Http\Request;
use App\Models\BookAppoinment;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\JamOperasionalDokter;
use Illuminate\Support\Facades\Auth;

class BookAppoinmentController extends Controller
{
    public function addBookAppoinment(Request $request){
        $request->validate([
            'doctor' => 'required',
            'date' => 'required|string',
            'pets' => 'required',
            'complaint' => 'required|string',   
            'shift' => 'required|string',
        ]);

        try {
        $orderId = 'BOOK_' . Carbon::now()->format('YmdHis') . '_' . Auth::user()->id;

        $book_appoinment = BookAppoinment::create([
            'user_id' => Auth::user()->id,
            'doctor' => $request->doctor,
            'date' => $request->date,
            'pets' => $request->pets,
            'complaint' => $request->complaint,
            'shift' => $request->shift,
            'order_id' => $orderId,
        ]);
        
        // Set your Merchant Server Key
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        

        $order = Order::create([
            'user_id' => Auth::user()->id,
            'book_appoinment_id' => $book_appoinment->id,
            'type' => 'book_appointment',
            'gross_amount' =>  15000,
            'payment_url' => null,
            'order_id' => $orderId,
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
            'data' => $book_appoinment,
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

    public function getBookAppoinmentForm()
    {
        return [
            [
                'name' => 'doctor',
                'type' => 'text',
                'label' => 'Dokter',
                'required' => true,
            ],
            [
                'name' => 'date',
                'type' => 'date',
                'label' => 'Hari',
                'required' => true,
            ],
            [
                'name' => 'date',
                'type' => 'date',
                'label' => 'Jam',
                'required' => true,
            ],
            [
                'name' => 'pets',
                'type' => 'dropdown',
                'label' => 'Pasien',
                'required' => true,
            ],
            [
                'name' => 'complaint',
                'type' => 'text',
                'label' => 'Keluhan',
                'required' => true,
            ],
        ];
    }

    public function getAllBookAppoinment(){
        try{
            if (Auth::user()->role == 'petshop_owner') {
                $data = BookAppoinment::where('doctor', Auth::user()->petshop_id)->get();
            } else {
                $data = BookAppoinment::where('doctor', Auth::user()->id)->get();
            }

            $response = [];
            foreach($data as $d) {
                $doctor = User::where('id', $d->doctor)->first();
                $orderCollection = Order::where('order_id', $d->order_id)->get();
                $orderArray = [];
                foreach ($orderCollection as $order) {
                        array_push($orderArray, [
                            'order_id' => $order->order_id,
                            'amount' => "Rp " . number_format($order->gross_amount, 0, ',', '.'),
                            'type' => $order->type,
                            'time' => $order->updated_at->format('H:i'),
                        ]);
                if ($order->transaction_status === 'settlement') {
                    $petCollection = Pet::where('id', $d->pets)->get();
                    $petArray = [];
                    foreach ($petCollection as $pet) {
                        array_push($petArray, [
                            'pet_name' => $pet->pet_name,
                            'pet_image' => $pet->pet_image,
                            'pet_weight' => $pet->weight,
                            'pet_age' => $pet->age,
                        ]);
                    }
                    $petshopCollection = Petshop::where('id', $doctor->petshop_id)->get();
                    $petshopArray = [];
                    foreach ($petshopCollection as $petshop) {
                        array_push($petshopArray, [
                            'petshop_name' => $petshop->petshop_name,
                        ]);
                    }
                    array_push($response, [
                        'doctor' => $doctor->name,
                        'date' => $d->date,
                        'shift' => $d->shift,
                        'complaint' => $d->complaint,
                        'status' => $d->status,
                        'pets' => $petArray,
                        'orders' => $orderArray,
                        'petshop' => $petshopArray,
                    ]);
                }
                }
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
    
    public function getAllUserBookAppoinment(){
        try{
            $data = BookAppoinment::where('user_id', Auth::user()->id)->get();
            $response = [];
            foreach($data as $d) {
                $doctor = User::where('id', $d->doctor)->first();
                $orderCollection = Order::where('order_id', $d->order_id)->get();
                $orderArray = [];
                foreach ($orderCollection as $order) {
                        array_push($orderArray, [
                            'order_id' => $order->order_id,
                            'amount' => "Rp " . number_format($order->gross_amount, 0, ',', '.'),
                            'type' => $order->type,
                            'time' => $order->updated_at->format('H:i'),
                        ]);
                if ($order->transaction_status === 'settlement') {
                    $petCollection = Pet::where('id', $d->pets)->get();
                    $petArray = [];
                    foreach ($petCollection as $pet) {
                        array_push($petArray, [
                            'pet_name' => $pet->pet_name,
                            'pet_image' => $pet->pet_image,
                            'pet_weight' => $pet->weight,
                            'pet_age' => $pet->age,
                        ]);
                    }
                    $petshopCollection = Petshop::where('id', $doctor->petshop_id)->get();
                    $petshopArray = [];
                    foreach ($petshopCollection as $petshop) {
                        array_push($petshopArray, [
                            'petshop_name' => $petshop->petshop_name,
                        ]);
                    }
                    array_push($response, [
                        'doctor' => $doctor->name,
                        'date' => $d->date,
                        'shift' => $d->shift,
                        'complaint' => $d->complaint,
                        'status' => $d->status,
                        'links' => 'book-appointment/' . $d->id,
                        'pets' => $petArray,
                        'orders' => $orderArray,
                        'petshop' => $petshopArray,
                    ]);
                }
                }
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

    public function getBookAppoinment($id)
    {
        try{
            $d = BookAppoinment::find($id);
            // dd($data);
            $doctor = User::where('id', $d->doctor)->first();
            $order = Order::where('order_id', $d->order_id)->first();
            $pet = Pet::where('id', $d->pets)->first();
            $petshop = Petshop::where('id', $doctor->petshop_id)->first();
            return response()->json([
                'doctor' => $doctor->name,
                'date' => $d->date,
                'shift' => $d->shift,
                'complaint' => $d->complaint,
                'status' => $d->status,
                'pet_name' => $pet->pet_name,
                'pet_image' => $pet->pet_image,
                'pet_weight' => $pet->weight,
                'pet_age' => $pet->age,
                'order_id' => $order->order_id,
                'amount' => "Rp " . number_format($order->gross_amount, 0, ',', '.'),
                'type' => $order->type,
                'time' => $order->updated_at->format('H:i'),
                'petshop_name' => $petshop->petshop_name,
            ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function acceptBookAppoinment($id){
        try {
        $book_appoinment = BookAppoinment::find($id);
        
        $book_appoinment->update([
            'status' => 'accepted',
        ]);

        $doctor = User::where('id', $book_appoinment->doctor)->first();
        $notification = Notifications::create([
            'user_id' => $book_appoinment->user_id,
            'petshop_id' => $doctor->petshop_id,
            'title' => 'Book Appointment Accepted',
            'body' => 'Your book appointment ' . ' for ' . $book_appoinment->shift . ' is Accepted',
        ]);
        return response()->json([
            'message' => 'Book Appoinment Accepted',
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function rejectBookAppoinment($id){
        try {
        $book_appoinment = BookAppoinment::find($id);
        
        $book_appoinment->update([
            'status' => 'rejected',
        ]);

        $doctor = User::where('id', $book_appoinment->doctor)->first();
        $notification = Notifications::create([
            'user_id' => $book_appoinment->user_id,
            'petshop_id' => $doctor->petshop_id,
            'title' => 'Book Appointment Rejected',
            'body' => 'Your book appointment ' . ' for ' . $book_appoinment->shift . ' is Rejected',
        ]);
        return response()->json([
            'message' => 'Book Appoinment Rejected',
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    
    public function finishBookAppointment($id){
        try {
        $book_appoinment = BookAppoinment::find($id);
        
        $book_appoinment->update([
            'status' => 'finished',
        ]);

        $doctor = User::where('id', $book_appoinment->doctor)->first();
        $notification = Notifications::create([
            'user_id' => $book_appoinment->user_id,
            'petshop_id' => $doctor->petshop_id,
            'title' => 'Book Appointment Finished',
            'body' => 'Your book appointment ' . ' for ' . $book_appoinment->shift . ' is Finished',
        ]);
        return response()->json([
            'message' => 'Book Appoinment Finished',
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
}
