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

        <!-- Charts -->
        <div class="grid lg:grid-cols-3 gap-6 mb-8">
            <!-- Monthly Earnings Bar Chart -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">📈 Monthly Earnings</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Last 6 months — paid & completed orders</p>
                    </div>
                </div>
                <div style="position: relative; height: 220px;">
                    <canvas id="farmerEarningsChart"></canvas>
                </div>
            </div>

            <!-- Order Status Donut -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <div class="mb-4">
                    <h2 class="text-lg font-bold text-gray-900">🗂️ Order Status</h2>
                    <p class="text-xs text-gray-400 mt-0.5">All-time breakdown</p>
                </div>
                <div style="position: relative; height: 180px;">
                    <canvas id="farmerOrderStatusChart"></canvas>
                </div>
                {{-- Legend --}}
                <div class="mt-4 space-y-1.5">
                    @php
                        $statusColors = [
                            'pending'   => ['bg' => 'bg-amber-400',  'label' => 'Pending'],
                            'paid'      => ['bg' => 'bg-blue-500',   'label' => 'Paid'],
                            'processing'=> ['bg' => 'bg-indigo-400', 'label' => 'Processing'],
                            'shipping'  => ['bg' => 'bg-purple-500', 'label' => 'Shipping'],
                            'shipped'   => ['bg' => 'bg-violet-500', 'label' => 'Shipped'],
                            'delivered' => ['bg' => 'bg-emerald-500','label' => 'Delivered'],
                            'completed' => ['bg' => 'bg-green-600',  'label' => 'Completed'],
                            'cancelled' => ['bg' => 'bg-red-400',    'label' => 'Cancelled'],
                        ];
                    @endphp
                    @foreach($orderStatusRows as $status => $count)
                        @if($count > 0)
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full {{ $statusColors[$status]['bg'] ?? 'bg-gray-400' }}"></div>
                                <span class="text-gray-600">{{ $statusColors[$status]['label'] ?? ucfirst($status) }}</span>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $count }}</span>
                        </div>
                        @endif
                    @endforeach
                    @if($orderStatusRows->isEmpty())
                        <p class="text-xs text-gray-400 text-center py-2">No orders yet</p>
                    @endif
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

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // ── Monthly Earnings Bar Chart ──────────────────────────────
        const earningsLabels  = @json($monthlyEarnings->pluck('month'));
        const earningsData    = @json($monthlyEarnings->pluck('earnings'));

        new Chart(document.getElementById('farmerEarningsChart'), {
            type: 'bar',
            data: {
                labels: earningsLabels,
                datasets: [{
                    label: 'Earnings (Rp)',
                    data: earningsData,
                    backgroundColor: 'rgba(16, 185, 129, 0.15)',
                    borderColor: '#059669',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID'),
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 }, color: '#6b7280' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: {
                            font: { size: 11 },
                            color: '#6b7280',
                            callback: val => 'Rp ' + (val >= 1000000
                                ? (val / 1000000).toFixed(1) + 'M'
                                : val >= 1000 ? (val / 1000).toFixed(0) + 'K' : val),
                        }
                    }
                }
            }
        });

        // ── Order Status Donut Chart ────────────────────────────────
        @php
            $statusColorMap = [
                'pending'    => '#fbbf24',
                'paid'       => '#3b82f6',
                'processing' => '#818cf8',
                'shipping'   => '#a855f7',
                'shipped'    => '#8b5cf6',
                'delivered'  => '#10b981',
                'completed'  => '#059669',
                'cancelled'  => '#f87171',
            ];
            $statusLabelMap = [
                'pending'    => 'Pending',
                'paid'       => 'Paid',
                'processing' => 'Processing',
                'shipping'   => 'Shipping',
                'shipped'    => 'Shipped',
                'delivered'  => 'Delivered',
                'completed'  => 'Completed',
                'cancelled'  => 'Cancelled',
            ];
            $donutLabels = $orderStatusRows->keys()->map(fn($s) => $statusLabelMap[$s] ?? ucfirst($s))->values();
            $donutData   = $orderStatusRows->values();
            $donutColors = $orderStatusRows->keys()->map(fn($s) => $statusColorMap[$s] ?? '#9ca3af')->values();
        @endphp

        const donutLabels = @json($donutLabels);
        const donutData   = @json($donutData);
        const donutColors = @json($donutColors);

        if (donutData.length > 0 && donutData.some(v => v > 0)) {
            new Chart(document.getElementById('farmerOrderStatusChart'), {
                type: 'doughnut',
                data: {
                    labels: donutLabels,
                    datasets: [{
                        data: donutData,
                        backgroundColor: donutColors,
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.label + ': ' + ctx.parsed + ' orders',
                            }
                        }
                    }
                }
            });
        } else {
            const canvas = document.getElementById('farmerOrderStatusChart');
            const ctx2 = canvas.getContext('2d');
            canvas.style.display = 'none';
            canvas.parentElement.innerHTML += '<p class="text-center text-gray-400 text-sm pt-12">No orders yet</p>';
        }
    });
    </script>
</x-app-layout>
