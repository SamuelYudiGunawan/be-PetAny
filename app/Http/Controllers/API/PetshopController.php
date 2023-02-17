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
    public function create(Request $request){
        if (Auth::user()->hasRole('petshop_staff')) {
            return response()->json([
                'message' => 'You can not have more than 1 petshop',
            ], 403);
        }
        $request->validate([
            'petshop_name' => 'required|string|unique:petshops',
            'petshop_image' => 'required|file|mimes:png,jpg',
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
        $imageName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('petshop_image')) . "." . $request->file('petshop_image')->getClientOriginalExtension();
        $imagePath = "storage/document/petshop_image/" . $imageName;
        $request->petshop_image->storeAs(
            "public/document/petshop_image",
            $imageName
        );

        $petshop = Petshop::create([
            'petshop_name' => $request->petshop_name,
            'petshop_image' => url('/').'/'.$imagePath,
            'company_name' => $request->company_name,
            'district' => $request->district,
            'phone_number' => $request->phone_number,
            'petshop_email' => $request->petshop_email,
            'permit' => url('/').'/'.$filePath,
            'province' => $request->province,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'petshop_address' => $request->petshop_address,
            'user_id' => Auth::user()->id,
        ]);
        return response()->json([
            'data' => $petshop,
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function getAllPetshop(){
        try{
            $data = Petshop::with('user_id:id,name')->get();
            $response = [];
            foreach ($data as $d) {
                array_push($response, [
                    'petshop_name' => $d->petshop_name,
                    'user_id' => $d->user_id,
                    'petshop_image' => $d->petshop_image,
                    'company_name' => $d->company_name,
                    'district' => $d->district,
                    'phone_number' => $d->phone_number,
                    'petshop_email' => $d->petshop_email,
                    'permit' => $d->permit,
                    'province' => $d->province,
                    'city' => $d->city,
                    'postal_code' => $d->postal_code,
                    'petshop_address' => $d->petshop_address,
                    'status'=>$d->status,
                    'links' => [
                        'self' => '/api/get-petshop/' . $d->id,
                    ],
                ]);
            }
        return response()->json([
            $response
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function getPetshop($id){
        try {
            $data = Petshop::with('user_id:id,name')->find($id);
        
            return response()->json([
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
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
}
