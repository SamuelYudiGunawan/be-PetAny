<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use App\Http\Controllers\API\PetController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\StaffController;
use App\Http\Controllers\API\PetshopController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\Admin\PetCrudController;
use App\Http\Controllers\Admin\PetshopCrudController;
use App\Http\Controllers\API\BookAppoinmentController;
use App\Http\Controllers\API\JamOperasionalController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\API\JamOperasionalDokterController;

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
Route::post('/logout', [UserController::class, 'logout'])->middleware(['auth:sanctum']);
// ->middleware('verified');
    // VERIFY EMAIL
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
    })->middleware(['auth:sanctum', 'signed'])->name('verification.verify');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification link sent!']);
    })->middleware(['auth:sanctum', 'throttle:6,1'])->name('verification.send');
        // // RESET PASSWORD
        // // SENDING EMAIL
        // Route::get('/forgot-password', function () {
        //     return view('auth.forgot-password');
        // })->middleware('guest')->name('password.request');
        // Route::post('/forgot-password', function (Request $request) {
        //     $request->validate(['email' => 'required|email']);
        //     $status = Password::sendResetLink(
        //         $request->only('email')
        //     );
        //     return $status === Password::RESET_LINK_SENT
        //                 ? back()->with(['status' => __($status)])
        //                 : back()->withErrors(['email' => __($status)]);
        // })->middleware('guest')->name('password.email');
        // Route::get('/reset-password/{token}', function ($token) {
        //     return view('auth.reset-password', ['token' => $token]);
        // })->middleware('guest')->name('password.reset');
// PETSHOP
Route::post('/create-petshop', [PetshopController::class, 'create'])->middleware(['auth:sanctum']);
Route::post('/update-petshop/{id}', [PetshopController::class, 'updatePetshopProfile'])->middleware(['auth:sanctum']);
Route::get('/get-petshop', [PetshopController::class, 'getAllPetshop']);
Route::get('/get-petshop-with-schedule', [PetshopController::class, 'getPetshopWithStaffAndSchedule']);
Route::get('/get-petshop/{id}', [PetshopController::class, 'getPetshop']);
Route::get('/petshop-form', [PetshopController::class, 'getPetshopForm']);
    // MANAGE STAFF
    Route::post('/add-staff', [StaffController::class, 'addStaff'])->middleware(['auth:sanctum']);
    Route::delete('/remove-role', [StaffController::class, 'removeRole'])->middleware(['auth:sanctum']);
    Route::delete('/remove-staff', [StaffController::class, 'removePetshopStaff'])->middleware(['auth:sanctum']);
    Route::get('/get-doctors/{petshop_id}', [StaffController::class, 'getPetshopDoctors'])->middleware(['auth:sanctum']);
    Route::get('/get-staffs/{petshop_id}', [StaffController::class, 'getPetshopStaffs'])->middleware(['auth:sanctum']);
        //JAM OPERASIONAL PETSHOP
        Route::post('/petshop/create-jam-operasional/{id}', [JamOperasionalController::class, 'createJamOperasional'])->middleware(['auth:sanctum']);
        Route::get('/petshop/get-jam-operasional-data/{id}', [JamOperasionalController::class, 'getJamOperasionalData'])->middleware(['auth:sanctum']);
        Route::get('/petshop/get-jam-operasional/{id}', [JamOperasionalController::class, 'getJamOperasional'])->middleware(['auth:sanctum']);
        //JAM OPERASIONAL DOKTER
        Route::post('/dokter/create-jam-operasional/{id}', [JamOperasionalDokterController::class, 'createJamOperasionalDokter'])->middleware(['auth:sanctum']);
        Route::get('/dokter/get-jam-operasional-data/{id}', [JamOperasionalDokterController::class, 'getJamOperasionalDataDokter'])->middleware(['auth:sanctum']);
        Route::get('/dokter/get-jam-operasional/{id}', [JamOperasionalDokterController::class, 'getJamOperasionalDokter'])->middleware(['auth:sanctum']);
        Route::get('/dokter/jam-operasional/{id}', [JamOperasionalDokterController::class, 'getJamOperasionalMingguan']);
//PET
Route::post('/add-pet', [PetController::class, 'addPet'])->middleware(['auth:sanctum']);
Route::delete('/delete-pet/{id}', [PetController::class, 'deletePet'])->middleware(['auth:sanctum']);
Route::post('/edit-pet/{id}', [PetController::class, 'editPet'])->middleware(['auth:sanctum']);
Route::get('/get-pet', [PetController::class, 'getAllPet'])->middleware(['auth:sanctum']);
Route::get('/get-pet/{id}', [PetController::class, 'getPet'])->middleware(['auth:sanctum']);
Route::get('/pet-form', [PetController::class, 'getPetForm']);

//MEDICAL RECORD
Route::post('/add-medicalrecord', [PetController::class, 'addMedicalRecord'])->middleware(['auth:sanctum']);
Route::post('/edit-medicalrecord/{id}', [PetController::class, 'editMedicalRecord'])->middleware(['auth:sanctum']);
Route::get('/get-medicalrecord', [PetController::class, 'getAllMedicalRecord'])->middleware(['auth:sanctum']);
Route::get('/get-medicalrecord/{id}', [PetController::class, 'getMedicalRecord'])->middleware(['auth:sanctum']);
Route::delete('/delete-medicalrecord/{id}', [PetController::class, 'deleteMedicalRecord'])->middleware(['auth:sanctum']);
Route::get('/medicalrecord-form', [PetController::class, 'getMedicalForm']);

//BOOK APPOINMENT
Route::post('/add-bookappoinment', [BookAppoinmentController::class, 'addBookAppoinment'])->middleware(['auth:sanctum']);
Route::post('/accept-bookappoinment/{id}', [BookAppoinmentController::class, 'acceptBookAppoinment'])->middleware(['auth:sanctum']);
Route::post('/reject-bookappoinment/{id}', [BookAppoinmentController::class, 'rejectBookAppoinment'])->middleware(['auth:sanctum']);
Route::post('/finish-bookappoinment/{id}', [BookAppoinmentController::class, 'finishBookAppoinment'])->middleware(['auth:sanctum']);
Route::get('/get-bookappoinment', [BookAppoinmentController::class, 'getAllBookAppoinment'])->middleware(['auth:sanctum']);
Route::get('/get-bookappoinment/{id}', [BookAppoinmentController::class, 'getBookAppoinment'])->middleware(['auth:sanctum']);
Route::get('/bookappoinment-form', [BookAppoinmentController::class, 'getBookAppoinmentForm']);

//PRODUCT
Route::post('/add-product', [ProductController::class, 'addProduct'])->middleware(['auth:sanctum']);
Route::get('/get-product', [ProductController::class, 'getAllProduct'])->middleware(['auth:sanctum']);
Route::get('/get-product/{id}', [ProductController::class, 'getProduct'])->middleware(['auth:sanctum']);
Route::delete('/delete-product/{id}', [ProductController::class, 'deleteProduct'])->middleware(['auth:sanctum']);
Route::post('/edit-product/{id}', [ProductController::class, 'editProduct'])->middleware(['auth:sanctum']);
Route::get('/product-form', [ProductController::class, 'getProductForm']);

//ORDER
Route::post('/create-order', [OrderController::class, 'store'])->middleware('auth:sanctum');
Route::post('/midtrans/handle-notification', [OrderController::class, 'handleMidtransNotification']);