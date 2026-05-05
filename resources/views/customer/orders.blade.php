@php $title = 'My Orders'; @endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-700 transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium">My Orders</span>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900" style="font-family: Georgia, serif;">
                My Orders
                <span class="text-green-700">📦</span>
            </h1>
            <p class="text-gray-500 text-sm mt-1">Track and manage all your orders</p>
        </div>

        @if($transactions->count() > 0)
            <div class="space-y-4">
                @foreach($transactions as $transaction)
                    <a href="{{ route('customer.orders.detail', $transaction->transaction_id) }}"
                       class="block bg-white rounded-2xl border border-gray-100 hover:shadow-lg hover:border-green-100 transition-all duration-300 group">
                        <div class="p-5 sm:p-6">
                            {{-- Top Row: Order ID + Status --}}
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                                <div>
                                    <p class="text-xs text-gray-400 font-medium mb-0.5">Order ID</p>
                                    <p class="font-mono text-sm font-bold text-gray-900">{{ $transaction->midtrans_order_id ?? '#' . $transaction->transaction_id }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $transaction->statusColor() }}">
                                        {{ $transaction->statusLabel() }}
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>

                            {{-- Items Preview --}}
                            <div class="flex items-center gap-3 mb-4">
                                <div class="flex -space-x-2">
                                    @foreach($transaction->items->take(4) as $item)
                                        <div class="w-10 h-10 bg-green-50 rounded-xl border-2 border-white flex items-center justify-center text-sm overflow-hidden flex-shrink-0">
                                            @php
                                                $emojis = ['🥬', '🥕', '🍅', '🌽', '🥦', '🍆', '🥒', '🫑'];
                                                $imgs = $item->listing ? $item->listing->getImagesArray() : [];
                                            @endphp
                                            @if(count($imgs))
                                                <img src="{{ asset('storage/' . $imgs[0]) }}" class="w-full h-full object-cover" alt="">
                                            @else
                                                {{ $emojis[$loop->index % count($emojis)] }}
                                            @endif
                                        </div>
                                    @endforeach
                                    @if($transaction->items->count() > 4)
                                        <div class="w-10 h-10 bg-gray-100 rounded-xl border-2 border-white flex items-center justify-center text-xs font-bold text-gray-500 flex-shrink-0">
                                            +{{ $transaction->items->count() - 4 }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-700 truncate">
                                        {{ $transaction->items->map(fn($i) => $i->listing->title ?? 'Product')->take(2)->join(', ') }}
                                        @if($transaction->items->count() > 2)
                                            <span class="text-gray-400">and {{ $transaction->items->count() - 2 }} more</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $transaction->items->count() }} {{ Str::plural('item', $transaction->items->count()) }}</p>
                                </div>
                            </div>

                            {{-- Bottom Row: Total + Arrow --}}
                            <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                <div>
                                    <span class="text-xs text-gray-400">Total</span>
                                    <p class="text-lg font-bold text-green-700">Rp {{ number_format($transaction->grandTotal(), 0, ',', '.') }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-300 group-hover:text-green-600 group-hover:translate-x-1 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($transactions->hasPages())
                <div class="mt-8">
                    {{ $transactions->links() }}
                </div>
            @endif
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-20">
                <div class="w-32 h-32 bg-gradient-to-br from-cream-100 to-cream-200 rounded-full flex items-center justify-center mb-6">
                    <span class="text-6xl">📭</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2" style="font-family: Georgia, serif;">No orders yet</h2>
                <p class="text-gray-500 text-center max-w-md mb-8">
                    You haven't placed any orders yet. Start shopping for fresh produce from local farmers!
                </p>
                <a href="{{ route('marketplace') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-700 to-green-800 text-white font-bold rounded-full shadow-lg shadow-green-200/50 hover:from-green-800 hover:to-green-900 hover:shadow-green-300/50 transition-all duration-300">
                    Start Shopping
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @endif
    </div>
</x-app-layout>
