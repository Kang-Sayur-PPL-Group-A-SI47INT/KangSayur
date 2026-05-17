<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\View\View;
class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_farmers' => User::where('role', 'farmer')->count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'pending_verifications' => User::where('role', 'farmer')
                ->where('verification_status', 'pending')->count(),
            'total_listings' => Listing::count(),
            'active_listings' => Listing::where('status', 'active')->count(),
            'total_transactions' => Transaction::count(),
            'pending_orders' => Transaction::where('status', 'pending')->count(),
            'paid_orders' => Transaction::where('status', 'paid')->count(),
            'shipped_orders' => Transaction::where('status', 'shipped')->count(),
            'delivered_orders' => Transaction::where('status', 'delivered')->count(),
        ];
        $recentTransactions = Transaction::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        $pendingFarmers = User::where('role', 'farmer')
            ->where('verification_status', 'pending')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        return view('admin.dashboard', compact('stats', 'recentTransactions', 'pendingFarmers'));
    }
}
