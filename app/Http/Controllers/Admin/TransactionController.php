<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
class TransactionController extends Controller
{
    /**
     * List all transactions with status filter.
     */
    public function index(Request $request): View
    {
        $query = Transaction::with('user');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.transactions.index', compact('transactions'));
    }
    /**
     * Update transaction status through the order lifecycle.
     * Allowed transitions: paid → shipping → delivered
     */
    public function updateStatus(Request $request, Transaction $transaction): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:paid,shipping,delivered',
        ]);
        $newStatus = $request->status;
        $currentStatus = $transaction->status;
        // Validate allowed transitions
        $allowed = match ($currentStatus) {
            'pending' => ['paid'],
            'paid' => ['shipping'],
            'shipping' => ['delivered'],
            default => [],
        };
        if (!in_array($newStatus, $allowed)) {
            return back()->with('error', "Tidak dapat mengubah status dari '{$currentStatus}' ke '{$newStatus}'.");
        }
        $updateData = ['status' => $newStatus];
        if ($newStatus === 'paid' && !$transaction->paid_at) {
            $updateData['paid_at'] = now();
        }
        $transaction->update($updateData);
        return back()->with('success', "Status transaksi berhasil diubah ke '{$newStatus}'.");
    }
    /**
     * Cancel a transaction (only pending or paid).
     */
    public function cancel(Transaction $transaction): RedirectResponse
    {
        if (!in_array($transaction->status, ['pending', 'paid'])) {
            return back()->with('error', 'Hanya transaksi pending atau paid yang dapat dibatalkan.');
        }
        // Restore stock if the order was paid (stock was decremented)
        if ($transaction->status === 'paid') {
            foreach ($transaction->items as $item) {
                $listing = $item->listing;
                if ($listing) {
                    $listing->update([
                        'quantity' => $listing->quantity + $item->quantity,
                        'status' => $listing->quantity + $item->quantity > 0 && $listing->status === 'sold_out'
                            ? 'active' : $listing->status,
                    ]);
                }
            }
        }
        $transaction->update(['status' => 'cancelled']);
        return back()->with('success', 'Transaksi berhasil dibatalkan.');
    }
}
