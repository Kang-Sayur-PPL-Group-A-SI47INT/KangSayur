<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Wishlist;

class FavoriteController extends Controller
{
    /**
     * Display the user's favorite products.
     */
    public function index()
    {
        $user = auth()->user();

        $favorites = Wishlist::where('user_user_id', $user->user_id)
            ->with(['listing.farmer', 'listing.produce', 'listing.ratings'])
            ->latest()
            ->get();

        return view('customer.favorites', compact('favorites'));
    }

    /**
     * Toggle a listing in the user's favorites.
     */
    public function toggle(Listing $listing)
    {
        $user = auth()->user();

        $existing = Wishlist::where('user_user_id', $user->user_id)
            ->where('listing_listing_id', $listing->listing_id)
            ->first();

        if ($existing) {
            $existing->delete();
            return redirect()->back()->with('success', 'Removed from favorites.');
        }

        Wishlist::create([
            'user_user_id' => $user->user_id,
            'listing_listing_id' => $listing->listing_id,
        ]);

        return redirect()->back()->with('success', 'Added to favorites!');
    }
}
