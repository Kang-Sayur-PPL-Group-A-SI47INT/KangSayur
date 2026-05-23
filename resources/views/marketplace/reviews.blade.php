<x-app-layout>
    @php $title = 'Reviews — ' . $listing->title; @endphp

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-8">
            <a href="{{ route('home') }}" class="hover:text-green-700 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('marketplace') }}" class="hover:text-green-700 transition-colors">Marketplace</a>
            <span>/</span>
            <a href="{{ route('marketplace.show', $listing->listing_id) }}" class="hover:text-green-700 transition-colors truncate max-w-[160px]">{{ $listing->title }}</a>
            <span>/</span>
            <span class="text-gray-700 font-medium">Reviews</span>
        </nav>

        {{-- Rating Summary Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 sm:p-8 mb-8">
            <div class="flex flex-col sm:flex-row gap-8">
                {{-- Left: Average Score --}}
                <div class="flex flex-col items-center justify-center sm:min-w-[160px]">
                    <span class="text-5xl font-extrabold text-gray-900 leading-none">{{ number_format($averageRating, 1) }}</span>
                    <div class="text-amber-400 text-xl mt-2 tracking-wide">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= round($averageRating))
                                ★
                            @else
                                <span class="text-gray-300">☆</span>
                            @endif
                        @endfor
                    </div>
                    <p class="text-sm text-gray-500 mt-2">{{ $totalCount }} {{ Str::plural('review', $totalCount) }}</p>
                </div>

                {{-- Right: Distribution Bars --}}
                <div class="flex-1 space-y-2.5">
                    @foreach($distribution as $star => $data)
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-600 w-12 text-right flex items-center justify-end gap-1">
                                {{ $star }} <span class="text-amber-400 text-xs">★</span>
                            </span>
                            <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full transition-all duration-500"
                                     style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 w-14 text-right">{{ $data['count'] }} ({{ round($data['percentage']) }}%)</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Current User's Review (highlighted) --}}
        @if($userRating)
            <div class="mb-8">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Your Review</h2>
                <div class="bg-white rounded-2xl border border-gray-100 p-5 border-l-4 border-l-green-500" x-data="{ confirmDelete: false }">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 min-w-0">
                            {{-- Avatar --}}
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($userRating->user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <span class="font-semibold text-gray-900 text-sm">{{ $userRating->user->name }}</span>
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-[10px] font-semibold uppercase tracking-wide">You</span>
                                </div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-amber-400 text-sm">{{ str_repeat('★', $userRating->score) }}{{ str_repeat('☆', 5 - $userRating->score) }}</span>
                                    <span class="text-xs text-gray-400">{{ $userRating->created_at->diffForHumans() }}</span>
                                </div>
                                @if($userRating->comment)
                                    <p class="text-gray-600 text-sm leading-relaxed">{{ $userRating->comment }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Delete Button --}}
                        <div class="flex-shrink-0">
                            <button @click="confirmDelete = true"
                                    x-show="!confirmDelete"
                                    class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                                    title="Delete review">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>

                            {{-- Confirm Delete --}}
                            <div x-show="confirmDelete" x-transition class="flex items-center gap-2">
                                <form method="POST" action="{{ route('ratings.destroy', $userRating->rating_id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-red-500 text-white text-xs font-semibold rounded-lg hover:bg-red-600 transition-colors">
                                        Delete
                                    </button>
                                </form>
                                <button @click="confirmDelete = false"
                                        class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- All Reviews --}}
        <div>
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">
                All Reviews ({{ $totalCount }})
            </h2>

            @if($ratings->count())
                <div class="space-y-4">
                    @foreach($ratings as $rating)
                        <div class="bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-sm transition-shadow duration-200">
                            <div class="flex items-start gap-4">
                                {{-- Avatar --}}
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($rating->user->name, 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                                        <span class="font-semibold text-gray-900 text-sm">{{ $rating->user->name }}</span>
                                        <span class="text-xs text-gray-400">{{ $rating->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-amber-400 text-sm mb-2">
                                        {{ str_repeat('★', $rating->score) }}{{ str_repeat('☆', 5 - $rating->score) }}
                                    </div>
                                    @if($rating->comment)
                                        <p class="text-gray-600 text-sm leading-relaxed">{{ $rating->comment }}</p>
                                    @else
                                        <p class="text-gray-300 text-sm italic">No comment</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $ratings->links() }}
                </div>
            @else
                <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center">
                    <div class="text-5xl mb-4">📝</div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">No reviews yet</h3>
                    <p class="text-gray-400 text-sm">Be the first to share your experience with this product.</p>
                </div>
            @endif
        </div>

        {{-- Back to Product --}}
        <div class="mt-10 text-center">
            <a href="{{ route('marketplace.show', $listing->listing_id) }}"
               class="inline-flex items-center gap-2 text-green-700 hover:text-green-800 font-semibold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to {{ $listing->title }}
            </a>
        </div>

    </div>
</x-app-layout>
