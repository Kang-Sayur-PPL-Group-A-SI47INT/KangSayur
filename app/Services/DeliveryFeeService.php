<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\User;

class DeliveryFeeService
{
    /**
     * Calculate delivery fee for a cart based on distance between
     * customer and farmer locations using Haversine formula.
     *
     * Tiered pricing:
     * - Base fee: Rp 5,000
     * - 0-5 km: Rp 2,000/km
     * - 5-15 km: Rp 1,500/km
     * - 15+ km: Rp 1,000/km
     */
    public static function calculateForCart(Cart $cart, User $customer): float
    {
        if (!$customer->latitude || !$customer->longitude) {
            // Default flat fee if customer location unknown
            return 10000;
        }

        $maxDistance = 0;

        foreach ($cart->items as $item) {
            $farmer = $item->listing->farmer ?? null;

            if (!$farmer || !$farmer->latitude || !$farmer->longitude) {
                continue;
            }

            $distance = self::haversineDistance(
                $customer->latitude,
                $customer->longitude,
                $farmer->latitude,
                $farmer->longitude
            );

            $maxDistance = max($maxDistance, $distance);
        }

        // If no valid farmer coordinates, use default
        if ($maxDistance == 0) {
            return 10000;
        }

        return self::calculateTieredFee($maxDistance);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     * Returns distance in kilometers.
     */
    private static function haversineDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Calculate tiered delivery fee based on distance.
     */
    private static function calculateTieredFee(float $distanceKm): float
    {
        $baseFee = 5000;
        $fee = $baseFee;

        if ($distanceKm <= 5) {
            $fee += $distanceKm * 2000;
        } elseif ($distanceKm <= 15) {
            $fee += 5 * 2000;
            $fee += ($distanceKm - 5) * 1500;
        } else {
            $fee += 5 * 2000;
            $fee += 10 * 1500;
            $fee += ($distanceKm - 15) * 1000;
        }

        return round($fee);
    }
}
