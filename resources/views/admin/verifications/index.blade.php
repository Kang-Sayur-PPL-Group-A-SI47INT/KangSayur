<x-admin-layout>
    @php $title = 'Verifikasi Petani'; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Verifikasi Petani 📋</h1>
                <p class="text-gray-500 mt-1">Kelola verifikasi dokumen petani</p>
            </div>
        </div>
        {{-- Filter Tabs --}}
        <div class="flex gap-2 mb-6 flex-wrap">
            @php
                $tabs = [
                    ['' => 'Semua (' . $counts['all'] . ')'],
                    ['pending' => '⏳ Pending (' . $counts['pending'] . ')'],
                    ['verified' => '✅ Verified (' . $counts['verified'] . ')'],
                    ['rejected' => '❌ Rejected (' . $counts['rejected'] . ')'],
                    ['unverified' => '📄 Unverified (' . $counts['unverified'] . ')'],
                ];
            @endphp
            @foreach($tabs as $tab)
                @foreach($tab as $value => $label)
                    <a href="{{ route('admin.verifications.index', ['status' => $value]) }}"
                       class="px-4 py-2 rounded-full text-sm font-medium transition-all duration-200
                              {{ request('status', '') === $value ? 'bg-green-100 text-green-800 border border-green-200' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700 border border-transparent' }}">
                        {{ $label }}
                    </a>
                @endforeach
            @endforeach
        </div>
        {{-- Farmers Table --}}
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Petani</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Dokumen</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Terdaftar</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($farmers as $farmer)
                        @php
                            $docCount = collect(['doc_skp', 'doc_nib', 'doc_ktp', 'doc_skt', 'doc_land_cert'])
                                ->filter(fn($f) => $farmer->$f)->count();
                            $statusBadge = match($farmer->verification_status) {
                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'verified' => 'bg-green-50 text-green-700 border-green-200',
                                'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                default => 'bg-gray-50 text-gray-500 border-gray-200',
                            };
                        @endphp
                        <tr class="hover:bg-green-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center text-green-700 text-sm font-bold">
                                        {{ strtoupper(substr($farmer->name, 0, 1)) }}
                                    </div>
                                    <span class="text-gray-900 text-sm font-medium">{{ $farmer->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $farmer->email }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium {{ $docCount === 5 ? 'text-green-700' : 'text-amber-600' }}">
                                    {{ $docCount }}/5
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full border {{ $statusBadge }}">
                                    {{ ucfirst($farmer->verification_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $farmer->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.verifications.show', $farmer) }}"
                                   class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-medium hover:bg-green-100 transition-colors">
                                    Review
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-8 text-gray-400">Tidak ada petani ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $farmers->appends(request()->query())->links() }}</div>
    </div>
</x-admin-layout>
