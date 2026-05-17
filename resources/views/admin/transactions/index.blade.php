<x-admin-layout>
    @php $title = 'Transaksi'; @endphp
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Kelola Transaksi</h1>
            <p class="text-gray-400 mt-1">Kelola semua transaksi dan status pengiriman</p>
        </div>
    </div>
    <form method="GET" class="flex gap-3 mb-6">
        <select name="status" class="px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:border-green-500 outline-none">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="shipping" {{ request('status') === 'shipping' ? 'selected' : '' }}>Shipping</option>
            <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button type="submit" class="px-6 py-2.5 bg-green-600 text-white rounded-xl text-sm font-medium hover:bg-green-700 transition-colors">Filter</button>
    </form>
    <div class="bg-gray-800/50 border border-gray-700/50 rounded-2xl overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-900/50">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase">Order ID</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase">Pelanggan</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase">Total</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase">Status</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-400 uppercase">Tanggal</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-gray-400 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($transactions as $tx)
                    @php
                        $statusBadge = match($tx->status) {
                            'pending' => 'bg-amber-500/10 text-amber-400',
                            'paid' => 'bg-blue-500/10 text-blue-400',
                            'shipping' => 'bg-purple-500/10 text-purple-400',
                            'delivered' => 'bg-green-500/10 text-green-400',
                            'cancelled' => 'bg-red-500/10 text-red-400',
                            default => 'bg-gray-500/10 text-gray-400',
                        };
                    @endphp
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4 text-gray-300 text-sm font-mono">{{ $tx->midtrans_order_id ?? '#' . $tx->transaction_id }}</td>
                        <td class="px-6 py-4 text-white text-sm">{{ $tx->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-green-400 text-sm font-semibold">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
            
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusBadge }}">
                                {{ ucfirst($tx->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $tx->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Status progression: paid → shipping → delivered --}}
                                @if($tx->status === 'paid')
            
                                    <form method="POST" action="{{ route('admin.transactions.updateStatus', $tx) }}">
                                        @csrf
                                        
                                        <input type="hidden" name="status" value="shipping">
                                        <button class="px-3 py-1.5 bg-purple-500/10 text-purple-400 rounded-lg text-xs font-medium hover:bg-purple-500/20 transition-colors">
                                            🚚 Kirim
                                        </button>
                                    </form>
                                @endif
                                @if($tx->status === 'shipping')
                                    <form method="POST" action="{{ route('admin.transactions.updateStatus', $tx) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="delivered">
                                        <button class="px-3 py-1.5 bg-green-500/10 text-green-400 rounded-lg text-xs font-medium hover:bg-green-500/20 transition-colors">
                                            ✅ Selesai
                                        </button>
                                    </form>
                                @endif
                                {{-- Cancel (only for pending/paid) --}}
                                @if(in_array($tx->status, ['pending', 'paid']))
                                    <form method="POST" action="{{ route('admin.transactions.cancel', $tx) }}" onsubmit="return confirm('Yakin batalkan transaksi?')">
                                        @csrf
                                        
                                        <button class="px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs font-medium hover:bg-red-500/20 transition-colors">
                                            ❌ Batalkan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-8 text-gray-500">Tidak ada transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $transactions->appends(request()->query())->links() }}</div>
</x-admin-layout>
