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

                            {{-- Delivery Location Map (required) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    📍 Pin Delivery Location
                                    <span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <p class="text-xs text-gray-500 mb-2">Click on the map or search to pin your exact delivery location for accurate shipping costs.</p>

                                {{-- Map Search --}}
                                <div class="relative mb-2">
                                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <input type="text" name="customer-map-search" id="checkout-map-search" placeholder="Search address..."
                                        class="w-full pl-10 pr-28 py-2.5 rounded-xl border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none text-sm">
                                    <button type="button" id="checkout-geolocate-btn"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-semibold rounded-lg hover:bg-green-100 transition-colors flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        My Location
                                    </button>
                                </div>

                                {{-- Map --}}
                                <div id="checkout-map" class="w-full rounded-xl border-2 border-gray-200 overflow-hidden" style="height: 280px; z-index: 1;"></div>

                                {{-- Coordinate Badges --}}
                                <div class="mt-2 flex items-center gap-3">
                                    <span id="checkout-coord-badge" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-50 rounded-lg text-xs text-gray-500">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                        <span id="checkout-coord-text">{{ ($userLatitude && $userLongitude) ? number_format($userLatitude, 6) . ', ' . number_format($userLongitude, 6) : 'Click map to pin location' }}</span>
                                    </span>
                                    <span id="checkout-distance-badge" class="hidden inline-flex items-center gap-1 px-3 py-1.5 bg-green-50 rounded-lg text-xs text-green-700 font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                                        <span id="checkout-distance-text"></span>
                                    </span>
                                </div>

                                {{-- Map-required error message --}}
                                <div id="checkout-map-error" class="hidden mt-2 p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-xs flex items-center gap-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.539-1.333-3.309 0L3.732 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    Please pin your delivery location on the map before continuing.
                                </div>

                                {{-- Hidden coordinate inputs --}}
                                <input type="hidden" name="delivery_latitude" id="checkout-latitude" value="{{ old('delivery_latitude', $userLatitude) }}">
                                <input type="hidden" name="delivery_longitude" id="checkout-longitude" value="{{ old('delivery_longitude', $userLongitude) }}">
                            </div>

                            {{-- Delivery Address — read-only, auto-filled from map --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Delivery Address
                                    <span id="checkout-addr-indicator" class="ml-2 text-xs text-green-600 font-normal"></span>
                                </label>
                                <textarea name="delivery_address" readonly rows="3"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm resize-none text-gray-600 cursor-default outline-none">{{ old('delivery_address', auth()->user()->address) }}</textarea>
                                <p class="text-xs text-gray-400 mt-1">📍 Auto-filled when you pin a location on the map above</p>
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

    {{-- Leaflet CSS & JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        #checkout-map { cursor: crosshair !important; }
        .leaflet-control-attribution { font-size: 9px !important; }
        .checkout-pin { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3)); }
    </style>

    <script>
        // Validate map pin and prevent double submission
        document.getElementById('checkout-form').addEventListener('submit', function (e) {
            const lat = document.getElementById('checkout-latitude').value;
            const lng = document.getElementById('checkout-longitude').value;

            if (!lat || !lng) {
                e.preventDefault();
                const mapErr = document.getElementById('checkout-map-error');
                if (mapErr) {
                    mapErr.classList.remove('hidden');
                    mapErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            // Hide any previous error and lock button
            const mapErr = document.getElementById('checkout-map-error');
            if (mapErr) mapErr.classList.add('hidden');

            const btn = document.getElementById('checkout-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Processing...';
        });

        // Checkout Map
        document.addEventListener('DOMContentLoaded', function() {
            const userLat = {{ $userLatitude ?? 'null' }};
            const userLng = {{ $userLongitude ?? 'null' }};
            const hasCoords = userLat !== null && userLng !== null;

            // Farmer locations from cart items
            const farmerLocations = [
                @foreach($cart->items as $item)
                    @if($item->listing->farmer && $item->listing->farmer->latitude && $item->listing->farmer->longitude)
                    {
                        lat: {{ $item->listing->farmer->latitude }},
                        lng: {{ $item->listing->farmer->longitude }},
                        name: @json($item->listing->farmer->name),
                    },
                    @endif
                @endforeach
            ];

            // Center: user location > first farmer > Bandung default
            const centerLat = hasCoords ? userLat : (farmerLocations.length ? farmerLocations[0].lat : -6.9175);
            const centerLng = hasCoords ? userLng : (farmerLocations.length ? farmerLocations[0].lng : 107.6191);

            const map = L.map('checkout-map', {
                zoomControl: true,
                scrollWheelZoom: true,
            }).setView([centerLat, centerLng], hasCoords ? 14 : 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
                maxZoom: 19,
            }).addTo(map);

            // Customer pin icon
            const customerIcon = L.divIcon({
                html: `<div style="
                    width: 32px; height: 32px;
                    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
                    border-radius: 50% 50% 50% 0;
                    transform: rotate(-45deg);
                    border: 3px solid white;
                    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
                    display: flex; align-items: center; justify-content: center;
                "><span style="transform: rotate(45deg); font-size: 14px;">📍</span></div>`,
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                className: 'checkout-pin',
            });

            // Farmer pin icon
            const farmerIcon = L.divIcon({
                html: `<div style="
                    width: 28px; height: 28px;
                    background: linear-gradient(135deg, #22c55e, #059669);
                    border-radius: 50%;
                    border: 2px solid white;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.25);
                    display: flex; align-items: center; justify-content: center;
                "><span style="font-size: 12px;">🌾</span></div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 14],
                className: 'checkout-pin',
            });

            // Add farmer markers
            const farmerMarkers = [];
            const uniqueFarmers = [];
            farmerLocations.forEach(f => {
                if (!uniqueFarmers.find(u => u.lat === f.lat && u.lng === f.lng)) {
                    uniqueFarmers.push(f);
                    const fm = L.marker([f.lat, f.lng], { icon: farmerIcon })
                        .addTo(map)
                        .bindPopup(`<div class="text-xs font-medium">🌾 ${f.name}</div><div class="text-xs text-gray-500">Farmer Location</div>`);
                    farmerMarkers.push(fm);
                }
            });

            let customerMarker = null;

            function haversineKm(lat1, lng1, lat2, lng2) {
                const R = 6371;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLon = (lng2 - lng1) * Math.PI / 180;
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                    Math.sin(dLon/2) * Math.sin(dLon/2);
                return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            }

            function placeCustomerPin(lat, lng) {
                if (customerMarker) {
                    customerMarker.setLatLng([lat, lng]);
                } else {
                    customerMarker = L.marker([lat, lng], { icon: customerIcon, draggable: true }).addTo(map);
                    customerMarker.on('dragend', function(e) {
                        const pos = e.target.getLatLng();
                        updateCheckoutCoords(pos.lat, pos.lng);
                    });
                }
                updateCheckoutCoords(lat, lng);
            }

            function updateCheckoutCoords(lat, lng) {
                const latF = parseFloat(lat).toFixed(8);
                const lngF = parseFloat(lng).toFixed(8);
                document.getElementById('checkout-latitude').value = latF;
                document.getElementById('checkout-longitude').value = lngF;
                document.getElementById('checkout-coord-text').textContent = parseFloat(lat).toFixed(6) + ', ' + parseFloat(lng).toFixed(6);
                document.getElementById('checkout-coord-badge').classList.remove('bg-gray-50', 'text-gray-500');
                document.getElementById('checkout-coord-badge').classList.add('bg-blue-50', 'text-blue-700');

                // Calculate max distance to farmers
                if (uniqueFarmers.length > 0) {
                    let maxDist = 0;
                    uniqueFarmers.forEach(f => {
                        const d = haversineKm(lat, lng, f.lat, f.lng);
                        if (d > maxDist) maxDist = d;
                    });
                    const badge = document.getElementById('checkout-distance-badge');
                    badge.classList.remove('hidden');
                    document.getElementById('checkout-distance-text').textContent = '~' + maxDist.toFixed(1) + ' km from farm';
                }

                // Reverse geocode to auto-fill delivery address
                const addrField = document.querySelector('textarea[name="delivery_address"]');
                const addrIndicator = document.getElementById('checkout-addr-indicator');
                if (addrIndicator) addrIndicator.textContent = '⏳ Fetching address...';
                fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latF}&lon=${lngF}&addressdetails=1`)
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.display_name && addrField) {
                            addrField.value = data.display_name;
                            if (addrIndicator) addrIndicator.textContent = '✅ Auto-filled from map';
                        }
                    })
                    .catch(() => {
                        if (addrIndicator) addrIndicator.textContent = '';
                    });
            }

            // Place existing pin
            if (hasCoords) {
                placeCustomerPin(userLat, userLng);
            }

            // Click to place pin
            map.on('click', function(e) {
                placeCustomerPin(e.latlng.lat, e.latlng.lng);
            });

            // Fit bounds to show all markers
            if (hasCoords && uniqueFarmers.length > 0) {
                const allPoints = [[userLat, userLng], ...uniqueFarmers.map(f => [f.lat, f.lng])];
                map.fitBounds(allPoints, { padding: [30, 30], maxZoom: 14 });
            }

            // Geolocation
            document.getElementById('checkout-geolocate-btn').addEventListener('click', function() {
                if (!navigator.geolocation) { alert('Geolocation is not supported.'); return; }
                const btn = this;
                btn.innerHTML = '<svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Finding...';
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        map.setView([pos.coords.latitude, pos.coords.longitude], 16);
                        placeCustomerPin(pos.coords.latitude, pos.coords.longitude);
                        btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> My Location';
                    },
                    (err) => {
                        alert('Could not get location: ' + err.message);
                        btn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> My Location';
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            });

            // Address search
            let searchTimer;
            const searchInput = document.getElementById('checkout-map-search');
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') { e.preventDefault(); doSearch(this.value); }
            });
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                if (this.value.length >= 3) searchTimer = setTimeout(() => doSearch(this.value), 800);
            });

            function doSearch(query) {
                if (!query.trim()) return;
                fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=id`)
                    .then(r => r.json())
                    .then(data => {
                        if (data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            map.setView([lat, lng], 16);
                            placeCustomerPin(lat, lng);
                        } else {
                            alert('Location not found. Try different keywords.');
                        }
                    })
                    .catch(() => alert('Search failed. Check your internet connection.'));
            }

            setTimeout(() => map.invalidateSize(), 200);
        });
    </script>
</x-app-layout>
