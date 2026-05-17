<?php

use App\Models\Listing;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Customer\CartController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\FavoriteController;
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

require __DIR__.'/auth.php';

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
        Route::delete('/orders/{id}', [Farmer\OrderController::class, 'destroy'])->name('orders.destroy');
    });
});
Route::middleware('auth')->group(function () {
    Route::get('/marketplace', [Customer\MarketplaceController::class, 'index'])->name('marketplace');
    Route::get('/marketplace/{listing}', [Customer\MarketplaceController::class, 'show'])->name('marketplace.show');
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
// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    // Farmer Verification
    Route::get('/verifications', [Admin\FarmerVerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{user}', [Admin\FarmerVerificationController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{user}/approve', [Admin\FarmerVerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('/verifications/{user}/reject', [Admin\FarmerVerificationController::class, 'reject'])->name('verifications.reject');
    // Transaction Management
    Route::get('/transactions', [Admin\TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions/{transaction}/status', [Admin\TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
    Route::post('/transactions/{transaction}/cancel', [Admin\TransactionController::class, 'cancel'])->name('transactions.cancel');
    // User & Listing Management
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/listings', [Admin\ListingController::class, 'index'])->name('listings.index');
});
