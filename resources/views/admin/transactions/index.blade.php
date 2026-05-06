<x-admin-layout>
    @php $title = 'Transaksi'; @endphp

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Kelola Transaksi</h1>
            <p class="text-gray-400 mt-1">Kelola semua transaksi pembayaran</p>
        </div>
    </div>

    <form method="GET" class="flex gap-3 mb-6">
        <select name="status" class="px-4 py-2.5 bg-gray-800 border border-gray-700 rounded-xl text-white text-sm focus:border-green-500 outline-none">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
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
                    <tr class="hover:bg-gray-800/30 transition-colors">
                        <td class="px-6 py-4 text-gray-300 text-sm font-mono">{{ $tx->midtrans_order_id ?? '#' . $tx->transaction_id }}</td>
                        <td class="px-6 py-4 text-white text-sm">{{ $tx->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-green-400 text-sm font-semibold">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $tx->status === 'completed' ? 'bg-green-500/10 text-green-400' : ($tx->status === 'paid' ? 'bg-blue-500/10 text-blue-400' : ($tx->status === 'cancelled' ? 'bg-red-500/10 text-red-400' : 'bg-amber-500/10 text-amber-400')) }}">
                                {{ ucfirst($tx->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $tx->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($tx->status === 'paid')
                                    <form method="POST" action="{{ route('admin.transactions.complete', $tx) }}">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-green-500/10 text-green-400 rounded-lg text-xs font-medium hover:bg-green-500/20">Selesaikan</button>
                                    </form>
                                @endif
                                @if(in_array($tx->status, ['pending', 'paid']))
                                    <form method="POST" action="{{ route('admin.transactions.cancel', $tx) }}" onsubmit="return confirm('Yakin batalkan transaksi?')">
                                        @csrf
                                        <button class="px-3 py-1.5 bg-red-500/10 text-red-400 rounded-lg text-xs font-medium hover:bg-red-500/20">Batalkan</button>
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
    <div class="mt-6">{{ $transactions->links() }}</div>
</x-admin-layout>
