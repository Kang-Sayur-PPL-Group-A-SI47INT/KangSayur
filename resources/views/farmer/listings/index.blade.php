<x-app-layout>
    @php $title = 'My Listings'; @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Produce 🌱</h1>
                <p class="text-gray-500 mt-1">Manage your listings and track inventory</p>
            </div>
            <a href="#"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-800 text-white font-semibold rounded-full hover:bg-green-900 transition-all duration-200 shadow-lg shadow-green-200/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Create New Listing
            </a>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-4 mb-6 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                {{-- Category Dropdown --}}
                <form method="GET" action="{{ route('farmer.listings.index') }}" class="flex items-center gap-3 flex-wrap flex-1">
                    {{-- Preserve status filter --}}
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <div class="relative">
                        <select name="category" onchange="this.form.submit()"
                                class="appearance-none pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-full text-sm font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-200 focus:border-green-400 cursor-pointer">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </form>

                {{-- Status Filter Pills --}}
                <div class="flex items-center gap-2 flex-wrap">
                    @php
                        $statuses = [
                            ''          => 'All',
                            'active'    => 'Active',
                            'inactive'  => 'Inactive',
                            'sold_out'  => 'Sold Out',
                            'low_stock' => 'Low Stock',
                        ];
                    @endphp
                    @foreach($statuses as $value => $label)
                        <a href="{{ route('farmer.listings.index', array_merge(request()->only('category'), $value ? ['status' => $value] : [])) }}"
                           class="px-4 py-2 text-sm font-medium rounded-full transition-all duration-200
                               {{ request('status', '') === $value
                                   ? 'bg-green-800 text-white shadow-md shadow-green-200/50'
                                   : 'bg-white text-gray-600 border border-gray-200 hover:bg-green-50 hover:text-green-700 hover:border-green-200' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Product Grid --}}
        @if($listings->count())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($listings as $listing)
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-xl hover:shadow-green-50 transition-all duration-300 hover:-translate-y-1 group">
                        {{-- Image --}}
                        <div class="relative aspect-video bg-gradient-to-br from-green-50 to-emerald-50 overflow-hidden">
                            @php $images = $listing->getImagesArray(); @endphp
                            @if(count($images) > 0)
                                <img src="{{ asset('storage/' . $images[0]) }}" alt="{{ $listing->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-6xl opacity-60 group-hover:scale-110 transition-transform duration-300">
                                        {{ $listing->produce->emoji ?? '🌿' }}
                                    </span>
                                </div>
                            @endif

                            {{-- Status Badge --}}
                            <div class="absolute top-3 left-3">
                                @switch($listing->status)
                                    @case('active')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-500/90 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                            <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                            Active
                                        </span>
                                        @break
                                    @case('inactive')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-500/90 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                            Inactive
                                        </span>
                                        @break
                                    @case('sold_out')
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-500/90 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                            Sold Out
                                        </span>
                                        @break
                                @endswitch
                            </div>

                            {{-- Low Stock Badge --}}
                            @if($listing->quantity <= 10 && $listing->status !== 'sold_out')
                                <div class="absolute top-3 right-3">
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-500/90 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Low Stock
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h3 class="font-semibold text-gray-900 text-base leading-tight line-clamp-1">{{ $listing->title }}</h3>
                            </div>

                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs font-medium text-green-700 bg-green-50 px-2.5 py-1 rounded-full">
                                    {{ $listing->produce->category ?? 'Uncategorized' }}
                                </span>
                                <span class="text-xs text-gray-400">•</span>
                                <span class="text-xs text-gray-500">{{ $listing->quantity }} {{ $listing->unit }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <p class="text-lg font-bold text-green-800">
                                    Rp {{ number_format($listing->price, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-gray-400">/{{ $listing->unit }}</span>
                                </p>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
                                <a href="{{ route('farmer.listings.edit', $listing) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-green-100 text-green-700 text-sm font-medium rounded-full hover:bg-green-200 transition-all duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('farmer.listings.destroy', $listing) }}"
                                      onsubmit="return confirm('Are you sure you want to delete this listing?')"
                                      class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-red-100 text-red-600 text-sm font-medium rounded-full hover:bg-red-200 transition-all duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $listings->withQueryString()->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-16">
                <div class="text-6xl mb-4">🌾</div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No listings found</h3>
                <p class="text-gray-500 mb-6">Start adding your fresh produce to reach customers!</p>
                <a href="#"
                   class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-800 text-white font-semibold rounded-full hover:bg-green-900 transition-all shadow-lg shadow-green-200/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Create Your First Listing
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
