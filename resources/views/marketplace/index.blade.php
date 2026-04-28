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
                            
                        </div>

                        <!-- Price Range -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Price Range (Rp)</h4>
                            <div class="flex gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-400 outline-none">
                                <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-400 outline-none">
                            </div>
                        </div>

                        

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
            </div>
        </form>
    </div>
</x-app-layout>
