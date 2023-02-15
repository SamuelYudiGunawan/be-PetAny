<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\JamOperasional;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class JamOperasionalController extends Controller
{
    public function getJamOperasional($id){
        $data = JamOperasional::with('petshop:id')->where('petshop_id', $id)->get();
        return response()->json($data);
    }

    public function createJamOperasional(Request $request, $id){
        $request->validate([
            'hari_buka.*' => 'required|integer|min:0|max:6',
            'is_open.*' => 'required|boolean',
            'jam_buka.*' => 'required_if:is_open,true|date_format:H:i',
            'jam_tutup.*' => 'required_if:is_open,true|date_format:H:i|after:jam_buka',
            // 'petshop_id' => 'required|exists:petshops,id'
        ]);

        $data_jam_operasional = [];

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
            'message' => true
        ]);
    }
}
