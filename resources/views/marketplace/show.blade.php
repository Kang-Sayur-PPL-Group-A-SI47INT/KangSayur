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

                <div class="mb-6">
                    @if($listing->hasDiscount())
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="text-xl text-gray-400 line-through font-serif">Rp {{ number_format($listing->price, 0, ',', '.') }}</span>
                            <span class="text-4xl font-bold text-green-700 font-serif">Rp {{ number_format($listing->effectivePrice(), 0, ',', '.') }}</span>
                            <span class="text-lg text-gray-400 font-normal">/{{ $listing->unit ?? 'kg' }}</span>
                        </div>
                        <div class="mt-2">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-bold bg-amber-100 text-amber-700 shadow-sm">
                                🏷️ {{ $listing->formattedDiscount() }} — Auto-discount from surplus harvest
                            </span>
                        </div>
                    @else
                        <div class="text-4xl font-bold text-green-700 font-serif">
                            Rp {{ number_format($listing->price, 0, ',', '.') }}<span class="text-lg text-gray-400 font-normal">/{{ $listing->unit ?? 'kg' }}</span>
                        </div>
                    @endif
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

                    <div class="flex gap-3 mb-6">
                        <form method="POST" action="{{ route('customer.favorites.toggle', $listing) }}" class="flex-1">
                            @csrf
                            <button class="w-full py-3 border-2 {{ $isWishlisted ? 'border-red-300 bg-red-50 text-red-600' : 'border-gray-200 text-gray-600 hover:border-green-300' }} rounded-full font-semibold transition-all">
                                {{ $isWishlisted ? '❤️ Saved' : '🤍 Save to Wishlist' }}
                            </button>
                        </form>
                    </div>

                    <!-- Make Offer -->
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6 mb-8" x-data="{ showOffer: false }">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-900">💰 Make an Offer</h3>
                                <p class="text-sm text-gray-500">Negotiate a better price with the farmer</p>
                            </div>
                            <button @click="showOffer = !showOffer" class="px-4 py-2 bg-amber-100 text-amber-700 rounded-full text-sm font-medium hover:bg-amber-200">Bargain</button>
                        </div>
                        <form method="POST" action="{{ route('customer.offers.store', $listing) }}" x-show="showOffer" x-transition class="mt-4 flex gap-3">
                            @csrf
                            <input type="number" name="offered_price" placeholder="Your offer (Rp)" required min="1"
                                class="flex-1 px-4 py-3 rounded-xl border border-amber-200 focus:border-amber-400 outline-none text-sm">
                            <button type="submit" class="px-6 py-3 bg-amber-500 text-white font-semibold rounded-xl hover:bg-amber-600">Send Offer</button>
                        </form>
                    </div>
                @endif

                <!-- Rating Section -->
                @if(auth()->user()->isCustomer())
                <div class="bg-white border border-gray-100 rounded-2xl p-6 mb-8">
                    <h3 class="font-semibold text-gray-900 mb-4">⭐ Rate This Produce</h3>
                    @if($deliveredTransaction && !$userRating)
                        <form method="POST" action="{{ route('ratings.store') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="listing_listing_id" value="{{ $listing->listing_id }}">
                            <input type="hidden" name="transaction_transaction_id" value="{{ $deliveredTransaction->transaction_id }}">
                            <div class="flex gap-2" x-data="{ rating: 0 }">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" @click="rating = {{ $i }}" class="text-3xl transition-transform hover:scale-125" :class="rating >= {{ $i }} ? 'text-amber-400' : 'text-gray-300'">★</button>
                                @endfor
                                <input type="hidden" name="score" x-bind:value="rating">
                            </div>
                            <textarea name="comment" rows="2" placeholder="Share your experience..." class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 outline-none text-sm"></textarea>
                            <button type="submit" class="px-6 py-2 bg-green-700 text-white rounded-full text-sm font-medium hover:bg-green-800">Submit Review</button>
                        </form>
                    @elseif($userRating)
                        <div class="flex items-center gap-2 text-amber-400 text-2xl mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                {{ $i <= $userRating->score ? '★' : '☆' }}
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600">{{ $userRating->comment ?? 'No comment.' }}</p>
                        <p class="text-xs text-gray-400 mt-1">Your review submitted {{ $userRating->created_at->diffForHumans() }}</p>
                    @else
                        <p class="text-sm text-gray-400">You can only rate products you have purchased and received.</p>
                    @endif
                </div>
                @endif


                <!-- Ratings & Reviews -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900">Ratings & Reviews</h3>
                        @if($totalReviews > 3)
                            <a href="{{ route('marketplace.reviews', $listing->listing_id) }}" class="text-sm text-green-700 hover:text-green-800 font-medium transition-colors">
                                View all {{ $totalReviews }} reviews →
                            </a>
                        @endif
                    </div>

                    {{-- Rating Summary --}}
                    @if($totalReviews > 0)
                    <div class="bg-gray-50 rounded-2xl p-5 mb-5">
                        <div class="flex flex-col sm:flex-row gap-6">
                            {{-- Average Score --}}
                            <div class="flex flex-col items-center justify-center sm:min-w-[120px]">
                                <span class="text-4xl font-extrabold text-gray-900 leading-none">{{ number_format($averageRating, 1) }}</span>
                                <div class="text-amber-400 text-lg mt-1.5 tracking-wide">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($averageRating))
                                            ★
                                        @else
                                            <span class="text-gray-300">☆</span>
                                        @endif
                                    @endfor
                                </div>
                                <p class="text-sm text-gray-500 mt-1.5">{{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}</p>
                            </div>

                            {{-- Distribution Bars --}}
                            <div class="flex-1 space-y-2">
                                @foreach($distribution as $star => $data)
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xs font-medium text-gray-600 w-8 text-right flex items-center justify-end gap-0.5">
                                            {{ $star }} <span class="text-amber-400 text-[10px]">★</span>
                                        </span>
                                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full transition-all duration-500"
                                                 style="width: {{ $data['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-[10px] text-gray-400 w-10 text-right">{{ $data['count'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="space-y-3">
                        {{-- Current user's review first (highlighted) --}}
                        @if($userRating)
                            <div class="bg-green-50 rounded-xl p-4 border-l-4 border-green-500" id="my-review"
                                 x-data="{
                                     editing: false,
                                     editScore: {{ $userRating->score }},
                                     hoveredScore: 0,
                                     editComment: @js($userRating->comment ?? '')
                                 }">

                                {{-- View Mode --}}
                                <div x-show="!editing">
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
                                        <div class="flex items-center gap-1">
                                            {{-- Edit Button --}}
                                            <button @click="editing = true"
                                                    dusk="edit-review-btn"
                                                    class="text-gray-400 hover:text-green-600 transition-colors p-1" title="Edit review">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                                </svg>
                                            </button>
                                            {{-- Delete Button --}}
                                            <form action="{{ route('ratings.destroy', $userRating->rating_id) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete your review?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" dusk="delete-review-btn" class="text-gray-400 hover:text-red-600 transition-colors p-1" title="Delete review">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="text-amber-400 text-sm ml-10">{{ str_repeat('★', $userRating->score) }}{{ str_repeat('☆', 5 - $userRating->score) }}</div>
                                    <p class="text-xs text-gray-400 mt-0.5 ml-10">{{ $userRating->created_at->diffForHumans() }}</p>
                                    @if($userRating->comment)
                                        <p class="text-gray-600 text-sm mt-2 ml-10">{{ $userRating->comment }}</p>
                                    @endif
                                </div>

                                {{-- Edit Mode --}}
                                <div x-show="editing" x-transition>
                                    <div class="flex items-center gap-2 mb-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($userRating->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900 text-sm">{{ $userRating->user->name }}</span>
                                            <span class="ml-2 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">Editing</span>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('ratings.update', $userRating->rating_id) }}" class="space-y-3 ml-10">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="score" x-model="editScore">

                                        {{-- Star Selector --}}
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Edit Rating</label>
                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button"
                                                            @click="editScore = {{ $i }}"
                                                            @mouseenter="hoveredScore = {{ $i }}"
                                                            @mouseleave="hoveredScore = 0"
                                                            class="text-2xl transition-all duration-150 transform hover:scale-110 focus:outline-none"
                                                            :class="(hoveredScore >= {{ $i }} || editScore >= {{ $i }}) ? 'text-amber-400' : 'text-gray-300'">
                                                        ★
                                                    </button>
                                                @endfor
                                                <span class="ml-2 text-sm font-semibold text-amber-600"
                                                      x-text="editScore + '/5'"></span>
                                            </div>
                                        </div>

                                        {{-- Comment --}}
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Edit Review <span class="text-gray-400 font-normal normal-case">(optional)</span></label>
                                            <textarea name="comment" rows="3" maxlength="1000"
                                                      x-model="editComment"
                                                      placeholder="Share your experience..."
                                                      class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:border-green-500 focus:ring-1 focus:ring-green-200 resize-none transition-all bg-white"></textarea>
                                        </div>

                                        {{-- Action Buttons --}}
                                        <div class="flex items-center gap-2">
                                            <button type="submit"
                                                    x-bind:disabled="editScore === 0"
                                                    class="flex-1 py-2.5 bg-green-800 text-white text-sm font-bold rounded-xl hover:bg-green-900 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                                                Update Review
                                            </button>
                                            <button type="button"
                                                    @click="editing = false; editScore = {{ $userRating->score }}; editComment = @js($userRating->comment ?? '')"
                                                    class="py-2.5 px-4 bg-gray-100 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-200 transition-all">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
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
                            @if($related->hasDiscount())
                                <p class="text-xs text-gray-400 line-through mt-1">Rp {{ number_format($related->price, 0, ',', '.') }}/{{ $related->unit ?? 'kg' }}</p>
                                <p class="text-green-700 font-bold">Rp {{ number_format($related->effectivePrice(), 0, ',', '.') }}/{{ $related->unit ?? 'kg' }}
                                    <span class="text-[10px] font-bold bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full ml-1">{{ $related->formattedDiscount() }}</span>
                                </p>
                            @else
                                <p class="text-green-700 font-bold mt-1">Rp {{ number_format($related->price, 0, ',', '.') }}/{{ $related->unit ?? 'kg' }}</p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</x-app-layout>
