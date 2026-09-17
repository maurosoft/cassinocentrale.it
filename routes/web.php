<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StripeController;
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
Route::get('/attivita', [DiscoverController::class, 'activities'])->name('discover.activities');
Route::get('/scopri-cassino/{place}', [DiscoverController::class, 'show'])->name('discover.show');

Route::get('/contatti', [ContactController::class, 'index'])->name('contact');

Route::get('/prenota', [BookingController::class, 'create'])->name('booking.create');
Route::post('/prenota', [BookingController::class, 'store'])->name('booking.store');
Route::get('/prenota/conferma/{reference}', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/prenota/{reference}/paga', [StripeController::class, 'pay'])->name('booking.pay');
Route::get('/prenota/{reference}/pagato', [StripeController::class, 'paid'])->name('booking.paid');
Route::post('/stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');

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
            Route::post('settings/hero', [Admin\SettingsController::class, 'hero'])->name('settings.hero');
            Route::post('settings/favicon', [Admin\SettingsController::class, 'favicon'])->name('settings.favicon');
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

            Route::get('logs/email', [Admin\LogController::class, 'email'])->name('logs.email');
            Route::get('logs/whatsapp', [Admin\LogController::class, 'whatsapp'])->name('logs.whatsapp');

            Route::get('calendar', [Admin\CalendarController::class, 'index'])->name('calendar.index');

            Route::get('closures', [Admin\ClosureController::class, 'index'])->name('closures.index');
            Route::post('closures', [Admin\ClosureController::class, 'store'])->name('closures.store');
            Route::delete('closures/{closure}', [Admin\ClosureController::class, 'destroy'])->name('closures.destroy');
        });

        // Utenti e manutenzione (solo superadmin)
        Route::middleware('role:superadmin')->group(function () {
            Route::resource('users', Admin\UserController::class)->except('show');

            Route::get('whatsapp', [Admin\WhatsappController::class, 'index'])->name('whatsapp.index');
            Route::post('whatsapp/settings', [Admin\WhatsappController::class, 'settings'])->name('whatsapp.settings');
            Route::post('whatsapp/providers', [Admin\WhatsappController::class, 'store'])->name('whatsapp.store');
            Route::put('whatsapp/providers/{provider}', [Admin\WhatsappController::class, 'update'])->name('whatsapp.update');
            Route::delete('whatsapp/providers/{provider}', [Admin\WhatsappController::class, 'destroy'])->name('whatsapp.destroy');
            Route::post('whatsapp/test', [Admin\WhatsappController::class, 'test'])->name('whatsapp.test');

            Route::post('settings/maintenance', [Admin\SettingsController::class, 'maintenance'])->name('settings.maintenance');
            Route::post('settings/notifications', [Admin\SettingsController::class, 'notifications'])->name('settings.notifications');
            Route::post('settings/smtp', [Admin\SettingsController::class, 'smtp'])->name('settings.smtp');
            Route::post('settings/test-email', [Admin\SettingsController::class, 'testEmail'])->name('settings.testEmail');
            Route::post('settings/stripe', [Admin\SettingsController::class, 'stripe'])->name('settings.stripe');
        });
    });
});
