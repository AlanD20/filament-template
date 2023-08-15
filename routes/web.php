<?php

use App\Http\Controllers\PrivateAssetController;
use App\Livewire\Auth\CompleteAccount;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('storage/private/{path}', [PrivateAssetController::class, 'show'])
    ->where('path', '.*')
    ->middleware('auth');

Route::get('users/complete-account', CompleteAccount::class)
    ->name('filament.resources.users.complete-account')
    ->middleware('guest');

// Route::fallback([DashboardController::class, 'index']);
