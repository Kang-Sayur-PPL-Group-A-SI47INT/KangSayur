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
        
        $user = auth()->user();
        $listingIds = $user->listings()->pluck('listing_id');
       
        // Get transactions that contain items from this farmer's listings
        $transactionIds = TransactionItem::whereIn('listing_listing_id', $listingIds)
            ->pluck('transaction_transaction_id')
            ->unique();
      
        $orders = Transaction::with(['user', 'items.listing.produce'])
            ->whereIn('transaction_id', $transactionIds)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('farmer.orders.index', compact('orders'));
    }
    public function updateStatus(Request $request, $id): RedirectResponse
    {
        $request->validate(['status' => 'required|in:paid,completed']);
        
        // MOCK UPDATE for UI testing
       
        $transaction = Transaction::findOrFail($id);
        // Verify this transaction includes items from the farmer's listings
        $user = auth()->user();
        $listingIds = $user->listings()->pluck('listing_id');
        $hasItems = $transaction->items()->whereIn('listing_listing_id', $listingIds)->exists();
        if (!$hasItems) {
            abort(403);
        }
        $transaction->update(['status' => $request->status]);
        return back()->with('success', 'Status pesanan berhasil diperbarui ke ' . $request->status . '!');
    }
    public function destroy($id): RedirectResponse
    {
        // MOCK DELETE for UI testing
      
        $transaction = Transaction::findOrFail($id);
        // Verify ownership
        $user = auth()->user();
        $listingIds = $user->listings()->pluck('listing_id');
        $hasItems = $transaction->items()->whereIn('listing_listing_id', $listingIds)->exists();
        if (!$hasItems) {
            abort(403);
        }
        // Only allow deleting cancelled orders
        if (!in_array($transaction->status, ['cancelled', 'delivered'])) {
            return back()->with('error', 'Hanya pesanan yang dibatalkan atau selesai yang dapat dihapus.');
        }
        $transaction->delete();
        return back()->with('success', 'Pesanan #' . $id . ' berhasil dihapus!');
    }
}
