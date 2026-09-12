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

    // Dati legali/fiscali (usati nelle pagine Privacy e Termini).
    'legal' => [
        'business_name' => 'Bed & Breakfast Cassino Centrale',
        'owner' => 'Jessica Vicalvi',
        'vat' => '03351130608',
        'registered_office' => 'Via Madonna delle Grazie 1, int. 1 – 03030 Villa Santa Lucia (FR)',
    ],

    'contact' => [
        'address' => 'Viale Dante, 6 – 03043 Cassino (FR)',
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

    // Politica prezzi/sconti (usata dal motore di prenotazione in Fase 3).
    //  - Prezzo base: 65 €/notte per 1 persona.
    //  - In due: 10% di sconto (→ 58,50 €/notte).
    //  - Soggiorni lunghi: da 3 notti, ulteriore 10% (valore di partenza, modificabile).
    //  - stack_discounts: se false, si applica UNO solo sconto (il più conveniente),
    //    per evitare sconti troppo alti sommati; se true, si sommano.
    'pricing' => [
        'single_price' => 65,
        'double_discount_percent' => 10,
        'long_stay_enabled' => true,
        'long_stay_min_nights' => 3,
        'long_stay_percent' => 10,
        'stack_discounts' => false,
        // Sconto sulle camere aggiuntive per gruppi (3-4 persone → 2 camere).
        'second_room_discount_percent' => 15,
        // Capienza di ogni camera: usata per capire quante camere servono.
        'room_capacity' => 2,
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
