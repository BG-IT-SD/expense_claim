<?php

use App\Http\Controllers\ApproveController;
use App\Http\Controllers\ApproveLoginController;
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

    // Profile
    Route::get('Profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('Profile/Reset', [ProfileController::class, 'resetPassword'])
        ->name('profile.reset');

    Route::put('Profile/{id}', [ProfileController::class, 'updatePassword'])
        ->name('profile.updatepassword');

    // Map
    Route::get('/map', [MapController::class, 'index']);
    Route::post('/calculate-distance', [MapController::class, 'calculateDistance'])->name('calculate.distance');
    // Map

    // Expense
    Route::get('Expense', [ExpenseController::class, 'index'])
        ->name('Expense.index');

    Route::get('Expense/{id}/create', [ExpenseController::class, 'create'])
        ->name('Expense.create');

    Route::get('/Expense/Heademp', [ExpenseController::class, 'Heademp']);

    Route::get('/Expense/GetAllHeadEmp', [ExpenseController::class, 'getAllHeadEmp'])
        ->middleware('check.module.access:Expense,Admin');

    Route::post('/Expense/Upload', [ExpenseController::class, 'upload']);

    Route::post('/Expense/Save', [ExpenseController::class, 'store'])
        ->name('Expense.store');
    // Expense

    // HeadApprove
    Route::get('HeadApprove', [HeadApprovedController::class, 'index'])
        ->name('HeadApprove.index');

    // DriverClaim
    Route::get('DriverClaim', [DriverClaimController::class, 'index'])
        ->middleware('check.module.access:DriverClaim,Driver|Admin')
        ->name('DriverClaim.index');

    // TechClaim
    Route::get('TechClaim', [TechClaimController::class, 'index'])
        ->middleware('check.module.access:TechClaim,Tech|Admin')
        ->name('TechClaim.index');
});


Route::group(['middleware' => ['auth', 'remember.login']], function () {
    // Backend
    // HR
    Route::prefix('HR')
    ->name('HR.')
    ->middleware('check.module.access:HR,Staff|Admin|SuperAdmin')
    ->group(function () {

        Route::get('/', [HRController::class, 'index'])->name('index');
        Route::get('edit/{id}', [HRController::class, 'edit'])->name('edit');

        // เพิ่มเติม: หากมี create, store, update, destroy ในอนาคต สามารถเพิ่มไว้ที่นี่ได้
        // Route::get('create', [HRController::class, 'create'])->name('create');
        // Route::post('/', [HRController::class, 'store'])->name('store');
        // Route::put('{id}', [HRController::class, 'update'])->name('update');
        // Route::delete('{id}', [HRController::class, 'destroy'])->name('destroy');
    });

    Route::middleware('check.module.access:User,Staff|Admin|SuperAdmin')
        ->group(function () {

            // 🔹 User
            Route::prefix('User')->name('User.')->group(function () {
                Route::get('/', [UserController::class, 'index'])->name('index');
                Route::put('{id}/reset', [UserController::class, 'ResetPassword'])->name('reset');

                Route::resource('/', UserController::class)
                    ->parameters(['' => 'user'])
                    ->except(['index']);
            });

            // 🔹 Role
            Route::prefix('Role')->name('Role.')->group(function () {
                Route::get('create/{type}', [RoleController::class, 'create'])->name('create');
                Route::get('{Role}/edit/{type?}', [RoleController::class, 'edit'])->name('edit');
                Route::delete('{id}/{type}', [RoleController::class, 'destroy'])->name('destroy');

                Route::resource('/', RoleController::class)
                    ->parameters(['' => 'role']);
            });
        });

    Route::middleware('check.module.access:Setting,Staff|Admin|SuperAdmin')
        ->group(function () {

            // FuelPrice91
            Route::prefix('FuelPrice91')->name('FuelPrice91.')->group(function () {
                Route::get('/', [FuelPrice91Controller::class, 'index'])->name('index');

                Route::resource('/', FuelPrice91Controller::class)
                    ->parameters(['' => 'fuelprice91'])
                    ->except(['index']);
            });
            // Pricepermeal
            Route::prefix('Pricepermeal')->name('Pricepermeal.')->group(function () {
                Route::get('list', [PricepermealController::class, 'list'])->name('list');

                Route::resource('/', PricepermealController::class)
                    ->parameters(['' => 'pricepermeal']);
            });

            // Groupprice
            Route::resource('Groupprice', GrouppriceController::class);

            // FuelPrice
            Route::resource('FuelPrice', FuelPriceController::class);

            // UserRole
            Route::resource('UserRole', UserroleController::class);

            // DistanceRate
            Route::resource('DistanceRate', DistanceRateController::class);

            // Typegroup
            Route::resource('Typegroup', TypegroupController::class);

            // Import
            Route::prefix('ImportList')->name('importlist.')->group(function () {
                Route::get('/', [ImportlistController::class, 'index'])->name('index');
                Route::get('import', [ImportlistController::class, 'importuser'])->name('import');
                Route::post('Excel', [ImportlistController::class, 'importexcel'])->name('excel');
            });
        });

    Route::get('/download-sample', function () {
        $filePath = storage_path('app/public/files/eximport_groupspecial.xlsx');

        if (!file_exists($filePath)) {
            abort(404, 'Sample file not found.');
        }

        return Response::download($filePath, 'eximport_expensegroup.xlsx');
    })->name('download.sample');



    // อนุมัติผ่านลิงก์ (ไม่ควบคุมสิทธิ์, แต่อิงจาก token)
    Route::get('/approve/login', [ApproveLoginController::class, 'loginWithToken'])->name('approve.magic.login');
    Route::get('/approve/view/{id}', [ApproveController::class, 'show'])->name('approve.page');
    Route::post('/approve/confirm/{id}', [ApproveController::class, 'confirm'])->name('approve.confirm');
});





// Update API น้ำมัน

// Update API น้ำมัน
