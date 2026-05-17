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
                                    <span class="text-gray-900 text-sm font-medium">{{ $u->name }}</span>
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
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-8 text-gray-400">Tidak ada pengguna ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $users->appends(request()->query())->links() }}</div>
    </div>
</x-admin-layout>
