<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::with('user');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.transactions.index', compact('transactions'));
    }

    public function cancel(Transaction $transaction): RedirectResponse
    {
        $transaction->update(['status' => 'cancelled']);
        return back()->with('success', 'Transaksi telah dibatalkan.');
    }

    //cation for delete from database
    public function delete(Transaction $transaction): RedirectResponse
    {
        // Permanently deletes the transaction from the database
        $transaction->delete();
        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function complete(Transaction $transaction): RedirectResponse
    {
        $transaction->update(['status' => 'completed']);
        return back()->with('success', 'Transaksi telah diselesaikan.');
    }
}
