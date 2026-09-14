@extends('admin.layouts.app')

@section('title', 'Manajemen Pengguna - ' . config('app.name'))
@section('page_title', 'Manajemen Pengguna')
@section('page_description', 'Kelola semua akun pengguna yang terdaftar di dalam sistem.')

@section('admin_content')

<div class="mt-8 mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-1 items-center gap-3">
        <div class="relative w-full max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email pengguna..." class="w-full rounded-xl border border-slate-300 pl-10 pr-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        
        <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Cari</button>
        
        @if(request('search'))
            <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-red-500 hover:text-red-700">Atur Ulang</a>
        @endif
    </form>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-500">
            <thead class="bg-slate-50 text-xs uppercase text-slate-700 border-b border-slate-200">
                <tr>
                    <th scope="col" class="px-6 py-4 font-semibold">Nama Pengguna</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Email & Kontak</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Perangkat GPS yang Dimiliki</th>
                    <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($users as $user)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-900">{{ $user->name }}</p>
                            <p class="text-xs text-slate-400 mt-1">Terdaftar: {{ $user->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-slate-700">{{ $user->email }}</p>
                            <p class="text-slate-500 text-xs mt-1">{{ $user->phone ?? '-' }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->devices->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->devices as $device)
                                        <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                            @if($device->vehicle)
                                                {{ $device->vehicle->name ?? $device->device_id }}
                                            @else
                                                {{ $device->device_id }}
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-400 text-xs italic">Belum ada perangkat</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button
                                type="button"
                                onclick="openDeleteUserModal('{{ route('admin.users.destroy', $user->id) }}', '{{ $user->name }}');"
                                class="text-red-500 hover:text-red-700 font-medium transition-colors"
                            >
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                            Belum ada pengguna terdaftar (selain admin).
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($users->hasPages())
        <div class="border-t border-slate-200 p-4">
            {{ $users->links() }}
        </div>
    @endif
</div>

{{-- Modal Hapus Pengguna --}}
<div id="deleteUserModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">

        {{-- Icon --}}
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>

        {{-- Content --}}
        <div class="mt-4 text-center">
            <h3 class="text-lg font-bold text-slate-900">Hapus Pengguna?</h3>
            <p class="mt-2 text-sm text-slate-500">
                Anda akan menghapus pengguna
                <span class="font-semibold text-slate-800" id="deleteUserName"></span>.
                Perangkat GPS yang dimiliki akan otomatis terlepas dari akun ini. Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex gap-3">
            <button
                type="button"
                onclick="closeDeleteUserModal()"
                class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Batal
            </button>

            <form id="deleteUserForm" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="w-full rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700"
                >
                    Ya, Hapus
                </button>
            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function openDeleteUserModal(actionUrl, userName) {
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteUserForm').action = actionUrl;
    document.getElementById('deleteUserModal').classList.remove('hidden');
    document.getElementById('deleteUserModal').classList.add('flex');
}

function closeDeleteUserModal() {
    document.getElementById('deleteUserModal').classList.add('hidden');
    document.getElementById('deleteUserModal').classList.remove('flex');
}

// Tutup modal jika klik area luar
document.getElementById('deleteUserModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteUserModal();
});
</script>
@endpush
