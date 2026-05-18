<x-app-layout>
    @php $title = 'Home'; @endphp

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
            <div class="relative bg-gradient-to-br from-green-50 via-cream-100 to-cream-200 rounded-3xl overflow-hidden">
                <div class="grid lg:grid-cols-2 gap-8 items-center">
                    <div class="p-8 lg:p-14">
                        <div class="inline-flex items-center gap-2 bg-green-100 px-4 py-1.5 rounded-full text-xs font-semibold text-green-800 mb-6">
                            <span class="w-1.5 h-1.5 bg-green-600 rounded-full animate-pulse"></span>
                            #1 Fresh Produce Platform in Indonesia
                        </div>
                        <h1 class="text-4xl lg:text-6xl font-extrabold text-gray-900 leading-tight mb-6 font-serif">
                            Direct From<br>
                            The Farmer's<br>
                            <span class="text-green-700">Hands.</span>
                        </h1>
                        <p class="text-gray-600 text-lg max-w-md leading-relaxed mb-8">
                            Connecting you directly to Indonesian local farmers. Fresher produce, fairer prices, and a more sustainable community.
                        </p>
                        <div class="flex flex-wrap gap-3">
                            <a href="#" class="px-8 py-4 bg-green-800 text-white font-bold rounded-full hover:bg-green-900 transition-all duration-300 shadow-lg shadow-green-200/50">
                                Start Shopping 
                            </a>
                            <a href="#" class="px-8 py-4 bg-white text-green-800 font-semibold rounded-full border border-green-200 hover:bg-green-50 transition-all duration-300">
                                Sell Your Harvest
                            </a>
                        </div>
                        <div class="mt-10 flex items-center gap-8 text-sm text-gray-500">
                            <div><span class="text-2xl font-bold text-gray-900 block">500+</span> Farmers</div>
                            <div><span class="text-2xl font-bold text-gray-900 block">10K+</span> Products</div>
                            <div><span class="text-2xl font-bold text-gray-900 block">50K+</span> Customers</div>
                        </div>
                    </div>
                    <div class="hidden lg:block">
                        <img src="{{ asset('images/farmer-hero.jpg') }}" alt="Farmer in field" class="w-full h-96 object-cover rounded-br-3xl">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 font-serif">Why Choose <span class="text-green-700">Kang Sayur</span>?</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">A marketplace connecting farmers directly to your kitchen table.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="group p-8 rounded-2xl bg-white border border-gray-100 hover:shadow-xl hover:shadow-green-100/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">🌱</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Direct From Farmers</h3>
                    <p class="text-gray-500 leading-relaxed">No middlemen. Produce shipped directly from the farm to your doorstep at fair prices.</p>
                </div>
                <div class="group p-8 rounded-2xl bg-white border border-gray-100 hover:shadow-xl hover:shadow-amber-100/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-amber-100 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">🌾</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Farm Fresh Quality</h3>
                    <p class="text-gray-500 leading-relaxed">Browse verified produce with detailed listings, farmer profiles, and community reviews you can trust.</p>
                </div>
                <div class="group p-8 rounded-2xl bg-white border border-gray-100 hover:shadow-xl hover:shadow-blue-100/50 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center text-2xl mb-6 group-hover:scale-110 transition-transform duration-300">🔒</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Secure Payment</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Listings -->
    @if(isset($featuredListings) && $featuredListings->count())
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Picked This Morning</p>
                    <h2 class="text-3xl font-bold text-gray-900">Featured <span class="text-green-700">Produce</span></h2>
                </div>
                <a href="{#" class="hidden sm:inline-flex items-center gap-2 text-sm font-semibold text-green-700 hover:text-green-800 transition-colors">
                    View All
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($featuredListings as $listing)
                <a href="#" class="product-card group">
                    <div class="aspect-square bg-cream-100 relative overflow-hidden">
                        @if($listing->image)
                            <img src="#" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            @php
                                $emojis = ['🍅', '🌶️', '🥬', '🥦', '🥕', '🌽', '🧅', '🍆', '🥔', '🧄'];
                                $emoji = $emojis[$listing->produce_produce_id % count($emojis)] ?? '🥬';
                            @endphp
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-green-50 to-emerald-50">
                                <span class="text-7xl group-hover:scale-110 transition-transform duration-500">{{ $emoji }}</span>
                            </div>
                        @endif
                        <span class="absolute top-3 right-3 px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-semibold text-green-700">{{ $listing->produce->category ?? 'Vegetables' }}</span>
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-gray-900 text-sm mb-0.5 group-hover:text-green-700 transition-colors">{{ $listing->title }}</h3>
                        <p class="text-xs text-gray-400 mb-2">{{ $listing->farmer->name ?? 'Farmer' }} · {{ $listing->farmer->city ?? '' }}</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-green-800">Rp {{ number_format($listing->price, 0, ',', '.') }} <span class="text-xs text-gray-400 font-normal">/kg</span></span>
                            <div class="flex items-center gap-0.5 text-xs">
                                <svg class="w-3.5 h-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <span class="font-semibold text-gray-600">{{ number_format($listing->averageRating() ?? 0, 1) }}</span>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- CTA Section -->
    <section class="py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden bg-green-900 rounded-3xl p-12 text-center">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg%20width%3D%2240%22%20height%3D%2240%22%20viewBox%3D%220%200%2040%2040%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cg%20fill%3D%22%23ffffff%22%20fill-opacity%3D%220.3%22%20fill-rule%3D%22evenodd%22%3E%3Cpath%20d%3D%22M0%2038.59l2.83-2.83%201.41%201.41L1.41%2040H0v-1.41zM0%201.4l2.83%202.83%201.41-1.41L1.41%200H0v1.41zM38.59%2040l-2.83-2.83%201.41-1.41L40%2038.59V40h-1.41zM40%201.41l-2.83%202.83-1.41-1.41L38.59%200H40v1.41zM20%2018.6l2.83-2.83%201.41%201.41L21.41%2020l2.83%202.83-1.41%201.41L20%2021.4l-2.83%202.83-1.41-1.41L18.59%2020l-2.83-2.83%201.41-1.41L20%2018.59z%22%2F%3E%3C%2Fg%3E%3C%2Fsvg%3E')]"></div>
                </div>
                <div class="relative">
                    <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4 font-serif">Ready to Get Started?</h2>
                    <p class="text-green-200 mb-8 max-w-lg mx-auto">Join local farmers and and enjoy the benefits of Kang Sayur.</p>
                    <a href="#" class="inline-flex px-8 py-4 bg-white text-green-800 font-bold rounded-full hover:bg-green-50 transition-all duration-300 shadow-xl">
                        Sign Up Now! 
                    </a>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

