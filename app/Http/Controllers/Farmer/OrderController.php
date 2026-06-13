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

        $orders = Transaction::with(['user', 'items.listing'])
            ->whereHas('items', fn($q) => $q->whereIn('listing_listing_id', $listingIds))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('farmer.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        $request->validate(['status' => 'required|in:paid,completed']);
        
        $transaction = Transaction::findOrFail($id);
        // Verify this transaction includes items from the farmer's listings
        $user = auth()->user();
        $listingIds = $user->listings()->pluck('listing_id');
        $hasItems = $transaction->items()->whereIn('listing_listing_id', $listingIds)->exists();
        if (!$hasItems) {
            abort(403);
        }

        $request->validate(['status' => 'required|in:paid,completed']);
        $transaction->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated!');
    }

    /**
     * Upload shipping proof for an order.
     */
    public function uploadShippingProof(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'shipping_proof' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'shipping_proof.required' => 'Bukti pengiriman wajib diupload.',
            'shipping_proof.image' => 'File harus berupa gambar.',
            'shipping_proof.mimes' => 'Format gambar: JPEG, PNG, JPG, atau GIF.',
            'shipping_proof.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $transaction = Transaction::findOrFail($id);

        // Verify this transaction includes items from the farmer's listings
        $user = auth()->user();
        $listingIds = $user->listings()->pluck('listing_id');
        $hasItems = $transaction->items()->whereIn('listing_listing_id', $listingIds)->exists();
        if (!$hasItems) {
            abort(403);
        }

        // Only allow upload for 'paid' status
        if ($transaction->status !== 'paid') {
            return back()->with('error', 'Bukti pengiriman hanya dapat diupload untuk pesanan yang sudah dibayar.');
        }

        // Store the proof image
        $path = $request->file('shipping_proof')->store('shipping-proofs', 'public');

        $transaction->update([
            'shipping_proof' => $path,
            'shipping_proof_uploaded_at' => now(),
            'status' => 'shipping',
        ]);

        return back()->with('success', 'Bukti pengiriman berhasil diupload! Status pesanan diubah ke Shipping. 🚚');
    }

    public function destroy($id): RedirectResponse
    {
        $transaction = Transaction::findOrFail($id);
        // Verify ownership
        $user = auth()->user();
        $listingIds = $user->listings()->pluck('listing_id');
        $hasItems = $transaction->items()->whereIn('listing_listing_id', $listingIds)->exists();
        if (!$hasItems) {
            abort(403);
        }

        $transaction->delete();

        return back()->with('success', 'Order deleted successfully!');
    }
}
