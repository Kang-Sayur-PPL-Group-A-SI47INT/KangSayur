<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Offer;
use App\Models\Listing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $offers = auth()->user()->offers()
            ->with('listing.farmer')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('customer.offers', compact('offers'));
    }

    public function store(Request $request, Listing $listing): RedirectResponse
    {
        $request->validate([
            'offered_price' => 'required|numeric|min:1',
        ]);

        $existingOffer = Offer::where('listing_listing_id', $listing->listing_id)
            ->where('user_user_id', auth()->user()->user_id)
            ->where('status', 'pending')
            ->first();

        if ($existingOffer) {
            return back()->with('error', 'You already have a pending offer for this listing.');
        }

        $offer = Offer::create([
            'offered_price' => $request->offered_price,
            'status' => 'pending',
            'listing_listing_id' => $listing->listing_id,
            'user_user_id' => auth()->user()->user_id,
        ]);

        // Initial message
        Message::create([
            'content' => 'I\'d like to offer Rp ' . number_format($request->offered_price, 0, ',', '.') . ' for ' . $listing->title,
            'sender_user_id' => auth()->user()->user_id,
            'receiver_user_id' => $listing->user_user_id,
            'user_user_id' => auth()->user()->user_id,
            'offer_offer_id' => $offer->offer_id,
        ]);

        return redirect()->route('customer.offers.show', $offer)->with('success', 'Offer sent!');
    }

    public function show(Offer $offer): View
    {
        if ($offer->user_user_id !== auth()->user()->user_id) {
            abort(403);
        }

        $offer->load(['listing.farmer', 'messages']);
        $messages = Message::where('offer_offer_id', $offer->offer_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('customer.offer-chat', compact('offer', 'messages'));
    }

    public function sendMessage(Request $request, Offer $offer): RedirectResponse
    {
        if ($offer->user_user_id !== auth()->user()->user_id) {
            abort(403);
        }

        $request->validate(['content' => 'required|string|max:500']);

        Message::create([
            'content' => $request->content,
            'sender_user_id' => auth()->user()->user_id,
            'receiver_user_id' => $offer->listing->user_user_id,
            'user_user_id' => auth()->user()->user_id,
            'offer_offer_id' => $offer->offer_id,
        ]);

        return back();
    }

    public function acceptCounter(Offer $offer): RedirectResponse
    {
        if ($offer->user_user_id !== auth()->user()->user_id) {
            abort(403);
        }

        $offer->update([
            'offered_price' => $offer->counter_price,
            'status' => 'accepted',
        ]);

        Message::create([
            'content' => 'Counter offer accepted at Rp ' . number_format($offer->counter_price, 0, ',', '.'),
            'sender_user_id' => auth()->user()->user_id,
            'receiver_user_id' => $offer->listing->user_user_id,
            'user_user_id' => auth()->user()->user_id,
            'offer_offer_id' => $offer->offer_id,
        ]);

        return back()->with('success', 'Counter offer accepted!');
    }
}
