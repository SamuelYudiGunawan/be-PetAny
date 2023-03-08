<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\BookAppoinment;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class BookAppoinmentController extends Controller
{
    public function addBookAppoinment(Request $request){
        $request->validate([
            'doctor' => 'required|string',
            'date' => 'required|date',
            'pets' => 'required|string',
            'complaint' => 'required|string',   
            'shift' => 'required|string',
        ]);

        try {
        $book_appoinment = BookAppoinment::create([
            'user_id' => Auth::user()->id,
            'doctor' => $request->doctor,
            'date' => $request->date,
            'pets' => $request->pets,
            'complaint' => $request->complaint,
            'shift' => $request->shift,
        ]);
        
        return response()->json([
            'data' => $book_appoinment,
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
            $data = BookAppoinment::with('user_id:id,name')->get();

            return response()->json([
                'data' => $data,
            ]);
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
            $data = BookAppoinment::with('user_id:id,name')->find($id);
                    
            return response()->json([
                'data' => $data,
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
        $book_appoinment = BookAppoinment::find($id)->update([
            'status' => 'accepted',
        ]);
        return response()->json([
            'message' => 'Book Appoinment Approved',
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
        $book_appoinment = BookAppoinment::find($id)->update([
            'status' => 'rejected',
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
        $book_appoinment = BookAppoinment::find($id)->update([
            'status' => 'finished',
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
