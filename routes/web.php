<?php

use App\Http\Controllers\CertificationController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\CommitmentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VendorController;
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

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return Inertia::render('HomeView');
// })->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/certifications/getCertificationByNumber', [CertificationController::class, 'getCertificationByNumber'])->name('certifications.getCertificationByNumber');
    Route::resource('certifications', CertificationController::class);

    Route::resource('commitments', CommitmentController::class);

    Route::get('/vendors/getVendorByNit', [VendorController::class, 'getVendorByNit'])->name('vendors.getVendorByNit');
    Route::resource('vendors', VendorController::class);
});

require __DIR__ . '/auth.php';
