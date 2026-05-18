@php $title = 'Checkout'; @endphp

<x-app-layout>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-green-700 transition-colors">Home</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('customer.cart') }}" class="hover:text-green-700 transition-colors">Cart</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium">Checkout</span>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-gray-900" style="font-family: Georgia, serif;">
                Checkout
                <span class="text-green-700">💳</span>
            </h1>
            <p class="text-gray-500 text-sm mt-1">Review your order and complete your purchase</p>
        </div>

        <form method="POST" action="{{ route('customer.checkout.process') }}" id="checkout-form">
            @csrf
            <div class="grid lg:grid-cols-5 gap-8">

                {{-- LEFT — Steps --}}
                <div class="lg:col-span-3 space-y-6">

                    {{-- Step 1: Delivery Info --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-700 to-green-800 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg shadow-green-200/50">1</div>
                            <h3 class="font-bold text-gray-900">Delivery Information</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" name="delivery_name" required value="{{ old('delivery_name', auth()->user()->name) }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm transition-all duration-200">
                                @error('delivery_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" name="delivery_phone" required value="{{ old('delivery_phone') }}" placeholder="08xxxxxxxxxx" pattern="[0-9]*" inputmode="numeric" maxlength="16"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm transition-all duration-200">
                                @error('delivery_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                                <textarea name="delivery_address" required rows="3" placeholder="Full street address, city, postal code..."
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm resize-none transition-all duration-200">{{ old('delivery_address', auth()->user()->address) }}</textarea>
                                @error('delivery_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Order Review --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-700 to-green-800 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg shadow-green-200/50">2</div>
                            <h3 class="font-bold text-gray-900">Order Review</h3>
                            <span class="ml-auto text-xs text-gray-400 font-medium">{{ $cart->items->count() }} {{ Str::plural('item', $cart->items->count()) }}</span>
                        </div>
                        <div class="space-y-0">
                            @foreach($cart->items as $item)
                                <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0 group">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-xl overflow-hidden flex-shrink-0">
                                            @php $imgs = $item->listing->getImagesArray(); @endphp
                                            @if(count($imgs))
                                                <img src="{{ asset('storage/' . $imgs[0]) }}" class="w-full h-full object-cover rounded-xl" alt="{{ $item->listing->title }}">
                                            @else
                                                @php
                                                    $emojis = ['🥬', '🥕', '🍅', '🌽', '🥦', '🍆', '🥒', '🫑'];
                                                    echo $emojis[$loop->index % count($emojis)];
                                                @endphp
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $item->listing->title }}</p>
                                            <p class="text-xs text-gray-500">{{ $item->quantity }} {{ $item->listing->unit ?? 'kg' }} × Rp {{ number_format($item->effectivePrice(), 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                    <p class="font-semibold text-gray-900 text-sm">Rp {{ number_format($item->subtotal(), 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Step 3: Payment Method Info --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 hover:shadow-md transition-shadow duration-300">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 bg-gradient-to-br from-green-700 to-green-800 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg shadow-green-200/50">3</div>
                            <h3 class="font-bold text-gray-900">Payment Method</h3>
                        </div>
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-100">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm">Secure Payment</p>
                                    <p class="text-xs text-gray-500">Choose your preferred method on the next page — Bank Transfer, Credit Card, GoPay, OVO, and more</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mt-4">
                            @foreach(['Bank Transfer', 'Credit Card', 'GoPay', 'OVO', 'QRIS', 'ShopeePay'] as $method)
                                <span class="px-3 py-1.5 bg-gray-50 border border-gray-100 rounded-lg text-xs text-gray-500 font-medium">{{ $method }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- RIGHT — Summary --}}
                <div class="lg:col-span-2">
                    <div class="sticky top-24">
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-green-50/50 to-emerald-50/50">
                                <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                    Payment Summary
                                </h3>
                            </div>
                            <div class="p-6 space-y-4">
                                {{-- Items breakdown --}}
                                <div class="space-y-2 pb-4 border-b border-dashed border-gray-200">
                                    @foreach($cart->items as $item)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600 truncate mr-2">{{ Str::limit($item->listing->title, 25) }} <span class="text-gray-400">×{{ $item->quantity }}</span></span>
                                            <span class="text-gray-900 font-medium whitespace-nowrap">Rp {{ number_format($item->subtotal(), 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Subtotal</span>
                                    <span class="text-gray-900 font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                        Delivery Fee
                                    </span>
                                    <span class="text-gray-900 font-semibold">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</span>
                                </div>

                                <div class="border-t-2 border-gray-200 pt-4 flex justify-between items-center">
                                    <span class="text-base font-bold text-gray-900">Grand Total</span>
                                    <span class="text-xl font-bold text-green-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>

                                <button type="submit" id="checkout-submit-btn"
                                    dusk="proceed-to-payment"
                                    class="w-full mt-2 py-4 bg-gradient-to-r from-green-700 to-green-800 text-white font-bold text-lg rounded-full hover:from-green-800 hover:to-green-900 transition-all duration-300 shadow-xl shadow-green-200/50 hover:shadow-green-300/50 flex items-center justify-center gap-2 group">
                                    <span>Proceed to Payment</span>
                                    <span> Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </button>

                                <div class="flex items-center justify-center gap-1.5 text-xs text-gray-400 mt-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                    Secure checkout
                                </div>
                            </div>
                        </div>

                        {{-- Back to Cart --}}
                        <a href="{{ route('customer.cart') }}"
                           class="flex items-center justify-center gap-2 mt-4 py-3 px-6 bg-cream-100 text-green-800 text-sm font-semibold rounded-2xl hover:bg-cream-200 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Prevent double submission
        document.getElementById('checkout-form').addEventListener('submit', function () {
            const btn = document.getElementById('checkout-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...';
        });
    </script>
</x-app-layout>