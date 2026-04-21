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

// Placeholder routes for redirect targets (to be replaced with actual controllers later)
Route::get('/login', function () {
    return 'Login — Coming Soon';
})->middleware('guest')->name('login');

Route::get('/marketplace', function () {
    return 'Marketplace — Coming Soon';
})->name('marketplace');

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

Route::middleware(['auth', 'role:customer'])->name('customer.')->group(function () {
    // Cart
    Route::get('/cart', [Customer\CartController::class, 'index'])->name('cart');
    Route::post('/cart/add/{listing}', [Customer\CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cartItem}', [Customer\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [Customer\CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout', [Customer\CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/process', [Customer\CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/payment/{transaction}', [Customer\CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/success/{transaction}', [Customer\CheckoutController::class, 'success'])->name('checkout.success');

    // Offers
    Route::get('/offers', [Customer\OfferController::class, 'index'])->name('offers');
    Route::post('/offers/{listing}', [Customer\OfferController::class, 'store'])->name('offers.store');
    Route::get('/offers/{offer}/chat', [Customer\OfferController::class, 'show'])->name('offers.show');
    Route::post('/offers/{offer}/message', [Customer\OfferController::class, 'sendMessage'])->name('offers.message');
    Route::post('/offers/{offer}/accept-counter', [Customer\OfferController::class, 'acceptCounter'])->name('offers.acceptCounter');

    // Wishlist
    Route::get('/wishlist', [Customer\WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/{listing}', [Customer\WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // Ratings
    Route::post('/rating/{listing}', [Customer\RatingController::class, 'store'])->name('rating.store');

    // Orders
    Route::get('/orders', [Customer\OrderController::class, 'index'])->name('orders');
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