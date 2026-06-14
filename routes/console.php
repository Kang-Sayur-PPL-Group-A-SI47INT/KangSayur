<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-cancel orders without shipping proof after 6 hours
Schedule::command('orders:auto-cancel')->everyMinute();

Artisan::command('discounts:remove-expired', function () {
    $count = \App\Services\HarvestDiscountService::removeExpiredDiscounts();
    $this->info("Removed {$count} expired auto-discounts.");
})->purpose('Remove expired auto-discounts from listings');

Schedule::command('discounts:remove-expired')->daily();
