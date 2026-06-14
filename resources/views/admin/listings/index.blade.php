<x-admin-layout>
    @php
        $title = 'Listing';
        $sortLink = fn(string $col): string => request()->fullUrlWithQuery([
            'sort'      => $col,
            'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc',
        ]);
        $sortIcon = function (string $col) use ($sort, $direction): string {
            if ($sort !== $col) {
                return '<svg class="w-3 h-3 inline opacity-25 ml-0.5" viewBox="0 0 20 20" fill="currentColor"><path d="M5 12l5-5 5 5H5z"/><path d="M5 8l5 5 5-5H5z" opacity=".5"/></svg>';
            }
            return $direction === 'asc'
                ? '<svg class="w-3 h-3 inline ml-0.5 text-green-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>'
                : '<svg class="w-3 h-3 inline ml-0.5 text-green-600" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>';
        };
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Listing 📦</h1>
                <p class="text-gray-500 mt-1">Daftar semua listing produk</p>
            </div>
        </div>

        {{-- Filters (preserve sort state) --}}
        <form method="GET" class="flex gap-3 mb-6 flex-wrap">
            <input type="hidden" name="sort" value="{{ $sort }}">
            <input type="hidden" name="direction" value="{{ $direction }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul listing..."
                   class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none flex-1 max-w-xs">
            <select name="status" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none">
                <option value="">Semua Status</option>
                <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="sold_out" {{ request('status') === 'sold_out' ? 'selected' : '' }}>Sold Out</option>
            </select>
            <button type="submit" class="px-6 py-2.5 bg-green-800 text-white rounded-xl text-sm font-medium hover:bg-green-900 transition-colors">Filter</button>
        </form>

        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortLink('title') }}" class="inline-flex items-center gap-0.5 hover:text-gray-700 transition-colors">
                                Listing {!! $sortIcon('title') !!}
                            </a>
                        </th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Petani</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortLink('price') }}" class="inline-flex items-center gap-0.5 hover:text-gray-700 transition-colors">
                                Harga {!! $sortIcon('price') !!}
                            </a>
                        </th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortLink('quantity') }}" class="inline-flex items-center gap-0.5 hover:text-gray-700 transition-colors">
                                Stok {!! $sortIcon('quantity') !!}
                            </a>
                        </th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortLink('created_at') }}" class="inline-flex items-center gap-0.5 hover:text-gray-700 transition-colors">
                                Dibuat {!! $sortIcon('created_at') !!}
                            </a>
                        </th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">
                            <a href="{{ $sortLink('avg_rating') }}" class="inline-flex items-center gap-0.5 hover:text-gray-700 transition-colors whitespace-nowrap">
                                ⭐ Avg Rating {!! $sortIcon('avg_rating') !!}
                            </a>
                        </th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($listings as $listing)
                        @php
                            $statusBadge = match($listing->status) {
                                'active'   => 'bg-green-50 text-green-700 border-green-200',
                                'inactive' => 'bg-gray-50 text-gray-600 border-gray-200',
                                'sold_out' => 'bg-red-50 text-red-700 border-red-200',
                                default    => 'bg-gray-50 text-gray-600 border-gray-200',
                            };
                            $avgScore = round((float) $listing->ratings_avg_score, 1);
                            $ratingBadge = $avgScore >= 4.0
                                ? 'bg-green-50 text-green-700 border-green-200'
                                : ($avgScore >= 2.5
                                    ? 'bg-amber-50 text-amber-700 border-amber-200'
                                    : 'bg-red-50 text-red-700 border-red-200');
                        @endphp
                        <tr class="hover:bg-green-50/30 transition-colors">
                            {{-- Listing title + produce --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-50 border border-green-100 rounded-xl flex items-center justify-center text-lg flex-shrink-0">
                                        {{ $listing->produce->emoji ?? '🌱' }}
                                    </div>
                                    <div>
                                        <p class="text-gray-900 text-sm font-medium">{{ $listing->title }}</p>
                                        <p class="text-gray-400 text-xs">{{ $listing->produce->name ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            {{-- Farmer --}}
                            <td class="px-6 py-4 text-gray-600 text-sm">{{ $listing->farmer->name ?? 'N/A' }}</td>
                            {{-- Price --}}
                            <td class="px-6 py-4 text-green-700 text-sm font-semibold">Rp {{ number_format($listing->price, 0, ',', '.') }}</td>
                            {{-- Stock --}}
                            <td class="px-6 py-4 text-gray-600 text-sm">{{ $listing->quantity }} {{ $listing->unit }}</td>
                            {{-- Status --}}
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full border {{ $statusBadge }}">{{ ucfirst($listing->status) }}</span>
                            </td>
                            {{-- Created --}}
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $listing->created_at->format('d M Y') }}</td>
                            {{-- Avg Rating --}}
                            <td class="px-6 py-4">
                                @if($avgScore > 0)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-xs font-semibold {{ $ratingBadge }}">
                                        ⭐ {{ number_format($avgScore, 1) }}
                                    </span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('admin.listings.destroy', $listing) }}"
                                      onsubmit="return confirm('Yakin nonaktifkan listing ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-lg text-xs font-medium hover:bg-red-100 transition-colors">
                                        🚫 Nonaktifkan
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-8 text-gray-400">Tidak ada listing ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $listings->appends(request()->query())->links() }}</div>
    </div>
</x-admin-layout>
