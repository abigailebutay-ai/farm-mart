<?php

namespace App\Services;

class DiscountService
{
    /**
     * Return the highest matching automatic bulk discount for the cart.
     */
    public function getEligibleDiscount(float $totalKg, float $subtotal): array
    {
        $rules = [
            ['minimum_kg' => 50, 'rate' => 15],
            ['minimum_kg' => 25, 'rate' => 10],
            ['minimum_kg' => 10, 'rate' => 5],
        ];

        foreach ($rules as $rule) {
            if ($totalKg >= $rule['minimum_kg']) {
                $discountAmount = min($subtotal * ($rule['rate'] / 100), $subtotal);

                return [
                    'eligible' => true,
                    'label' => $rule['rate'] . '% bulk order discount',
                    'discount_type' => 'percent',
                    'discount_rate' => $rule['rate'],
                    'discount_amount' => round($discountAmount, 2),
                    'minimum_kg' => $rule['minimum_kg'],
                ];
            }
        }

        return [
            'eligible' => false,
            'discount_amount' => 0,
        ];
    }
}
