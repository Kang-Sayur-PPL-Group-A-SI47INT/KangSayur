<x-app-layout>
    @php $title = 'Farmer Dashboard'; @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}! 🌾</h1>
                <p class="text-gray-500 mt-1">Here's what's happening on your farm today.</p>
            </div>
            <a href="{{ route('farmer.listings.create') }}" class="px-5 py-2.5 bg-green-800 text-white font-semibold rounded-full hover:bg-green-900 transition-all shadow-lg shadow-green-200/50">
                + New Listing
            </a>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            @foreach([
                ['label' => 'Total Earnings', 'value' => 'Rp ' . number_format($stats['total_earnings'], 0, ',', '.'), 'icon' => '💰', 'bg' => 'bg-emerald-50 border-emerald-100'],
                ['label' => 'New Orders', 'value' => $stats['new_orders'], 'icon' => '📦', 'bg' => 'bg-blue-50 border-blue-100'],
                ['label' => 'Active Listings', 'value' => $stats['active_listings'], 'icon' => '🌱', 'bg' => 'bg-green-50 border-green-100'],
                ['label' => 'Pending Offers', 'value' => $stats['pending_offers'], 'icon' => '💬', 'bg' => 'bg-amber-50 border-amber-100'],
                ['label' => 'Avg. Rating', 'value' => $stats['average_rating'] . '/5 ⭐', 'icon' => '⭐', 'bg' => 'bg-yellow-50 border-yellow-100'],
            ] as $stat)
                <div class="rounded-2xl border p-5 {{ $stat['bg'] }} hover:shadow-lg transition-all duration-300">
                    <div class="text-2xl mb-2">{{ $stat['icon'] }}</div>
                    <p class="text-xl font-bold text-gray-900">{{ $stat['value'] }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        <!-- Daily Tip -->
        <div class="bg-gradient-to-r from-green-800 to-emerald-700 rounded-2xl p-6 mb-8 text-white">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">🌿</div>
                <div>
                    <h3 class="font-bold text-lg mb-1">Daily Farm Tip</h3>
                    <p class="text-green-100 leading-relaxed">{{ $dailyTip }}</p>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Recent Orders -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-900">Recent Orders</h2>
                    <a href="{{ route('farmer.orders.index') }}" class="text-sm text-green-700 hover:text-green-800 font-medium">View All →</a>
                </div>
                <div class="space-y-3">
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                            <div class="min-w-0 flex-1">
                                <p class="font-semibold text-gray-900 text-sm">{{ $order->user->name ?? 'Customer' }}</p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $order->items->map(fn($item) => ($item->listing->title ?? 'Item') . ' (x' . $item->quantity . ')')->implode(', ') }}
                                </p>
                                <p class="text-green-700 font-bold text-sm mt-1">Rp {{ number_format($order->grandTotal(), 0, ',', '.') }}</p>
                            </div>
                            <div class="flex gap-2 flex-shrink-0 ml-3">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $order->status === 'paid' ? 'bg-blue-100 text-blue-700' : ($order->status === 'completed' || $order->status === 'delivered' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="text-4xl mb-2">📦</div>
                            <p class="text-gray-400 text-sm">No recent orders</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Ratings -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Recent Reviews</h2>
                <div class="space-y-3">
                    @forelse($recentRatings as $rating)
                        <div class="p-4 bg-gray-50 rounded-xl">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-gray-900 text-sm">{{ $rating->user->name }}</p>
                                <div class="flex text-amber-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $rating->rating ? '★' : '☆' }}
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">{{ $rating->listing->title }}</p>
                            @if($rating->comment)
                                <p class="text-sm text-gray-600 mt-1 italic">"{{ $rating->comment }}"</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="text-4xl mb-2">⭐</div>
                            <p class="text-gray-400 text-sm">No reviews yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['route' => 'farmer.listings.index', 'icon' => '🌱', 'label' => 'Manage Produce'],
                ['route' => 'farmer.orders.index', 'icon' => '📦', 'label' => 'Orders'],
                ['route' => 'farmer.harvest-calendar.index', 'icon' => '📅', 'label' => 'Harvest Calendar'],
                ['route' => 'farmer.profile.edit', 'icon' => '👤', 'label' => 'Edit Profile'],
            ] as $link)
                <a href="{{ route($link['route']) }}" class="bg-white border border-gray-100 rounded-2xl p-5 text-center hover:shadow-lg hover:shadow-green-50 transition-all duration-300 hover:-translate-y-1 group">
                    <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">{{ $link['icon'] }}</div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $link['label'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
