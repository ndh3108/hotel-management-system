<?php

namespace App\Services;

use PDO;

class InvoiceCalculatorService
{
    public function calculate(
        array $booking,
        float $roomAmount,
        float $serviceAmount,
        float $addonAmount,
        float $lateFee,
        float $damageFee,
        float $discountAmount,
        float $prepaidAmount
    ): array {
        $subtotal = $roomAmount + $serviceAmount + $addonAmount + $lateFee + $damageFee;
        $finalTotal = max(0.0, $subtotal - $discountAmount);
        $balanceDue = max(0.0, $finalTotal - $prepaidAmount);

        return [
            'booking_id' => (int) ($booking['id'] ?? 0),
            'room_amount' => round($roomAmount, 2),
            'service_amount' => round($serviceAmount, 2),
            'addon_amount' => round($addonAmount, 2),
            'late_fee' => round($lateFee, 2),
            'damage_fee' => round($damageFee, 2),
            'discount_amount' => round($discountAmount, 2),
            'prepaid_amount' => round($prepaidAmount, 2),
            'final_total' => round($finalTotal, 2),
            'balance_due' => round($balanceDue, 2),
            'status' => $balanceDue > 0 ? 'open' : 'paid',
        ];
    }
}

