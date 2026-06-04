<x-admin-layout>
    @php $title = 'Pengguna'; @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Pengguna 👥</h1>
                <p class="text-gray-500 mt-1">Daftar semua pengguna terdaftar</p>
            </div>
        </div>
        <form method="GET" class="flex gap-3 mb-6">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
                   class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none flex-1 max-w-xs">
            <select name="role" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="farmer" {{ request('role') === 'farmer' ? 'selected' : '' }}>Farmer</option>
                <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Customer</option>
            </select>
            <select name="status" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="banned" {{ request('status') === 'banned' ? 'selected' : '' }}>Banned</option>
            </select>
            <button type="submit" class="px-6 py-2.5 bg-green-800 text-white rounded-xl text-sm font-medium hover:bg-green-900 transition-colors">Filter</button>
        </form>
        <div class="bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Pengguna</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Email</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Role</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Verifikasi</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Terdaftar</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $u)
                        @php
                            $roleBadge = match($u->role) {
                                'admin' => 'bg-red-50 text-red-700 border-red-200',
                                'farmer' => 'bg-green-50 text-green-700 border-green-200',
                                'customer' => 'bg-blue-50 text-blue-700 border-blue-200',
                                default => 'bg-gray-50 text-gray-600 border-gray-200',
                            };
                            $verBadge = match($u->verification_status ?? 'unverified') {
                                'verified' => 'bg-green-50 text-green-700 border-green-200',
                                'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'rejected' => 'bg-red-50 text-red-700 border-red-200',
                                default => 'bg-gray-50 text-gray-500 border-gray-200',
                            };
                        @endphp
                        <tr class="hover:bg-green-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-green-100 to-emerald-100 flex items-center justify-center text-green-700 text-sm font-bold">
                                        {{ strtoupper(substr($u->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="text-gray-900 text-sm font-medium">{{ $u->name }}</span>
                                        @if($u->role === 'farmer')
                                            @php $avgRating = $u->averageRating(); @endphp
                                            @if($avgRating)
                                                <p class="text-xs text-amber-600">⭐ {{ number_format($avgRating, 1) }}</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $u->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full border {{ $roleBadge }}">{{ ucfirst($u->role) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($u->role === 'farmer')
                                    <span class="px-2 py-1 text-xs rounded-full border {{ $verBadge }}">{{ ucfirst($u->verification_status ?? 'unverified') }}</span>
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-sm">{{ $u->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if($u->is_banned)
                                    <span class="px-2 py-1 text-xs rounded-full border bg-red-50 text-red-700 border-red-200">Banned</span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full border bg-green-50 text-green-700 border-green-200">Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(!$u->isAdmin())
                                    @if($u->is_banned)
                                        <form method="POST" action="{{ route('admin.users.unban', $u) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-green-50 text-green-700 border border-green-200 rounded-lg text-xs font-medium hover:bg-green-100 transition-colors">
                                                ✅ Unban
                                            </button>
                                        </form>
                                    @else
                                        <button @click="banModal = true; banUserId = {{ $u->user_id }}; banUserName = '{{ addslashes($u->name) }}'" class="px-3 py-1.5 bg-red-50 text-red-700 border border-red-200 rounded-lg text-xs font-medium hover:bg-red-100 transition-colors">
                                            🚫 Ban
                                        </button>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-8 text-gray-400">Tidak ada pengguna ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $users->appends(request()->query())->links() }}</div>
    </div>

    {{-- Ban Modal --}}
    <div x-data="{ banModal: false, banUserId: null, banUserName: '' }" @keydown.escape.window="banModal = false">
        <template x-if="banModal">
            <div class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="fixed inset-0 bg-black/50" @click="banModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Ban Pengguna</h3>
                    <p class="text-sm text-gray-500 mb-4">Anda akan mem-ban pengguna <span class="font-semibold text-gray-700" x-text="banUserName"></span>.</p>
                    <form method="POST" :action="'/admin/users/' + banUserId + '/ban'">
                        @csrf
                        <div class="mb-4">
                            <label for="ban_reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan Ban</label>
                            <textarea name="ban_reason" id="ban_reason" rows="3" required maxlength="500"
                                      class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:border-green-500 focus:ring-2 focus:ring-green-100 outline-none"
                                      placeholder="Tuliskan alasan ban..."></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="banModal = false" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition-colors">
                                Ban Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-admin-layout>
