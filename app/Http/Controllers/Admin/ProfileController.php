<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Profil admin tidak lagi punya halaman sendiri - satu-satunya
     * tempatnya adalah modal pada dropdown profil di pojok kanan atas
     * (lihat resources/views/admin/layouts/app.blade.php), supaya tidak
     * ada dua formulir berbeda untuk data yang sama.
     */
    public function edit()
    {
        return redirect()->route('admin.dashboard');
    }

    public function update(Request $request)
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Kebijakan kata sandi admin mengikuti Password::defaults()
        | (app/Providers/AppServiceProvider.php) supaya sama dengan
        | akun pengguna biasa - dan sama dengan syarat yang ditampilkan
        | komponen <x-password-requirements /> di formulirnya.
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.mixed' => 'Kata sandi harus mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Kata sandi harus mengandung minimal satu angka (0-9).',
            'password.symbols' => 'Kata sandi harus mengandung minimal satu karakter khusus (contoh: ! @ # $ %).',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.dashboard')->with('success', 'Profil admin berhasil diperbarui.');
    }
}
