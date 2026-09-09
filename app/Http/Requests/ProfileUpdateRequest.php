<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {

        if ($this->isMethod('post')) {
            return [

                'name' => [
                    'required',
                    'string',
                    'max:100',
                ],

                'email' => [
                    'required',
                    'email:rfc,dns',
                    'max:255',
                    'unique:users,email',
                ],

                'phone' => [
                    'required',
                    'string',
                    'max:20',
                    'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/',
                ],

                'password' => [
                    'required',
                    'confirmed',
                    Password::defaults(),
                ],

            ];
        }





        /** @var User $user */
        $user = $this->user();

        return [

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'email' => [
                'required',
                'email:rfc,dns',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],

            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/',
            ],

            /*
            |------------------------------------------------------------------
            | Zona Waktu
            |------------------------------------------------------------------
            |
            | Indonesia punya tiga zona waktu. Pilihan ini menentukan jam
            | yang dilihat pengguna di seluruh aplikasi - halaman, export
            | PDF, dan notifikasi Email/WhatsApp - tanpa mengubah cara
            | data disimpan. Dibatasi ke daftar resmi supaya tidak ada
            | nilai aneh yang lolos ke database.
            |
            */

            'timezone' => [
                'nullable',
                Rule::in(User::SUPPORTED_TIMEZONES),
            ],

            'current_password' => [
                'nullable',
                'required_with:password',
            ],

            'password' => [
                'nullable',
                'confirmed',
                Password::defaults(),
            ],

        ];
    }

    /**
     * Validation Messages
     */
    public function messages(): array
    {
        return [

            'name.required' => 'Nama wajib diisi.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid atau domain email tidak dapat ditemukan.',
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain.',

            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Format nomor WhatsApp tidak valid. Gunakan format 08xxxxxxxxxx atau +628xxxxxxxxxx.',

            'password.min' => 'Kata sandi minimal 8 karakter.',

            'password.mixed' => 'Kata sandi harus mengandung huruf besar dan huruf kecil.',

            'password.numbers' => 'Kata sandi harus mengandung minimal satu angka (0-9).',

            'password.symbols' => 'Kata sandi harus mengandung minimal satu karakter khusus (contoh: ! @ # $ %).',

            'timezone.in' => 'Zona waktu yang dipilih tidak dikenali.',

        ];
    }
}
