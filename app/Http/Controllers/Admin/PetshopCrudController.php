<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Petshop;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
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
        CRUD::setListView('backpack::crud.petshop_list');   
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::column('petshop_name');
        // CRUD::column('company_name');
        // CRUD::column('owner');
        // CRUD::column('phone_number');
        // CRUD::column('petshop_email');
        // CRUD::column('permit');
        // CRUD::column('province');
        // CRUD::column('city');
        // CRUD::column('district');
        // CRUD::column('postal_code');
        // CRUD::column('petshop_address');

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

    public function getPetshopList(Request $request)
    {
        $data = [];
        try{
            $status = $request->status;
            
            $petshop = Petshop::with('user_id:id,name');

            if ($status == 'pending') {
                $petshop = $petshop->status('pending')->latest();
            } else if($status == 'accepted') {
                $petshop =  $petshop->status('accepted')->latest();
            } else if($status == 'rejected') {
                $petshop =  $petshop->status('rejected')->latest();
            }

            return $petshop->get();
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function acceptPetshop($id){
        try {

        $petshop = Petshop::find($id)->first();
        $petshop->update([
            'status' => 'accepted',
        ]);

        $user = User::where('id', $petshop->user_id)->first();
        $user->assignRole('petshop_staff');
        return response()->json([
            'message' => 'Petshop Approved',
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function rejectPetshop($id){
        try {
        $Petshop = Petshop::find($id)->update([
            'status' => 'rejected',
        ]);
        return response()->json([
            'message' => 'Petshop Rejected',
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
}
