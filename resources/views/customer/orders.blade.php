<x-app-layout>
    @php $title = 'My Orders'; @endphp
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">My Orders 📦</h1>

        <div class="space-y-4">
            @forelse($orders as $order)
                @php
                    $statusColors = [
                        'pending' => 'bg-amber-100 text-amber-700',
                        'paid' => 'bg-blue-100 text-blue-700',
                        'completed' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                    ];
                    $statusLabels = [
                        'pending' => 'Awaiting Payment',
                        'paid' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ];
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="font-bold text-gray-900">{{ $order->midtrans_order_id ?? '#' . $order->transaction_id }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y · h:i A') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                            {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-4">
                        @foreach($order->cart->items ?? [] as $item)
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center overflow-hidden">
                                        @php $imgs = ($item->listing ? $item->listing->getImagesArray() : []); @endphp
                                        @if(count($imgs))
                                            <img src="{{ asset('storage/' . $imgs[0]) }}" class="w-full h-full object-cover">
                                        @else <span class="text-lg">🥬</span> @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $item->listing->title ?? 'Item' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->quantity }} × Rp {{ number_format($item->listing->price ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">Rp {{ number_format(($item->listing->price ?? 0) * $item->quantity, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <div class="text-sm text-gray-500">
                            @if($order->delivery_fee)
                                Delivery: Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}
                            @endif
                        </div>
                        <p class="font-bold text-gray-900">Total: <span class="text-green-700">Rp {{ number_format($order->grandTotal(), 0, ',', '.') }}</span></p>
                    </div>

                    @if($order->status === 'pending')
                    <div class="mt-4">
                        
                        <h1 class="text-green-700">INI BUAT TEST</h1>
                    </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">📦</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Orders Yet</h3>
                    <p class="text-gray-500 mb-6">Start shopping for fresh produce!</p>
                    <a href="{{ route('marketplace') }}" class="px-8 py-3 bg-green-800 text-white rounded-full font-semibold hover:bg-green-900">Browse Marketplace</a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    </div>
</x-app-layout>
