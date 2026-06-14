<x-app-layout>
    @php $title = 'Negotiation'; @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <a href="{{ route('farmer.offers.index') }}" class="inline-flex items-center gap-1.5 text-green-700 hover:text-green-800 text-sm font-medium mb-6 group">
            <svg class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Negotiations
        </a>

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Offer Info Sidebar -->
            <div class="lg:col-span-1 space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Offer Details
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wider">Customer</p>
                            <p class="font-semibold text-gray-900 mt-0.5">{{ $offer->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wider">Produce</p>
                            <p class="font-semibold text-gray-900 mt-0.5">{{ $offer->listing->title }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wider">Listed Price</p>
                            <p class="font-semibold text-gray-900 mt-0.5">Rp {{ number_format($offer->listing->price, 0, ',', '.') }}</p>
                        </div>

                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-gray-400 text-xs uppercase tracking-wider">Offered Price</p>
                            <p class="font-bold text-green-700 text-lg mt-0.5">Rp {{ number_format($offer->offered_price, 0, ',', '.') }}</p>
                        </div>
                        @if($offer->counter_price)
                        <div>
                            <p class="text-gray-400 text-xs uppercase tracking-wider">Your Counter</p>
                            <p class="font-bold text-blue-600 text-lg mt-0.5">Rp {{ number_format($offer->counter_price, 0, ',', '.') }}</p>
                        </div>
                        @endif

                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-gray-400 text-xs uppercase tracking-wider">Status</p>
                            @php
                                $statusConfig = [
                                    'pending' => ['bg-amber-50 text-amber-700 border-amber-200', '⏳'],
                                    'countered' => ['bg-blue-50 text-blue-700 border-blue-200', '↩'],
                                    'accepted' => ['bg-green-50 text-green-700 border-green-200', '✓'],
                                    'rejected' => ['bg-red-50 text-red-700 border-red-200', '✕'],
                                ];
                                $sc = $statusConfig[$offer->status] ?? ['bg-gray-50 text-gray-600 border-gray-200', '•'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 mt-1 px-3 py-1.5 rounded-lg text-xs font-semibold border {{ $sc[0] }}">
                                {{ $sc[1] }} {{ ucfirst($offer->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                @if(in_array($offer->status, ['pending', 'countered']))
                <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-3">
                    <form method="POST" action="{{ route('farmer.offers.accept', $offer) }}">
                        @csrf
                        <button class="w-full py-3 bg-green-700 text-white font-bold rounded-xl hover:bg-green-800 transition-all duration-200 flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Accept Offer
                        </button>
                    </form>
                    <form method="POST" action="{{ route('farmer.offers.counter', $offer) }}" class="space-y-2">
                        @csrf
                        <input type="number" name="counter_price" placeholder="Your counter price (Rp)" required min="1"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none text-sm transition-all">
                        <button class="w-full py-2.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition-all duration-200 text-sm">↩ Counter Offer</button>
                    </form>
                    <form method="POST" action="{{ route('farmer.offers.reject', $offer) }}">
                        @csrf
                        <button class="w-full py-2.5 border border-red-200 text-red-500 font-medium rounded-xl hover:bg-red-50 transition-all duration-200 text-sm">✕ Reject</button>
                    </form>
                </div>
                @endif
            </div>

            <!-- Chat Thread -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 relative" style="height: 600px;">
                    <!-- Chat Header -->
                    <div class="absolute top-0 left-0 right-0 px-5 py-4 border-b border-gray-100 flex items-center gap-3 bg-white rounded-t-2xl z-10">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ strtoupper(substr($offer->user->name ?? '', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $offer->user->name ?? 'Customer' }}</h3>
                            <p class="text-xs text-gray-400 truncate">{{ $offer->listing->title }}</p>
                        </div>
                    </div>

                    <!-- Messages (absolute positioned between header and send bar) -->
                    <div class="absolute left-0 right-0 overflow-y-auto px-5 py-4 space-y-4" style="top: 65px; bottom: 65px;" id="chatMessages">
                        @forelse($messages as $msg)
                            @php $isMine = $msg->sender_user_id == auth()->user()->user_id; @endphp
                            <div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[75%]">
                                    <div class="px-4 py-2.5 rounded-2xl {{ $isMine ? 'bg-green-700 text-white rounded-br-md' : 'bg-gray-100 text-gray-800 rounded-bl-md' }}">
                                        <p class="text-sm leading-relaxed">{{ $msg->content }}</p>
                                    </div>
                                    <p class="text-[11px] mt-1 {{ $isMine ? 'text-right text-gray-400' : 'text-gray-400' }}">{{ $msg->created_at->format('H:i') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center h-full text-gray-300">
                                <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                <p class="text-sm font-medium text-gray-400">No messages yet</p>
                                <p class="text-xs text-gray-300 mt-1">Start the conversation below</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Send Bar (pinned to bottom) -->
                    @if(in_array($offer->status, ['pending', 'countered']))
                    <form method="POST" action="{{ route('farmer.offers.message', $offer) }}" class="absolute bottom-0 left-0 right-0 px-4 py-3 border-t border-gray-100 bg-white rounded-b-2xl z-10">
                        @csrf
                        <div class="flex items-center gap-2">
                            <input type="text" name="content" placeholder="Type a message..." required
                                class="flex-1 px-4 py-2.5 rounded-full bg-gray-50 border border-gray-200 focus:border-green-400 focus:ring-2 focus:ring-green-100 focus:bg-white outline-none text-sm transition-all">
                            <button class="w-10 h-10 flex items-center justify-center rounded-full bg-green-700 text-white hover:bg-green-800 transition-all duration-200 flex-shrink-0 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                            </button>
                        </div>
                    </form>
                    @else
                    <div class="absolute bottom-0 left-0 right-0 px-4 py-3 border-t border-gray-100 bg-white rounded-b-2xl text-center z-10">
                        <p class="text-sm text-gray-400">This negotiation is <span class="font-medium">{{ $offer->status }}</span>.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const chat = document.getElementById('chatMessages');
        if (chat) chat.scrollTop = chat.scrollHeight;
    });
    </script>
</x-app-layout>
