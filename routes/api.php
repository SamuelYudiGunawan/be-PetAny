<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\PetshopCrudController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
    

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// AUTH
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

// PETSHOP
Route::post('/create-petshop', [PetshopCrudController::class, 'create'])->middleware(['auth:sanctum']);
Route::get('/get-petshop', [PetshopCrudController::class, 'getAllPetshop']);
Route::get('/get-petshop/{id}', [PetshopCrudController::class, 'getPetshop']);
