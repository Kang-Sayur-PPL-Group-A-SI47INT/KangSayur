<?php

require __DIR__.'/auth.php';

use App\Models\Listing;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\MarketplaceController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Farmer;
use App\Http\Controllers\Farmer\ListingController;

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

// Public farmer profile
Route::get('/farmer/profile/{userId}', [Farmer\ProfileController::class, 'show'])->name('farmer.profile.show');


Route::get('/average-price/{produce_id}', [ListingController::class, 'getAveragePrice']);

Route::middleware(['auth', 'role:farmer'])->prefix('farmer')->name('farmer.')->group(function () {
    // Listings
    Route::get('/listings', [Farmer\ListingController::class, 'index'])->name('listings.index');
    Route::get('/listings/{listing}/edit', [Farmer\ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [Farmer\ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [Farmer\ListingController::class, 'destroy'])->name('listings.destroy');

    // Orders
    Route::get('/orders', [Farmer\OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{id}/status', [Farmer\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::delete('/orders/{id}', [Farmer\OrderController::class, 'destroy'])->name('orders.destroy');
});


Route::middleware('auth')->group(function () {
    Route::get('/marketplace', [Customer\MarketplaceController::class, 'index'])->name('marketplace');
    Route::get('/marketplace/{listing}', [Customer\MarketplaceController::class, 'show'])->name('marketplace.show');

    Route::get('/farmer/{id}', [MarketplaceController::class, 'showFarmer'])->name('farmer.show');
});

// Shopping Cart routes
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('customer.cart');
    Route::post('/cart/add/{listing}', [CartController::class, 'add'])->name('customer.cart.add');
    Route::put('/cart/update/{cartItem}', [CartController::class, 'update'])->name('customer.cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('customer.cart.remove');
});

// Favorites routes
Route::middleware(['auth'])->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('customer.favorites');
    Route::post('/favorites/{listing}/toggle', [FavoriteController::class, 'toggle'])->name('customer.favorites.toggle');
});

 
// customer orders
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // Orders
    Route::get('/orders', [Customer\OrderController::class, 'index'])->name('orders');
});