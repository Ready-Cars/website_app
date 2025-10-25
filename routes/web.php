<?php

use App\Http\Controllers\WalletFundingController;
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\CustomerOnly;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Models\Car;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home.index')->name('home');
Route::view('/book-online', 'home.index')->name('home');

// Terms and Conditions page
Route::view('/terms', 'terms.index')->name('terms.index');

// Contact Us page
Route::view('/contact-us', 'contact.index')->name('contact.index');

// All cars catalog page
Route::view('/cars', 'cars.index')->name('cars.index');

// Rent page - using a blade shell to include Livewire assets consistently
Route::get('/rent/{car}', function (Car $car) {
    return view('rent.show', ['car' => $car]);
})->name('rent.show');

// My trips (requires auth)
Route::view('/trips', 'trips.index')
    ->middleware(['auth', CustomerOnly::class])
    ->name('trips.index');

// Wallet (requires auth)
Route::view('/wallet', 'wallet.index')
    ->middleware(['auth', CustomerOnly::class])
    ->name('wallet.index');

// Notifications (requires auth)
Route::view('/notifications', 'notifications.index')
    ->middleware(['auth', CustomerOnly::class])
    ->name('notifications.index');

// Profile (customer-facing)
Route::view('/profile', 'profile.index')
    ->middleware(['auth', CustomerOnly::class])
    ->name('profile.index');

// Wallet funding via Paystack
Route::middleware(['auth', CustomerOnly::class])->group(function () {
    Route::post('/wallet/paystack/init', [WalletFundingController::class, 'init'])->name('wallet.paystack.init');
    Route::get('/wallet/paystack/callback', [WalletFundingController::class, 'callback'])->name('wallet.paystack.callback');
});

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

Route::view('/admin/settings', 'admin.settings')
    ->middleware(['auth', 'verified', AdminOnly::class])
    ->name('admin.settings');

Route::view('/admin/profile', 'admin.profile')
    ->middleware(['auth', 'verified', AdminOnly::class])
    ->name('admin.profile');

Route::middleware(['auth', CustomerOnly::class])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});

require __DIR__.'/auth.php';
