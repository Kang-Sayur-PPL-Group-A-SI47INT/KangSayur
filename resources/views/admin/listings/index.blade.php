<x-admin-layout>
    @php $title = 'Listing'; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Listing 📦</h1>
                <p class="text-gray-500 mt-1">Daftar semua listing produk</p>
            </div>
        </div>
        <form method="GET" class="flex gap-3 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul listing..."
                   class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none flex-1 max-w-xs">
            <select name="status" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="sold_out" {{ request('status') === 'sold_out' ? 'selected' : '' }}>Sold Out</option>
            </select>
            <button type="submit" class="px-6 py-2.5 bg-green-800 text-white rounded-xl text-sm font-medium hover:bg-green-900 transition-colors">Filter</button>
        </form>
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Listing</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Petani</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Harga</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Stok</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Dibuat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($listings as $listing)
                        @php
                            $statusBadge = match($listing->status) {
                                'active' => 'bg-green-50 text-green-700 border-green-200',
                                'inactive' => 'bg-gray-50 text-gray-600 border-gray-200',
                                'sold_out' => 'bg-red-50 text-red-700 border-red-200',
                                default => 'bg-gray-50 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <tr class="hover:bg-green-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-50 border border-green-100 rounded-xl flex items-center justify-center text-lg">
                                        {{ $listing->produce->emoji ?? '🌱' }}
                                    </div>
                                    <div>
                                        <p class="text-gray-900 text-sm font-medium">{{ $listing->title }}</p>
                                        <p class="text-gray-400 text-xs">{{ $listing->produce->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">{{ $listing->farmer->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-green-700 text-sm font-semibold">Rp {{ number_format($listing->price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-gray-600 text-sm">{{ $listing->quantity }} {{ $listing->unit }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full border {{ $statusBadge }}">{{ ucfirst($listing->status) }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $listing->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-8 text-gray-400">Tidak ada listing ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $listings->appends(request()->query())->links() }}</div>
    </div>
</x-admin-layout>
