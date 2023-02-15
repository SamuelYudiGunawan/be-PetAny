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

    public function editPet(Request $request, $id) {
        $request->validate([
            'pet_name' => 'string',
            'pet_image' => 'file',
            'age' =>'string',
            'allergies' => 'string',
            'pet_genus' => 'string',
            'pet_species' => 'string',
            'weight' => 'string',
        ]);
    
        $pet = Pet::find($id);
        if (!$pet) {
            return response()->json(['error' => 'Product not found.'], 404);
        }
    
        try {
            if ($request->hasFile('pet_image')) {
                $imageName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('pet_image')) . "." . $request->file('pet_image')->getClientOriginalExtension();
                $imagePath = "storage/document/pet_image/" . $imageName;
                $request->pet_image->storeAs(
                    "public/document/pet_image",
                    $imageName
                );
                $pet->pet_image = url('/').'/'.$imagePath;
            }
    
            $pet::where('id', $id)->update([
                'pet_name' => $request->pet_name,
                'age' => $request->age,
                'allergies' => $request->allergies,
                'pet_genus' => $request->pet_genus,
                'pet_species' => $request->pet_species,
                'weight' => $request->weight,
                'pet_image' => $request->hasFile('pet_image') ? url('/').'/'.$imagePath : $pet->image,
            ]);
    
            return response()->json([
                'message' => 'Pet updated',
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['error' => 'Error updating product.'], 500);
        }
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
            $data = Pet::with('user_id:id,name')->where('user_id', Auth::user()->id)->get();
            $response = [];
            foreach ($data as $d) {
                array_push($response, [
                    'id' => $d->id,
                    'user_id' => $d->user_id,
                    'pet_name' => $d->pet_name,
                    'pet_image' => $d->pet_image,
                    'age' => $d->age,
                    'allergies' => $d->allergies,
                    'pet_genus' => $d->pet_genus,
                    'pet_species' => $d->pet_species,
                    'weight' => $d->weight,
                    'links' => [
                        'self' => '/api/get-pet/' . $d->id,
                    ],
                ]);
            }
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
        
        return response()->json([
            $response
        ]);
    }

    public function getPet($id)
    {
        // try{
        //     $data = Pet::with('user_id:id,name')->find($id);
        // } catch (\Exception $e) {
        // Log::error($e->getMessage());
        // }
        
        // return response()->json([
        //     'data' => $data,
        // ]);
        try{
            $d = Pet::with('user_id:id,name')->find($id)->first();
                    return response()->json([
                    'id' => $d->id,
                    'user_id' => $d->user_id,
                    'pet_name' => $d->pet_name,
                    'pet_image' => $d->pet_image,
                    'age' => $d->age,
                    'allergies' => $d->allergies,
                    'pet_genus' => $d->pet_genus,
                    'pet_species' => $d->pet_species,
                    'weight' => $d->weight,
                    'links' => [
                        'add_medical_record' => 'api/add-medicalrecord?pet_id=' . $d->id, 
                    ],
                ]);
        return response()->json([
            $response
        ]);
            // return $data;
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
    }

    public function deletePet($id) {
        try{
            $pet = Pet::where('id', $id)->first();
            if(!$pet) { 
                return response()->json(['message' => 'Pet not found']); 
            }
            $pet->delete();
            return response()->json(['message' => 'Pet deleted']); 
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
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
            $data = MedicalRecord::with('pet_id:id,pet_name')->get();
            $response = [];
            foreach ($data as $d) {
                array_push($response, [
                    'title' => $d->title,
                    'description' => $d->description,
                    'treatment' => $d->treatment,
                    'date' => $d->date,
                    'attachment' => $d->attachment,
                    'pet_id' => $d->pet_id,
                    'links' => [
                        'self' => '/api/get-medicalrecord/' . $d->id,
                    ],
                ]);
            }
            return response()->json([
                $response
            ]);
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
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

    public function deleteMedicalRecord($id) {
        try{
        $medical_record = MedicalRecord::where('id', $id)->first();
        if(!$medical_record) { 
            return response()->json(['message' => 'Medical record not found']); 
        }
        $medical_record->delete();
        return response()->json(['message' => 'Medical record deleted']); 
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
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
