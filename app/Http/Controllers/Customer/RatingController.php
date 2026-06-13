<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Rating;
use App\Models\Transaction;
use Illuminate\Http\Request;

class RatingController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'score'              => 'required|integer|min:1|max:5',
            'comment'            => 'nullable|string|max:1000',
            'listing_listing_id' => 'required|exists:listings,listing_id',
            'transaction_transaction_id' => 'required|exists:transactions,transaction_id',
        ]);

        $user = auth()->user();
        $listingId = $request->listing_listing_id;
        $transactionId = $request->transaction_transaction_id;


        $transaction = Transaction::where('transaction_id', $transactionId)
            ->where('user_user_id', $user->user_id)
            ->where('status', 'delivered')
            ->first();

        if (! $transaction) {
            return redirect()->back()->with('error', 'You can only review items from delivered orders.');
        }


        $hasItem = $transaction->items()
            ->where('listing_listing_id', $listingId)
            ->exists();

        if (! $hasItem) {
            return redirect()->back()->with('error', 'This product is not part of this order.');
        }


        $existing = Rating::where('listing_listing_id', $listingId)
            ->where('user_user_id', $user->user_id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You have already reviewed this product.');
        }

        Rating::create([
            'score'                      => $request->score,
            'comment'                    => $request->comment,
            'listing_listing_id'         => $listingId,
            'user_user_id'               => $user->user_id,
            'transaction_transaction_id' => $transactionId,
        ]);

        return redirect()->back()->with('success', 'Thank you for your review! 🌟');
    }


    public function destroy(Rating $rating)
    {
        if ($rating->user_user_id !== auth()->user()->user_id) {
            abort(403, 'You can only delete your own reviews.');
        }

        $rating->delete();

        return redirect()->back()->with('success', 'Your review has been deleted.');
    }


    public function index(Listing $listing)
    {
        $listing->load(['farmer', 'produce']);

        $ratings = Rating::where('listing_listing_id', $listing->listing_id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $distribution = [];
        $totalCount = Rating::where('listing_listing_id', $listing->listing_id)->count();
        for ($i = 5; $i >= 1; $i--) {
            $count = Rating::where('listing_listing_id', $listing->listing_id)
                ->where('score', $i)
                ->count();
            $distribution[$i] = [
                'count'      => $count,
                'percentage' => $totalCount > 0 ? round(($count / $totalCount) * 100) : 0,
            ];
        }

        $averageRating = $listing->averageRating();

        
        $userRating = null;
        if (auth()->check()) {
            $userRating = Rating::where('listing_listing_id', $listing->listing_id)
                ->where('user_user_id', auth()->user()->user_id)
                ->first();
        }

        return view('marketplace.reviews', compact(
            'listing', 'ratings', 'distribution', 'averageRating', 'totalCount', 'userRating'
        ));
    }
}
