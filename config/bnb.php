<?php

/*
|--------------------------------------------------------------------------
| Configurazione del B&B Cassino Centrale
|--------------------------------------------------------------------------
| Questi sono i valori "di base". In futuro molti saranno modificabili
| dall'area admin (tabella site_settings) e questi resteranno come
| valori di riserva (fallback).
*/

return [

    'name' => env('APP_NAME', 'B&B Cassino Centrale'),
    'tagline' => 'Nel Cuore della Città',
    'slogan' => 'Soggiorna a Cassino Centrale e hai Tutto a Portata di Mano!',

    'contact' => [
        'address' => 'Viale Dante, 6 – Cassino (FR)',
        'phone' => '0776 1400205',
        'phone_raw' => '+390776140020',
        'email' => env('MAIL_FROM_ADDRESS', 'info@cassinocentrale.it'),
        // Coordinate approssimative del centro di Cassino (da affinare con l'indirizzo esatto).
        'lat' => 41.4900,
        'lng' => 13.8300,
        'maps_url' => 'https://www.google.com/maps/search/?api=1&query=Viale+Dante+6+Cassino',
    ],

    // Numero WhatsApp per il pulsante "Chatta con noi" (formato internazionale senza +).
    'whatsapp_number' => env('WHATSAPP_CONTACT_NUMBER'),

    // Politica sconti (Fase 3). Valore di partenza: 10% per soggiorni > 1 notte.
    'discount' => [
        'long_stay_enabled' => true,
        'long_stay_min_nights' => 2,
        'long_stay_percent' => 10,
    ],

    'checkin' => [
        'from' => '14:00',
        'to' => '20:00',
    ],
    'checkout' => [
        'until' => '10:30',
    ],

    'social' => [
        'facebook' => null,
        'instagram' => null,
    ],
];
