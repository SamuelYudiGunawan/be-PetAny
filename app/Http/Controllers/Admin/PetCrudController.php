<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Requests\PetRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PetCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PetCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Pet::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/pet');
        CRUD::setEntityNameStrings('pet', 'pets');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('pet_name');
        CRUD::column('age');
        CRUD::column('allergies');
        CRUD::column('pet_genus');
        CRUD::column('pet_species');
        CRUD::column('weight');
        CRUD::column('created_at');
        CRUD::column('updated_at');

        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PetRequest::class);

        CRUD::column('pet_name');
        CRUD::column('age');
        CRUD::column('allergies');
        CRUD::column('pet_genus');
        CRUD::column('pet_species');
        CRUD::column('weight');

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function addPet(Request $request){
        $request->validate([
            'pet_name' => 'required|string',
            'age' => 'required|int',
            'pet_genus' => 'required|string',
            'pet_species' => 'required|string',
            'weight' => 'required|int',
        ]);

        $pet = Pet::create([
            'pet_name' => $request->pet_name,
            'age' => $request->age,
            'allergies' => $request->allergies,
            'pet_genus' => $request->pet_genus,
            'pet_species' => $request->pet_species,
            'weight' => $request->weight,
        ]);

        return response()->json([
            'data' => $pet,
        ]);
    }
    protected function getAllPet(){
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
}
