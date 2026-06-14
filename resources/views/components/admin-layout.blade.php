<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Kang Sayur Admin Panel - Manage users, listings, verifications and transactions.">
        <title>{{ $title ?? 'Admin' }} — Kang Sayur Admin</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>* { font-family: 'Inter', sans-serif; }</style>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-cream-100">
            <!-- Navigation (matching app-layout) -->
            <nav class="bg-white/90 backdrop-blur-lg border-b border-gray-100 sticky top-0 z-50">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                                <span class="text-xl font-extrabold text-green-900">Kang Sayur</span>
                                <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] font-bold uppercase tracking-wider rounded-full">Admin</span>
                            </a>
                            <div class="hidden sm:flex sm:ml-10 sm:space-x-1">
                                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-sm {{ request()->routeIs('admin.dashboard') ? 'nav-link-active' : 'nav-link' }}">
                                    Dashboard
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-sm {{ request()->routeIs('admin.users.*') ? 'nav-link-active' : 'nav-link' }}">
                                    Pengguna
                                </a>
                                <a href="{{ route('admin.listings.index') }}" class="px-4 py-2 text-sm {{ request()->routeIs('admin.listings.*') ? 'nav-link-active' : 'nav-link' }}">
                                    Listing
                                </a>
                                <a href="{{ route('admin.verifications.index') }}" class="relative px-4 py-2 text-sm {{ request()->routeIs('admin.verifications.*') ? 'nav-link-active' : 'nav-link' }}">
                                    Verifikasi
                                    @php $pendingCount = \App\Models\User::where('role','farmer')->where('verification_status','pending')->count(); @endphp
                                    @if($pendingCount > 0)
                                        <span class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-pulse">{{ $pendingCount }}</span>
                                    @endif
                                </a>
                                <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 text-sm {{ request()->routeIs('admin.transactions.*') ? 'nav-link-active' : 'nav-link' }}">
                                    Order
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Marketplace link -->
                            <a href="{{ route('marketplace') }}" class="hidden md:inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-green-700 transition-colors border border-gray-200 rounded-full hover:border-green-200">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                Marketplace
                            </a>

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
                                    <a href="{{ route('marketplace') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-green-50 transition-colors">Marketplace</a>
                                    <div class="border-t border-gray-100 mt-1 pt-1">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">Keluar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile nav -->
                <div class="sm:hidden border-t border-gray-100 px-4 py-2 flex gap-1 overflow-x-auto">
                    <a href="{{ route('admin.dashboard') }}" class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-full {{ request()->routeIs('admin.dashboard') ? 'bg-green-100 text-green-800' : 'text-gray-500' }}">Dashboard</a>
                    <a href="{{ route('admin.users.index') }}" class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-full {{ request()->routeIs('admin.users.*') ? 'bg-green-100 text-green-800' : 'text-gray-500' }}">Pengguna</a>
                    <a href="{{ route('admin.listings.index') }}" class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-full {{ request()->routeIs('admin.listings.*') ? 'bg-green-100 text-green-800' : 'text-gray-500' }}">Listing</a>
                    <a href="{{ route('admin.verifications.index') }}" class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-full {{ request()->routeIs('admin.verifications.*') ? 'bg-green-100 text-green-800' : 'text-gray-500' }}">Verifikasi</a>
                    <a href="{{ route('admin.transactions.index') }}" class="flex-shrink-0 px-3 py-1.5 text-xs font-medium rounded-full {{ request()->routeIs('admin.transactions.*') ? 'bg-green-100 text-green-800' : 'text-gray-500' }}">Order</a>
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
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Admin</h4>
                            <ul class="space-y-3 text-sm">
                                <li><a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-green-700 transition-colors">Dashboard</a></li>
                                <li><a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-green-700 transition-colors">Pengguna</a></li>
                                <li><a href="{{ route('admin.verifications.index') }}" class="text-gray-600 hover:text-green-700 transition-colors">Verifikasi</a></li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Legal</h4>
                            <ul class="space-y-3 text-sm">
                                <li><a href="#" class="text-gray-600 hover:text-green-700 transition-colors">Privacy Policy</a></li>
                                <li><a href="#" class="text-gray-600 hover:text-green-700 transition-colors">Terms of Service</a></li>
                            </ul>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-xl font-extrabold text-green-900 mb-3">Kang Sayur</h4>
                            <p class="text-sm text-gray-600 leading-relaxed max-w-sm">
                                Bringing the richness of Indonesian soil directly to your kitchen table. Supporting over 400 local farming families.
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

        <!-- Alpine.js for interactivity -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </body>
</html>
