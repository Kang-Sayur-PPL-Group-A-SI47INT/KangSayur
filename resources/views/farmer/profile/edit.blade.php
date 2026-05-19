<x-app-layout>
    @php $title = 'Edit Profil'; @endphp
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Edit Profil Petani 👤</h1>
        {{-- Verification Status Banner --}}
        @if(auth()->user()->verification_status === 'rejected')
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl">
                <div class="flex items-start gap-3">
                    <span class="text-xl">❌</span>
                    <div>
                        <p class="font-semibold text-red-800">Verifikasi Ditolak</p>
                        <p class="text-sm text-red-600 mt-1">{{ auth()->user()->rejection_note ?? 'Dokumen tidak memenuhi persyaratan.' }}</p>
                        <p class="text-sm text-red-600 mt-1">Silakan perbaiki dan unggah ulang dokumen di bawah, lalu kirim ulang untuk verifikasi.</p>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->verification_status === 'pending')
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-2xl">
                <div class="flex items-center gap-3">
                    <span class="text-xl">⏳</span>
                    <div>
                        <p class="font-semibold text-amber-800">Menunggu Verifikasi</p>
                        <p class="text-sm text-amber-600">Dokumen Anda sedang ditinjau oleh admin. Proses ini membutuhkan 1-3 hari kerja.</p>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->verification_status === 'verified')
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl">
                <div class="flex items-center gap-3">
                    <span class="text-xl">✅</span>
                    <div>
                        <p class="font-semibold text-green-800">Terverifikasi</p>
                        <p class="text-sm text-green-600">Akun Anda telah diverifikasi pada {{ auth()->user()->verified_at?->format('d M Y') }}.</p>
                    </div>
                </div>
            </div>
        @elseif(auth()->user()->verification_status === 'unverified')
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-2xl">
                <div class="flex items-start gap-3">
                    <span class="text-xl">📋</span>
                    <div>
                        <p class="font-semibold text-blue-800">Verifikasi Diperlukan</p>
                        <p class="text-sm text-blue-600">Untuk mengakses fitur listing dan manajemen pesanan, silakan unggah dokumen verifikasi di bawah dan kirim untuk ditinjau.</p>
                    </div>
                </div>
            </div>
        @endif
        {{-- Verification Warning from Middleware --}}
        @if(session('verification_warning'))
            <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-2xl">
                <div class="flex items-start gap-3">
                    <span class="text-xl">⚠️</span>
                    <p class="text-sm font-medium text-orange-800">{{ session('verification_warning') }}</p>
                </div>
            </div>
        @endif
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3">
                <span>✅</span>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center gap-3">
                <span>❌</span>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif
        @if(session('info'))
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-700 rounded-2xl flex items-center gap-3">
                <span>ℹ️</span>
                <p class="text-sm font-medium">{{ session('info') }}</p>
            </div>
        @endif
        {{-- Profile Form --}}
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
                {{-- Interactive Map for Farm Location --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">📍 Lokasi Pertanian</label>
                    <p class="text-xs text-gray-500 mb-3">Klik pada peta atau gunakan pencarian untuk menentukan lokasi pertanian Anda secara akurat.</p>

                    {{-- Search Bar --}}
                    <div class="relative mb-3">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="farmer-map-search" placeholder="Cari alamat atau nama tempat..."
                            class="w-full pl-10 pr-24 py-3 rounded-xl border border-gray-200 focus:border-green-500 focus:ring-2 focus:ring-green-200 outline-none text-sm">
                        <button type="button" id="farmer-geolocate-btn"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 bg-green-50 text-green-700 text-xs font-semibold rounded-lg hover:bg-green-100 transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                            </svg>
                            Lokasi Saya
                        </button>
                    </div>

                    {{-- Map Container --}}
                    <div id="farmer-map" class="w-full rounded-xl border-2 border-gray-200 overflow-hidden" style="height: 350px; z-index: 1;"></div>

                    {{-- Coordinates Display --}}
                    <div class="mt-3 flex items-center gap-4">
                        <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg flex-1">
                            <span class="text-xs text-gray-500 font-medium">Lat:</span>
                            <span id="farmer-lat-display" class="text-xs font-mono text-gray-700">{{ $user->latitude ?? 'Belum diatur' }}</span>
                        </div>
                        <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg flex-1">
                            <span class="text-xs text-gray-500 font-medium">Lng:</span>
                            <span id="farmer-lng-display" class="text-xs font-mono text-gray-700">{{ $user->longitude ?? 'Belum diatur' }}</span>
                        </div>
                    </div>

                    {{-- Hidden Inputs --}}
                    <input type="hidden" name="latitude" id="farmer-latitude" value="{{ old('latitude', $user->latitude) }}">
                    <input type="hidden" name="longitude" id="farmer-longitude" value="{{ old('longitude', $user->longitude) }}">
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
        {{-- Verification Documents Section --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-8 mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-2">📄 Dokumen Verifikasi</h2>
            <p class="text-sm text-gray-500 mb-6">Unggah semua dokumen yang diperlukan untuk verifikasi akun petani Anda. Format: JPG, PNG, atau PDF (maks. 5MB).</p>
            <form method="POST" action="{{ route('farmer.profile.updateDocuments') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                @php
                    $documents = [
                        ['field' => 'doc_skp', 'label' => 'Surat Keterangan Profesi Petani (SKP)', 'desc' => 'Surat resmi dari Kepala Desa/Lurah yang menerangkan identitas dan pekerjaan sebagai petani.'],
                        ['field' => 'doc_nib', 'label' => 'Kartu Tani / Nomor Induk Berusaha (NIB)', 'desc' => 'Tanda resmi usaha bagi petani untuk mengakses bantuan pemerintah dan pembiayaan.'],
                        ['field' => 'doc_ktp', 'label' => 'Kartu Tanda Penduduk (KTP)', 'desc' => 'KTP dengan kolom pekerjaan tercantum sebagai "Petani/Pekebun".'],
                        ['field' => 'doc_skt', 'label' => 'Surat Keterangan Anggota Kelompok Tani', 'desc' => 'Surat disahkan oleh BPP/dinas terkait yang menunjukkan terdaftar dalam kelompok tani.'],
                        ['field' => 'doc_land_cert', 'label' => 'Sertifikat Kepemilikan Lahan / Bukti Garapan', 'desc' => 'Dokumen bukti kepemilikan lahan atau hak garap atas tanah pertanian.'],
                    ];
                @endphp
                @foreach($documents as $doc)
                    <div class="border border-gray-100 rounded-xl p-5 hover:border-green-200 transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    @if($user->{$doc['field']})
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">✅ Diunggah</span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-600">❌ Belum diunggah</span>
                                    @endif
                                </div>
                                <label class="block text-sm font-semibold text-gray-800 mb-1">{{ $doc['label'] }}</label>
                                <p class="text-xs text-gray-500">{{ $doc['desc'] }}</p>
                                @if($user->{$doc['field']})
                                    <a href="{{ asset('storage/' . $user->{$doc['field']}) }}" target="_blank"
                                       class="inline-flex items-center gap-1 mt-2 text-xs text-green-600 hover:text-green-700 font-medium">
                                        📎 Lihat dokumen saat ini
                                    </a>
                                @endif
                            </div>
                            <div class="flex-shrink-0">
                                <input type="file" name="{{ $doc['field'] }}" accept=".jpg,.jpeg,.png,.pdf"
                                       class="block text-xs text-gray-500
                                              file:mr-2 file:py-2 file:px-4
                                              file:rounded-full file:border-0
                                              file:text-xs file:font-semibold
                                              file:bg-green-50 file:text-green-700
                                              hover:file:bg-green-100 file:cursor-pointer
                                              file:transition-colors">
                            </div>
                        </div>
                        @error($doc['field'])
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-bold rounded-xl hover:from-blue-600 hover:to-indigo-700 shadow-lg shadow-blue-200">
                    💾 Simpan Dokumen
                </button>
            </form>
            {{-- Submit for Verification --}}
            @if(auth()->user()->verification_status !== 'verified' && auth()->user()->verification_status !== 'pending')
                <div class="mt-6 pt-6 border-t border-gray-100">
                    @if($user->hasAllDocuments())
                        <form method="POST" action="{{ route('farmer.profile.submitVerification') }}">
                            @csrf
                            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-bold rounded-xl hover:from-amber-600 hover:to-orange-700 shadow-lg shadow-amber-200">
                                🚀 Kirim untuk Verifikasi
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 text-center mt-3">Admin akan meninjau dokumen Anda dalam 1-3 hari kerja.</p>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">Unggah semua 5 dokumen di atas untuk dapat mengirim verifikasi.</p>
                            <div class="mt-2">
                                @php
                                    $uploaded = collect(['doc_skp', 'doc_nib', 'doc_ktp', 'doc_skt', 'doc_land_cert'])
                                        ->filter(fn($f) => $user->$f)->count();
                                @endphp
                                <span class="text-xs font-semibold text-gray-400">{{ $uploaded }}/5 dokumen diunggah</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Leaflet CSS & JS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        #farmer-map { cursor: crosshair !important; }
        .leaflet-control-attribution { font-size: 9px !important; }
        .farmer-map-pin {
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Default to Bandung area if no coordinates set
        const defaultLat = {{ $user->latitude ?? -6.9175 }};
        const defaultLng = {{ $user->longitude ?? 107.6191 }};
        const hasExisting = {{ ($user->latitude && $user->longitude) ? 'true' : 'false' }};

        // Initialize map
        const map = L.map('farmer-map', {
            zoomControl: true,
            scrollWheelZoom: true,
        }).setView([defaultLat, defaultLng], hasExisting ? 15 : 12);

        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(map);

        // Custom marker icon
        const farmIcon = L.divIcon({
            html: `<div style="
                width: 36px; height: 36px;
                background: linear-gradient(135deg, #22c55e, #059669);
                border-radius: 50% 50% 50% 0;
                transform: rotate(-45deg);
                border: 3px solid white;
                box-shadow: 0 3px 10px rgba(0,0,0,0.3);
                display: flex; align-items: center; justify-content: center;
            "><span style="transform: rotate(45deg); font-size: 16px;">🌾</span></div>`,
            iconSize: [36, 36],
            iconAnchor: [18, 36],
            className: 'farmer-map-pin',
        });

        let marker = null;

        function placeMarker(lat, lng) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { icon: farmIcon, draggable: true }).addTo(map);
                marker.on('dragend', function(e) {
                    const pos = e.target.getLatLng();
                    updateCoordinates(pos.lat, pos.lng);
                });
            }
            updateCoordinates(lat, lng);
        }

        function updateCoordinates(lat, lng) {
            const latFixed = parseFloat(lat).toFixed(8);
            const lngFixed = parseFloat(lng).toFixed(8);
            document.getElementById('farmer-latitude').value = latFixed;
            document.getElementById('farmer-longitude').value = lngFixed;
            document.getElementById('farmer-lat-display').textContent = latFixed;
            document.getElementById('farmer-lng-display').textContent = lngFixed;
        }

        // Place existing marker
        if (hasExisting) {
            placeMarker(defaultLat, defaultLng);
        }

        // Click on map to place pin
        map.on('click', function(e) {
            placeMarker(e.latlng.lat, e.latlng.lng);
        });

        // Geolocation button
        document.getElementById('farmer-geolocate-btn').addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Geolocation tidak didukung oleh browser Anda.');
                return;
            }
            this.innerHTML = '<svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Mencari...';
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setView([lat, lng], 16);
                    placeMarker(lat, lng);
                    this.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg> Lokasi Saya';
                },
                (error) => {
                    alert('Tidak dapat mengakses lokasi: ' + error.message);
                    this.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg> Lokasi Saya';
                },
                { enableHighAccuracy: true, timeout: 10000 }
            );
        });

        // Address search
        let searchTimeout;
        const searchInput = document.getElementById('farmer-map-search');
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch(this.value);
            }
        });
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            if (this.value.length >= 3) {
                searchTimeout = setTimeout(() => performSearch(this.value), 800);
            }
        });

        function performSearch(query) {
            if (!query.trim()) return;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&countrycodes=id`)
                .then(r => r.json())
                .then(data => {
                    if (data.length > 0) {
                        const lat = parseFloat(data[0].lat);
                        const lng = parseFloat(data[0].lon);
                        map.setView([lat, lng], 16);
                        placeMarker(lat, lng);
                    } else {
                        alert('Lokasi tidak ditemukan. Coba kata kunci lain.');
                    }
                })
                .catch(() => alert('Gagal mencari lokasi. Periksa koneksi internet Anda.'));
        }

        // Fix map rendering in case container was hidden
        setTimeout(() => map.invalidateSize(), 200);
    });
    </script>
</x-app-layout>
