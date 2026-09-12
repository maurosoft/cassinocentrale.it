<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotte pubbliche del sito
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/camere', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/camere/{room}', [RoomController::class, 'show'])->name('rooms.show');

Route::get('/scopri-cassino', [DiscoverController::class, 'index'])->name('discover.index');
Route::get('/scopri-cassino/{place}', [DiscoverController::class, 'show'])->name('discover.show');

Route::get('/contatti', [ContactController::class, 'index'])->name('contact');

Route::get('/prenota', [BookingController::class, 'create'])->name('booking.create');
Route::post('/prenota', [BookingController::class, 'store'])->name('booking.store');
Route::get('/prenota/conferma/{reference}', [BookingController::class, 'confirmation'])->name('booking.confirmation');

Route::view('/offline', 'offline')->name('offline');

// Pagine legali
Route::view('/privacy', 'legal.privacy')->name('legal.privacy');
Route::view('/cookie-policy', 'legal.cookie')->name('legal.cookie');
Route::view('/termini', 'legal.terms')->name('legal.terms');

/*
|--------------------------------------------------------------------------
| Area riservata (admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Accesso (solo per chi NON è già entrato)
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'show'])->name('login');
        Route::post('login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.attempt');
    });

    // Sezioni protette (serve essere autenticati)
    Route::middleware('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Contenuti: camere e servizi (superadmin, reception, editor)
        Route::middleware('role:superadmin,reception,editor')->group(function () {
            Route::resource('rooms', Admin\RoomController::class)->except('show');
            Route::resource('services', Admin\ServiceController::class)->except('show');
        });

        // Contenuti: luoghi, recensioni, testi del sito (superadmin, editor)
        Route::middleware('role:superadmin,editor')->group(function () {
            Route::resource('places', Admin\PlaceController::class)->except('show');
            Route::resource('reviews', Admin\ReviewController::class)->except('show');
            Route::get('settings', [Admin\SettingsController::class, 'index'])->name('settings.index');
            Route::put('settings', [Admin\SettingsController::class, 'update'])->name('settings.update');
            Route::post('settings/branding', [Admin\SettingsController::class, 'branding'])->name('settings.branding');
            Route::post('settings/pricing', [Admin\SettingsController::class, 'pricing'])->name('settings.pricing');
        });

        // Prenotazioni, calendario e chiusure (superadmin, reception)
        Route::middleware('role:superadmin,reception')->group(function () {
            Route::get('bookings', [Admin\BookingController::class, 'index'])->name('bookings.index');
            Route::get('bookings/create', [Admin\BookingController::class, 'create'])->name('bookings.create');
            Route::post('bookings', [Admin\BookingController::class, 'store'])->name('bookings.store');
            Route::get('bookings/{booking}', [Admin\BookingController::class, 'show'])->name('bookings.show');
            Route::get('bookings/{booking}/edit', [Admin\BookingController::class, 'edit'])->name('bookings.edit');
            Route::put('bookings/{booking}', [Admin\BookingController::class, 'update'])->name('bookings.update');
            Route::patch('bookings/{booking}/status', [Admin\BookingController::class, 'statusUpdate'])->name('bookings.status');
            Route::delete('bookings/{booking}', [Admin\BookingController::class, 'destroy'])->name('bookings.destroy');

            Route::get('customers', [Admin\CustomerController::class, 'index'])->name('customers.index');
            Route::get('customers/{customer}', [Admin\CustomerController::class, 'show'])->name('customers.show');

            Route::get('calendar', [Admin\CalendarController::class, 'index'])->name('calendar.index');

            Route::get('closures', [Admin\ClosureController::class, 'index'])->name('closures.index');
            Route::post('closures', [Admin\ClosureController::class, 'store'])->name('closures.store');
            Route::delete('closures/{closure}', [Admin\ClosureController::class, 'destroy'])->name('closures.destroy');
        });

        // Utenti e manutenzione (solo superadmin)
        Route::middleware('role:superadmin')->group(function () {
            Route::resource('users', Admin\UserController::class)->except('show');
            Route::post('settings/maintenance', [Admin\SettingsController::class, 'maintenance'])->name('settings.maintenance');
            Route::post('settings/notifications', [Admin\SettingsController::class, 'notifications'])->name('settings.notifications');
        });
    });
});
