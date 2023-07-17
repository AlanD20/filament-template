<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PrivateAssetController;
use App\Http\Livewire\Auth\CompleteAccount;
use Illuminate\Support\Facades\Route;

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

Route::get('storage/private/{path}', [PrivateAssetController::class, 'show'])
    ->where('path', '.*')
    ->middleware('auth');

Route::get('users/complete-account', CompleteAccount::class)
    ->name('filament.resources.users.complete-account')
    ->middleware('guest');

Route::fallback([DashboardController::class, 'index']);

// require __DIR__ . '/auth.php';
