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

        {{-- Payment Method Selection --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6"
             x-data="{
                selected: 'bank_transfer',
                selectedBank: 'bca',
                loading: false,
                resultModal: false,
                resultData: null,
                resultMethod: null
             }">

            <div class="flex items-center gap-3 mb-5">
                <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Select Payment Method</h3>
                    <p class="text-xs text-gray-500">Powered by Midtrans</p>
                </div>
            </div>

            {{-- Method Tabs --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                @php
                    $methods = [
                        ['id' => 'bank_transfer', 'name' => 'Bank Transfer', 'icon' => '🏦'],
                        ['id' => 'credit_card',   'name' => 'Credit Card',   'icon' => '💳'],
                        ['id' => 'gopay',         'name' => 'GoPay',         'icon' => '📱'],
                        ['id' => 'qris',          'name' => 'QRIS',          'icon' => '📷'],
                    ];
                @endphp
                @foreach($methods as $method)
                    <div @click="selected = '{{ $method['id'] }}'"
                         :class="selected === '{{ $method['id'] }}' ? 'border-green-500 bg-green-50 ring-2 ring-green-100' : 'border-gray-200 hover:border-green-300'"
                         class="p-3 border-2 rounded-xl text-center cursor-pointer transition-all duration-200">
                        <span class="text-2xl block mb-1">{{ $method['icon'] }}</span>
                        <span class="text-xs font-semibold text-gray-700">{{ $method['name'] }}</span>
                    </div>
                @endforeach
            </div>

            {{-- Bank Transfer: bank picker --}}
            <div x-show="selected === 'bank_transfer'" x-transition class="mb-5">
                <p class="text-sm font-semibold text-gray-700 mb-2">Select Bank</p>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                    @foreach(['bca','bni','bri','mandiri','permata'] as $bank)
                        <div @click="selectedBank = '{{ $bank }}'"
                             :class="selectedBank === '{{ $bank }}' ? 'border-green-500 bg-green-50 ring-2 ring-green-100' : 'border-gray-200 hover:border-green-300'"
                             class="py-2 px-3 border-2 rounded-xl text-center cursor-pointer transition-all duration-200">
                            <span class="text-xs font-bold text-gray-700 uppercase">{{ $bank }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Credit Card Form --}}
            <div x-show="selected === 'credit_card'" x-transition class="mb-5 space-y-3">
                <p class="text-sm font-semibold text-gray-700 mb-2">Card Details</p>
                <div>
                    <label class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Card Number</label>
                    <input id="card_number" type="text" maxlength="19" placeholder="4811 1111 1111 1114"
                           class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent"
                           oninput="this.value=this.value.replace(/\D/g,'').replace(/(.{4})/g,'$1 ').trim()">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Exp Month</label>
                        <input id="card_exp_month" type="text" maxlength="2" placeholder="01"
                               class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Exp Year</label>
                        <input id="card_exp_year" type="text" maxlength="4" placeholder="2025"
                               class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                    </div>
                    <div>
                        <label class="text-xs text-gray-500 font-semibold uppercase tracking-wider">CVV</label>
                        <input id="card_cvv" type="password" maxlength="3" placeholder="123"
                               class="mt-1 w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent">
                    </div>
                </div>
                <p class="text-xs text-gray-400">Demo card: 4811 1111 1111 1114 · Any future date · Any CVV</p>
            </div>

            {{-- GoPay / QRIS info --}}
            <div x-show="selected === 'gopay' || selected === 'qris'" x-transition class="mb-5">
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-3 text-sm text-blue-700">
                    A QR code will be generated after clicking pay. Scan it with your GoPay / e-wallet app.
                </div>
            </div>

            {{-- Hidden sync for JS --}}
            <input type="hidden" id="selected-method" :value="selected">
            <input type="hidden" id="selected-bank" :value="selectedBank">

            {{-- Pay Button --}}
            <button id="pay-button" @click="
                    if (loading) return;
                    loading = true;
                    handlePay(selected, selectedBank, $el)
                        .then(data => {
                            loading = false;
                            resultData = data;
                            resultMethod = selected;
                            resultModal = true;
                        })
                        .catch(() => { loading = false; });
                "
                :disabled="loading"
                class="w-full mt-2 py-4 bg-gradient-to-r from-green-700 to-green-800 text-white font-bold text-lg rounded-full hover:from-green-800 hover:to-green-900 transition-all duration-300 shadow-xl shadow-green-200/50 hover:shadow-green-300/50 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                <template x-if="!loading">
                    <span class="flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        Confirm & Pay — Rp {{ number_format($transaction->grandTotal(), 0, ',', '.') }}
                    </span>
                </template>
                <template x-if="loading">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Processing...
                    </span>
                </template>
            </button>

            {{-- Result Modal --}}
            <div x-show="resultModal" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl p-6 max-w-sm w-full text-center shadow-2xl">

                    {{-- Bank Transfer Result --}}
                    <template x-if="resultMethod === 'bank_transfer'">
                        <div>
                            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">🏦</span>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 mb-1">Virtual Account Created</h3>
                            <p class="text-xs text-gray-500 mb-4">Complete payment before expiry</p>
                            <div class="bg-gray-50 rounded-xl p-4 mb-4 text-left space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Bank</span>
                                    <span class="font-bold uppercase" x-text="resultData?.va_numbers?.[0]?.bank ?? resultData?.permata_va_number ? 'Permata' : '-'"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">VA Number</span>
                                    <span class="font-mono font-bold text-green-700"
                                          x-text="resultData?.va_numbers?.[0]?.va_number ?? resultData?.permata_va_number ?? '-'"></span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500">Amount</span>
                                    <span class="font-bold">Rp {{ number_format($transaction->grandTotal(), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- GoPay / QRIS Result --}}
                    <template x-if="resultMethod === 'gopay' || resultMethod === 'qris'">
                        <div>
                            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl" x-text="resultMethod === 'gopay' ? '📱' : '📷'"></span>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 mb-1">Scan to Pay</h3>
                            <p class="text-xs text-gray-500 mb-4">Use your e-wallet app to scan</p>
                            <template x-if="resultData?.actions">
                                <img :src="resultData.actions.find(a => a.name === 'generate-qr-code')?.url"
                                     class="w-48 h-48 mx-auto rounded-xl border border-gray-100 mb-4" alt="QR Code">
                            </template>
                        </div>
                    </template>

                    {{-- Credit Card Result --}}
                    <template x-if="resultMethod === 'credit_card'">
                        <div>
                            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <span class="text-3xl">💳</span>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 mb-1">3DS Authentication</h3>
                            <p class="text-xs text-gray-500 mb-4">You'll be redirected to verify your card</p>
                            <template x-if="resultData?.redirect_url">
                                <a :href="resultData.redirect_url" target="_blank"
                                   class="inline-block w-full py-3 bg-green-700 text-white rounded-xl font-semibold text-sm mb-3">
                                    Open 3DS Verification →
                                </a>
                            </template>
                        </div>
                    </template>

                    <button @click="resultModal = false; window.location='{{ route('customer.orders') }}'"
                            class="w-full py-3 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                        Done — View My Orders
                    </button>
                </div>
            </div>
        
        {{-- Cancel Order --}}
        @if($transaction->status === 'pending')
            <div class="mt-4 text-center">
                <form method="POST" action="{{ route('customer.orders.cancel', $transaction->transaction_id) }}"
                    onsubmit="return confirm('Are you sure you want to cancel this order? This cannot be undone.')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 border-2 border-red-300 text-red-600 text-sm font-semibold rounded-full hover:bg-red-50 hover:border-red-400 transition-all duration-200">
                        <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel Order
                    </button>
                </form>
            </div>
        @endif
        

        </div>
            <a href="{{ route('customer.orders') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-green-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                View my orders
            </a>
        </div>

    {{-- Midtrans Core API JS (for card tokenization) --}}
    <script src="https://api.sandbox.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js"
            id="midtrans-script"
            data-environment="sandbox"
            data-client-key="{{ config('midtrans.client_key') }}"></script>

    <script>
        async function handlePay(method, bank, btnEl) {
            const csrfToken = '{{ csrf_token() }}';
            const payUrl = '{{ route('customer.checkout.pay', $transaction->transaction_id) }}';

            if (method === 'credit_card') {
                const cardToken = await getCardToken();
                const res = await fetch(payUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ method, card_token: cardToken }),
                });
                const data = await res.json();
                if (data.status_code >= 400) throw new Error(data.status_message);
                return data;
            }

            const body = { method };
            if (method === 'bank_transfer') body.bank = bank;

            const res = await fetch(payUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(body),
            });
            const data = await res.json();
            if (data.status_code >= 400) throw new Error(data.status_message);
            return data;
        }

        function getCardToken() {
            return new Promise((resolve, reject) => {
                const cardData = {
                    card_number:    document.getElementById('card_number').value.replace(/\s/g, ''),
                    card_exp_month: document.getElementById('card_exp_month').value,
                    card_exp_year:  document.getElementById('card_exp_year').value,
                    card_cvv:       document.getElementById('card_cvv').value,
                };

                if (!cardData.card_number || !cardData.card_exp_month || !cardData.card_exp_year || !cardData.card_cvv) {
                    alert('Please fill in all card details.');
                    return reject(new Error('Missing card details'));
                }

                MidtransNew3ds.getCardToken(cardData, {
                    onSuccess: (res) => resolve(res.token_id),
                    onFailure: (err) => {
                        alert('Card error: ' + err.status_message);
                        reject(new Error(err.status_message));
                    },
                });
            });
        }
    </script>
</x-app-layout>