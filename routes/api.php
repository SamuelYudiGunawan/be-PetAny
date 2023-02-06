<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PetController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PetshopController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\Admin\PetCrudController;
use App\Http\Controllers\Admin\PetshopCrudController;
use App\Http\Controllers\API\BookAppoinmentController;

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
// ->middleware('verified');

// PETSHOP
Route::post('/create-petshop', [PetshopController::class, 'create'])->middleware(['auth:sanctum']);
Route::get('/get-petshop', [PetshopController::class, 'getAllPetshop']);
Route::get('/get-petshop/{id}', [PetshopController::class, 'getPetshop']);
Route::get('/petshop-form', [PetshopController::class, 'getPetshopForm']);

//PET
Route::post('/add-pet', [PetController::class, 'addPet'])->middleware(['auth:sanctum']);
Route::get('/get-pet', [PetController::class, 'getAllPet'])->middleware(['auth:sanctum']);
Route::get('/get-pet/{id}', [PetController::class, 'getPet'])->middleware(['auth:sanctum']);
Route::get('/pet-form', [PetController::class, 'getPetForm']);

//MEDICAL RECORD
Route::post('/add-medicalrecord', [PetController::class, 'addMedicalRecord'])->middleware(['auth:sanctum']);
Route::get('/get-medicalrecord', [PetController::class, 'getAllMedicalRecord'])->middleware(['auth:sanctum']);
Route::get('/get-medicalrecord/{id}', [PetController::class, 'getMedicalRecord'])->middleware(['auth:sanctum']);
Route::get('/medicalrecord-form', [PetController::class, 'getMedicalForm']);

//BOOK APPOINMENT
Route::post('/add-bookappoinment', [BookAppoinmentController::class, 'addBookAppoinment'])->middleware(['auth:sanctum']);
Route::get('/get-bookappoinment', [BookAppoinmentController::class, 'getAllBookAppoinment'])->middleware(['auth:sanctum']);
Route::get('/get-bookappoinment/{id}', [BookAppoinmentController::class, 'getBookAppoinment'])->middleware(['auth:sanctum']);
Route::get('/bookappoinment-form', [BookAppoinmentController::class, 'getBookAppoinmentForm']);

//PRODUCT
Route::post('/add-product', [ProductController::class, 'addProduct'])->middleware(['auth:sanctum']);
Route::get('/get-product', [ProductController::class, 'getAllProduct'])->middleware(['auth:sanctum']);
Route::get('/get-product/{id}', [ProductController::class, 'getBProduct'])->middleware(['auth:sanctum']);
Route::get('/product-form', [ProductController::class, 'getProductForm']);