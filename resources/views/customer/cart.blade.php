@php $title = 'Shopping Cart'; @endphp

<x-app-layout>
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="cartPage()">

        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-3">
                <a href="{{ route('home') }}" class="hover:text-green-700 transition-colors">Home</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span class="text-gray-900 font-medium">Shopping Cart</span>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900" style="font-family: Georgia, serif;">
                Shopping Cart
                <span class="text-green-700">🛒</span>
            </h1>
        </div>

        @if($cart->items->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT — Cart Items --}}
                <div class="lg:col-span-2 space-y-4">

                    {{-- Cart Item Count Header --}}
                    <div class="flex items-center justify-between bg-white rounded-2xl px-6 py-4 border border-gray-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                            </div>
                            <span class="text-sm font-semibold text-gray-700">{{ $cart->items->count() }} {{ Str::plural('item', $cart->items->count()) }} in your cart</span>
                        </div>
                    </div>

                    {{-- Cart Items --}}
                    @foreach($cart->items as $item)
                        <div class="bg-white rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 border border-gray-100 group"
                             x-data="{ removing: false }"
                             :class="{ 'opacity-50 scale-95': removing }">
                            <div class="p-5 sm:p-6">
                                <div class="flex gap-4 sm:gap-6">

                                    {{-- Product Image/Emoji --}}
                                    <div class="flex-shrink-0">
                                        @if($item->listing->image && !str_starts_with($item->listing->image, '['))
                                            <img src="{{ asset('storage/' . $item->listing->image) }}"
                                                 alt="{{ $item->listing->title }}"
                                                 class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover">
                                        @else
                                            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-gradient-to-br from-green-100 to-emerald-200 flex items-center justify-center">
                                                <span class="text-4xl sm:text-5xl">
                                                    @php
                                                        $emojis = ['🥬', '🥕', '🍅', '🌽', '🥦', '🍆', '🥒', '🫑'];
                                                        echo $emojis[$loop->index % count($emojis)];
                                                    @endphp
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Product Details --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2">
                                            <div class="flex-1">
                                                {{-- Category Badge --}}
                                                @if($item->listing->produce)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-700 mb-2">
                                                        {{ $item->listing->produce->name }}
                                                    </span>
                                                @endif

                                                {{-- Title --}}
                                                <h3 class="text-base sm:text-lg font-bold text-gray-900 truncate">
                                                    {{ $item->listing->title }}
                                                </h3>

                                                {{-- Farmer --}}
                                                <p class="text-sm text-gray-500 mt-0.5 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                                    {{ $item->listing->farmer->name ?? 'Local Farmer' }}
                                                </p>

                                                {{-- Unit Price --}}
                                                <p class="text-sm text-gray-500 mt-1">
                                                    Rp {{ number_format($item->listing->price, 0, ',', '.') }}
                                                    <span class="text-gray-400">/ {{ $item->listing->unit ?? 'kg' }}</span>
                                                </p>
                                            </div>

                                            {{-- Subtotal (Desktop) --}}
                                            <div class="hidden sm:block text-right">
                                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Subtotal</p>
                                                <p class="text-lg font-bold text-green-700 mt-0.5">
                                                    Rp {{ number_format($item->quantity * $item->listing->price, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Quantity Controls & Actions --}}
                                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-50">

                                            {{-- Quantity Controls --}}
                                            <div class="flex items-center gap-1">
                                                <form method="POST" action="{{ route('customer.cart.update', $item->cart_item_id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="quantity" value="{{ max(1, $item->quantity - 1) }}">
                                                    <button type="submit"
                                                            class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition-all duration-200 {{ $item->quantity <= 1 ? 'opacity-40 cursor-not-allowed' : '' }}"
                                                            {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                                                    </button>
                                                </form>

                                                <div class="w-12 h-9 flex items-center justify-center rounded-xl bg-gray-50 text-sm font-bold text-gray-900">
                                                    {{ $item->quantity }}
                                                </div>

                                                <form method="POST" action="{{ route('customer.cart.update', $item->cart_item_id) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="quantity" value="{{ $item->quantity + 1 }}">
                                                    <button type="submit"
                                                            class="w-9 h-9 flex items-center justify-center rounded-xl border border-gray-200 text-gray-500 hover:bg-green-50 hover:border-green-300 hover:text-green-700 transition-all duration-200">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- Mobile Subtotal --}}
                                            <div class="sm:hidden">
                                                <p class="text-base font-bold text-green-700">
                                                    Rp {{ number_format($item->quantity * $item->listing->price, 0, ',', '.') }}
                                                </p>
                                            </div>

                                            {{-- Remove Button --}}
                                            <form method="POST" action="{{ route('customer.cart.remove', $item->cart_item_id) }}"
                                                  @submit="removing = true">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                                    <span class="hidden sm:inline">Remove</span>
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- RIGHT — Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-24">
                        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

                            {{-- Header --}}
                            <div class="px-6 py-5 border-b border-gray-100">
                                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                                    Order Summary
                                </h2>
                            </div>

                            {{-- Details --}}
                            <div class="p-6 space-y-4">

                                {{-- Items breakdown --}}
                                <div class="space-y-3 pb-4 border-b border-dashed border-gray-200">
                                    @foreach($cart->items as $item)
                                        <div class="flex justify-between items-start text-sm">
                                            <div class="flex-1 mr-3">
                                                <p class="text-gray-700 font-medium truncate">{{ $item->listing->title }}</p>
                                                <p class="text-gray-400 text-xs">{{ $item->quantity }} × Rp {{ number_format($item->listing->price, 0, ',', '.') }}</p>
                                            </div>
                                            <p class="text-gray-900 font-medium whitespace-nowrap">Rp {{ number_format($item->quantity * $item->listing->price, 0, ',', '.') }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Subtotal --}}
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Subtotal</span>
                                    <span class="text-gray-900 font-semibold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>

                                {{-- Delivery Fee --}}
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500 flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                        Delivery Fee
                                    </span>
                                    <span class="text-gray-900 font-semibold">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</span>
                                </div>

                                {{-- Divider --}}
                                <div class="border-t-2 border-gray-200 my-1"></div>

                                {{-- Grand Total --}}
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-bold text-gray-900">Grand Total</span>
                                    <span class="text-xl font-bold text-green-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                                </div>

                                {{-- Checkout Button --}}
                                <a href="#" class="block w-full mt-4">
                                    <button class="btn-primary-green flex items-center justify-center gap-2">
                                        Proceed to Checkout
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                    </button>
                                </a>

                                {{-- Secure Payment Note --}}
                                <div class="flex items-center justify-center gap-1.5 text-xs text-gray-400 mt-2">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                                    Secure checkout powered by Midtrans
                                </div>
                            </div>
                        </div>

                        {{-- Continue Shopping --}}
                        <a href="{{ route('marketplace') }}"
                           class="flex items-center justify-center gap-2 mt-4 py-3 px-6 bg-cream-100 text-green-800 text-sm font-semibold rounded-2xl hover:bg-cream-200 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

        @else
            {{-- Empty Cart State --}}
            <div class="flex flex-col items-center justify-center py-20">
                <div class="w-32 h-32 bg-gradient-to-br from-cream-100 to-cream-200 rounded-full flex items-center justify-center mb-6 animate-pulse">
                    <span class="text-6xl">🛍️</span>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2" style="font-family: Georgia, serif;">Your cart is empty</h2>
                <p class="text-gray-500 text-center max-w-md mb-8">
                    Looks like you haven't added any fresh produce yet. Explore our marketplace to find the freshest harvests from local farmers!
                </p>
                <a href="{{ route('marketplace') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 bg-gradient-to-r from-green-700 to-green-800 text-white font-bold rounded-full shadow-lg shadow-green-200/50 hover:from-green-800 hover:to-green-900 hover:shadow-green-300/50 transition-all duration-300">
                    Start Shopping
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        @endif

        {{-- Recommended Section --}}
        @if($recommended->count() > 0)
            <div class="mt-16">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Picked just for you</p>
                        <h2 class="text-2xl font-bold text-gray-900" style="font-family: Georgia, serif;">You might also like</h2>
                    </div>
                    <a href="{{ route('marketplace') }}" class="text-sm font-semibold text-green-700 hover:text-green-800 flex items-center gap-1 transition-colors">
                        View All
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($recommended as $product)
                        <div class="product-card group">
                            {{-- Image --}}
                            <div class="aspect-square overflow-hidden relative">
                                @if(is_object($product) && isset($product->emoji))
                                    {{-- Dummy data display --}}
                                    <div class="w-full h-full bg-gradient-to-br {{ $product->gradient ?? 'from-green-100 to-emerald-200' }} flex items-center justify-center group-hover:scale-105 transition-transform duration-500">
                                        <span class="text-6xl">{{ $product->emoji }}</span>
                                    </div>
                                    @if(isset($product->category))
                                        <span class="product-badge product-badge-new">{{ $product->category }}</span>
                                    @endif
                                @elseif($product->image && !str_starts_with($product->image, '['))
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         alt="{{ $product->title }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @if($product->produce)
                                        <span class="product-badge product-badge-new">{{ $product->produce->name }}</span>
                                    @endif
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-green-100 to-emerald-200 flex items-center justify-center group-hover:scale-105 transition-transform duration-500">
                                        @php
                                            $emojis = ['🥬', '🥕', '🍅', '🌽', '🥦', '🍆', '🥒', '🫑'];
                                            echo '<span class="text-6xl">' . $emojis[$loop->index % count($emojis)] . '</span>';
                                        @endphp
                                    </div>
                                    @if($product->produce)
                                        <span class="product-badge product-badge-new">{{ $product->produce->name }}</span>
                                    @endif
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="p-4">
                                <h3 class="text-sm font-bold text-gray-900 truncate mb-1">{{ $product->title }}</h3>

                                <p class="text-xs text-gray-500 flex items-center gap-1 mb-2">
                                    <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                    @if(is_object($product) && isset($product->farmer_name))
                                        {{ $product->farmer_name }}
                                    @else
                                        {{ $product->farmer->name ?? 'Local Farmer' }}
                                    @endif
                                </p>

                                <div class="flex items-center justify-between">
                                    <p class="text-base font-bold text-green-700">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                        <span class="text-xs font-normal text-gray-400">/{{ $product->unit ?? 'kg' }}</span>
                                    </p>

                                    {{-- Rating --}}
                                    @php
                                        $rating = is_object($product) && isset($product->rating) ? $product->rating : ($product->averageRating() ?? 0);
                                    @endphp
                                    @if($rating > 0)
                                        <div class="flex items-center gap-0.5">
                                            <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                            <span class="text-xs font-semibold text-gray-600">{{ number_format($rating, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    <script>
        function cartPage() {
            return {
                // Alpine.js data for future interactivity
            }
        }
    </script>
</x-app-layout>
