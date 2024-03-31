<?php

use App\Livewire\Auth\SetupAccount;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrivateAssetController;

Route::get('storage/private/{path}', [PrivateAssetController::class, 'show'])
    ->where('path', '.*')
    ->middleware('auth');

Route::get('auth/setup-account', SetupAccount::class)
    ->name('filament.admin.auth.setup-account')
    ->middleware('guest');

// Route::fallback([DashboardController::class, 'index']);
