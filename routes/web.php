<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\DiscoverController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotte pubbliche del sito
|--------------------------------------------------------------------------
| Ogni riga collega un indirizzo (URL) alla pagina corrispondente.
| L'area admin, le prenotazioni online e il chatbot arriveranno nelle
| fasi successive.
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/camere', [RoomController::class, 'index'])->name('rooms.index');
Route::get('/camere/{room}', [RoomController::class, 'show'])->name('rooms.show');

Route::get('/scopri-cassino', [DiscoverController::class, 'index'])->name('discover.index');

Route::get('/contatti', [ContactController::class, 'index'])->name('contact');

// Pagina "Prenota": in Fase 1 mostra i contatti diretti (telefono/WhatsApp/email).
// In Fase 3 diventerà il vero motore di prenotazione con calendario.
Route::view('/prenota', 'bookings.create')->name('booking.create');

// Pagina mostrata quando si è offline (usata dalla PWA / service worker).
Route::view('/offline', 'offline')->name('offline');
