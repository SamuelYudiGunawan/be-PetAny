<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Models\JamOperasional;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class JamOperasionalController extends Controller
{
    public function getJamOperasional($id){
        try {
            $data = JamOperasional::with('petshop:id')->where('petshop_id', $id)->get();
            return response()->json($data);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    public function getJamOperasionalData($id){
        try {
            $data = JamOperasional::with('petshop:id')->where('petshop_id', $id)->get();
        if(count($data) > 0) {
            return response()->json([
                'message' => true,
        ]);
        } else {
            return response()->json([
                'message' => false,
            ]);  
        }
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function createJamOperasional(Request $request, $id){
        $request->validate([
            'hari_buka.*' => 'required|string',
            'is_open.*' => 'required|boolean',
            'jam_buka.*' => 'required_if:is_open,true|date_format:H:i',
            'jam_tutup.*' => 'required_if:is_open,true|date_format:H:i|after:jam_buka',
            // 'petshop_id' => 'required|exists:petshops,id'
        ]);
        try {
            foreach ($request->all() as $d) {
                JamOperasional::updateOrCreate(['petshop_id' => $id, 'hari_buka' => $d['hari_buka'],], [
                    'hari_buka' => $d['hari_buka'],
                    'is_open' => $d['is_open'],
                    'jam_buka' => $d['jam_buka'],
                    'jam_tutup' => $d['jam_tutup'],
                    'petshop_id' => $id,
                ]);
            }
            return response()->json([
                'message' => true,
            ]);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
}
