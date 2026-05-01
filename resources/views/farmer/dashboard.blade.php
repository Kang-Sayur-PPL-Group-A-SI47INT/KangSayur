<x-app-layout>
    @php $title = 'Farmer Dashboard'; @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ auth()->user()->name }}! 🌾</h1>
                <p class="text-gray-500 mt-1">Here's what's happening on your market today.</p>
            </div>
            <div class="mt-4 bg-green-100 border border-green-200 rounded-xl p-4 w-fit">
                <p class="text-sm text-gray-600">Farmer Reputation</p>
                <h2 class="text-lg font-bold text-green-900">⭐ {{ $score }}</h2>
            </div>
            <a href="#" class="px-5 py-2.5 bg-green-800 text-white font-semibold rounded-full hover:bg-green-900 transition-all shadow-lg shadow-green-200/50">
                Add New Listing
            </a>
        </div>

        

        

        

        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['route' => 'farmer.listings.index', 'icon' => '🌱', 'label' => 'Manage Produce'],
         
            ] as $link)
                <a href="{{ route($link['route']) }}" class="bg-white border border-gray-100 rounded-2xl p-5 text-center hover:shadow-lg hover:shadow-green-50 transition-all duration-300 hover:-translate-y-1 group">
                    <div class="text-3xl mb-2 group-hover:scale-110 transition-transform">{{ $link['icon'] }}</div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $link['label'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</x-app-layout>
