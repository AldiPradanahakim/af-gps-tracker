@extends('admin.layouts.app')

@section('title', 'Manajemen Perangkat - ' . config('app.name'))
@section('page_title', 'Manajemen Perangkat')
@section('page_description', 'Kelola semua perangkat GPS yang terdaftar di dalam sistem.')

@section('admin_content')

<div class="flex items-center justify-end">
    <button onclick="document.getElementById('addDeviceModal').classList.remove('hidden')" class="flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/30">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Perangkat
    </button>
</div>

@if(session('generated_devices_data') && session('generated_devices_key'))
<div class="mt-6 rounded-2xl border border-green-200 bg-green-50 p-6 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-lg font-bold text-green-800">Perangkat Berhasil Dibuat!</h3>
            <p class="mt-1 text-sm text-green-700">Silakan unduh daftar ID dan kata sandi di bawah ini. <strong>Kata sandi ini hanya ditampilkan sekali ini saja.</strong></p>
        </div>
        <a href="{{ route('admin.devices.export_pdf', session('generated_devices_key')) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-md transition-all hover:bg-green-700">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Unduh PDF
        </a>
    </div>
    
    <div class="mt-4 max-h-64 overflow-y-auto rounded-xl border border-green-200 bg-white">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-4 py-3 font-semibold">Device ID</th>
                    <th class="px-4 py-3 font-semibold">Kata Sandi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach(session('generated_devices_data') as $d)
                <tr>
                    <td class="px-4 py-2 font-medium text-slate-900">{{ $d['device_id'] }}</td>
                    <td class="px-4 py-2 font-mono text-slate-700">{{ $d['device_password'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<div class="mt-8 mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <form action="{{ route('admin.devices.index') }}" method="GET" class="flex flex-1 items-center gap-3">
        <div class="relative w-full max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Device ID..." class="w-full rounded-xl border border-slate-300 pl-10 pr-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        
        <select name="status" class="rounded-xl border border-slate-300 pl-4 pr-10 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-blue-500 appearance-none bg-no-repeat bg-[right_0.5rem_center] bg-[length:1.5em_1.5em]" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22none%22%20stroke%3D%22%236b7280%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpath%20d%3D%22M6%208l4%204%204-4%22%2F%3E%3C%2Fsvg%3E');">
            <option value="">Semua Status</option>
            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="used" {{ request('status') === 'used' ? 'selected' : '' }}>Digunakan</option>
        </select>
        
        <button type="submit" class="rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Filter</button>
        
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('admin.devices.index') }}" class="text-sm font-medium text-red-500 hover:text-red-700">Reset</a>
        @endif
    </form>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-500">
            <thead class="bg-slate-50 text-xs uppercase text-slate-700 border-b border-slate-200">
                <tr>
                    <th scope="col" class="px-6 py-4 font-semibold">Device ID</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Password</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Status Kepemilikan</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Status Jaringan</th>
                    <th scope="col" class="px-6 py-4 font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($devices as $device)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">
                            {{ $device->device_id }}
                        </td>
                        <td class="px-6 py-4">
                            {{--
                                device_password di database SELALU berupa hash
                                bcrypt (lihat Device::setDevicePasswordAttribute),
                                bukan kata sandi asli - tidak ada cara untuk
                                menampilkannya lagi setelah pembuatan. Jangan
                                render kolom ini langsung, itu cuma
                                menampilkan hash yang terlihat seperti kata
                                sandi asli tapi tidak bisa dipakai login.
                            --}}
                            <span class="text-slate-400 italic text-sm" title="Kata sandi hanya ditampilkan sekali saat perangkat dibuat (lewat Export PDF) dan tidak bisa dilihat lagi setelahnya.">
                                Hanya tampil sekali saat dibuat
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($device->user_id)
                                <div class="flex items-center gap-2">
                                    <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                                    <span class="text-slate-700">Dipakai oleh <span class="font-semibold">{{ $device->user->name ?? 'User' }}</span></span>
                                </div>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="flex h-2 w-2 rounded-full bg-slate-300"></span>
                                    <span class="text-slate-500">Tersedia (Kosong)</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($device->user_id)
                                @if($device->is_online)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Online</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-600">Offline</span>
                                @endif
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if(!$device->user_id)
                                <button
                                    type="button"
                                    onclick="openDeleteModal('{{ route('admin.devices.destroy', $device->id) }}', '{{ $device->device_id }}');"
                                    class="text-red-500 hover:text-red-700 font-medium transition-colors"
                                >
                                    Hapus
                                </button>
                            @else
                                <span class="text-slate-300 text-xs" title="Tidak dapat dihapus karena sedang dipakai">Tidak dapat dihapus</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            Belum ada perangkat GPS terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($devices->hasPages())
        <div class="border-t border-slate-200 p-4">
            {{ $devices->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah Perangkat --}}
<div id="addDeviceModal" class="fixed inset-0 z-50 flex hidden items-center justify-center bg-slate-900/50 backdrop-blur-sm">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Tambah Perangkat GPS</h3>
            <button onclick="document.getElementById('addDeviceModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form action="{{ route('admin.devices.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="jumlah_perangkat" class="block text-sm font-medium text-slate-700">Jumlah Perangkat yang Ingin Dibuat</label>
                    <input type="number" name="jumlah_perangkat" id="jumlah_perangkat" required min="1" max="100" value="1" class="mt-1 block w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:border-blue-500 focus:ring-blue-500" placeholder="Misal: 5">
                    <p class="mt-1 text-xs text-slate-500">Device ID dan password akan di-generate secara otomatis secara berurutan dan acak.</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addDeviceModal').classList.add('hidden')" class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">Simpan Perangkat</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Hapus Perangkat --}}
<div id="deleteDeviceModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm">
    <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">

        {{-- Icon --}}
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </div>

        {{-- Content --}}
        <div class="mt-4 text-center">
            <h3 class="text-lg font-bold text-slate-900">Hapus Perangkat?</h3>
            <p class="mt-2 text-sm text-slate-500">
                Anda akan menghapus perangkat
                <span class="font-semibold text-slate-800" id="deleteDeviceId"></span>.
                Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>

        {{-- Actions --}}
        <div class="mt-6 flex gap-3">
            <button
                type="button"
                onclick="closeDeleteModal()"
                class="flex-1 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
            >
                Batal
            </button>

            <form id="deleteDeviceForm" method="POST" class="flex-1">
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
function openDeleteModal(actionUrl, deviceId) {
    document.getElementById('deleteDeviceId').textContent = deviceId;
    document.getElementById('deleteDeviceForm').action = actionUrl;
    document.getElementById('deleteDeviceModal').classList.remove('hidden');
    document.getElementById('deleteDeviceModal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('deleteDeviceModal').classList.add('hidden');
    document.getElementById('deleteDeviceModal').classList.remove('flex');
}

// Tutup modal jika klik area luar
document.getElementById('deleteDeviceModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
@endpush
