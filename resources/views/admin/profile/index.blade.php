@extends('admin.layouts.app')

@section('title', 'Profil Admin')
@section('page_title', 'Profil Admin')
@section('page_description', 'Kelola informasi kredensial akun administrator Anda.')

@section('admin_content')

<div class="mt-8 max-w-2xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <form action="{{ route('admin.profile.update') }}" method="POST">
        @csrf
        @method('PATCH')
        
        <div class="space-y-6">
            
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" required class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email Login</label>
                <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" required class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                @error('email')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>
            
            <hr class="border-slate-100">

            <div>
                <h3 class="text-sm font-bold text-slate-900">Ubah Password</h3>
                <p class="text-xs text-slate-500">Kosongkan kolom di bawah ini jika tidak ingin mengubah password.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password Baru</label>
                    <input type="password" name="password" id="password" class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('password')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-2 block w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            
        </div>
        
        <div class="mt-8 flex justify-end">
            <button type="submit" class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-md transition-all hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/30">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection
