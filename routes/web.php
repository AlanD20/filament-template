<?php

use App\Livewire\Auth\CompleteAccount;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrivateAssetController;

Route::get('storage/private/{path}', [PrivateAssetController::class, 'show'])
    ->where('path', '.*')
    ->middleware('auth');

Route::get('users/complete-account', CompleteAccount::class)
    ->name('filament.resources.users.complete-account')
    ->middleware('guest');

// Route::fallback([DashboardController::class, 'index']);
