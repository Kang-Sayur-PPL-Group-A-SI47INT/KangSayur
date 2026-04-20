<?php

use App\Models\Listing;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredListings = Listing::with(['farmer', 'produce', 'ratings'])
        ->where('status', 'active')
        ->orderBy('created_at', 'desc')
        ->take(6)
        ->get();

    return view('welcome', compact('featuredListings'));
})->name('home');
