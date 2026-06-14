<x-admin-layout>
    @php $title = 'Dashboard'; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin 🎛️</h1>
            <p class="text-gray-500 mt-1">Ringkasan aktivitas sistem KangSayur</p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @php
                $cards = [
                    ['label' => 'Total Pengguna', 'value' => $stats['total_users'],    'icon' => '👥', 'color' => 'from-blue-50 to-blue-100 border-blue-200',       'text' => 'text-blue-700'],
                    ['label' => 'Petani',          'value' => $stats['total_farmers'],  'icon' => '🌾', 'color' => 'from-green-50 to-green-100 border-green-200',     'text' => 'text-green-700'],
                    ['label' => 'Pelanggan',       'value' => $stats['total_customers'],'icon' => '🛒', 'color' => 'from-purple-50 to-purple-100 border-purple-200', 'text' => 'text-purple-700'],
                    ['label' => 'Listing Aktif',   'value' => $stats['active_listings'],'icon' => '📦', 'color' => 'from-emerald-50 to-emerald-100 border-emerald-200','text' => 'text-emerald-700'],
                ];
            @endphp
            @foreach($cards as $card)
                <div class="bg-gradient-to-br {{ $card['color'] }} border rounded-2xl p-5 hover:shadow-md transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-2xl">{{ $card['icon'] }}</span>
                        <span class="{{ $card['text'] }} text-2xl font-bold">{{ $card['value'] }}</span>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Order Status Chips --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-8">
            @php
                $orderCards = [
                    ['label' => 'Pending',            'value' => $stats['pending_orders'],        'color' => 'bg-amber-50 text-amber-700 border-amber-200'],
                    ['label' => 'Paid',               'value' => $stats['paid_orders'],           'color' => 'bg-blue-50 text-blue-700 border-blue-200'],
                    ['label' => 'Shipping',           'value' => $stats['shipped_orders'],        'color' => 'bg-purple-50 text-purple-700 border-purple-200'],
                    ['label' => 'Delivered',          'value' => $stats['delivered_orders'],      'color' => 'bg-green-50 text-green-700 border-green-200'],
                    ['label' => 'Verifikasi Pending', 'value' => $stats['pending_verifications'], 'color' => 'bg-orange-50 text-orange-700 border-orange-200'],
                ];
            @endphp
            @foreach($orderCards as $card)
                <div class="{{ $card['color'] }} border rounded-xl p-4 text-center">
                    <p class="text-2xl font-bold">{{ $card['value'] }}</p>
                    <p class="text-xs font-medium mt-1 opacity-80">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Charts Row --}}
        <div class="grid lg:grid-cols-3 gap-6 mb-6">

            {{-- Monthly Revenue Line Chart --}}
            <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">📈 Revenue Bulanan</h2>
                        <p class="text-xs text-gray-400 mt-0.5">6 bulan terakhir — transaksi terbayar</p>
                    </div>
                </div>
                <div style="position: relative; height: 200px;">
                    <canvas id="adminRevenueChart"></canvas>
                </div>
            </div>

            {{-- Order Status Donut --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="mb-4">
                    <h2 class="text-base font-bold text-gray-900">🗂️ Status Order</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Distribusi semua order</p>
                </div>
                <div style="position: relative; height: 160px;">
                    <canvas id="adminOrderStatusChart"></canvas>
                </div>
                <div class="mt-4 space-y-2">
                    @php
                        $dotColors = [
                            'Pending'   => 'bg-amber-400',
                            'Paid'      => 'bg-blue-500',
                            'Shipping'  => 'bg-purple-500',
                            'Delivered' => 'bg-emerald-500',
                            'Cancelled' => 'bg-red-400',
                        ];
                    @endphp
                    @foreach($orderStatusBreakdown as $label => $count)
                        <div class="flex items-center justify-between text-xs">
                            <div class="flex items-center gap-1.5">
                                <div class="w-2.5 h-2.5 rounded-full {{ $dotColors[$label] ?? 'bg-gray-400' }}"></div>
                                <span class="text-gray-500">{{ $label }}</span>
                            </div>
                            <span class="font-semibold text-gray-800">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Monthly Users Bar Chart --}}
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm mb-6">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-base font-bold text-gray-900">👥 Registrasi Pengguna Baru</h2>
                    <p class="text-xs text-gray-400 mt-0.5">6 bulan terakhir</p>
                </div>
            </div>
            <div style="position: relative; height: 180px;">
                <canvas id="adminUsersChart"></canvas>
            </div>
        </div>

        {{-- Bottom Two-Column Grid --}}
        <div class="grid lg:grid-cols-2 gap-6">

            {{-- Pending Verifications --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">📋 Verifikasi Menunggu</h2>
                    <a href="{{ route('admin.verifications.index') }}" class="text-sm text-green-700 hover:text-green-800 font-medium">Lihat Semua →</a>
                </div>
                @forelse($pendingFarmers as $farmer)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center text-amber-700 text-sm font-bold">
                                {{ strtoupper(substr($farmer->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-gray-900 text-sm font-medium">{{ $farmer->name }}</p>
                                <p class="text-gray-400 text-xs">{{ $farmer->email }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.verifications.show', $farmer) }}"
                           class="px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-medium hover:bg-amber-100 transition-colors">
                            Review
                        </a>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-3xl mb-2">✅</div>
                        <p class="text-gray-400 text-sm">Tidak ada verifikasi yang menunggu.</p>
                    </div>
                @endforelse
            </div>

            {{-- Recent Transactions --}}
            <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold text-gray-900">📦 Order Terbaru</h2>
                    <a href="{{ route('admin.transactions.index') }}" class="text-sm text-green-700 hover:text-green-800 font-medium">Lihat Semua →</a>
                </div>
                @forelse($recentTransactions as $tx)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="text-gray-900 text-sm font-medium">{{ $tx->midtrans_order_id ?? '#' . $tx->transaction_id }}</p>
                            <p class="text-gray-400 text-xs">{{ $tx->user->name ?? 'N/A' }} · {{ $tx->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-green-700 text-sm font-semibold">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</p>
                            @php
                                $badgeColor = match($tx->status) {
                                    'pending'   => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'paid'      => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'shipping'  => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'delivered' => 'bg-green-50 text-green-700 border-green-200',
                                    'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                    default     => 'bg-gray-50 text-gray-600 border-gray-200',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs border {{ $badgeColor }}">{{ ucfirst($tx->status) }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="text-3xl mb-2">📭</div>
                        <p class="text-gray-400 text-sm">Belum ada transaksi.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const gridColor = 'rgba(0,0,0,0.05)';
        const tickColor = '#6b7280';
        const tickFont  = { size: 11 };

        // ── Monthly Revenue Line Chart ─────────────────────────────────
        const revLabels = @json($monthlyRevenue->pluck('month'));
        const revData   = @json($monthlyRevenue->pluck('revenue'));

        new Chart(document.getElementById('adminRevenueChart'), {
            type: 'line',
            data: {
                labels: revLabels,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: revData,
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5,150,105,0.07)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#059669',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.35,
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
                        grid: { color: gridColor },
                        ticks: { color: tickColor, font: tickFont },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: tickColor,
                            font: tickFont,
                            callback: v => v >= 1000000
                                ? 'Rp ' + (v / 1000000).toFixed(1) + 'M'
                                : v >= 1000 ? 'Rp ' + (v / 1000).toFixed(0) + 'K'
                                : 'Rp ' + v,
                        }
                    }
                }
            }
        });

        // ── Order Status Donut ─────────────────────────────────────────
        const statusLabels = @json(array_keys($orderStatusBreakdown));
        const statusData   = @json(array_values($orderStatusBreakdown));
        const statusColors = ['#fbbf24', '#3b82f6', '#a855f7', '#10b981', '#f87171'];

        new Chart(document.getElementById('adminOrderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: statusColors,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: { label: ctx => ctx.label + ': ' + ctx.parsed + ' orders' }
                    }
                }
            }
        });

        // ── Monthly Users Bar Chart ────────────────────────────────────
        const userLabels = @json($monthlyUsers->pluck('month'));
        const userData   = @json($monthlyUsers->pluck('count'));

        new Chart(document.getElementById('adminUsersChart'), {
            type: 'bar',
            data: {
                labels: userLabels,
                datasets: [{
                    label: 'Pengguna Baru',
                    data: userData,
                    backgroundColor: 'rgba(99,102,241,0.12)',
                    borderColor: '#6366f1',
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
                        callbacks: { label: ctx => ctx.parsed.y + ' pengguna baru' }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: tickColor, font: tickFont },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: { color: tickColor, font: tickFont, precision: 0 }
                    }
                }
            }
        });
    });
    </script>
</x-admin-layout>
