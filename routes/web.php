<?php

require __DIR__.'/auth.php';

use App\Models\Listing;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer\MarketplaceController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\Customer\RatingController;
use App\Http\Controllers\Farmer;
use App\Http\Controllers\Farmer\ListingController;
use App\Http\Controllers\Admin;

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

    // Profile & Dashboard (accessible without verification)
    Route::get('/dashboard', [Farmer\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [Farmer\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [Farmer\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/documents', [Farmer\ProfileController::class, 'updateDocuments'])->name('profile.updateDocuments');
    Route::post('/profile/submit-verification', [Farmer\ProfileController::class, 'submitVerification'])->name('profile.submitVerification');
    // Routes that require farmer verification
    Route::middleware('farmer.verified')->group(function () {
        // Listings
        Route::get('/listings', [Farmer\ListingController::class, 'index'])->name('listings.index');
        Route::get('/listings/create', [Farmer\ListingController::class, 'create'])->name('listings.create');
        Route::post('/listings', [Farmer\ListingController::class, 'store'])->name('listings.store');
        Route::get('/listings/{listing}/edit', [Farmer\ListingController::class, 'edit'])->name('listings.edit');
        Route::put('/listings/{listing}', [Farmer\ListingController::class, 'update'])->name('listings.update');
        Route::delete('/listings/{listing}', [Farmer\ListingController::class, 'destroy'])->name('listings.destroy');
        // Orders
        Route::get('/orders', [Farmer\OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{id}/status', [Farmer\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::post('/orders/{id}/shipping-proof', [Farmer\OrderController::class, 'uploadShippingProof'])->name('orders.uploadShippingProof');
        Route::delete('/orders/{id}', [Farmer\OrderController::class, 'destroy'])->name('orders.destroy');

        // Harvest Calendar
        Route::get('/harvest-calendar', [Farmer\HarvestScheduleController::class, 'index'])->name('harvest-calendar.index');
        Route::post('/harvest-schedules', [Farmer\HarvestScheduleController::class, 'store'])->name('harvest-schedules.store');
        Route::put('/harvest-schedules/{harvestSchedule}', [Farmer\HarvestScheduleController::class, 'update'])->name('harvest-schedules.update');
        Route::delete('/harvest-schedules/{harvestSchedule}', [Farmer\HarvestScheduleController::class, 'destroy'])->name('harvest-schedules.destroy');
    });
});
Route::middleware('auth')->group(function () {
    Route::get('/marketplace', [Customer\MarketplaceController::class, 'index'])->name('marketplace');
    Route::get('/marketplace/{listing}', [Customer\MarketplaceController::class, 'show'])->name('marketplace.show');

    Route::get('/farmer/{id}', [MarketplaceController::class, 'showFarmer'])->where('id', '[0-9]+')->name('farmer.show');
});
// Shopping Cart routes
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('customer.cart');
    Route::post('/cart/add/{listing}', [CartController::class, 'add'])->name('customer.cart.add');
    Route::put('/cart/update/{cartItem}', [CartController::class, 'update'])->name('customer.cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('customer.cart.remove');
});
// Checkout & Payment routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('customer.checkout');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('customer.checkout.process');
    Route::get('/checkout/payment/{transaction}', [CheckoutController::class, 'paymentPage'])->name('customer.checkout.payment');
    Route::post('/checkout/payment/{transaction}/simulate', [CheckoutController::class, 'simulatePayment'])->name('customer.checkout.simulate');
    Route::get('/orders', [CheckoutController::class, 'orders'])->name('customer.orders');
    Route::get('/orders/{transaction}', [CheckoutController::class, 'orderDetail'])->name('customer.orders.detail');
    Route::post('/orders/{transaction}/cancel', [CheckoutController::class, 'cancelOrder'])->name('customer.orders.cancel');
});
// Favorites routes
Route::middleware(['auth'])->group(function () {
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('customer.favorites');
    Route::post('/favorites/{listing}/toggle', [FavoriteController::class, 'toggle'])->name('customer.favorites.toggle');
});

// Rating & Review routes (PBI 25-28)
Route::middleware(['auth'])->group(function () {
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');
    Route::get('/marketplace/{listing}/reviews', [RatingController::class, 'index'])->name('marketplace.reviews');
});

// customer orders
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    // Orders
    Route::get('/orders', [Customer\OrderController::class, 'index'])->name('orders');

    // Harvest Calendar (read-only)
    Route::get('/harvest-calendar', [Customer\HarvestCalendarController::class, 'index'])->name('harvest-calendar.index');
});
// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    // Farmer Verification
    Route::get('/verifications', [Admin\FarmerVerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{user}', [Admin\FarmerVerificationController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{user}/approve', [Admin\FarmerVerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('/verifications/{user}/reject', [Admin\FarmerVerificationController::class, 'reject'])->name('verifications.reject');
    // Order Management (renamed from Transaction)
    Route::get('/transactions', [Admin\TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/{transaction}/status', [Admin\TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
    Route::post('/transactions/{transaction}/cancel', [Admin\TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::post('/transactions/{transaction}/verify-proof', [Admin\TransactionController::class, 'verifyShippingProof'])->name('transactions.verifyProof');
    // User & Listing Management
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/listings', [Admin\ListingController::class, 'index'])->name('listings.index');
    Route::post('/users/{user}/ban', [Admin\UserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [Admin\UserController::class, 'unban'])->name('users.unban');
    Route::delete('/listings/{listing}', [Admin\ListingController::class, 'destroy'])->name('listings.destroy');
});

// Bargain / Offer routes — Customer
Route::middleware(['auth'])->prefix('offers')->name('customer.offers')->group(function () {
    Route::get('/', [Customer\OfferController::class, 'index']);
    Route::post('/{listing}', [Customer\OfferController::class, 'store'])->name('.store');
    Route::get('/{offer}', [Customer\OfferController::class, 'show'])->name('.show');
    Route::post('/{offer}/message', [Customer\OfferController::class, 'sendMessage'])->name('.message');
    Route::post('/{offer}/accept-counter', [Customer\OfferController::class, 'acceptCounter'])->name('.acceptCounter');
    Route::put('/{offer}', [Customer\OfferController::class, 'update'])->name('.update');
    Route::delete('/{offer}', [Customer\OfferController::class, 'destroy'])->name('.destroy');
});

// Bargain / Offer routes — Farmer
Route::middleware(['auth', 'role:farmer', 'farmer.verified'])->prefix('farmer/offers')->name('farmer.offers.')->group(function () {
    Route::get('/', [Farmer\OfferController::class, 'index'])->name('index');
    Route::get('/{offer}', [Farmer\OfferController::class, 'show'])->name('show');
    Route::post('/{offer}/accept', [Farmer\OfferController::class, 'accept'])->name('accept');
    Route::post('/{offer}/reject', [Farmer\OfferController::class, 'reject'])->name('reject');
    Route::post('/{offer}/counter', [Farmer\OfferController::class, 'counter'])->name('counter');
    Route::post('/{offer}/message', [Farmer\OfferController::class, 'sendMessage'])->name('message');
});