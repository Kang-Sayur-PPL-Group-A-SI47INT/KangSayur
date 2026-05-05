<x-app-layout>
    @php $title = 'orders'; @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Order Management 📦</h1>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3">
                <span>✅</span>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="space-y-4">
            @forelse($orders as $order)
                @php
                    $statusColors = ['pending' => 'bg-amber-100 text-amber-700', 'paid' => 'bg-blue-100 text-blue-700', 'completed' => 'bg-green-100 text-green-700', 'cancelled' => 'bg-red-100 text-red-700'];
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="font-bold text-gray-900">{{ $order->midtrans_order_id ?? 'Order #' . $order->transaction_id }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y · H:i') }} · {{ $order->user->name ?? 'Customer' }}</p>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">{{ ucfirst($order->status) }}</span>
                    </div>

                    <!-- Items -->
                    <div class="space-y-2 mb-4">
                        @foreach($order->cart->items ?? [] as $item)
                            <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-lg">🥬</div>
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
                        <p class="font-bold text-gray-900">Total: <span class="text-green-700">Rp {{ number_format($order->grandTotal(), 0, ',', '.') }}</span></p>

                        @if($order->status === 'paid')
                        <form method="POST" action="{{ route('farmer.orders.updateStatus', $order->transaction_id) }}">
                            @csrf
                            <input type="hidden" name="status" value="completed">
                            <button class="px-5 py-2 bg-green-800 text-white text-sm font-semibold rounded-full hover:bg-green-900 transition-all">
                                ✓ Mark as Ready
                            </button>
                        </form>
                        @endif

                        <form method="POST" action="{{ route('farmer.orders.destroy', $order->transaction_id) }}" onsubmit="return confirm('Are you sure you want to delete this order?')">
                            @csrf
                            @method('DELETE')
                            <button dusk="delete-order-{{ $order->transaction_id }}" class="px-5 py-2 border border-red-200 text-red-600 text-sm font-semibold rounded-full hover:bg-red-50 transition-all">
                                🗑️ Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-20">
                    <div class="text-6xl mb-4">📦</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">No Orders Yet</h3>
                    <p class="text-gray-500 text-sm">Orders for your produce will appear here.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">{{ $orders->links() }}</div>
    </div>
</x-app-layout>
