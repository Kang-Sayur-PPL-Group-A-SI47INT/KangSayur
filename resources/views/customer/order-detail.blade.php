@php $title = 'Order #' . ($transaction->midtrans_order_id ?? $transaction->transaction_id); @endphp

<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-700 transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('customer.orders') }}" class="hover:text-green-700 transition-colors">My Orders</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium">Order Detail</span>
        </div>

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900" style="font-family: Georgia, serif;">
                    Order Details
                </h1>
                <p class="text-sm text-gray-500 mt-1 font-mono">{{ $transaction->midtrans_order_id ?? '#' . $transaction->transaction_id }}</p>
            </div>
            <span class="inline-flex items-center self-start px-4 py-2 rounded-full text-sm font-bold {{ $transaction->statusColor() }}">
                {{ $transaction->statusLabel() }}
            </span>
        </div>

        <div class="grid lg:grid-cols-3 gap-6">
            {{-- LEFT: Items + Timeline --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Order Items --}}
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            Order Items
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($transaction->items as $item)
                            <div class="p-5 flex items-center gap-4">
                                <div class="w-14 h-14 bg-green-50 rounded-xl flex items-center justify-center text-2xl overflow-hidden flex-shrink-0">
                                    @php
                                        $emojis = ['🥬', '🥕', '🍅', '🌽', '🥦', '🍆', '🥒', '🫑'];
                                        $imgs = $item->listing ? $item->listing->getImagesArray() : [];
                                    @endphp
                                    @if(count($imgs))
                                        <img src="{{ asset('storage/' . $imgs[0]) }}" class="w-full h-full object-cover rounded-xl" alt="">
                                    @else
                                        {{ $emojis[$loop->index % count($emojis)] }}
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm">{{ $item->listing->title ?? 'Product' }}</h4>
                                    <p class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                        {{ $item->listing->farmer->name ?? 'Local Farmer' }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                                <p class="font-bold text-gray-900 text-sm whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Order Timeline --}}
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Order Timeline
                    </h3>
                    @php
                        $statuses = ['pending', 'paid', 'processing', 'shipped', 'delivered'];
                        $currentIndex = array_search($transaction->status, $statuses);
                        if ($currentIndex === false) $currentIndex = -1;
                        $isCancelled = $transaction->status === 'cancelled';
                    @endphp

                    <div class="relative">
                        @foreach($statuses as $index => $status)
                            @php
                                $isActive = $index <= $currentIndex && !$isCancelled;
                                $isCurrent = $index === $currentIndex && !$isCancelled;
                                $labels = [
                                    'pending' => ['label' => 'Order Placed', 'desc' => 'Your order has been created'],
                                    'paid' => ['label' => 'Payment Confirmed', 'desc' => 'Payment has been verified'],
                                    'processing' => ['label' => 'Processing', 'desc' => 'Farmer is preparing your order'],
                                    'shipped' => ['label' => 'Shipped', 'desc' => 'Your order is on its way'],
                                    'delivered' => ['label' => 'Delivered', 'desc' => 'Order has been delivered'],
                                ];
                            @endphp
                            <div class="flex items-start gap-4 {{ !$loop->last ? 'pb-6' : '' }} relative">
                                {{-- Line connector --}}
                                @if(!$loop->last)
                                    <div class="absolute left-[15px] top-[30px] w-0.5 h-[calc(100%-20px)] {{ $isActive && ($index + 1) <= $currentIndex ? 'bg-green-500' : 'bg-gray-200' }}"></div>
                                @endif
                                {{-- Dot --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 z-10
                                    {{ $isCurrent ? 'bg-green-600 ring-4 ring-green-100' : ($isActive ? 'bg-green-500' : 'bg-gray-200') }}">
                                    @if($isActive)
                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                    @else
                                        <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                                    @endif
                                </div>
                                {{-- Label --}}
                                <div>
                                    <p class="font-semibold text-sm {{ $isActive ? 'text-gray-900' : 'text-gray-400' }}">{{ $labels[$status]['label'] }}</p>
                                    <p class="text-xs {{ $isActive ? 'text-gray-500' : 'text-gray-300' }}">{{ $labels[$status]['desc'] }}</p>
                                    @if($isCurrent && $status === 'paid' && $transaction->paid_at)
                                        <p class="text-xs text-green-600 font-medium mt-0.5">{{ $transaction->paid_at->format('d M Y, H:i') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        @if($isCancelled)
                            <div class="flex items-start gap-4 pt-2">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 bg-red-500 ring-4 ring-red-100 z-10">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-red-600">Cancelled</p>
                                    <p class="text-xs text-red-400">This order has been cancelled</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT: Summary + Delivery --}}
            <div class="space-y-6">

                {{-- Payment Summary --}}
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50/50 to-emerald-50/50">
                        <h3 class="font-bold text-gray-900 text-sm">Payment Summary</h3>
                    </div>
                    <div class="p-5 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-semibold">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Delivery Fee</span>
                            <span class="font-semibold">Rp {{ number_format($transaction->delivery_fee, 0, ',', '.') }}</span>
                        </div>
                        @if($transaction->payment_type)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Payment Method</span>
                                <span class="font-semibold capitalize">{{ str_replace('_', ' ', $transaction->payment_type) }}</span>
                            </div>
                        @endif
                        <div class="border-t-2 border-gray-200 pt-3 flex justify-between items-center">
                            <span class="font-bold text-gray-900">Grand Total</span>
                            <span class="text-xl font-bold text-green-700">Rp {{ number_format($transaction->grandTotal(), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Delivery Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Delivery Details
                        </h3>
                    </div>
                    <div class="p-5 space-y-3 text-sm">
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wider font-semibold mb-0.5">Recipient</p>
                            <p class="text-gray-900 font-medium">{{ $transaction->delivery_name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wider font-semibold mb-0.5">Phone</p>
                            <p class="text-gray-900 font-medium">{{ $transaction->delivery_phone }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wider font-semibold mb-0.5">Address</p>
                            <p class="text-gray-900 font-medium">{{ $transaction->delivery_address }}</p>
                        </div>
                    </div>
                </div>

                {{-- Order Info --}}
                <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900 text-sm">Order Info</h3>
                    </div>
                    <div class="p-5 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Order Date</span>
                            <span class="font-medium">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        @if($transaction->paid_at)
                            <div class="flex justify-between">
                                <span class="text-gray-500">Paid At</span>
                                <span class="font-medium">{{ $transaction->paid_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Items</span>
                            <span class="font-medium">{{ $transaction->items->sum('quantity') }} {{ Str::plural('item', $transaction->items->sum('quantity')) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                @if($transaction->status === 'pending' && $transaction->snap_token)
                    <a href="{{ route('customer.checkout.payment', $transaction->transaction_id) }}"
                       class="btn-primary-green flex items-center justify-center gap-2">
                        💳 Complete Payment
                    </a>
                @endif

                <a href="{{ route('customer.orders') }}"
                   class="flex items-center justify-center gap-2 py-3 px-6 bg-cream-100 text-green-800 text-sm font-semibold rounded-2xl hover:bg-cream-200 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Orders
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
