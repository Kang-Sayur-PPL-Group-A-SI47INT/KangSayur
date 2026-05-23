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

                {{-- Rate & Review Section (PBI 25 + 28) — only for delivered orders --}}
                @if($transaction->status === 'delivered')
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-50/50 to-yellow-50/50">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                Rate Your Order
                            </h3>
                            <p class="text-xs text-gray-500 mt-1">Share your experience with the products you purchased</p>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($transaction->items as $item)
                                @if($item->listing)
                                    @php
                                        $existingRating = $existingRatings->get($item->listing_listing_id);
                                    @endphp
                                    <div class="p-5">
                                        {{-- Product info --}}
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-xl overflow-hidden flex-shrink-0">
                                                @php $imgs = $item->listing->getImagesArray(); @endphp
                                                @if(count($imgs))
                                                    <img src="{{ asset('storage/' . $imgs[0]) }}" class="w-full h-full object-cover rounded-xl" alt="">
                                                @else
                                                    @php $emojis = ['🥬','🥕','🍅','🌽','🥦','🍆','🥒','🫑']; @endphp
                                                    {{ $emojis[$loop->index % count($emojis)] }}
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900 text-sm">{{ $item->listing->title }}</h4>
                                                <p class="text-xs text-gray-500">{{ $item->listing->farmer->name ?? 'Local Farmer' }}</p>
                                            </div>
                                        </div>

                                        @if($existingRating)
                                            {{-- Show existing review with delete option (PBI 28) --}}
                                            <div class="bg-green-50 rounded-xl p-4 border-l-4 border-green-500">
                                                <div class="flex items-start justify-between">
                                                    <div>
                                                        <div class="text-amber-400 text-sm">
                                                            {{ str_repeat('★', $existingRating->score) }}{{ str_repeat('☆', 5 - $existingRating->score) }}
                                                        </div>
                                                        @if($existingRating->comment)
                                                            <p class="text-gray-600 text-sm mt-2">{{ $existingRating->comment }}</p>
                                                        @endif
                                                        <p class="text-xs text-gray-400 mt-1">Reviewed {{ $existingRating->created_at->diffForHumans() }}</p>
                                                    </div>
                                                    <form action="{{ route('ratings.destroy', $existingRating->rating_id) }}" method="POST"
                                                          onsubmit="return confirm('Are you sure you want to delete this review?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" dusk="delete-review-{{ $item->listing_listing_id }}"
                                                                class="text-xs text-red-500 hover:text-red-700 font-medium flex items-center gap-1 transition-colors">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Review form (PBI 25) --}}
                                            <form method="POST" action="{{ route('ratings.store') }}"
                                                  x-data="{ score: 0, hoveredScore: 0 }"
                                                  class="space-y-3"
                                                  dusk="review-form-{{ $item->listing_listing_id }}">
                                                @csrf
                                                <input type="hidden" name="listing_listing_id" value="{{ $item->listing_listing_id }}">
                                                <input type="hidden" name="transaction_transaction_id" value="{{ $transaction->transaction_id }}">
                                                <input type="hidden" name="score" x-model="score">

                                                {{-- Star Selector --}}
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Your Rating</label>
                                                    <div class="flex items-center gap-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <button type="button"
                                                                    @click="score = {{ $i }}"
                                                                    @mouseenter="hoveredScore = {{ $i }}"
                                                                    @mouseleave="hoveredScore = 0"
                                                                    class="text-3xl transition-all duration-150 transform hover:scale-110 focus:outline-none"
                                                                    :class="(hoveredScore >= {{ $i }} || score >= {{ $i }}) ? 'text-amber-400' : 'text-gray-300'"
                                                                    dusk="star-{{ $i }}-{{ $item->listing_listing_id }}">
                                                                ★
                                                            </button>
                                                        @endfor
                                                        <span class="ml-2 text-sm text-gray-500"
                                                              x-text="score > 0 ? score + '/5' : 'Tap to rate'"
                                                              x-bind:class="score > 0 ? 'text-amber-600 font-semibold' : 'text-gray-400'"></span>
                                                    </div>
                                                </div>

                                                {{-- Comment --}}
                                                <div>
                                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Your Review <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                                                    <textarea name="comment" rows="3" maxlength="1000"
                                                              placeholder="Share your experience with this product..."
                                                              class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-200 resize-none transition-all"></textarea>
                                                </div>

                                                {{-- Submit --}}
                                                <button type="submit"
                                                        x-bind:disabled="score === 0"
                                                        class="w-full py-2.5 bg-green-800 text-white text-sm font-bold rounded-xl hover:bg-green-900 transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                                                        dusk="submit-review-{{ $item->listing_listing_id }}">
                                                    Submit Review
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
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
