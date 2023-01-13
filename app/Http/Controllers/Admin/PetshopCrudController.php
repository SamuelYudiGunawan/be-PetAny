<?php

namespace App\Http\Controllers\Admin;

use App\Models\Petshop;
use Illuminate\Http\Request;
use App\Http\Requests\PetshopRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class PetshopCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PetshopCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Petshop::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/petshop');
        CRUD::setEntityNameStrings('petshop', 'petshops');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('petshop_name');
        CRUD::column('company_name');
        CRUD::column('owner');
        CRUD::column('phone_number');
        CRUD::column('petshop_email');
        CRUD::column('permit');
        CRUD::column('province');
        CRUD::column('city');
        CRUD::column('district');
        CRUD::column('postal_code');
        CRUD::column('petshop_address');

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
        CRUD::setValidation(PetshopRequest::class);

        CRUD::field('petshop_name');
        CRUD::field('company_name');
        CRUD::field('owner');
        CRUD::field('phone_number');
        CRUD::field('petshop_email');
        CRUD::field('permit');
        CRUD::field('province');
        CRUD::field('city');
        CRUD::field('district');
        CRUD::field('postal_code');
        CRUD::field('petshop_address');

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

    protected function getAllPetshop(){
        return Petshop::all();
    }

    public function getPetshop($id)
    {
        return Petshop::find($id);
    }

    protected function create(Request $request){
        $request->validate([
            'petshop_name' => 'required|string|unique:petshops',
            'company_name' => 'required|string|unique:petshops',
            'owner' => 'required|string',
            'phone_number' => 'required|string',
            'petshop_email' => 'required|email|string|unique:petshops',
            'permit' => 'required|file|mimes:pdf',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'postal_code' => 'required|string',
            'petshop_address' => 'required|string',
        ]);

        $petshop = Petshop::create([
            'petshop_name' => $request->petshop_name,
            'company_name' => $request->company_name,
            'owner' => $request->owner,
            'district' => $request->district,
            'phone_number' => $request->phone_number,
            'petshop_email' => $request->petshop_email,
            'permit' => $request->permit,
            'province' => $request->province,
            'city' => $request->city,
            'postal_code' => $request->postal_code,
            'petshop_address' => $request->petshop_address,
        ]);

        return response()->json([
            'data' => $petshop,
        ]);
    }
}
