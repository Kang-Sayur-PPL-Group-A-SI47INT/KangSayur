<?php

use Illuminate\Support\Facades\Route;
<<<<<<< Updated upstream
=======
use App\Http\Controllers\Customer;
use App\Http\Controllers\Customer\MarketplaceController;
>>>>>>> Stashed changes

Route::get('/', function () {
    return view('welcome');
});
<<<<<<< Updated upstream
=======

Route::middleware('auth')->group(function () {
    Route::get('/marketplace', [Customer\MarketplaceController::class, 'index'])->name('marketplace');
    Route::get('/marketplace/{listing}', [Customer\MarketplaceController::class, 'show'])->name('marketplace.show');
    Route::get('/farmer/{id}', [MarketplaceController::class, 'showFarmer'])->name('farmer.show');
});
>>>>>>> Stashed changes
