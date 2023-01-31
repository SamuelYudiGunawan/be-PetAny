<?php

namespace App\Http\Controllers\API;

use App\Models\Pet;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class PetController extends Controller
{
    public function addPet(Request $request){
        $request->validate([
            'pet_name' => 'required|string',
            'pet_image' => 'required|file|mimes:png,jpg',
            'age' => 'required|int',
            'pet_genus' => 'required|string',
            'pet_species' => 'required|string',
            'weight' => 'required|int',
        ]);

        try {

        $imageName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('pet_image')) . "." . $request->file('pet_image')->getClientOriginalExtension();
        $imagePath = "storage/document/pet_image/" . $imageName;
        $request->pet_image->storeAs(
            "public/document/pet_image",
            $imageName
        );

        $pet = Pet::create([
            'user_id' => Auth::user()->id,
            'pet_name' => $request->pet_name,
            'pet_image' => url('/').'/'.$imagePath,
            'age' => $request->age,
            'allergies' => $request->allergies,
            'pet_genus' => $request->pet_genus,
            'pet_species' => $request->pet_species,
            'weight' => $request->weight,
        ]);
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }

        return response()->json([
            'data' => $pet,
        ]);
    

    }

    public function getPetForm()
    {
        return [
            [
                'name' => 'pet_name',
                'type' => 'text',
                'label' => 'Nama Peliharaan',
                'required' => true,
            ],
            [
                'name' => 'age',
                'type' => 'number',
                'label' => 'Usia',
                'required' => true,
            ],
            [
                'name' => 'allergies',
                'type' => 'text',
                'label' => 'Alergi',
                'required' => true,
            ],
            [
                'name' => 'pet_genus',
                'type' => 'dropdown',
                'label' => 'Jenis Hewan',
                'required' => true,
            ],
            [
                'name' => 'pet_species',
                'type' => 'dropdown ',
                'label' => 'Ras',
                'required' => true,
            ],
            [
                'name' => 'weight',
                'type' => 'number',
                'label' => 'Berat Badan',
                'required' => true,
            ],
        ];
    }

    public function getAllPet(){
        try{
            $data = Pet::with('user_id:id,name')->get();
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
        
        return response()->json([
            'data' => $data,
        ]);
    }

    public function getPet($id)
    {
        try{
            $data = Pet::with('user_id:id,name')->find($id);
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
        
        return response()->json([
            'data' => $data,
        ]);
    }

    public function addMedicalRecord(Request $request){
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'treatment' => 'required|string',
            'date' => 'required|date',
            'attachment' => 'required|file',
            'pet_id' => 'required|string'
        ]);

        try{
        $fileName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('attachment')) . "." . $request->file('attachment')->getClientOriginalExtension();
        $filePath = "storage/document/attachment/" . $fileName;
        $request->attachment->storeAs(
            "public/document/attachment",
            $fileName
        );


        $medical_record = MedicalRecord::create([
            'title' => $request->title,
            'description' => $request->description,
            'treatment' => $request->treatment,
            'date' => $request->date,
            'attachment' => url('/').'/'.$filePath,
            'pet_id' => $request->pet_id,
        ]);

        
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }

        return response()->json([
            'data' => $medical_record,
        ]);
    }
    protected function getAllMedicalRecord(){
        try{
            // $data = MedicalRecord::all();
            $data = MedicalRecord::with('pet_id:id,pet_name')->get();
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
        
        return response()->json([
            'data' => $data,
        ]);
    }

    public function getMedicalRecord($id)
    {
        try{
            // $data = MedicalRecord::find($id);
            $data = MedicalRecord::with('pet_id:id,pet_name')->find($id);
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
        
        return response()->json([
            'data' => $data,
        ]);
    }

    public function getMedicalForm()
    {
        return [
            [
                'name' => 'title',
                'type' => 'text',
                'label' => 'Judul',
                'required' => true,
            ],
            [
                'name' => 'description',
                'type' => 'text',
                'label' => 'Deskripsi',
                'required' => true,
            ],
            [
                'name' => 'treatment',
                'type' => 'text',
                'label' => 'Pengobatan',
                'required' => true,
            ],
            [
                'name' => 'date',
                'type' => 'date_picker',
                'label' => 'Tanggal',
                'required' => true,
            ],
            [
                'name' => 'attachment',
                'type' => 'file',
                'label' => 'Lampiran',
                'required' => true,
            ],
        ];
    }
}
