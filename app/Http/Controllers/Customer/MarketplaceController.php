<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Produce;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MarketplaceController extends Controller
{
    public function index(Request $request): View
    {
        return view('marketplace.index');
    }

    public function show(Listing $listing): View
    {
        $listing->load(['farmer', 'produce', 'ratings.user']);

        $relatedListings = Listing::where('produce_produce_id', $listing->produce_produce_id)
            ->where('listing_id', '!=', $listing->listing_id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        $userRating = null;
        $isWishlisted = false;
        if (auth()->check()) {
            $userRating = $listing->ratings()
                ->where('user_user_id', auth()->user()->user_id)
                ->first();
            
        }

        return view('marketplace.show', compact('listing', 'relatedListings', 'userRating'));
    }

    public function showFarmer($id): View
    {
        $farmer = User::where('user_id', $id)->firstOrFail();

        // calculate average rating
        $avgRating = $farmer->ratings()->avg('score') ?? 0;

        // count total sales (based on listings sold or transactions if you have it)
        $totalSales = $farmer->listings()->count();

        // score formula
        $score = ($avgRating * 0.7) + ($totalSales * 0.3);

        $score = round($score, 2);

        return view('farmer.profile', compact('farmer', 'score'));
    }
    
    private function sortByNearest($query)
    {
        $user = auth()->user();
        if ($user && $user->latitude && $user->longitude) {
            return $query->selectRaw('*, (6371 * acos(cos(radians(?)) * cos(radians(users.latitude)) * cos(radians(users.longitude) - radians(?)) + sin(radians(?)) * sin(radians(users.latitude)))) AS distance', [$user->latitude, $user->longitude, $user->latitude])
                ->join('users', 'listings.user_user_id', '=', 'users.user_id')
                ->orderBy('distance', 'asc');
        }
        return $query->orderBy('created_at', 'desc');
    }
}
