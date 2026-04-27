<x-guest-layout>
    @php $title = 'Login'; @endphp

    <div class="min-h-screen flex" x-data="{ role: '{{ old('role', 'customer') }}' }">
        <!-- Left Panel - Branding -->
        <div class="hidden lg:flex lg:w-1/2 auth-left-panel p-12">
            <div class="relative z-10 flex flex-col h-full">
                <!-- Logo -->
                <div class="mb-auto">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-green-800 italic font-serif">Kang Sayur</a>
                </div>

                <!-- Tagline -->
                <div class="mb-16">
                    <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-6 font-serif">
                        Fresh from<br>
                        <span class="text-green-700">earth to your</span><br>
                        kitchen table.
                    </h1>
                    <p class="text-gray-600 text-lg max-w-md leading-relaxed">
                        Join our organic ecosystem where local farmers and conscious consumers cultivate a healthier future together.
                    </p>
                </div>

                <!-- Vegetables Image -->
                <div class="relative h-64 rounded-2xl overflow-hidden">
                    <img src="{{ asset('images/auth-vegetables.png') }}" alt="Fresh vegetables" class="w-full h-full object-cover rounded-2xl">

                    <!-- Testimonial Card -->
                    <div class="auth-testimonial-card">
                        <div class="flex gap-1 mb-2">
                            @for($i = 0; $i < 5; $i++)
                                <svg class="w-4 h-4 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-700 italic leading-relaxed mb-3">
                            "The quality of the kale and heirloom tomatoes I get here is unmatched by any supermarket."
                        </p>
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/testimonial-avatar.png') }}" alt="Siti Nurbaya" class="w-9 h-9 rounded-full object-cover">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">Siti Nurbaya</p>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Home Chef</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 bg-white">
            <div class="w-full max-w-md">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back !</h2>
                <p class="text-gray-500 mb-8">Start your journey into the greenhouse community.</p>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Role Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Select Your Role</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="customer" x-model="role" class="hidden peer">
                                <div class="role-card" :class="role === 'customer' ? 'active' : ''">
                                    <svg class="w-7 h-7" :class="role === 'customer' ? 'text-green-700' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                                    </svg>
                                    <span class="text-sm font-semibold" :class="role === 'customer' ? 'text-green-700' : 'text-gray-600'">Customer</span>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="farmer" x-model="role" class="hidden peer">
                                <div class="role-card" :class="role === 'farmer' ? 'active' : ''">
                                    <svg class="w-7 h-7" :class="role === 'farmer' ? 'text-green-700' : 'text-gray-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/>
                                    </svg>
                                    <span class="text-sm font-semibold" :class="role === 'farmer' ? 'text-green-700' : 'text-gray-600'">Farmer</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Email Address</label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="auth-input" placeholder="hello@example.com">
                            <div class="auth-input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                            </div>
                        </div>
                        @error('email')
                            <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                class="auth-input" placeholder="••••••••">
                            <div class="auth-input-icon">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                            </div>
                        </div>
                        @error('password')
                            <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        @if (Route::has('password.request'))
                            <div class="text-right mt-2">
                                <a href="{{ route('password.request') }}" class="text-sm text-green-700 hover:text-green-800 font-medium">Forgot password?</a>
                            </div>
                        @endif
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-primary-green">
                        Login
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
                    <div class="relative flex justify-center">
                        <span class="bg-white px-4 text-xs font-semibold text-gray-400 uppercase tracking-widest">Or Continue With</span>
                    </div>
                </div>

                <!-- Social Login -->
                <div class="grid grid-cols-2 gap-3">
                    <a href="#" class="btn-social">
                        <svg class="w-5 h-5" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                        <span>Google</span>
                    </a>
                    <a href="#" class="btn-social">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/></svg>
                        <span>Apple</span>
                    </a>
                </div>

                <!-- Register Link -->
                <p class="text-center text-sm text-gray-500 mt-8">
                    Don't have account yet? <a href="{{ route('register') }}" class="text-green-700 hover:text-green-800 font-semibold">Signup</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
