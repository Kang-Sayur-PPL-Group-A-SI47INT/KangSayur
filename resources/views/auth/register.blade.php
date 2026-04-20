<x-guest-layout>
    <div class="flex min-h-screen">
        {{-- LEFT PANEL --}}
        <div class="hidden lg:flex lg:w-1/2 auth-left-panel">
            {{-- Logo --}}
            <div>
                <a href="/" class="text-2xl font-bold italic font-serif text-green-800">
                    Kang Sayur
                </a>
            </div>

            {{-- Tagline --}}
            <div class="flex-1 flex flex-col justify-center -mt-16">
                <h1 class="text-5xl font-bold font-serif text-gray-900 leading-tight">
                    Fresh from<br>
                    earth to your<br>
                    kitchen table.
                </h1>
                <p class="mt-6 text-gray-600 text-lg max-w-md leading-relaxed">
                    Join thousands of families who trust local farmers for their daily fresh produce. Quality you can taste, prices you'll love.
                </p>
            </div>

            {{-- Bottom illustration area with testimonial --}}
            <div class="relative mt-8">
                {{-- Decorative vegetable illustration --}}
                <div class="relative w-full h-48 rounded-2xl bg-gradient-to-br from-green-100/60 to-cream-200/40 flex items-end justify-center overflow-hidden">
                    <div class="text-8xl mb-4 opacity-80">🥬🥕🍅</div>
                </div>

                {{-- Testimonial card overlay --}}
                <div class="auth-testimonial-card absolute -top-4 right-4 max-w-xs">
                    {{-- Stars --}}
                    <div class="flex gap-1 mb-2">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4 text-amber-400 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-sm italic text-gray-700 mb-3">
                        "The freshest vegetables I've ever ordered online. Delivered straight from the farm!"
                    </p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-semibold text-sm">
                            SN
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">Siti Nurbaya</p>
                            <p class="text-xs text-gray-500">Home Chef</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="w-full lg:w-1/2 bg-white flex items-center justify-center px-6 py-12 lg:px-16">
            <div class="w-full max-w-md">
                {{-- Mobile logo --}}
                <div class="lg:hidden mb-8">
                    <a href="/" class="text-2xl font-bold italic font-serif text-green-800">
                        Kang Sayur
                    </a>
                </div>

                {{-- Title --}}
                <h2 class="text-3xl font-bold text-gray-900">Create an account</h2>
                <p class="mt-2 text-gray-500">Start your journey into the greenhouse community.</p>

                {{-- Form --}}
                <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                    @csrf

                    {{-- Role Selector --}}
                    <div x-data="{ role: '{{ old('role', 'customer') }}' }">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                            I am a
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Customer Card --}}
                            <div class="role-card" :class="{ 'active': role === 'customer' }" @click="role = 'customer'">
                                <input type="radio" name="role" value="customer" x-model="role" class="hidden">
                                <svg class="w-5 h-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Customer</span>
                            </div>

                            {{-- Farmer Card --}}
                            <div class="role-card" :class="{ 'active': role === 'farmer' }" @click="role = 'farmer'">
                                <input type="radio" name="role" value="farmer" x-model="role" class="hidden">
                                <svg class="w-5 h-5 text-green-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Farmer</span>
                            </div>
                        </div>
                        @error('role')
                            <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Full Name --}}
                    <div>
                        <label for="name" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Full Name
                        </label>
                        <div class="relative">
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter your full name"
                                class="auth-input"
                                required
                                autofocus
                            >
                            <div class="auth-input-icon">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                        </div>
                        @error('name')
                            <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Email Address
                        </label>
                        <div class="relative">
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                class="auth-input"
                                required
                            >
                            <div class="auth-input-icon">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                        </div>
                        @error('email')
                            <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Min. 8 characters"
                                class="auth-input"
                                required
                            >
                            <div class="auth-input-icon">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                        </div>
                        @error('password')
                            <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Confirm Password
                        </label>
                        <div class="relative">
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                placeholder="Re-enter your password"
                                class="auth-input"
                                required
                            >
                            <div class="auth-input-icon">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                                </svg>
                            </div>
                        </div>
                        @error('password_confirmation')
                            <span class="text-sm text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="pt-2">
                        <button type="submit" class="btn-primary-green">
                            Join the Community
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </button>
                    </div>
                </form>

                {{-- Divider --}}
                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-widest">Or Continue With</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                {{-- Social Login --}}
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" class="btn-social">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Google
                    </button>
                    <button type="button" class="btn-social">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.8-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                        </svg>
                        Apple
                    </button>
                </div>

                {{-- Login Link --}}
                <p class="text-center text-sm text-gray-500 mt-8">
                    Already part of the harvest?
                    <a href="{{ route('login') }}" class="text-green-700 font-semibold hover:text-green-800 transition-colors">
                        Log In
                    </a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
