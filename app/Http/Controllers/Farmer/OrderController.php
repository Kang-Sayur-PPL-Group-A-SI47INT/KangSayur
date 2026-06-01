<?php

namespace App\Http\Controllers\Farmer;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $listingIds = auth()->user()->listings()->pluck('listing_id');

        $orders = Transaction::with(['user', 'cart.items.listing'])
            ->whereHas('cart.items', fn($q) => $q->whereIn('listing_listing_id', $listingIds))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('farmer.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        $listingIds = auth()->user()->listings()->pluck('listing_id')->toArray();
        $hasItems = $transaction->cart->items->contains(fn($item) => in_array($item->listing_listing_id, $listingIds));

        if (!$hasItems) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:paid,completed']);
        $transaction->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated!');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $listingIds = auth()->user()->listings()->pluck('listing_id')->toArray();
        $hasItems = $transaction->cart->items->contains(fn($item) => in_array($item->listing_listing_id, $listingIds));

        if (!$hasItems) {
            abort(403);
        }

        $transaction->delete();

        return back()->with('success', 'Order deleted successfully!');
    }
}
