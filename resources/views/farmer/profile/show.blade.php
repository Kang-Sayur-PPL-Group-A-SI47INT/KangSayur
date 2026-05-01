<x-app-layout>
    @php $title = $farmer->name; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <a href="{{ url()->previous() }}" class="text-green-600 hover:text-green-700 text-sm flex items-center gap-1 mb-6">← Kembali</a>

        <!-- Profile Header -->
        <div class="bg-white rounded-2xl border border-gray-100 p-8 mb-8">
            <div class="flex items-center gap-6">
                <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white text-3xl font-bold overflow-hidden">
                    @if($farmer->profile_photo)
                        <img src="{{ asset('storage/' . $farmer->profile_photo) }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($farmer->name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $farmer->name }}</h1>
                    <p class="text-gray-500">📍 {{ $farmer->city }} · 🌾 Petani</p>
                    <div class="flex items-center gap-4 mt-2">
                        <span class="text-sm text-amber-500 font-semibold">⭐ {{ number_format($averageRating ?? 0, 1) }}</span>
                        <span class="text-sm text-gray-500">{{ $totalListings }} listing</span>
                    </div>
                    @if($farmer->farm_description)
                        <p class="text-gray-600 mt-3 max-w-2xl">{{ $farmer->farm_description }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Farmer's Listings -->
        <h2 class="text-xl font-bold text-gray-900 mb-6">Produk dari {{ $farmer->name }}</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($listings as $listing)
                <a href="{{ route('marketplace.show', $listing) }}" class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <div class="aspect-video bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center text-4xl">
                        @if($listing->image)
                            <img src="{{ asset('storage/' . $listing->image) }}" class="w-full h-full object-cover">
                        @else
                            🥬
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-900 text-sm group-hover:text-green-600">{{ $listing->title }}</h3>
                        <p class="text-green-600 font-bold mt-1">Rp {{ number_format($listing->price, 0, ',', '.') }}/kg</p>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-gray-500 text-center py-8">Belum ada listing aktif.</p>
            @endforelse
        </div>
        <div class="mt-8">{{ $listings->links() }}</div>
    </div>
</x-app-layout>
