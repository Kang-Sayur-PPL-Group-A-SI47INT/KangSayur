@php $title = 'Payment'; @endphp

<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-700 transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('customer.orders') }}" class="hover:text-green-700 transition-colors">Orders</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium">Payment</span>
        </div>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900" style="font-family: Georgia, serif;">
                Complete Your Payment <span class="text-green-700">🔒</span>
            </h1>
            <p class="text-gray-500 text-sm mt-2">Order ID: <span class="font-mono text-gray-700">{{ $transaction->midtrans_order_id ?? '#' . $transaction->transaction_id }}</span></p>
        </div>

        {{-- Order Summary Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50/50 to-emerald-50/50">
                <h3 class="font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                    Order Summary
                </h3>
            </div>
            <div class="p-6">
                {{-- Items --}}
                <div class="space-y-3 mb-4">
                    @foreach($transaction->items as $item)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center text-lg flex-shrink-0">
                                    @php
                                        $emojis = ['🥬', '🥕', '🍅', '🌽', '🥦', '🍆', '🥒', '🫑'];
                                        $imgs = $item->listing ? $item->listing->getImagesArray() : [];
                                    @endphp
                                    @if(count($imgs))
                                        <img src="{{ asset('storage/' . $imgs[0]) }}" class="w-full h-full object-cover rounded-lg" alt="">
                                    @else
                                        {{ $emojis[$loop->index % count($emojis)] }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item->listing->title ?? 'Product' }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <p class="font-semibold text-gray-900">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="border-t border-dashed border-gray-200 pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Delivery Fee</span>
                        <span class="font-semibold">Rp {{ number_format($transaction->delivery_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="border-t-2 border-gray-200 pt-3 flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900">Grand Total</span>
                        <span class="text-2xl font-bold text-green-700">Rp {{ number_format($transaction->grandTotal(), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Delivery Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-8">
            <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Delivery Details
            </h3>
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider font-semibold mb-1">Recipient</p>
                    <p class="text-gray-900 font-medium">{{ $transaction->delivery_name }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs uppercase tracking-wider font-semibold mb-1">Phone</p>
                    <p class="text-gray-900 font-medium">{{ $transaction->delivery_phone }}</p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-gray-400 text-xs uppercase tracking-wider font-semibold mb-1">Address</p>
                    <p class="text-gray-900 font-medium">{{ $transaction->delivery_address }}</p>
                </div>
            </div>
        </div>

        {{-- Payment Simulation --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Select Payment Method</h3>
                    <p class="text-xs text-gray-500">Choose your preferred payment method</p>
                </div>
            </div>

            <div x-data="{ selected: 'bank_transfer' }" class="space-y-3">
                {{-- Payment Options --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @php
                        $methods = [
                            ['id' => 'bank_transfer', 'name' => 'Bank Transfer', 'icon' => '🏦'],
                            ['id' => 'credit_card', 'name' => 'Credit Card', 'icon' => '💳'],
                            ['id' => 'gopay', 'name' => 'GoPay', 'icon' => '📱'],
                            ['id' => 'ovo', 'name' => 'OVO', 'icon' => '💜'],
                            ['id' => 'qris', 'name' => 'QRIS', 'icon' => '📷'],
                            ['id' => 'shopeepay', 'name' => 'ShopeePay', 'icon' => '🧡'],
                        ];
                    @endphp

                    @foreach($methods as $method)
                        <div @click="selected = '{{ $method['id'] }}'"
                             :class="selected === '{{ $method['id'] }}' ? 'border-green-500 bg-green-50 ring-2 ring-green-100' : 'border-gray-200 hover:border-green-300'"
                             class="p-3 border-2 rounded-xl text-center cursor-pointer transition-all duration-200">
                            <span class="text-2xl block mb-1" dusk="payment-method">{{ $method['icon'] }}</span>
                            <span class="text-xs font-semibold text-gray-700">{{ $method['name'] }}</span>
                        </div>
                    @endforeach
                </div>

                {{-- Pay Button --}}
                <form method="POST" action="{{ route('customer.checkout.simulate', $transaction->transaction_id) }}" id="payment-form">
                    @csrf
                    <button type="submit" id="pay-button" dusk="confirm-pay"
                        class="w-full mt-4 py-4 bg-gradient-to-r from-green-700 to-green-800 text-white font-bold text-lg rounded-full hover:from-green-800 hover:to-green-900 transition-all duration-300 shadow-xl shadow-green-200/50 hover:shadow-green-300/50 flex items-center justify-center gap-2 group">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        Confirm & Pay — Rp {{ number_format($transaction->grandTotal(), 0, ',', '.') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center space-y-3">
            <p class="text-xs text-gray-400 flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                Simulated payment — no real charge will be made
            </p>

            <a href="{{ route('customer.orders') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                View my orders
            </a>
        </div>
    </div>

    <script>
        // Prevent double submission
        document.getElementById('payment-form').addEventListener('submit', function () {
            const btn = document.getElementById('pay-button');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing Payment...';
        });
    </script>
</x-app-layout>
