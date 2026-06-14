<?php

namespace App\Services;

use App\Models\HarvestSchedule;
use App\Models\Listing;
use App\Models\ListingStockLog;

class HarvestDiscountService
{
    /**
     * Discount tier table: surplus_percentage_threshold => discount_percentage.
     * Ordered from highest to lowest so the first match wins.
     */
    private const DISCOUNT_TIERS = [
        25 => 15,
        20 => 12,
        15 => 10,
        10 =>  7,
         7 =>  5,
         5 =>  3,
    ];

    /**
     * Calculate the average harvest/stock for a listing.
     * Combines harvest_schedules.estimated_stock and listing_stock_logs.quantity.
     */
    public static function calculateAverageHarvest(Listing $listing): float
    {
        // Gather all data points
        $harvestStocks = HarvestSchedule::where('listing_id', $listing->listing_id)
            ->pluck('estimated_stock')
            ->map(fn($v) => (float) $v);

        $logStocks = ListingStockLog::where('listing_id', $listing->listing_id)
            ->pluck('quantity')
            ->map(fn($v) => (float) $v);

        $allPoints = $harvestStocks->merge($logStocks);

        if ($allPoints->isEmpty()) {
            // Fall back to the listing's current quantity as a single data point
            return max((float) $listing->quantity, 1);
        }

        return $allPoints->avg();
    }

    /**
     * Determine the discount tier based on how much the upcoming stock
     * exceeds the average.
     *
     * @return float Discount percentage (e.g. 5.00), or 0 if below threshold
     */
    public static function determineDiscountTier(float $upcoming, float $average): float
    {
        if ($average <= 0) {
            return 0;
        }

        $surplusPercent = (($upcoming - $average) / $average) * 100;

        foreach (self::DISCOUNT_TIERS as $threshold => $discount) {
            if ($surplusPercent >= $threshold) {
                return (float) $discount;
            }
        }

        return 0;
    }

    /**
     * Calculate and apply auto-discount to a listing based on a harvest schedule.
     *
     * @return float The discount percentage that was applied (0 if none)
     */
    public static function applyDiscount(Listing $listing, HarvestSchedule $schedule): float
    {
        $average  = self::calculateAverageHarvest($listing);
        $upcoming = (float) $schedule->estimated_stock;
        $discount = self::determineDiscountTier($upcoming, $average);

        if ($discount > 0) {
            // Preserve original price if not already saved
            if (!$listing->original_price) {
                $listing->original_price = $listing->price;
            }

            $listing->discount_percentage = $discount;
            $listing->auto_discount       = true;
            // Discount expires 7 days after the scheduled harvest date
            $listing->discount_expires_at = $schedule->availability_date->copy()->addDays(7);
            $listing->save();
        } else {
            // If this schedule no longer triggers a discount, remove auto-discount
            self::removeDiscount($listing);
        }

        return $discount;
    }

    /**
     * Recalculate discount for a listing by finding the upcoming schedule
     * with the highest surplus above average.
     *
     * @return float The discount percentage that was applied (0 if none)
     */
    public static function recalculateForListing(Listing $listing): float
    {
        $average = self::calculateAverageHarvest($listing);

        // Get all future schedules for this listing
        $futureSchedules = HarvestSchedule::where('listing_id', $listing->listing_id)
            ->where('availability_date', '>', today())
            ->orderBy('estimated_stock', 'desc')
            ->get();

        if ($futureSchedules->isEmpty()) {
            self::removeDiscount($listing);
            return 0;
        }

        // Find the schedule with the highest surplus
        $bestDiscount = 0;
        $bestSchedule = null;

        foreach ($futureSchedules as $schedule) {
            $discount = self::determineDiscountTier((float) $schedule->estimated_stock, $average);
            if ($discount > $bestDiscount) {
                $bestDiscount = $discount;
                $bestSchedule = $schedule;
            }
        }

        if ($bestDiscount > 0 && $bestSchedule) {
            if (!$listing->original_price) {
                $listing->original_price = $listing->price;
            }

            $listing->discount_percentage = $bestDiscount;
            $listing->auto_discount       = true;
            $listing->discount_expires_at = $bestSchedule->availability_date->copy()->addDays(7);
            $listing->save();
        } else {
            self::removeDiscount($listing);
        }

        return $bestDiscount;
    }

    /**
     * Remove auto-discount from a listing.
     */
    public static function removeDiscount(Listing $listing): void
    {
        if ($listing->auto_discount) {
            $listing->discount_percentage = 0;
            $listing->auto_discount       = false;
            $listing->discount_expires_at = null;
            // Don't reset original_price — keep it as a reference
            $listing->save();
        }
    }

    /**
     * Remove all expired auto-discounts. Called by scheduler.
     */
    public static function removeExpiredDiscounts(): int
    {
        $expired = Listing::where('auto_discount', true)
            ->whereNotNull('discount_expires_at')
            ->where('discount_expires_at', '<', now())
            ->get();

        foreach ($expired as $listing) {
            self::removeDiscount($listing);
        }

        return $expired->count();
    }
}
