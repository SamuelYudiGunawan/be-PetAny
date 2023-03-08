<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Staff;
use App\Models\Petshop;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use App\Models\JamOperasional;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Backpack\PermissionManager\app\Models\Role;

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
            // 'petshop_image' => 'required|file|mimes:png,jpg',
            'company_name' => 'required|string|unique:petshops',
            'phone_number' => 'required|string',
            'petshop_email' => 'required|email|string|unique:petshops',
            'permit' => 'required|array',
            'permit.*' => 'required|file',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'postal_code' => 'required|string',
            'petshop_address' => 'required|string',
        ]);

        try{
        $petshop_image = null;
        $imagePath = null;
        $filePaths = [];
        foreach($request->file('permit') as $file){
            $fileName = Carbon::now()->format('YmdHis') . "_" . md5_file($file) . "." . $file->getClientOriginalExtension();
            $filePath = "storage/document/permit/" . $fileName;
            $file->storeAs(
                "public/document/permit",
                $fileName
            );
            array_push($filePaths, url('/').'/'.$filePath);
        }

        $petshop = Petshop::create([
            'petshop_name' => $request->petshop_name,
            'petshop_image' => $petshop_image,
            'company_name' => $request->company_name,
            'district' => $request->district,
            'phone_number' => $request->phone_number,
            'petshop_email' => $request->petshop_email,
            'permit' => json_encode($filePaths),
            'province' => $request->province,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'petshop_address' => $request->petshop_address,
            'user_id' => Auth::user()->id,
        ]);

        
        Auth::user()->assignRole(['petshop_staff', 'petshop_owner']);
        $user = Auth::user();
        // Auth::user()->update([
        //     'petshop_id' => $petshop->id,
        // ]);
        $user->petshop_id = $petshop->id;
        $user->save();
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

    public function updatePetshopProfile(Request $request, $id)
    {
        // Validate the request data
        // $request->validate([
        //     'petshop_name' => 'nullable|string',
        //     'petshop_image' => 'nullable|file',
        //     'description' => 'nullable|string',
        //     'website' => 'nullable|url',
        //     'category' => 'nullable|array',
        //     'category.*' => 'string|in:grooming,klinik,laboratorium,rawat inap,petshop'
        // ]);

        try {
            $petshop = Petshop::findOrFail($id);
            $petshop_image = null;
            if ($request->hasFile('petshop_image')) {
                $imageName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('petshop_image')) . "." . $request->file('petshop_image')->getClientOriginalExtension();
                $imagePath = "storage/document/petshop_image/" . $imageName;
                $request->petshop_image->storeAs(
                    "public/document/petshop_image",
                    $imageName
                );
                $petshop_image = url('/').'/'.$imagePath;
                $petshop->petshop_image = $petshop_image;
                $petshop->save();
            }
            // Get the petshop record based on the provided ID, or create a new one if it doesn't exist
            $petshop->update([
                'petshop_name' => $request->petshop_name,
                // 'petshop_image' => $petshop_image,
                'description' => $request->description,
                'website' => $request->website,
                'category' => $request->category,
            ]);

            // Return a response indicating that the update or create was successful
            return response()->json([
                'message' => 'Petshop record updated',
                'data' => $petshop
            ], 200);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function getAllPetshop()
    {
        try {
            // $data = Petshop::with(['user_id:id,name', 'jamOperasional'])->get();
            $subquery = DB::table('jam_operasionals')
                ->select('petshop_id', DB::raw('MAX(jam_buka) AS jam_buka'), DB::raw('MAX(jam_tutup) AS jam_tutup'), DB::raw('MAX(hari_buka) AS hari_buka'))
                ->where('hari_buka', Carbon::now()->locale('id')->translatedFormat('l'))
                ->groupBy('petshop_id');

            $data = Petshop::leftJoinSub($subquery, 'jam_operasionals', function($join) {
                    $join->on('petshops.id', '=', 'jam_operasionals.petshop_id');
                })
                ->select('petshops.*', DB::raw('IFNULL(DATE_FORMAT(jam_operasionals.jam_buka,"%H:%i"), null) AS jam_buka'), DB::raw('IFNULL(DATE_FORMAT(jam_operasionals.jam_tutup,"%H:%i"), null) AS jam_tutup'),  DB::raw('IFNULL(jam_operasionals.hari_buka, null) AS hari_buka'))
                ->get();
            $response = [];
            // $isOpen = false;
            foreach ($data as $d) {
                // // $isOpen = false;
                // if ($d->hari_buka) {
                //     $openTime = $d->jam_buka;
                //     $closeTime = $d->jam_tutup;
                //     $currentTime = Carbon::now()->setTimezone('Asia/Jakarta');
                //     // $currentTime = $now->format('H:i');
                //     // $currentTime->format('H:i');
                //     if ($currentTime->between($openTime, $closeTime) && $d->is_open) {
                //         $isOpen = true;
                //     }
                // }
                // // dd($currentTime, $openTime, $closeTime, $isOpen);
                array_push($response, [
                    'petshop_name' => $d->petshop_name,
                    'user_id' => $d->user_id,
                    'petshop_image' => $d->petshop_image,
                    'company_name' => $d->company_name,
                    'district' => $d->district,
                    'phone_number' => $d->phone_number,
                    'petshop_email' => $d->petshop_email,
                    'permit.*' => $d->permit,
                    'province' => $d->province,
                    'city' => $d->city,
                    'postal_code' => $d->postal_code,
                    'petshop_address' => $d->petshop_address,
                    'status' => $d->status,
                    'website' => $d->website,
                    'description' => $d->description,
                    'category' => json_decode($d->category),
                    // 'is_open' => $isOpen,
                    'hari' => $d->hari_buka,
                    'jam_buka' => $d->jam_buka,
                    'jam_tutup' => $d->jam_tutup,
                    'links' => [
                        'self' => '/api/get-petshop/' . $d->id,
                    ],
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


    public function getPetshop($id){
        try {
            $d = Petshop::with('user_id:id,name')->find($id);
            $store = null;
            if ($hari = Carbon::now()->locale('id')->translatedFormat('l')) {
                $store = JamOperasional::where('hari_buka', $hari)->first();
            }
            
            $openTime = null;
            $closeTime = null;
            if ($store) {
                $openTime = Carbon::parse($store->jam_buka)->format('H:i');
                $closeTime = Carbon::parse($store->jam_tutup)->format('H:i');
            }
            return response()->json([
                'data' => [
                    'petshop_name' => $d->petshop_name,
                    'user_id' => $d->user_id,
                    'petshop_image' => $d->petshop_image,
                    'company_name' => $d->company_name,
                    'district' => $d->district,
                    'phone_number' => $d->phone_number,
                    'petshop_email' => $d->petshop_email,
                    'permit.*' => $d->permit,
                    'province' => $d->province,
                    'city' => $d->city,
                    'postal_code' => $d->postal_code,
                    'petshop_address' => $d->petshop_address,
                    'status' => $d->status,
                    'website' => $d->website,
                    'description' => $d->description,
                    'category' => [json_decode($d->category)],
                    // 'is_open' => $isOpen,
                    'hari' => $store ? $store->hari_buka : null,
                    'jam_buka' => $openTime,
                    'jam_tutup' => $closeTime,
                ],
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
    public function editPetshopForm()
    {
        return [
            [
                'name' => 'petshop_name',
                'type' => 'text',
                'label' => 'Nama Klinik',
                'required' => false,
            ],
            [
                'name' => 'description',
                'type' => 'text',
                'label' => 'Deskripsi Toko',
                'required' => false,
            ],
                        [
                'name' => 'webstite',
                'type' => 'text',
                'label' => 'Link Website Toko',
                'required' => false,
            ],
            [
                'name' => 'category',
                'type' => 'array',
                'label' => 'Layanan Kami',
                'required' => false,
            ],
            [
                'name' => 'petshop_image',
                'type' => 'file',
                'label' => 'Petshop Image',
                'required' => false,
            ],
            // [
            //     'name' => 'phone_number',
            //     'type' => 'number',
            //     'label' => 'Nomor Telepon',
            //     'required' => false,
            // ],
        ];
    }
}
