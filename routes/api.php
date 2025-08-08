<?php

use App\Http\Controllers\API\Appointment\AppointmentController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\Billing\BillingController;
use App\Http\Controllers\API\Doctor\DoctorController;
use App\Http\Controllers\API\Medicine\MedicineController;
use App\Http\Controllers\API\PatientController;
use App\Http\Controllers\API\Prescription\PrescriptionController;
use App\Http\Controllers\Specialization\SpecializationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
//
//Route::post('/login', [AuthController::class, 'login'])->name('login');
//
//Route::middleware('auth:api')->group(function () {
//    Route::get('/profile', [AuthController::class, 'profile']);
//    Route::post('/logout', [AuthController::class, 'logout']);
//
//});
//
//Route::middleware(['auth:api', 'role:admin'])->group(function () {
//    Route::apiResource('patients', PatientController::class);
//    Route::apiResource('doctors', DoctorController::class);
//    Route::apiResource('medicines', MedicineController::class);
//
//    // Admin-only appointment routes
//    Route::get('appointments', [AppointmentController::class, 'index']);
//    Route::get('appointments/{appointment}', [AppointmentController::class, 'show']);
//    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
//    Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy']);
//});
//
//Route::middleware(['auth:api'])->group(function () {
//    Route::post('appointments', [AppointmentController::class, 'store']);
//    Route::get('appointments', [AppointmentController::class, 'index']);
//
//});
//
//Route::middleware(['auth:api'])->get('/test-auth', function () {
//    return [
//        'user' => auth()->user(),
//        'roles' => auth()->user()->getRoleNames()
//    ];
//});



//Route::middleware('auth:api')->group(function () {
//    Route::apiResource('patients', PatientController::class);
//});

//Route::apiResource('patients', PatientController::class);

//Route::post('/login', [AuthController::class, 'login'])->name('login');
//
//Route::middleware('auth:api')->group(function () {
//    Route::post('/logout', [AuthController::class, 'logout']);
//    Route::get('/profile', [AuthController::class, 'profile']);
//
//    Route::get('appointments', [AppointmentController::class, 'index']);
//
//    Route::middleware('role:patient')->post('appointments', [AppointmentController::class, 'store']);
//
//    Route::middleware('role:admin|doctor')->patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
//
//    Route::get('appointments/{appointment}', [AppointmentController::class, 'show']);
//});
//
//Route::middleware(['auth:api', 'role:admin'])->group(function () {
//    Route::apiResource('patients', PatientController::class);
//    //Route::get('patients/filter', [PatientController::class, 'index']);
//    Route::apiResource('doctors', DoctorController::class);
//    Route::get('medicines/export', [MedicineController::class, 'export']);
//    Route::apiResource('medicines', MedicineController::class);
//    Route::get('billing/pending', [BillingController::class, 'pendingAppointments']);
//    Route::post('billing/appointments/{appointment}', [BillingController::class, 'store']);
//    Route::get('billing/appointments/{appointment}/bill/export', [BillingController::class, 'exportBill']);
//    Route::get('billing/appointments/{appointment}/prescription/export', [BillingController::class, 'exportPrescription']);
//
//
//    Route::delete('appointments/{appointment}', [AppointmentController::class, 'destroy']);
//});
//
//Route::middleware(['auth:api', 'role:doctor'])->group(function () {
//    Route::post('/prescriptions', [PrescriptionController::class, 'store']);
//    Route::get('/appointments/{appointment}/prescription', [PrescriptionController::class, 'show']);
//});

/*
|--------------------------------------------------------------------------
| Public Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register']);



/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    /*
    Patient Routes
    */
    Route::prefix('patient')->middleware('role:patient')->group(function () {
        Route::post('/appointments', [AppointmentController::class, 'store']);
    });

    /*
    Doctor Routes
    */
    Route::prefix('doctor')->middleware('role:doctor')->group(function () {
        Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
        Route::post('/prescriptions', [PrescriptionController::class, 'store']);
        Route::get('/prescription/appointments/{appointment}', [PrescriptionController::class, 'show']);

    });

    /*
    Admin Routes
    */
    Route::prefix('admin')->middleware('role:admin')->group(function () {

        // Patients
        Route::get('/patients', [PatientController::class, 'index']);
        Route::post('/patients', [PatientController::class, 'store']);
        Route::get('/patients/{patient}', [PatientController::class, 'show']);
        Route::put('/patients/{patient}', [PatientController::class, 'update']);
        Route::delete('/patients/{patient}', [PatientController::class, 'destroy']);

        // Doctors
        Route::get('/doctors', [DoctorController::class, 'index']);
        Route::post('/doctors', [DoctorController::class, 'store']);
        Route::get('/doctors/{doctor}', [DoctorController::class, 'show']);
        Route::put('/doctors/{doctor}', [DoctorController::class, 'update']);
        Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy']);
        Route::get('/specializations', [SpecializationController::class, 'index']);


        // Medicines
        Route::get('/medicines/export', [MedicineController::class, 'export']);
        Route::get('/medicines', [MedicineController::class, 'index']);
        Route::post('/medicines', [MedicineController::class, 'store']);
        Route::get('/medicines/{medicine}', [MedicineController::class, 'show']);
        Route::put('/medicines/{medicine}', [MedicineController::class, 'update']);
        Route::delete('/medicines/{medicine}', [MedicineController::class, 'destroy']);

        // Billing
        Route::get('/billing/pending', [BillingController::class, 'pendingAppointments']);
        Route::post('/billing/appointments/{appointment}', [BillingController::class, 'store']);
        Route::get('/billing/appointments/{appointment}/bill/export', [BillingController::class, 'exportBill']);
        Route::get('/billing/appointments/{appointment}/prescription/export', [BillingController::class, 'exportPrescription']);

        // Appointment deletion only admin can
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy']);
    });

    /*
    Shared Routes
    */
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);


});

Route::any('{any}', function () {
    return \App\Helpers\ResponseHelper::error("api not found please check your method.",404);
})->where('any', '.*');

