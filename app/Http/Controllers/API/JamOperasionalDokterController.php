<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\JamOperasional;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\JamOperasionalDokter;

class JamOperasionalDokterController extends Controller
{
    public function getJamOperasionalDokter($id){
        try {
            $data = JamOperasionalDokter::with('user:id')->where('user_id', $id)->get();
            return response()->json($data);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    public function getJamOperasionalDataDokter($id){
        try {
            $data = JamOperasionalDokter::with('user:id')->where('user_id', $id)->get();
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

    public function createJamOperasionalDokter(Request $request, $id){
        $request->validate([
            'hari_buka.*' => 'required|string',
            'is_open.*' => 'required|boolean',
            'jam_buka.*' => 'required_if:is_open,true|date_format:H:i',
            'jam_tutup.*' => 'required_if:is_open,true|date_format:H:i|after:jam_buka',
            'jam_buka2.*' => 'nullable|date_format:H:i|after:jam_tutup',
            'jam_tutup2.*' => 'nullable|date_format:H:i|after:jam_buka2',
            // 'petshop_id' => 'required|exists:petshops,id'
        ]);
        try {
            foreach ($request->all() as $d) {
                JamOperasionalDokter::updateOrCreate(['user_id' => $id, 'hari_buka' => $d['hari_buka'],], [
                    'hari_buka' => $d['hari_buka'],
                    'is_open' => $d['is_open'],
                    'jam_buka' => $d['jam_buka'],
                    'jam_tutup' => $d['jam_tutup'],
                    'jam_buka2' => $d['jam_buka2'],
                    'jam_tutup2' => $d['jam_tutup2'],
                    'user_id' => $id,
                ]);
            }
            return response()->json([
                'message' => true
            ]);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    public function getJamOperasionalMingguan($id) {
        $jamOperasionals = JamOperasionalDokter::where('user_id', $id)->get();
        $hariIni = Carbon::now()->format('l');
        $data = collect();
        $hari = Carbon::now();
        for ($i = 1; $i <= 14; $i++) {
            $jamOperasional = $jamOperasionals->where('hari_buka', $hari->translatedFormat('l'))->first();
            if ($jamOperasional && $jamOperasional->is_open) {
                $openTime = Carbon::parse($jamOperasional->jam_buka)->format('H:i');
                $closeTime = Carbon::parse($jamOperasional->jam_tutup)->format('H:i');
                $openTime2 = Carbon::parse($jamOperasional->jam_buka2)->format('H:i');
                $closeTime2 = Carbon::parse($jamOperasional->jam_tutup2)->format('H:i');
                $data->push([
                    'hari' => $hari->locale('id')->translatedFormat('l'),
                    'tanggal' => $hari->locale('id')->translatedFormat('l, j M'),
                    'shift1' => $openTime . " - " . $closeTime,
                    // 'jam_tutup' => $closeTime,
                    'shift2' => $openTime2 . " - " . $closeTime2,
                    // 'jam_tutup2' => $closeTime2,
                ]);
            }
            $hari->addDay();
        }
        return response()->json([
            'data' => $data,
        ]);
    }    
}
