<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Kang Sayur - Bringing the richness of Indonesian soil directly to your kitchen table. Supporting over 400 local farming families.">

        <title>{{ $title ?? 'Kang Sayur' }} — Kang Sayur</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            * { font-family: 'Inter', sans-serif; }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-cream-100">
            <!-- Navigation -->
            <nav class="bg-white/90 backdrop-blur-lg border-b border-gray-100 sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="{{ route('home') }}" class="flex items-center gap-1">
                                <span class="text-xl font-extrabold text-green-900">Kang Sayur</span>
                            </a>
                            @auth
                                <div class="hidden sm:flex sm:ml-10 sm:space-x-1">
                                    <a href="{{ route('marketplace') }}" class="px-4 py-2 text-sm {{ request()->routeIs('marketplace') ? 'nav-link-active' : 'nav-link' }}">
                                        Marketplace
                                    </a>
                                    @if(auth()->user()->isCustomer())
                                        <a href="{{ route('customer.offers') }}" class="px-4 py-2 text-sm {{ request()->routeIs('customer.offers*') ? 'nav-link-active' : 'nav-link' }}">
                                            Negotiations
                                        </a>
                                        <a href="{{ route('customer.favorites') }}" class="px-4 py-2 text-sm {{ request()->routeIs('customer.favorites') ? 'nav-link-active' : 'nav-link' }}">
                                            Favorites
                                        </a>
                                    @elseif(auth()->user()->isFarmer())
                                        <a href="{{ route('farmer.listings.index') }}" class="px-4 py-2 text-sm {{ request()->routeIs('farmer.listings*') ? 'nav-link-active' : 'nav-link' }}">
                                            My Produce
                                        </a>
                                        <a href="{{ route('farmer.offers.index') }}" class="px-4 py-2 text-sm {{ request()->routeIs('farmer.offers*') ? 'nav-link-active' : 'nav-link' }}">
                                            Offers
                                        </a>
                                        <a href="{{ route('farmer.orders.index') }}" class="px-4 py-2 text-sm {{ request()->routeIs('farmer.orders*') ? 'nav-link-active' : 'nav-link' }}">
                                            Orders
                                        </a>
                                    @endif
                                </div>
                            @endauth
                        </div>

                        <div class="flex items-center gap-2">
                            @auth
                                <!-- Search in nav -->
                                <div class="hidden md:block relative">
                                    <form method="GET" action="{{ route('marketplace') }}">
                                        <div class="relative">
                                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search fresh harvest..."
                                                class="w-52 pl-9 pr-4 py-2 rounded-full bg-gray-50 border border-gray-200 text-sm focus:outline-none focus:border-green-400 focus:ring-1 focus:ring-green-200 transition-all">
                                        </div> 
                                    </form>
                                </div>

                                @if(auth()->user()->isCustomer())
                                    <!-- Cart Icon -->
                                    <a href="{{ route('customer.cart') }}" class="relative p-2 text-gray-500 hover:text-green-700 transition-colors {{ request()->routeIs('customer.cart') ? 'text-green-700' : '' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                                        @php
                                            $cartItemCount = auth()->user()->cart?->items?->count() ?? 0;
                                        @endphp
                                        @if($cartItemCount > 0)
                                            <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse">
                                                {{ $cartItemCount > 9 ? '9+' : $cartItemCount }}
                                            </span>
                                        @endif
                                    </a>
                                @endif

                                <!-- Notification Icon -->
                                <button class="relative p-2 text-gray-500 hover:text-green-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                                </button>

                                <!-- Profile Dropdown -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="flex items-center p-1 rounded-full hover:ring-2 hover:ring-green-200 transition-all duration-200">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white font-semibold text-xs">
                                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                        </div>
                                    </button>
                                    <div x-show="open" @click.away="open = false" x-transition
                                        class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                                        <div class="px-4 py-3 border-b border-gray-100">
                                            <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                                            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->role }}</p>
                                        </div>
                                        @if(auth()->user()->isAdmin())
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors">Admin Dashboard</a>
                                        @elseif(auth()->user()->isFarmer())
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors">Farmer Dashboard</a>
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors">Edit Profile</a>
                                        @else
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors">My Offers</a>
                                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors">My Orders</a>
                                            <a href="{{ route('customer.favorites') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors">Favorites</a>
                                        @endif
                                        <div class="border-t border-gray-100 mt-1 pt-1">
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Log Out</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-green-700 transition-colors">Sign In</a>
                                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-green-800 text-white text-sm font-semibold rounded-full hover:bg-green-900 transition-all duration-200">Get Started</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2" x-data="{ show: true }" x-show="show">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('success') }}
                        <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">&times;</button>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                        {{ session('error') }}
                    </div>
                </div>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-cream-200 border-t border-cream-300 mt-16">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Company -->
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Company</h4>
                            <ul class="space-y-3 text-sm">
                                <li><a href="#" class="text-gray-600 hover:text-green-700 transition-colors">About Our Farmers</a></li>
                                <li><a href="#" class="text-gray-600 hover:text-green-700 transition-colors">Sustainability</a></li>
                                <li><a href="#" class="text-gray-600 hover:text-green-700 transition-colors">Contact Support</a></li>
                            </ul>
                        </div>

                        <!-- Legal -->
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Legal</h4>
                            <ul class="space-y-3 text-sm">
                                <li><a href="#" class="text-gray-600 hover:text-green-700 transition-colors">Privacy Policy</a></li>
                                <li><a href="#" class="text-gray-600 hover:text-green-700 transition-colors">Terms of Service</a></li>
                            </ul>
                        </div>

                        <!-- Brand description -->
                        <div class="md:col-span-2">
                            <h4 class="text-xl font-extrabold text-green-900 mb-3">Kang Sayur</h4>
                            <p class="text-sm text-gray-600 leading-relaxed max-w-sm">
                                Bringing the richness of Indonesian soil directly to your kitchen table. Supporting over 400 local farming families.
                            </p>
                            <div class="flex items-center gap-3 mt-5">
                                <a href="#" class="w-9 h-9 flex items-center justify-center rounded-full bg-green-800 text-white hover:bg-green-900 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                                <a href="#" class="w-9 h-9 flex items-center justify-center rounded-full bg-green-800 text-white hover:bg-green-900 transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zM9 16V8l8 3.993L9 16z"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Alpine.js for interactivity -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </body>
</html>
