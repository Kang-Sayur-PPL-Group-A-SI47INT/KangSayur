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
        $query = Listing::with(['farmer', 'produce', 'ratings'])
            ->where('status', 'active');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('produce', fn($p) => $p->where('name', 'like', "%{$search}%"));
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('produce', fn($q) => $q->where('category', $request->category));
        }

        // Price range
        if ($request->filled('min_price')) {
            $query->whereRaw('CAST(price AS UNSIGNED) >= ?', [$request->min_price]);
        }
        if ($request->filled('max_price')) {
            $query->whereRaw('CAST(price AS UNSIGNED) <= ?', [$request->max_price]);
        }

        // City filter
        if ($request->filled('city')) {
            $query->whereHas('farmer', fn($q) => $q->where('city', $request->city));
        }

        // Rating filter
        if ($request->filled('min_rating')) {
            $query->withAvg('ratings', 'rating')
                ->having('ratings_avg_rating', '>=', $request->min_rating);
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        $query = match ($sort) {
            'price_low' => $query->orderByRaw('CAST(price AS UNSIGNED) ASC'),
            'price_high' => $query->orderByRaw('CAST(price AS UNSIGNED) DESC'),
            'popular' => $query->withCount('ratings')->orderBy('ratings_count', 'desc'),
            'nearest' => $this->sortByNearest($query),
            default => $query->orderBy('created_at', 'desc'),
        };

        $listings = $query->paginate(12);
        $categories = Produce::distinct()->pluck('category');
        $cities = \App\Models\User::where('role', 'farmer')
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city');


        return view('marketplace.index', compact('listings', 'categories', 'cities'));
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
