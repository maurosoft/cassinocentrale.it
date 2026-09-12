<?php

namespace App\Services;

use App\Models\Room;
use App\Support\Settings;
use Illuminate\Support\Collection;

/**
 * Calcola il prezzo di un soggiorno applicando le regole di sconto
 * definite in config/bnb.php ('pricing') e modificabili da admin.
 */
class PricingService
{
    /** Numero di camere necessarie per il numero di ospiti indicato. */
    public function roomsNeeded(int $guests): int
    {
        $capacity = (int) config('bnb.pricing.room_capacity', 2);

        return max(1, (int) ceil(max($guests, 1) / max($capacity, 1)));
    }

    /**
     * Preventivo per un gruppo su più camere: la prima a prezzo pieno,
     * le successive con lo sconto "seconda camera". (Niente sconto "in due".)
     *
     * @param  Collection<int,Room>  $rooms
     * @return array{rooms:array<int,array>, total:float}
     */
    public function quoteGroup(Collection $rooms, int $nights): array
    {
        $cfg = config('bnb.pricing', []);
        $nights = max($nights, 1);
        $longStay = (! empty($cfg['long_stay_enabled']) && $nights >= (int) ($cfg['long_stay_min_nights'] ?? 99))
            ? (float) $cfg['long_stay_percent'] : 0.0;
        $secondPercent = (float) Settings::get('pricing.second_room_discount_percent', $cfg['second_room_discount_percent'] ?? 15);

        $rows = [];
        $total = 0.0;

        foreach ($rooms->values() as $i => $room) {
            $percent = $longStay + ($i > 0 ? $secondPercent : 0);
            $percent = min($percent, 90);
            $labels = [];
            if ($i > 0) {
                $labels[] = '2ª camera';
            }
            if ($longStay > 0) {
                $labels[] = 'Soggiorno lungo';
            }

            $ppn = round((float) $room->base_price * (1 - $percent / 100), 2);
            $sub = round($ppn * $nights, 2);
            $total += $sub;

            $rows[] = [
                'room' => $room,
                'price_per_night' => $ppn,
                'nights' => $nights,
                'subtotal' => $sub,
                'discount_percent' => $percent,
                'discount_label' => $labels ? implode(' + ', $labels) : null,
            ];
        }

        return ['rooms' => $rows, 'total' => round($total, 2)];
    }

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
