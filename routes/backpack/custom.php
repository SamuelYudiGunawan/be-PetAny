<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('petshop', 'PetshopCrudController');
    Route::crud('book-appoinment', 'BookAppoinmentCrudController');
    Route::crud('user', 'UserCrudController');
    Route::crud('pet', 'PetCrudController');
    Route::crud('product', 'ProductCrudController');

    //REGISTER PETSHOP STATUS
    Route::post('get_petshop_list', 'PetshopCrudController@getPetshopList');
    Route::post('accept_petshop', 'PetshopCrudController@acceptPetshop');
    Route::post('reject_petshop', 'PetshopCrudController@rejectPetshop');
}); // this should be the absolute last line of this file