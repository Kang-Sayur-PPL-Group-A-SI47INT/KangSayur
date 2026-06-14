<x-app-layout>
    @php $title = 'Negotiations'; @endphp
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Negotiations 💬</h1>

        <div class="space-y-4">
            @forelse($offers as $offer)
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-100 text-amber-700',
                        'countered' => 'bg-blue-100 text-blue-700',
                        'accepted' => 'bg-green-100 text-green-700',
                        'rejected' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <a href="{{ route('customer.offers.show', $offer) }}" class="block bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4 min-w-0 flex-1">
                            <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">🥬</div>
                            <div class="min-w-0">
                                <p class="font-bold text-gray-900 text-sm">{{ $offer->listing->title }}</p>
                                <p class="text-xs text-gray-500">{{ $offer->listing->farmer->name ?? '' }} · {{ $offer->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 flex-shrink-0">
                            <div class="text-right">
                                <p class="text-sm font-bold text-green-800">Rp {{ number_format($offer->offered_price, 0, ',', '.') }}</p>
                                @if($offer->counter_price)
                                    <p class="text-xs text-blue-600">Counter: Rp {{ number_format($offer->counter_price, 0, ',', '.') }}</p>
                                @endif
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$offer->status] ?? 'bg-gray-100' }}">{{ ucfirst($offer->status) }}</span>
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">💬</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">No Offers Yet</h3>
                    <p class="text-gray-500 text-sm mb-6">Make an offer on any product to start negotiating!</p>
                    <a href="{{ route('marketplace') }}" class="px-8 py-3 bg-green-800 text-white rounded-full font-semibold hover:bg-green-900">Browse Marketplace</a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $offers->links() }}</div>
    </div>
</x-app-layout>
