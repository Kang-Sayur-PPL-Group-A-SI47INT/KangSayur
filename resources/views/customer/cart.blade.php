<x-app-layout>
    @php $title = 'Cart'; @endphp
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Shopping Cart 🧺</h1>

        @if($cart->items->count())
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 space-y-4">
                    @foreach($cart->items as $item)
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 flex items-center gap-5">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl flex items-center justify-center text-3xl flex-shrink-0 overflow-hidden">
                                @php $images = $item->listing->getImagesArray(); @endphp
                                @if(count($images))
                                    <img src="{{ asset('storage/' . $images[0]) }}" class="w-full h-full object-cover rounded-xl">
                                @else
                                    🥬
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 text-sm">{{ $item->listing->title }}</h3>
                                <p class="text-xs text-gray-500">{{ $item->listing->farmer->name ?? '' }}</p>
                                @if($item->offer && $item->offer->status === 'accepted')
                                    <p class="text-xs text-amber-600 font-medium mt-1">💰 Negotiated: Rp {{ number_format($item->offer->offered_price, 0, ',', '.') }}/{{ $item->listing->unit ?? 'kg' }}</p>
                                @endif
                                @if($item->listing->hasDiscount() && (!$item->offer || $item->offer->status !== 'accepted'))
                                    <p class="text-xs text-gray-400 line-through mt-1">Rp {{ number_format($item->listing->price, 0, ',', '.') }}/{{ $item->listing->unit ?? 'kg' }}</p>
                                    <p class="text-green-700 font-bold text-sm mt-0.5">Rp {{ number_format($item->effectivePrice(), 0, ',', '.') }}/{{ $item->listing->unit ?? 'kg' }}
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 ml-1">🏷️ {{ $item->listing->formattedDiscount() }}</span>
                                    </p>
                                @else
                                    <p class="text-green-700 font-bold text-sm mt-1">Rp {{ number_format($item->effectivePrice(), 0, ',', '.') }}/{{ $item->listing->unit ?? 'kg' }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('customer.cart.update', $item) }}" class="flex items-center gap-1">
                                    @csrf @method('PUT')
                                    <button type="button" onclick="this.nextElementSibling.stepDown(); this.closest('form').submit()" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200 font-bold">−</button>
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" class="w-14 text-center py-1.5 rounded-lg border border-gray-200 text-sm font-semibold" onchange="this.form.submit()">
                                    <button type="button" onclick="this.previousElementSibling.stepUp(); this.closest('form').submit()" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200 font-bold">+</button>
                                </form>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-bold text-gray-900">Rp {{ number_format($item->subtotal(), 0, ',', '.') }}</p>
                                <form method="POST" action="{{ route('customer.cart.remove', $item) }}" class="mt-1">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-400 hover:text-red-600">Remove</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Summary -->
                <div>
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
                        <h3 class="font-bold text-gray-900 text-lg mb-4">Order Summary</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal ({{ $cart->items->count() }} items)</span>
                                <span>Rp {{ number_format($cart->totalPrice(), 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Delivery Fee</span>
                                <span>Rp {{ number_format($deliveryFee, 0, ',', '.') }}</span>
                            </div>

                            <!-- Promo Code Stub -->
                            <div class="pt-2">
                                <div class="flex gap-2">
                                    <input type="text" placeholder="Promo code" class="flex-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-green-400 outline-none">
                                    <button type="button" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200">Apply</button>
                                </div>
                            </div>

                            <div class="border-t border-gray-100 pt-3 flex justify-between text-lg font-bold text-gray-900">
                                <span>Total</span>
                                <span class="text-green-700">Rp {{ number_format($cart->totalPrice() + $deliveryFee, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <a href="{{ route('customer.checkout') }}" class="block w-full mt-6 py-3.5 bg-green-800 text-white font-bold rounded-full text-center hover:bg-green-900 transition-all shadow-lg shadow-green-200/50">
                            Proceed to Checkout →
                        </a>
                        <a href="{{ route('marketplace') }}" class="block text-center mt-3 text-sm text-green-700 hover:text-green-800 font-medium">Continue Shopping</a>
                    </div>
                </div>
            </div>

            <!-- Cross-sell -->
            @if($recommended->count())
            <div class="mt-12">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Farmers Recommend 🌿</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($recommended as $rec)
                        <a href="{{ route('marketplace.show', $rec) }}" class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-lg transition-all group">
                            <div class="aspect-video bg-green-50 flex items-center justify-center text-3xl overflow-hidden">
                                @php $imgs = $rec->getImagesArray(); @endphp
                                @if(count($imgs))
                                    <img src="{{ asset('storage/' . $imgs[0]) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                @else 🥬 @endif
                            </div>
                            <div class="p-3">
                                <p class="text-sm font-semibold text-gray-900 line-clamp-1">{{ $rec->title }}</p>
                                <p class="text-green-700 font-bold text-sm">Rp {{ number_format($rec->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        @else
            <div class="text-center py-20">
                <div class="text-6xl mb-4">🧺</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Your Cart is Empty</h3>
                <p class="text-gray-500 mb-6">Start shopping for fresh produce!</p>
                <a href="{{ route('marketplace') }}" class="px-8 py-3 bg-green-800 text-white rounded-full font-semibold hover:bg-green-900">Browse Marketplace</a>
            </div>
        @endif
    </div>
</x-app-layout>
