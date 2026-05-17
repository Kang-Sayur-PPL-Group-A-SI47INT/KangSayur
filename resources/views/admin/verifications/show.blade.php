<x-admin-layout>
    @php $title = 'Review Verifikasi - ' . $user->name; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <a href="{{ route('admin.verifications.index') }}" class="text-green-700 hover:text-green-800 text-sm flex items-center gap-1 mb-4 font-medium">
                ← Kembali ke Daftar Verifikasi
            </a>
        </div>
        <div class="grid lg:grid-cols-3 gap-6">
            {{-- Farmer Info --}}
            <div class="lg:col-span-1">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center text-green-700 text-3xl font-bold mx-auto mb-4">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                    </div>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            @php
                                $badge = match($user->verification_status) {
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'verified' => 'bg-green-50 text-green-700 border-green-200',
                                    'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                    default => 'bg-gray-50 text-gray-500 border-gray-200',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs border {{ $badge }}">{{ ucfirst($user->verification_status) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kota</span>
                            <span class="text-gray-900">{{ $user->city ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Terdaftar</span>
                            <span class="text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                        @if($user->verified_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Diverifikasi</span>
                            <span class="text-green-700">{{ $user->verified_at->format('d M Y') }}</span>
                        </div>
                        @endif
                        @if($user->farm_description)
                        <div class="pt-3 border-t border-gray-100">
                            <span class="text-gray-500 block mb-1">Deskripsi</span>
                            <p class="text-gray-600 text-xs">{{ $user->farm_description }}</p>
                        </div>
                        @endif
                    </div>
                    {{-- Action Buttons --}}
                    @if($user->verification_status === 'pending')
                        <div class="mt-6 space-y-3">
                            <form method="POST" action="{{ route('admin.verifications.approve', $user) }}">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-green-800 text-white font-bold rounded-xl hover:bg-green-900 transition-colors shadow-lg shadow-green-200/50">
                                    ✅ Setujui Verifikasi
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.verifications.reject', $user) }}" x-data="{ showNote: false }">
                                @csrf
                                <button type="button" @click="showNote = !showNote" class="w-full py-3 bg-red-50 text-red-700 font-bold rounded-xl hover:bg-red-100 transition-colors border border-red-200">
                                    ❌ Tolak Verifikasi
                                </button>
                                <div x-show="showNote" x-transition class="mt-3 space-y-2">
                                    <textarea name="rejection_note" rows="3" placeholder="Alasan penolakan (opsional)..."
                                        class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-red-400 focus:ring-2 focus:ring-red-100 outline-none resize-none"></textarea>
                                    <button type="submit" class="w-full py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition-colors text-sm">
                                        Konfirmasi Penolakan
                                    </button>
                                </div>
                            </form>
                        </div>
                    @elseif($user->verification_status === 'verified')
                        <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-xl text-center">
                            <p class="text-green-700 text-sm font-medium">✅ Petani ini sudah terverifikasi</p>
                        </div>
                    @elseif($user->verification_status === 'rejected')
                        <div class="mt-6">
                            @if($user->rejection_note)
                                <div class="p-4 bg-red-50 border border-red-200 rounded-xl mb-3">
                                    <p class="text-red-700 text-xs font-medium mb-1">Alasan Penolakan:</p>
                                    <p class="text-red-600 text-sm">{{ $user->rejection_note }}</p>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('admin.verifications.approve', $user) }}">
                                @csrf
                                <button type="submit" class="w-full py-3 bg-green-800 text-white font-bold rounded-xl hover:bg-green-900 transition-colors shadow-lg shadow-green-200/50">
                                    ✅ Setujui Ulang
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            {{-- Documents --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">📄 Dokumen Verifikasi</h2>
                    @php
                        $documents = [
                            ['field' => 'doc_skp', 'label' => 'Surat Keterangan Profesi Petani (SKP)', 'desc' => 'Surat resmi dari Kepala Desa/Lurah yang menerangkan identitas dan pekerjaan sebagai petani.'],
                            ['field' => 'doc_nib', 'label' => 'Kartu Tani / NIB', 'desc' => 'Nomor Induk Berusaha atau Kartu Tani resmi.'],
                            ['field' => 'doc_ktp', 'label' => 'Kartu Tanda Penduduk (KTP)', 'desc' => 'KTP dengan kolom pekerjaan "Petani/Pekebun".'],
                            ['field' => 'doc_skt', 'label' => 'Surat Keterangan Kelompok Tani', 'desc' => 'Surat disahkan oleh BPP yang menunjukkan keanggotaan kelompok tani.'],
                            ['field' => 'doc_land_cert', 'label' => 'Sertifikat Lahan / Bukti Garapan', 'desc' => 'Dokumen bukti kepemilikan lahan atau hak garap.'],
                        ];
                    @endphp
                    <div class="space-y-4">
                        @foreach($documents as $doc)
                            <div class="border rounded-xl p-5 {{ $user->{$doc['field']} ? 'bg-green-50/50 border-green-200' : 'bg-red-50/50 border-red-200' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            @if($user->{$doc['field']})
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">✅ Diunggah</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700 border border-red-200">❌ Belum diunggah</span>
                                            @endif
                                        </div>
                                        <h3 class="text-gray-900 text-sm font-semibold">{{ $doc['label'] }}</h3>
                                        <p class="text-gray-400 text-xs mt-1">{{ $doc['desc'] }}</p>
                                    </div>
                                    @if($user->{$doc['field']})
                                        <a href="{{ asset('storage/' . $user->{$doc['field']}) }}" target="_blank"
                                           class="flex-shrink-0 px-4 py-2 bg-green-100 text-green-700 border border-green-200 rounded-lg text-xs font-medium hover:bg-green-200 transition-colors">
                                            📎 Lihat Dokumen
                                        </a>
                                    @endif
                                </div>
                                @if($user->{$doc['field']})
                                    @php
                                        $filePath = $user->{$doc['field']};
                                        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
                                    @endphp
                                    @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                                        <div class="mt-4">
                                            <img src="{{ asset('storage/' . $filePath) }}" alt="{{ $doc['label'] }}"
                                                 class="max-w-full max-h-64 rounded-lg border border-gray-200 object-contain">
                                        </div>
                                    @elseif(strtolower($ext) === 'pdf')
                                        <div class="mt-4 p-3 bg-white rounded-lg border border-gray-200 flex items-center gap-3">
                                            <span class="text-2xl">📄</span>
                                            <div>
                                                <p class="text-gray-900 text-sm font-medium">PDF Document</p>
                                                <a href="{{ asset('storage/' . $filePath) }}" target="_blank" class="text-green-700 text-xs hover:underline">Buka di tab baru →</a>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                    {{-- Document completion summary --}}
                    @php
                        $uploaded = collect(['doc_skp', 'doc_nib', 'doc_ktp', 'doc_skt', 'doc_land_cert'])
                            ->filter(fn($f) => $user->$f)->count();
                    @endphp
                    <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200 text-center">
                        <p class="text-gray-500 text-sm">
                            Kelengkapan Dokumen: <span class="font-bold {{ $uploaded === 5 ? 'text-green-700' : 'text-amber-600' }}">{{ $uploaded }}/5</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
