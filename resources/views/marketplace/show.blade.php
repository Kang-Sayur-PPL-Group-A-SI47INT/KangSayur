<x-app-layout>
    @php $title = $listing->title; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <a href="{{ route('marketplace') }}" class="text-green-700 hover:text-green-800 text-sm flex items-center gap-1 mb-6">← Back to Marketplace</a>

        <div class="grid lg:grid-cols-2 gap-10">
            <!-- Image Gallery -->
            <div x-data="{ current: 0 }">
                @php $images = $listing->getImagesArray(); @endphp
                <div class="aspect-square bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl overflow-hidden relative">
                    @if(count($images))
                        @foreach($images as $i => $img)
                            <img x-show="current === {{ $i }}" src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover" x-transition>
                        @endforeach
                    @elseif($listing->image)
                        <img src="{{ asset('storage/' . $listing->image) }}" class="w-full h-full object-cover">
                    @else
                        @php $emojis = ['🍅','🌶️','🥬','🥦','🥕','🌽','🧅','🍆','🥔','🧄']; @endphp
                        <div class="w-full h-full flex items-center justify-center text-8xl">{{ $emojis[($listing->produce_produce_id - 1) % count($emojis)] ?? '🥬' }}</div>
                    @endif
                    @if($listing->created_at->diffInDays(now()) < 1)
                        <span class="absolute top-4 left-4 px-3 py-1.5 bg-green-600 text-white rounded-full text-xs font-bold">🌿 Harvested Today</span>
                    @endif
                </div>
                @if(count($images) > 1)
                <div class="flex gap-2 mt-3">
                    @foreach($images as $i => $img)
                        <button @click="current = {{ $i }}" :class="current === {{ $i }} ? 'border-green-500 ring-2 ring-green-200' : 'border-gray-200'"
                            class="w-16 h-16 rounded-xl border-2 overflow-hidden transition-all">
                            <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Details -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">{{ $listing->produce->category ?? 'Vegetables' }}</span>
                    <span class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-xs">Stock: {{ $listing->quantity }} {{ $listing->unit ?? 'kg' }}</span>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4 font-serif">{{ $listing->title }}</h1>

                <!-- Farmer Info -->
                <div class="flex items-center gap-4 mb-6">
                    <a href="{{ route('farmer.profile.show', $listing->farmer->user_id) }}" class="flex items-center gap-3 hover:text-green-700 transition-colors">
                        <div class="w-11 h-11 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($listing->farmer->name ?? '', 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $listing->farmer->name }}</p>
                            <p class="text-xs text-gray-500">📍 {{ $listing->farmer->city ?? 'Indonesia' }}</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-1 text-amber-500 text-sm">
                        ⭐ {{ number_format($listing->averageRating() ?? 0, 1) }}
                        <span class="text-gray-400">({{ $listing->ratings->count() }})</span>
                    </div>
                </div>

                <div class="text-4xl font-bold text-green-700 mb-6 font-serif">
                    Rp {{ number_format($listing->price, 0, ',', '.') }}<span class="text-lg text-gray-400 font-normal">/{{ $listing->unit ?? 'kg' }}</span>
                </div>

                <!-- Description -->
                <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-600 leading-relaxed text-sm">{{ $listing->content }}</p>
                </div>

                @if(auth()->user()->isCustomer())
                    <!-- Add to Cart -->
                    <form method="POST" action="{{ route('customer.cart.add', $listing) }}" class="flex gap-3 mb-4">
                        @csrf
                        <input type="number" name="quantity" value="1" min="1" max="{{ $listing->quantity }}"
                            class="w-24 px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 text-center font-semibold">
                        <button type="submit" class="flex-1 py-3 bg-green-800 text-white font-bold rounded-full hover:bg-green-900 transition-all shadow-lg shadow-green-200/50">
                            🛒 Add to Cart
                        </button>
                    </form>

                    <!-- Make an Offer (Bargain) -->
                    <form method="POST" action="{{ route('customer.offers.store', $listing) }}" class="flex gap-3 mb-4">
                        @csrf
                        <input type="number" name="offered_price" placeholder="Your price (Rp)" min="1" required
                            class="w-40 px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 text-center font-semibold">
                        <button type="submit" class="flex-1 py-3 bg-blue-600 text-white font-bold rounded-full hover:bg-blue-700 transition-all shadow-lg shadow-blue-200/50">
                            💬 Make an Offer
                        </button>
                    </form>
                @endif




                <!-- Average Rating & Distribution (PBI 27) -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        Ratings & Reviews
                    </h3>

                    @if($totalReviews > 0)
                        <div class="flex flex-col sm:flex-row gap-6 items-start">
                            <!-- Average Score -->
                            <div class="text-center sm:min-w-[120px]">
                                <div class="text-5xl font-bold text-gray-900" style="font-family: Georgia, serif;">{{ $averageRating }}</div>
                                <div class="flex justify-center gap-0.5 mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            <span class="text-amber-400 text-lg">★</span>
                                        @else
                                            <span class="text-gray-300 text-lg">★</span>
                                        @endif
                                    @endfor
                                </div>
                                <p class="text-sm text-gray-500 mt-1">{{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}</p>
                            </div>

                            <!-- Distribution Bars -->
                            <div class="flex-1 w-full space-y-1.5">
                                @for($i = 5; $i >= 1; $i--)
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-semibold text-gray-500 w-4 text-right">{{ $i }}</span>
                                        <span class="text-amber-400 text-xs">★</span>
                                        <div class="flex-1 bg-gray-100 rounded-full h-2.5 overflow-hidden">
                                            <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-full rounded-full transition-all duration-500"
                                                 style="width: {{ $distribution[$i]['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-400 w-12 text-right">{{ $distribution[$i]['count'] }} ({{ $distribution[$i]['percentage'] }}%)</span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <div class="text-4xl mb-2">🌱</div>
                            <p class="text-gray-500 text-sm">No reviews yet. Be the first to share your experience!</p>
                        </div>
                    @endif
                </div>

                <!-- Reviews List (PBI 26) -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900">Customer Reviews</h3>
                        @if($totalReviews > 3)
                            <a href="{{ route('marketplace.reviews', $listing->listing_id) }}" class="text-sm text-green-700 hover:text-green-800 font-medium transition-colors">
                                View all {{ $totalReviews }} reviews →
                            </a>
                        @endif
                    </div>

                    <div class="space-y-3">
                        {{-- Current user's review first (highlighted) --}}
                        @if($userRating)
                            <div class="bg-green-50 rounded-xl p-4 border-l-4 border-green-500" id="my-review">
                                <div class="flex items-start justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($userRating->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900 text-sm">{{ $userRating->user->name }}</span>
                                            <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Your Review</span>
                                        </div>
                                    </div>
                                    <form action="{{ route('ratings.destroy', $userRating->rating_id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete your review?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" dusk="delete-review-btn" class="text-red-400 hover:text-red-600 transition-colors p-1" title="Delete review">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <div class="text-amber-400 text-sm ml-10">{{ str_repeat('★', $userRating->score) }}{{ str_repeat('☆', 5 - $userRating->score) }}</div>
                                <p class="text-xs text-gray-400 mt-0.5 ml-10">{{ $userRating->created_at->diffForHumans() }}</p>
                                @if($userRating->comment)
                                    <p class="text-gray-600 text-sm mt-2 ml-10">{{ $userRating->comment }}</p>
                                @endif
                            </div>
                        @endif

                        {{-- Other reviews --}}
                        @forelse($listing->ratings->where('user_user_id', '!=', auth()->user()->user_id ?? 0)->take(3) as $rating)
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($rating->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-900 text-sm">{{ $rating->user->name }}</span>
                                        <div class="text-amber-400 text-xs">{{ str_repeat('★', $rating->score) }}{{ str_repeat('☆', 5 - $rating->score) }}</div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5 ml-10">{{ $rating->created_at->diffForHumans() }}</p>
                                @if($rating->comment)
                                    <p class="text-gray-600 text-sm mt-1 ml-10">{{ $rating->comment }}</p>
                                @endif
                            </div>
                        @empty
                            @if(!$userRating)
                                <p class="text-gray-400 text-sm">No reviews yet. Be the first!</p>
                            @endif
                        @endforelse

                        @if($totalReviews > 3)
                            <a href="{{ route('marketplace.reviews', $listing->listing_id) }}"
                               class="block text-center py-3 bg-gray-50 hover:bg-gray-100 rounded-xl text-sm font-medium text-green-700 transition-colors">
                                See All Reviews ({{ $totalReviews }})
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Related -->
        @if($relatedListings->count())
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Similar Produce</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($relatedListings as $related)
                    <a href="{{ route('marketplace.show', $related) }}" class="group bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <div class="aspect-video bg-gradient-to-br from-green-50 to-emerald-50 flex items-center justify-center text-4xl overflow-hidden">
                            @php $relImages = $related->getImagesArray(); @endphp
                            @if(count($relImages))
                                <img src="{{ asset('storage/' . $relImages[0]) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                🥬
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 text-sm group-hover:text-green-700">{{ $related->title }}</h3>
                            <p class="text-green-700 font-bold mt-1">Rp {{ number_format($related->price, 0, ',', '.') }}/{{ $related->unit ?? 'kg' }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>