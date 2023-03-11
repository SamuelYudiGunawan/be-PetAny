<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getUserNotification(){
        try {
            $notification = Notifications::where('user_id', Auth::user()->id)->get();

            return response()->json($notification);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function getPetshopNotification($id){
        try {
            $notification = Notifications::where('petshop_id', $id)->get();

            return response()->json($notification);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
}
