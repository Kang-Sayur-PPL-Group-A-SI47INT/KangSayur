<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Offer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfferController extends Controller
{
    public function index(): View
    {
        $listingIds = auth()->user()->listings()->pluck('listing_id');

        $offers = Offer::with(['listing', 'user'])
            ->whereIn('listing_listing_id', $listingIds)
            ->orderByRaw("FIELD(status, 'pending', 'countered', 'accepted', 'rejected')")
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return view('farmer.offers.index', compact('offers'));
    }

    public function show(Offer $offer): View
    {
        $this->authorizeOffer($offer);
        $offer->load(['listing', 'user', 'messages']);

        $messages = Message::where('offer_offer_id', $offer->offer_id)
            ->orderBy('created_at', 'asc')
            ->get();

        return view('farmer.offers.show', compact('offer', 'messages'));
    }

    public function accept(Offer $offer): RedirectResponse
    {
        $this->authorizeOffer($offer);
        $offer->update(['status' => 'accepted']);

        // System message
        Message::create([
            'content' => 'Offer accepted at Rp ' . number_format($offer->currentPrice(), 0, ',', '.'),
            'sender_user_id' => auth()->user()->user_id,
            'receiver_user_id' => $offer->user_user_id,
            'user_user_id' => auth()->user()->user_id,
            'offer_offer_id' => $offer->offer_id,
        ]);

        return back()->with('success', 'Offer accepted!');
    }

    public function reject(Offer $offer): RedirectResponse
    {
        $this->authorizeOffer($offer);
        $offer->update(['status' => 'rejected']);

        Message::create([
            'content' => 'Offer rejected.',
            'sender_user_id' => auth()->user()->user_id,
            'receiver_user_id' => $offer->user_user_id,
            'user_user_id' => auth()->user()->user_id,
            'offer_offer_id' => $offer->offer_id,
        ]);

        return back()->with('success', 'Offer rejected.');
    }

    public function counter(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorizeOffer($offer);

        $request->validate([
            'counter_price' => 'required|numeric|min:1',
        ]);

        $offer->update([
            'counter_price' => $request->counter_price,
            'status' => 'countered',
        ]);

        Message::create([
            'content' => 'Counter offer: Rp ' . number_format($request->counter_price, 0, ',', '.'),
            'sender_user_id' => auth()->user()->user_id,
            'receiver_user_id' => $offer->user_user_id,
            'user_user_id' => auth()->user()->user_id,
            'offer_offer_id' => $offer->offer_id,
        ]);

        return back()->with('success', 'Counter offer sent!');
    }

    public function sendMessage(Request $request, Offer $offer): RedirectResponse
    {
        $this->authorizeOffer($offer);

        $request->validate(['content' => 'required|string|max:500']);

        Message::create([
            'content' => $request->content,
            'sender_user_id' => auth()->user()->user_id,
            'receiver_user_id' => $offer->user_user_id,
            'user_user_id' => auth()->user()->user_id,
            'offer_offer_id' => $offer->offer_id,
        ]);

        return back();
    }

    private function authorizeOffer(Offer $offer): void
    {
        $listingIds = auth()->user()->listings()->pluck('listing_id')->toArray();
        if (!in_array($offer->listing_listing_id, $listingIds)) {
            abort(403);
        }
    }
}
