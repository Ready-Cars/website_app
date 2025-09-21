<?php

use App\Livewire\RentCar;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home.index')->name('home');

// All cars catalog page
Route::view('/cars', 'cars.index')->name('cars.index');

// Rent page - using a blade shell to include Livewire assets consistently
use App\Models\Car;
Route::get('/rent/{car}', function (Car $car) {
    return view('rent.show', ['car' => $car]);
})->name('rent.show');

// My trips (requires auth)
Route::view('/trips', 'trips.index')
    ->middleware(['auth'])
    ->name('trips.index');

// Wallet (requires auth)
Route::view('/wallet', 'wallet.index')
    ->middleware(['auth'])
    ->name('wallet.index');

// Wallet funding via Paystack
use App\Http\Controllers\WalletFundingController;
Route::middleware(['auth'])->group(function () {
    Route::post('/wallet/paystack/init', [WalletFundingController::class, 'init'])->name('wallet.paystack.init');
    Route::get('/wallet/paystack/callback', [WalletFundingController::class, 'callback'])->name('wallet.paystack.callback');
});

use App\Http\Middleware\AdminOnly;

Route::view('dashboard', 'admin.dashboard')
    ->middleware(['auth', 'verified', AdminOnly::class])
    ->name('dashboard');

Route::view('/admin/bookings', 'admin.bookings')
    ->middleware(['auth', 'verified', AdminOnly::class])
    ->name('admin.bookings');

Route::view('/admin/cars', 'admin.cars')
    ->middleware(['auth', 'verified', AdminOnly::class])
    ->name('admin.cars');

Route::view('/admin/car-options', 'admin.car-options')
    ->middleware(['auth', 'verified', AdminOnly::class])
    ->name('admin.car-options');

Route::view('/admin/customers', 'admin.customers')
    ->middleware(['auth', 'verified', AdminOnly::class])
    ->name('admin.customers');

Route::view('/admin/reports', 'admin.reports')
    ->middleware(['auth', 'verified', AdminOnly::class])
    ->name('admin.reports');

Route::view('/admin/profile', 'admin.profile')
    ->middleware(['auth', 'verified', AdminOnly::class])
    ->name('admin.profile');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
