<?php

use App\Http\Controllers\AccrualController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\CommitmentController;
use App\Http\Controllers\ContractAdministratorController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('HomeView', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->middleware(['auth', 'verified']);

Route::get('/dashboard', [ChartController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::middleware('role:admin_role')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('departments', DepartmentController::class);
    });

    Route::get('/certifications/getCertificationByNumber', [CertificationController::class, 'getCertificationByNumber'])
        ->name('certifications.getCertificationByNumber')
        ->middleware('can:commitment');
    Route::resource('certifications', CertificationController::class)
        ->middleware('can:certification');

    Route::get('/commitments/getCommitmentByNumber', [CommitmentController::class, 'getCommitmentByNumber'])
        ->name('commitments.getCommitmentByNumber')
        ->middleware('can:accrual');
    Route::resource('commitments', CommitmentController::class)
        ->middleware('can:commitment');

    Route::resource('accruals', AccrualController::class)
        ->middleware('can:accrual');

    Route::resource('payments', PaymentController::class)
        ->middleware('can:payment');

    Route::get('/vendors/getVendorByNit', [VendorController::class, 'getVendorByNit'])
        ->name('vendors.getVendorByNit')
        ->middleware('can:commitment');
    Route::resource('vendors', VendorController::class)
        ->middleware('can:vendor');

    Route::get('/contractAdministrators/getContractAdministratorByCi', [ContractAdministratorController::class, 'getContractAdministratorByCi'])
        ->name('contractAdministrators.getContractAdministratorByCi')
        ->middleware('can:commitment');
    // Route::resource('contractAdministrators', ContractAdministratorController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
});

require __DIR__ . '/auth.php';
