<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Petshop;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PetshopController extends Controller
{
    public function getAllPetshop(){
        $data = Petshop::with('user_id')->get();
        // $user = User::with('petshop')->where('id',1)->first();

        return response()->json([
            'data' => $data,
            // 'user' => $user,
        ]);
    }

    public function getPetshop($id)
    {
        $data = Petshop::with('user_id')->find($id);
        
        return response()->json([
            'data' => $data,
        ]);
    }

    public function getPetshopForm()
    {
        return [
            [
                'name' => 'petshop_name',
                'type' => 'text',
                'label' => 'Nama Klinik',
                'required' => true,
            ],
            [
                'name' => 'company_name',
                'type' => 'text',
                'label' => 'Nama Perusahaan',
                'required' => true,
            ],
            [
                'name' => 'owner',
                'type' => 'text',
                'label' => 'Nama Pemilik',
                'required' => true,
            ],
            [
                'name' => 'phone_number',
                'type' => 'number',
                'label' => 'Nomor Telepon',
                'required' => true,
            ],
            [
                'name' => 'petshop_email',
                'type' => 'email',
                'label' => 'Email Klinik',
                'required' => true,
            ],
            [
                'name' => 'permit',
                'type' => 'file',
                'label' => 'Surat Izin Usaha Klinik Hewan',
                'required' => true,
            ],
            [
                'name' => 'province',
                'type' => 'dropdown',
                'label' => 'Provinsi',
                'required' => true,
            ],
            [
                'name' => 'city',
                'type' => 'dropdown',
                'label' => 'Kota',
                'required' => true,
            ],
            [
                'name' => 'district',
                'type' => 'dropdown',
                'label' => 'Kecamatan',
                'required' => true,
            ],
            [
                'name' => 'postal_code',
                'type' => 'number',
                'label' => 'Kode Pos',
                'required' => true,
            ],
            [
                'name' => 'petshop_address',
                'type' => 'text',
                'label' => 'DetaiL Alamat',
                'required' => true,
            ],
        ];
    }

    public function create(Request $request){
        $request->validate([
            'petshop_name' => 'required|string|unique:petshops',
            'company_name' => 'required|string|unique:petshops',
            'phone_number' => 'required|string',
            'petshop_email' => 'required|email|string|unique:petshops',
            'permit' => 'required|file|mimes:pdf',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'postal_code' => 'required|string',
            'petshop_address' => 'required|string',
        ]);

        try{
        $fileName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('permit')) . "." . $request->file('permit')->getClientOriginalExtension();
        $filePath = "storage/document/permit/" . $fileName;
        $request->permit->storeAs(
            "public/document/permit",
            $fileName
        );

        $petshop = Petshop::create([
            'user_id' => Auth::user()->id,
            'petshop_name' => $request->petshop_name,
            'company_name' => $request->company_name,
            'district' => $request->district,
            'phone_number' => $request->phone_number,
            'petshop_email' => $request->petshop_email,
            'permit' => url('/').'/'.$filePath,
            'province' => $request->province,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'petshop_address' => $request->petshop_address,
        ]);
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }

        return response()->json([
            'data' => $petshop,
        ]);
    }
}
