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
            'total_users'           => User::count(),
            'total_farmers'         => User::where('role', 'farmer')->count(),
            'total_customers'       => User::where('role', 'customer')->count(),
            'pending_verifications' => User::where('role', 'farmer')
                ->where('verification_status', 'pending')->count(),
            'total_listings'        => Listing::count(),
            'active_listings'       => Listing::where('status', 'active')->count(),
            'total_transactions'    => Transaction::count(),
            'pending_orders'        => Transaction::where('status', 'pending')->count(),
            'paid_orders'           => Transaction::where('status', 'paid')->count(),
            'shipped_orders'        => Transaction::where('status', 'shipping')->count(),
            'delivered_orders'      => Transaction::where('status', 'delivered')->count(),
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

        // --- Chart data ---

        // Monthly revenue (total_price + delivery_fee) for last 6 months — paid/active orders
        $monthlyRevenue = collect();
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end   = now()->subMonths($i)->endOfMonth();
            $revenue = Transaction::whereIn('status', ['paid', 'processing', 'shipping', 'shipped', 'delivered', 'completed'])
                ->whereBetween('created_at', [$start, $end])
                ->selectRaw('COALESCE(SUM(total_price + delivery_fee), 0) as total')
                ->value('total');
            $monthlyRevenue->push([
                'month'   => $start->format('M Y'),
                'revenue' => (float) $revenue,
            ]);
        }

        // Monthly new user registrations for last 6 months
        $monthlyUsers = collect();
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end   = now()->subMonths($i)->endOfMonth();
            $count = User::whereBetween('created_at', [$start, $end])->count();
            $monthlyUsers->push([
                'month' => $start->format('M Y'),
                'count' => $count,
            ]);
        }

        // Order status breakdown (all time)
        $orderStatusBreakdown = [
            'Pending'   => $stats['pending_orders'],
            'Paid'      => $stats['paid_orders'],
            'Shipping'  => $stats['shipped_orders'],
            'Delivered' => $stats['delivered_orders'],
            'Cancelled' => Transaction::where('status', 'cancelled')->count(),
        ];

        return view('admin.dashboard', compact(
            'stats', 'recentTransactions', 'pendingFarmers',
            'monthlyRevenue', 'monthlyUsers', 'orderStatusBreakdown'
        ));
    }
}
