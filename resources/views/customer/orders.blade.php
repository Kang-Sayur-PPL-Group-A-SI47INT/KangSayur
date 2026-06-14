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
                        'shipping' => 'bg-indigo-100 text-indigo-700',
                        'delivered' => 'bg-green-100 text-green-700',
                        'cancelled' => 'bg-red-100 text-red-700',
                    ];
                    $statusLabels = [
                        'pending' => 'Awaiting Payment',
                        'paid' => 'Paid',
                        'shipping' => 'Shipping',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ];
                @endphp
                <div class="block bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-md hover:border-green-200 transition-all duration-200 group">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="font-bold text-gray-900 group-hover:text-green-700 transition-colors">{{ $order->midtrans_order_id ?? '#' . $order->transaction_id }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y · h:i A') }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                            {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="space-y-2 mb-4">
                        @foreach($order->items as $item)
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
                                        <p class="text-xs text-gray-500">{{ $item->quantity }} × Rp {{ number_format($item->unit_price ?? $item->listing->price ?? 0, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($item->subtotal ?? ($item->unit_price ?? $item->listing->price ?? 0) * $item->quantity, 0, ',', '.') }}</p>
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

                    {{-- Button: View Details visible; Except for pending --}}
                    <div class="mt-4 flex items-center gap-2">
                    <a href="{{ route('customer.orders.detail', $order->transaction_id) }}"
                    class="flex-1 text-center py-2 px-4 text-sm font-semibold text-green-700 border border-green-200 rounded-xl hover:bg-green-50 transition-all duration-200">
                        Track Order
                    </a>

                    {{-- Continue Payment --}}
                    @if($order->status === 'pending')
                        <a href="{{ route('customer.checkout.payment',$order->transaction_id) }}"
                            class="flex-1 text-center py-2 px-4 text-sm font-semibold text-white bg-green-700 rounded-xl hover:bg-green-800 transition-all duration-200" dusk="continue-payment-button">
                            Continue Payment
                        </a>
                    @endif

                    {{-- Cancel Order button --}}
                    @if($order->status === 'pending')
                    <form method="POST"
                        action="{{ route('customer.orders.cancel', $order->transaction_id) }}"
                        onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.')"
                        class="flex-1">
                        @csrf
                        <button type="submit"
                                dusk="cancel-order-button"
                                class="w-full py-2 px-4 text-sm font-semibold text-red-600 border border-red-200 rounded-xl hover:bg-red-50 hover:border-red-300 transition-all duration-200 flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel Order
                        </button>
                    </form>
                @endif
            </div>


                    @if($order->status === 'delivered')
                    <div class="mt-4">
                        <a href="{{ route('customer.orders.detail', $order->transaction_id) }}"
                           class="inline-flex items-center gap-2 px-6 py-2 bg-amber-500 text-white text-sm font-semibold rounded-full hover:bg-amber-600 transition-colors cursor-pointer"
                           dusk="write-review-button">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Write a Review ⭐
                        </a>
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
