<?php

use App\Models\Listing;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    return view('welcome');
});

// Placeholder routes for redirect targets (to be replaced with actual controllers later)
Route::get('/login', function () {
    return 'Login — Coming Soon';
})->middleware('guest')->name('login');

Route::get('/marketplace', function () {
    return 'Marketplace — Coming Soon';
})->name('marketplace');

Route::get('/farmer/dashboard', function () {
    return 'Farmer Dashboard — Coming Soon';
})->middleware('auth')->name('farmer.dashboard');

require __DIR__.'/auth.php';
    $featuredListings = Listing::with(['farmer', 'produce', 'ratings'])
        ->where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();

    return view('welcome', compact('featuredListings'));
})->name('home');

