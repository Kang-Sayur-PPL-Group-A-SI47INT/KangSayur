@php $title = 'Favorites'; @endphp

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                <a href="{{ route('home') }}" class="hover:text-green-700 transition-colors">Home</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900 font-medium">Favorites</span>
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900" style="font-family: Georgia, serif;">
                        My Favorites
                        <span class="text-red-500">❤️</span>
                    </h1>
                    <p class="text-gray-500 mt-1">
                        @if($favorites->count() > 0)
                            {{ $favorites->count() }} {{ Str::plural('product', $favorites->count()) }} saved
                        @else
                            No favorites yet
                        @endif
                    </p>
                </div>
                @if($favorites->count() > 0)
                    <a href="{{ route('marketplace') }}" class="hidden sm:flex items-center gap-2 px-5 py-2.5 bg-cream-100 text-green-800 text-sm font-semibold rounded-full hover:bg-cream-200 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Discover More
                    </a>
                @endif
            </div>
        </div>

        @if($favorites->count() > 0)
            {{-- Favorites Grid --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($favorites as $favorite)
                    @php $listing = $favorite->listing; @endphp

                    <div class="product-card group relative" x-data="{ removing: false }" :class="{ 'opacity-50 scale-95': removing }">

                        {{-- Favorite Heart Button (Remove) --}}
                        <form method="POST" action="{{ route('customer.favorites.toggle', $listing->listing_id) }}"
                              @submit="removing = true"
                              class="absolute top-3 right-3 z-10">
                            @csrf
                            <button type="submit"
                                    dusk="add-to-favoritePage"
                                    class="w-9 h-9 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-lg hover:bg-red-50 transition-all duration-200 hover:scale-110 group/heart"
                                    title="Remove from favorites">
                                <svg class="w-5 h-5 text-red-500 group-hover/heart:text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/>
                                </svg>
                            </button>
                        </form>

                        {{-- Product Image --}}
                        <a href="{{ route('marketplace.show', $listing) }}">
                            <div class="aspect-square overflow-hidden relative">
                                @if($listing->image && !str_starts_with($listing->image, '['))
                                    <img src="{{ asset('storage/' . $listing->image) }}"
                                         alt="{{ $listing->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    @php
                                        $emojis = ['🍅','🌶️','🥬','🥦','🥕','🌽','🧅','🍆','🥔','🧄'];
                                        $emoji = $emojis[($listing->produce_produce_id - 1) % count($emojis)] ?? '🥬';
                                        $colors = ['from-orange-100 to-amber-50','from-red-100 to-rose-50','from-green-100 to-lime-50','from-emerald-100 to-teal-50','from-orange-100 to-yellow-50','from-yellow-100 to-amber-50','from-purple-100 to-pink-50','from-violet-100 to-purple-50','from-amber-100 to-yellow-50','from-rose-100 to-pink-50'];
                                        $bg = $colors[($listing->produce_produce_id - 1) % count($colors)] ?? 'from-green-100 to-lime-50';
                                    @endphp
                                    <div class="w-full h-full bg-gradient-to-br {{ $bg }} flex items-center justify-center group-hover:scale-105 transition-transform duration-500">
                                        <span class="text-7xl drop-shadow-sm">{{ $emoji }}</span>
                                    </div>
                                @endif

                                {{-- Category Badge --}}
                                @if($listing->produce)
                                    <span class="product-badge product-badge-new">{{ $listing->produce->name }}</span>
                                @endif
                            </div>
                        </a>

                        {{-- Product Info --}}
                        <div class="p-4">
                            <a href="{{ route('marketplace.show', $listing) }}">
                                <h3 class="text-sm font-bold text-gray-900 truncate mb-1.5 hover:text-green-700 transition-colors">{{ $listing->title }}</h3>
                            </a>

                            {{-- Farmer --}}
                            <p class="text-xs text-gray-500 flex items-center gap-1 mb-3">
                                <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                {{ $listing->farmer->name ?? 'Local Farmer' }}
                            </p>

                            {{-- Price & Rating --}}
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-base font-bold text-green-700">
                                    Rp {{ number_format($listing->price, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-gray-400">/{{ $listing->unit ?? 'kg' }}</span>
                                </p>

                                @php $rating = $listing->averageRating() ?? 0; @endphp
                                @if($rating > 0)
                                    <div class="flex items-center gap-0.5">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        <span class="text-xs font-semibold text-gray-600">{{ number_format($rating, 1) }}</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Add to Cart Button --}}
                            <form method="POST" action="{{ route('customer.cart.add', $listing->listing_id) }}">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 py-2.5 px-4 bg-gradient-to-r from-green-700 to-green-800 text-white text-sm font-semibold rounded-full hover:from-green-800 hover:to-green-900 transition-all duration-300 shadow-md shadow-green-200/50 hover:shadow-green-300/50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-20">
                <div class="w-32 h-32 bg-gradient-to-br from-red-50 to-pink-100 rounded-full flex items-center justify-center mb-6 relative">
                    <span class="text-6xl">❤️</span>
                    <div class="absolute inset-0 rounded-full animate-ping bg-red-100 opacity-20"></div>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2" style="font-family: Georgia, serif;">No favorites yet</h2>
                <p class="text-gray-500 text-center max-w-md mb-8">
                    Start exploring the marketplace and tap the heart icon on products you love. They'll show up here for easy access!
                </p>
                <a href="{{ route('marketplace') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-700 to-green-800 text-white font-bold rounded-full shadow-lg shadow-green-200/50 hover:from-green-800 hover:to-green-900 hover:shadow-green-300/50 transition-all duration-300">
                    Explore Marketplace
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
