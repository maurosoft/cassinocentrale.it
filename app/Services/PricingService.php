<?php

namespace App\Services;

use App\Models\Room;

/**
 * Calcola il prezzo di un soggiorno applicando le regole di sconto
 * definite in config/bnb.php ('pricing').
 */
class PricingService
{
    /**
     * @return array{base:float, price_per_night:float, nights:int, subtotal:float, discount_percent:float, discount_label:?string}
     */
    public function quote(Room $room, int $nights, int $guests = 1): array
    {
        $cfg = config('bnb.pricing', []);
        $base = (float) $room->base_price;
        $nights = max($nights, 1);

        $discounts = [];

        // Sconto per 2+ persone
        if ($guests >= 2 && ! empty($cfg['double_discount_percent'])) {
            $discounts['In due'] = (float) $cfg['double_discount_percent'];
        }

        // Sconto soggiorni lunghi
        if (! empty($cfg['long_stay_enabled']) && $nights >= (int) ($cfg['long_stay_min_nights'] ?? 99)) {
            $discounts['Soggiorno lungo'] = (float) $cfg['long_stay_percent'];
        }

        $percent = 0.0;
        $label = null;

        if (! empty($discounts)) {
            if (! empty($cfg['stack_discounts'])) {
                // Somma gli sconti (con un tetto di sicurezza al 100%)
                $percent = min(array_sum($discounts), 100);
                $label = implode(' + ', array_keys($discounts));
            } else {
                // Applica solo lo sconto più conveniente
                $label = array_search(max($discounts), $discounts, true) ?: null;
                $percent = max($discounts);
            }
        }

        $pricePerNight = round($base * (1 - $percent / 100), 2);
        $subtotal = round($pricePerNight * $nights, 2);

        return [
            'base' => $base,
            'price_per_night' => $pricePerNight,
            'nights' => $nights,
            'subtotal' => $subtotal,
            'discount_percent' => $percent,
            'discount_label' => $label,
        ];
    }
}
