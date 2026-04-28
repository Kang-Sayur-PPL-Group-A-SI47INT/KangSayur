<x-app-layout>
    @php $title = 'Edit Profil'; @endphp
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Edit Profil Petani 👤</h1>
        <div class="bg-white rounded-2xl border border-gray-100 p-8">
            <form method="POST" action="{{ route('farmer.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')
                <div class="flex items-center gap-6 mb-6">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center text-white text-2xl font-bold overflow-hidden">
                        @if($user->profile_photo)
                            <img src="{{ asset('storage/' . $user->profile_photo) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-700">Foto Profil</label>
                        <input type="file" name="profile_photo" accept="image/*" class="block mt-1 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Pertanian</label>
                    <textarea name="farm_description" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">{{ old('farm_description', $user->farm_description) }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kota</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat</label>
                        <input type="text" name="address" value="{{ old('address', $user->address) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Latitude</label>
                        <input type="text" name="latitude" value="{{ old('latitude', $user->latitude) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none" placeholder="-6.81148000">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Longitude</label>
                        <input type="text" name="longitude" value="{{ old('longitude', $user->longitude) }}" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none" placeholder="107.61878000">
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="is_public_profile" value="1" {{ $user->is_public_profile ? 'checked' : '' }} class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500" id="public_profile">
                    <label for="public_profile" class="text-sm text-gray-700">Profil publik (terlihat oleh semua pengguna)</label>
                </div>
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-bold rounded-xl hover:from-green-600 hover:to-emerald-700 shadow-lg shadow-green-200">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
