<?php

namespace App\Http\Controllers\API;

use App\Models\Pet;
use Illuminate\Http\Request;
use App\Models\MedicalRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;


class PetController extends Controller
{
    public function addPet(Request $request){
        $request->validate([
            'pet_name' => 'required|string',
            'age' => 'required|int',
            'pet_genus' => 'required|string',
            'pet_species' => 'required|string',
            'weight' => 'required|int',
        ]);

        try {
        $pet = Pet::create([
            'pet_name' => $request->pet_name,
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
        $data = Pet::all();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function getPet($id)
    {
        $data =  Pet::find($id);
        
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
        ]);

        $pet = MedicalRecord::create([
            'title' => $request->title,
            'description' => $request->description,
            'treatment' => $request->treatment,
            'date' => $request->date,
            'attachment' => $request->attachment,
        ]);

        $fileName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->attachment) . "." . $request->attachment->getClientOriginalExtension();
            $filePath = "storage/document/document/attachment/" . $fileName;
            $request->attachment->storeAs(
                "public/document/document/attachment",
                $fileName
            );

        return response()->json([
            'data' => $pet,
        ]);
    }
    protected function getAllMedicalRecord(){
        $data = MedicalRecord::all();

        return response()->json([
            'data' => $data,
        ]);
    }

    public function getMedicalRecord($id)
    {
        $data =  MedicalRecord::find($id);
        
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
