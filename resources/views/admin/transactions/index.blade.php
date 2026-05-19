<x-admin-layout>
    @php $title = 'Transaksi'; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Transaksi 💰</h1>
                <p class="text-gray-500 mt-1">Kelola semua transaksi dan status pengiriman</p>
            </div>
        </div>
        <form method="GET" class="flex gap-3 mb-6">
            <select name="status" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Shipping</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" class="px-6 py-2.5 bg-green-800 text-white rounded-xl text-sm font-medium hover:bg-green-900 transition-colors">Filter</button>
        </form>
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Order ID</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Pelanggan</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transactions as $tx)
                        @php
                            $statusBadge = match($tx->status) {
                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'paid' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'shipping' => 'bg-purple-50 text-purple-700 border-purple-200',
                                'delivered' => 'bg-green-50 text-green-700 border-green-200',
                                'cancelled' => 'bg-red-50 text-red-700 border-red-200',
                                default => 'bg-gray-50 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <tr class="hover:bg-green-50/30 transition-colors">
                            <td class="px-6 py-4 text-gray-600 text-sm font-mono">{{ $tx->midtrans_order_id ?? '#' . $tx->transaction_id }}</td>
                            <td class="px-6 py-4 text-gray-900 text-sm">{{ $tx->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-green-700 text-sm font-semibold">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full border {{ $statusBadge }}">
                                    {{ ucfirst($tx->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $tx->created_at->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    {{-- Status progression: paid → shipping → delivered --}}
                                    @if($tx->status === 'paid')
                                        <form method="POST" action="{{ route('admin.transactions.updateStatus', $tx) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="shipping">
                                            <button class="px-3 py-1.5 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg text-xs font-medium hover:bg-purple-100 transition-colors">
                                                🚚 Kirim
                                            </button>
                                        </form>
                                    @endif
                                    @if($tx->status === 'shipping')
                                        <form method="POST" action="{{ route('admin.transactions.updateStatus', $tx) }}">
                                            @csrf
                                            <input type="hidden" name="status" value="delivered">
                                            <button class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-medium hover:bg-green-100 transition-colors">
                                                ✅ Selesai
                                            </button>
                                        </form>
                                    @endif
                                    {{-- Cancel (only for pending/paid) --}}
                                    @if(in_array($tx->status, ['pending', 'paid']))
                                        <form method="POST" action="{{ route('admin.transactions.cancel', $tx) }}" onsubmit="return confirm('Yakin batalkan transaksi?')">
                                            @csrf
                                            <button class="px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-lg text-xs font-medium hover:bg-red-100 transition-colors">
                                                ❌ Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-8 text-gray-400">Tidak ada transaksi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $transactions->appends(request()->query())->links() }}</div>
    </div>
</x-admin-layout>
