<x-app-layout>
    @php $title = 'Checkout'; @endphp
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Checkout 💳</h1>

        <form method="POST" action="{{ route('customer.checkout.process') }}">
            @csrf
            <div class="grid lg:grid-cols-5 gap-8">
                <div class="lg:col-span-3 space-y-6">
                    <!-- Step 1: Delivery Info -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 bg-green-800 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                            <h3 class="font-bold text-gray-900">Delivery Information</h3>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <input type="text" name="delivery_name" required value="{{ old('delivery_name', auth()->user()->name) }}"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                                @error('delivery_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="tel" name="delivery_phone" required value="{{ old('delivery_phone') }}" placeholder="08xxxxxxxxxx"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                                @error('delivery_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                                <textarea name="delivery_address" required rows="3" placeholder="Full street address, city, postal code..."
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm resize-none">{{ old('delivery_address', auth()->user()->address) }}</textarea>
                                @error('delivery_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Order Review -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-8 h-8 bg-green-800 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                            <h3 class="font-bold text-gray-900">Order Review</h3>
                        </div>
                        <div class="space-y-3">
                            @foreach($cart->items as $item)
                                <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                                    <div class="flex items-center gap-3">
                                        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-xl overflow-hidden">
                                            @php $imgs = $item->listing->getImagesArray(); @endphp
                                            @if(count($imgs))
                                                <img src="{{ asset('storage/' . $imgs[0]) }}" class="w-full h-full object-cover rounded-xl">
                                            @else 🥬 @endif
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

                    <!-- Step 3: Payment -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 bg-green-800 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                            <h3 class="font-bold text-gray-900">Payment Method</h3>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach(['Bank Transfer', 'Credit Card', 'OVO', 'GoPay'] as $method)
                                <div class="p-3 border border-gray-200 rounded-xl text-center text-sm text-gray-600 hover:border-green-400 cursor-pointer transition-all">
                                    {{ $method }}
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-3">Payment processed securely via Midtrans</p>
                    </div>
                </div>

                <!-- Summary -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
                        <h3 class="font-bold text-gray-900 text-lg mb-5">Payment Summary</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Delivery Fee</span>
                                <span>Rp {{ number_format($deliveryFee, 0, ',', '.') }}</span>
                            </div>
                            <div class="border-t border-gray-100 pt-3 flex justify-between text-xl font-bold text-gray-900">
                                <span>Grand Total</span>
                                <span class="text-green-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <button type="submit" class="w-full mt-6 py-4 bg-green-800 text-white font-bold text-lg rounded-full hover:bg-green-900 transition-all shadow-xl shadow-green-200/50">
                            💳 Pay Now — Rp {{ number_format($grandTotal, 0, ',', '.') }}
                        </button>
                        <p class="text-center text-xs text-gray-400 mt-3">Secure payment via Midtrans gateway</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>