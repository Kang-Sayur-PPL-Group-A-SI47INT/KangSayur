<?php

use App\Models\Listing;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Farmer;

Route::get('/', function () {
    $featuredListings = collect();

    // Only query if the listings table exists
    if (\Illuminate\Support\Facades\Schema::hasTable('listings')) {
        $featuredListings = Listing::with(['farmer', 'produce', 'ratings'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
    }

    return view('welcome', compact('featuredListings'));
})->name('home');


Route::get('/farmer/dashboard', function () {
    return view('farmer.dashboard');
})->middleware('auth')->name('farmer.dashboard');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    // Listings
    Route::get('/listings', [Farmer\ListingController::class, 'index'])->name('listings.index');
    Route::get('/listings/{listing}/edit', [Farmer\ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [Farmer\ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [Farmer\ListingController::class, 'destroy'])->name('listings.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/marketplace', [Customer\MarketplaceController::class, 'index'])->name('marketplace');
    Route::get('/marketplace/{listing}', [Customer\MarketplaceController::class, 'show'])->name('marketplace.show');
});

Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    Route::get('/dashboard', [Farmer\DashboardController::class, 'index'])->name('dashboard');

    // Listings CRUD
    Route::get('/listings', [Farmer\ListingController::class, 'index'])->name('listings.index');
});