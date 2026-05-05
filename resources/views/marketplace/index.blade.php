<x-app-layout>
    @php $title = 'Marketplace'; @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero -->
        <div class="relative bg-gradient-to-br from-green-50 via-cream-100 to-cream-200 rounded-3xl overflow-hidden mb-8">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div class="p-8 lg:p-12">
                    <span class="inline-flex items-center gap-2 bg-green-100 px-3 py-1 rounded-full text-xs font-semibold text-green-800 mb-4">
                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span> Early Harvest
                    </span>
                    <h1 class="text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight mb-3 font-serif">The Marketplace</h1>
                    <p class="text-gray-600 text-sm max-w-md">Browse fresh produce directly from local Indonesian farmers. Better prices, better quality.</p>
                </div>
                <div class="hidden lg:block">
                    <img src="{{ asset('images/farmer-hero.png') }}" alt="Farm" class="w-full h-56 object-cover rounded-br-3xl">
                </div>
            </div>
        </div>

        <!-- Search -->
        <form method="GET" id="filterForm" class="mb-8">
            <div class="flex flex-wrap gap-3">
                <div class="flex-1 min-w-[250px] relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search produce or farm names..."
                        class="w-full pl-12 pr-4 py-3 rounded-full bg-white border border-gray-200 text-sm focus:outline-none focus:border-green-400 focus:ring-2 focus:ring-green-100">
                </div>
                <select name="sort" onchange="document.getElementById('filterForm').submit()"
                    class="px-5 py-3 rounded-full bg-white border border-gray-200 text-sm text-gray-600 focus:outline-none focus:border-green-400 cursor-pointer">
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>🕐 Newest</option>
                    <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>💰 Price: Low-High</option>
                    <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>💰 Price: High-Low</option>
                    <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>⭐ Highest Rated</option>
                    <option value="nearest" {{ request('sort') === 'nearest' ? 'selected' : '' }}>📍 Nearest</option>
                </select>
                <button type="submit" class="px-6 py-3 bg-green-800 text-white rounded-full text-sm font-semibold hover:bg-green-900 transition-all">Search</button>
            </div>

            <!-- Sidebar Filters (collapsible on mobile) -->
            <div class="mt-6 grid lg:grid-cols-4 gap-6" x-data="{ open: false }">
                <div class="lg:col-span-1">

                    <button type="button" @click="open = !open" class="lg:hidden w-full py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 mb-3">
                        🔽 Filters
                    </button>
                    <div :class="open ? 'block' : 'hidden lg:block'" class="bg-white rounded-2xl border border-gray-100 p-5 space-y-5">
                        <!-- Category -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Category</h4>
                            <div class="space-y-2">
                                @foreach($categories as $cat)
                                    <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer hover:text-green-700">
                                        <input type="radio" name="category" value="{{ $cat }}" {{ request('category') === $cat ? 'checked' : '' }}
                                            class="text-green-600 focus:ring-green-500" onchange="document.getElementById('filterForm').submit()">
                                        {{ $cat }}
                                    </label>
                                @endforeach
                                @if(request('category'))
                                    <a href="{{ route('marketplace', request()->except('category')) }}" class="text-xs text-red-500 hover:text-red-600">Clear category</a>
                                @endif
                            </div>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Price Range (Rp)</h4>
                            <div class="flex gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-400 outline-none">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-400 outline-none">
                            </div>
                        </div>

                        <!-- City -->
                        @if(isset($cities) && $cities->count())
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Location</h4>
                            <select name="city" onchange="document.getElementById('filterForm').submit()"
                                class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-400 outline-none">
                                <option value="">All Cities</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <!-- Rating -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Min Rating</h4>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="submit" name="min_rating" value="{{ $i }}" class="text-2xl {{ request('min_rating') >= $i ? 'text-amber-400' : 'text-gray-300' }} hover:text-amber-400 transition-colors">★</button>
                                @endfor
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-green-800 text-white rounded-xl text-sm font-semibold hover:bg-green-900">Apply Filters</button>
                        <a href="{{ route('marketplace') }}" class="block text-center text-xs text-gray-400 hover:text-gray-600">Reset all</a>
                    </div>
                </div>
        </form>

                <!-- Product Grid -->
                <div class="lg:col-span-3">
                    <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @forelse($listings as $listing)
                            @php $images = $listing->getImagesArray(); @endphp
                            <div class="product-card group relative">
                                <a href="{{ route('marketplace.show', $listing) }}">
                                    <div class="aspect-square bg-cream-100 relative overflow-hidden">
                                        @if(count($images))
                                            <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            @php
                                                $emojis = ['🍅','🌶️','🥬','🥦','🥕','🌽','🧅','🍆','🥔','🧄'];
                                                $emoji = $emojis[($listing->produce_produce_id - 1) % count($emojis)] ?? '🥬';
                                                $colors = ['from-orange-100 to-amber-50','from-red-100 to-rose-50','from-green-100 to-lime-50','from-emerald-100 to-teal-50','from-orange-100 to-yellow-50','from-yellow-100 to-amber-50','from-purple-100 to-pink-50','from-violet-100 to-purple-50','from-amber-100 to-yellow-50','from-rose-100 to-pink-50'];
                                                $bg = $colors[($listing->produce_produce_id - 1) % count($colors)] ?? 'from-green-100 to-lime-50';
                                            @endphp
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br {{ $bg }}">
                                                <span class="text-7xl group-hover:scale-110 transition-transform duration-500">{{ $emoji }}</span>
                                            </div>
                                        @endif
                                        @if($listing->created_at->diffInDays(now()) < 3)
                                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-green-600 text-white rounded-full text-xs font-semibold">New</span>
                                        @endif
                                        <span class="absolute top-3 left-3 px-2.5 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-semibold text-green-700">{{ $listing->produce->category ?? '' }}</span>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-900 text-sm mb-0.5 group-hover:text-green-700 transition-colors line-clamp-1">{{ $listing->title }}</h3>
                                        <p class="text-xs text-gray-400 mb-2 line-clamp-1">{{ $listing->farmer->name ?? 'Farmer' }} · {{ $listing->farmer->city ?? '' }}</p>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-bold text-green-800">Rp {{ number_format($listing->price, 0, ',', '.') }} <span class="text-xs text-gray-400 font-normal">/{{ $listing->unit ?? 'kg' }}</span></span>
                                            <div class="flex items-center gap-0.5 text-xs">
                                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                <span class="font-semibold text-gray-600">{{ number_format($listing->averageRating() ?? 0, 1) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>

                                {{-- Favorite Heart Toggle --}}
                                @auth
                                    @if(auth()->user()->isCustomer())
                                        <form method="POST" action="{{ route('customer.favorites.toggle', $listing->listing_id) }}" class="absolute top-3 right-3 z-10">
                                            @csrf
                                            @php
                                                $isFavorited = auth()->user()->wishlists()->where('listing_listing_id', $listing->listing_id)->exists();
                                            @endphp
                                            <button type="submit"
                                                    dusk="add-to-favorite"
                                                    class="w-8 h-8 bg-white/90 backdrop-blur-sm rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-all duration-200"
                                                    title="{{ $isFavorited ? 'Remove from favorites' : 'Add to favorites' }}">
                                                @if($isFavorited)
                                                    <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-400 hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        @empty
                            <div class="col-span-full text-center py-20">
                                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-cream-200 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 mb-1">No Products Found</h3>
                                <p class="text-gray-500 text-sm">No products found matching these criteria.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-8">{{ $listings->appends(request()->query())->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
