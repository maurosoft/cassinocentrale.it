<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
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

Route::get('/contatti', [ContactController::class, 'index'])->name('contact');

Route::view('/prenota', 'bookings.create')->name('booking.create');

Route::view('/offline', 'offline')->name('offline');

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
        });

        // Prenotazioni (superadmin, reception)
        Route::middleware('role:superadmin,reception')->group(function () {
            Route::get('bookings', [Admin\BookingController::class, 'index'])->name('bookings.index');
            Route::get('bookings/{booking}', [Admin\BookingController::class, 'show'])->name('bookings.show');
            Route::put('bookings/{booking}', [Admin\BookingController::class, 'update'])->name('bookings.update');
        });

        // Utenti (solo superadmin)
        Route::middleware('role:superadmin')->group(function () {
            Route::resource('users', Admin\UserController::class)->except('show');
        });
    });
});
