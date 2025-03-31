<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Back\DistanceRateController;
use App\Http\Controllers\Back\FuelPrice91Controller;
use App\Http\Controllers\Back\FuelPriceController;
use App\Http\Controllers\Back\GrouppriceController;
use App\Http\Controllers\Back\GroupspecialController;
use App\Http\Controllers\Back\HRController;
use App\Http\Controllers\Back\ImportlistController;
use App\Http\Controllers\Back\PricepermealController;
use App\Http\Controllers\Back\RoleController;
use App\Http\Controllers\Back\TypegroupController;
use App\Http\Controllers\Back\UserController;
use App\Http\Controllers\Back\UserroleController;
use App\Http\Controllers\DistanceController;
use App\Http\Controllers\Front\DriverClaimController;
use App\Http\Controllers\Front\ExpenseController;
use App\Http\Controllers\Front\HeadApprovedController;
use App\Http\Controllers\Front\ProfileController;
use App\Http\Controllers\Front\TechClaimController;
use App\Http\Controllers\MapController;
use App\Models\Role;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

// Route::get('/', function () {
//     return view('welcome');
// });

// Auth
Route::get('/', [AuthController::class, 'index'])->name('login');
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/CheckEmpID', [AuthController::class, 'CheckEmpID'])->name('CheckEmpID');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
// End Atuh

Route::group(['middleware' => ['auth', 'remember.login']], function () {
    Route::get('/secure-js/{filename}', function (Request $request, $filename) {
        $path = storage_path("app/private/{$filename}");

        if (!file_exists($path)) {
            abort(404);
        }

        // Check if the URL has a valid signature
        if (!$request->hasValidSignature()) {
            abort(403, "Unauthorized Access");
        }

        return response()->file($path, [
            'Content-Type' => 'application/javascript',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    })->where('filename', '.*')->name('secure.js');
});
Route::group(['middleware' => ['auth', 'remember.login']], function () {

    // Frontend
    // Profile
    Route::get('Profile',[ProfileController::class,'index'])->name('profile.index');
    Route::get('Profile/Reset',[ProfileController::class,'resetPassword'])->name('profile.reset');
    Route::put('Profile/{id}',[ProfileController::class,'updatePassword'])->name('profile.updatepassword');
    // Route::get('/distance', [DistanceController::class, 'showForm']);
    // Route::post('/distance', [DistanceController::class, 'calculateDistance']);

    Route::get('/map', [MapController::class, 'index']);
    Route::post('/calculate-distance', [MapController::class, 'calculateDistance'])->name('calculate.distance');
    // Booker
    // Route::resource('Expense', ExpenseController::class);
    Route::get('Expense', [ExpenseController::class, 'index'])->name('Expense.index');
    Route::get('Expense/{id}/create', [ExpenseController::class, 'create'])->name('Expense.create');
    // HeadApprove
    // Route::resource('HeadApprove', HeadApprovedController::class);
    Route::get('HeadApprove', [HeadApprovedController::class, 'index'])->name('HeadApprove.index');

    // DriverClaim
    // Route::resource('DriverClaim', DriverClaimController::class);
    Route::get('DriverClaim', [DriverClaimController::class, 'index'])->name('DriverClaim.index');

    // TechClaim
    Route::get('TechClaim', [TechClaimController::class, 'index'])->name('TechClaim.index');
    // EndFrontend
});

Route::group(['middleware' => ['auth', 'remember.login']], function () {
    // Backend
    // HR
    // Route::resource('HR', HRController::class);
    Route::get('HR', [HRController::class, 'index'])->name('HR.index');
    Route::get('HR/edit/{id}', [HRController::class, 'edit'])->name('HR.edit');
    // Setting
    // User
    Route::get('User', [UserController::class, 'index'])->name('User.index');
    Route::put('User/{id}/reset',[UserController::class,'ResetPassword'])->name('User.reset');
    Route::resource('User', UserController::class);
    Route::get('Role/create/{type}', [RoleController::class, 'create'])->name('Role.create');
    Route::get('Role/{Role}/edit/{type?}', [RoleController::class, 'edit'])->name('Role.edit');
    Route::delete('Role/{id}/{type}', [RoleController::class, 'destroy'])->name('Role.destroy');
    Route::resource('Role', RoleController::class);
    // 91
    Route::resource('FuelPrice91', FuelPrice91Controller::class);
    Route::get('FuelPrice91', [FuelPrice91Controller::class, 'index'])->name('FuelPrice91.index');
    // Pricepermeal
    Route::get('Pricepermeal/list', [PricepermealController::class, 'list'])->name('Pricepermeal.list');
    Route::resource('Pricepermeal', PricepermealController::class);
    Route::resource('Groupprice', GrouppriceController::class);
    // FuelPrice
    Route::resource('FuelPrice', FuelPriceController::class);
    Route::resource('UserRole', UserroleController::class);

    // DistanceRate
    Route::resource('DistanceRate',DistanceRateController::class);

    // Import
    Route::get('ImportList', [ImportlistController::class, 'index'])->name('importlist.index');
    Route::get('ImportList/import', [ImportlistController::class, 'importuser'])->name('importlist.import');
    Route::post('ImportList/Excel', [ImportlistController::class, 'importexcel'])->name('importlist.excel');

    Route::get('/download-sample', function () {
        $filePath = storage_path('app/public/files/eximport_groupspecial.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'Sample file not found.');
        }

        return Response::download($filePath, 'eximport_expensegroup.xlsx');
    })->name('download.sample');

    Route::resource('Typegroup', TypegroupController::class);

    // EndBackend
    // Map

});



// Update API น้ำมัน
