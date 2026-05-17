<x-app-layout>
    @php $title = 'Farmer Dashboard'; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Verification Status Banner --}}
        @if(auth()->user()->verification_status === 'unverified')
            <div class="mb-6 p-5 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-2xl">
                <div class="flex items-start gap-4">
                    <div class="text-3xl">🔒</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-amber-900 text-lg">Verifikasi Akun Diperlukan</h3>
                        <p class="text-sm text-amber-700 mt-1">Untuk mengakses fitur listing dan manajemen pesanan, Anda harus menyelesaikan verifikasi terlebih dahulu.</p>
                        <a href="{{ route('farmer.profile.edit') }}" class="inline-flex items-center gap-2 mt-3 px-5 py-2.5 bg-amber-500 text-white text-sm font-semibold rounded-full hover:bg-amber-600 transition-all shadow-lg shadow-amber-200/50">
                            📋 Lengkapi Verifikasi
                        </a>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->verification_status === 'pending')
            <div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl">
                <div class="flex items-center gap-4">
                    <div class="text-3xl">⏳</div>
                    <div>
                        <h3 class="font-bold text-blue-900">Menunggu Verifikasi</h3>
                        <p class="text-sm text-blue-700 mt-1">Dokumen Anda sedang ditinjau oleh admin. Anda akan dapat mengakses semua fitur setelah diverifikasi.</p>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->verification_status === 'rejected')
            <div class="mb-6 p-5 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-2xl">
                <div class="flex items-start gap-4">
                    <div class="text-3xl">❌</div>
                    <div class="flex-1">
                        <h3 class="font-bold text-red-900">Verifikasi Ditolak</h3>
                        <p class="text-sm text-red-700 mt-1">{{ auth()->user()->rejection_note ?? 'Dokumen tidak memenuhi persyaratan.' }}</p>
                        <a href="{{ route('farmer.profile.edit') }}" class="inline-flex items-center gap-2 mt-3 px-5 py-2.5 bg-red-500 text-white text-sm font-semibold rounded-full hover:bg-red-600 transition-all">
                            🔄 Perbaiki Dokumen
                        </a>
                    </div>
                </div>
            </div>
        @endif
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}! 🌾</h1>
                <p class="text-gray-500 mt-1">Here's what's happening on your market today.</p>
            </div>
            
            @if(auth()->user()->isVerified())
            <a href="{{ route('farmer.listings.create') }}" class="px-5 py-2.5 bg-green-800 text-white font-semibold rounded-full hover:bg-green-900 transition-all shadow-lg shadow-green-200/50">
                + New Listing
            </a>
            @endif
        </div>
        
        
        
        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
           
            @php
                $links = [
                    ['route' => 'farmer.profile.edit', 'icon' => '👤', 'label' => 'Edit Profile', 'requiresVerification' => false],
                ];
                if (auth()->user()->isVerified()) {
                    $links = array_merge([
                        ['route' => 'farmer.listings.index', 'icon' => '🌱', 'label' => 'Manage Produce', 'requiresVerification' => true],
                        ['route' => 'farmer.orders.index', 'icon' => '📦', 'label' => 'Orders', 'requiresVerification' => true],
                    ], $links);
                }
            @endphp
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}" class="bg-white border border-gray-100 rounded-2xl p-5 text-center hover:shadow-lg hover:shadow-green-50 transition-all duration-300 hover:-translate-y-1 group">
                    <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">{{ $link['icon'] }}</div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $link['label'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>