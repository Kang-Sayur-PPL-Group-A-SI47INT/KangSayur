<?php
namespace App\Http\Controllers\Farmer;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Rating;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\View\View;
class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $listingIds = $user->listings()->pluck('listing_id');

        $activeListings = $user->listings()->where('status', 'active')->count();

        // Calculate total earnings for the farmer's items in paid/completed/processing/shipped/delivered transactions
        $totalEarnings = TransactionItem::whereIn('listing_listing_id', $listingIds)
            ->whereHas('transaction', function ($q) {
                $q->whereIn('status', ['paid', 'processing', 'shipping', 'shipped', 'delivered', 'completed']);
            })
            ->sum('subtotal');

        // Count new orders (paid or processing) that contain the farmer's listings
        $newOrders = Transaction::whereIn('status', ['paid', 'processing'])
            ->whereHas('items', function ($q) use ($listingIds) {
                $q->whereIn('listing_listing_id', $listingIds);
            })
            ->count();

        $stats = [
            'total_earnings' => $totalEarnings,
            'new_orders' => $newOrders,
            'active_listings' => $activeListings,
            'pending_offers' => 0,
            'average_rating' => round($user->averageRating() ?? 0, 1),
        ];

        // Recent ratings
        $recentRatings = Rating::with(['user', 'listing'])
            ->whereIn('listing_listing_id', $listingIds)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Recent orders
        $recentOrders = Transaction::with(['user', 'items.listing'])
            ->whereHas('items', fn($q) => $q->whereIn('listing_listing_id', $listingIds))
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Daily farm tip
        $tips = [
            "🌱 Water your crops early morning or late evening to minimize evaporation.",
            "🌾 Rotate your crops each season to maintain soil nutrients.",
            "🐛 Companion planting with marigolds can help repel common pests naturally.",
            "💧 Mulching around plants helps retain moisture and suppress weeds.",
            "☀️ Most vegetables need at least 6 hours of direct sunlight daily.",
            "🌿 Adding compost regularly improves soil structure and fertility.",
            "🍅 Prune tomato suckers for larger, healthier fruits.",
        ];
        $dailyTip = $tips[date('z') % count($tips)];

        // --- Chart data ---

        // Monthly earnings for last 6 months
        $monthlyEarnings = collect();
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end   = now()->subMonths($i)->endOfMonth();
            $earnings = TransactionItem::whereIn('listing_listing_id', $listingIds)
                ->whereHas('transaction', function ($q) use ($start, $end) {
                    $q->whereIn('status', ['paid', 'processing', 'shipping', 'shipped', 'delivered', 'completed'])
                      ->whereBetween('created_at', [$start, $end]);
                })
                ->sum('subtotal');
            $monthlyEarnings->push([
                'month'    => $start->format('M Y'),
                'earnings' => (float) $earnings,
            ]);
        }

        // Order status breakdown (all time for this farmer)
        $orderStatusRows = Transaction::whereHas('items', fn($q) => $q->whereIn('listing_listing_id', $listingIds))
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('farmer.dashboard', compact(
            'stats', 'recentRatings', 'recentOrders', 'dailyTip',
            'monthlyEarnings', 'orderStatusRows'
        ));
    }
}
