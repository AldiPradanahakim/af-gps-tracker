{{--
|--------------------------------------------------------------------------
| Petunjuk Syarat Kata Sandi
|--------------------------------------------------------------------------
|
| Dipakai di SEMUA tempat pengguna mengetik kata sandi baru (lengkapi
| profil, reset kata sandi, ubah kata sandi di profil, profil admin)
| supaya syaratnya terlihat SEBELUM formulir dikirim - bukan baru muncul
| sebagai pesan error setelah gagal.
|
| Daftar syarat di sini harus selaras dengan Password::defaults()
| di app/Providers/AppServiceProvider.php.
|
--}}

@props([
    'target' => null,
    'compact' => false,
])

<div
    {{ $attributes->merge([
        'class' => 'rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5',
    ]) }}
>

    <p class="text-[0.75rem] font-semibold text-slate-700">
        Kata sandi harus memuat:
    </p>

    <ul class="mt-1.5 space-y-1 text-[0.7188rem] text-slate-600">

        <li class="flex items-start gap-1.5">
            <i class="fa-solid fa-circle-check mt-[0.1875rem] text-[0.5625rem] text-slate-400"></i>
            Minimal 8 karakter
        </li>

        <li class="flex items-start gap-1.5">
            <i class="fa-solid fa-circle-check mt-[0.1875rem] text-[0.5625rem] text-slate-400"></i>
            Huruf besar (A&ndash;Z)
        </li>

        <li class="flex items-start gap-1.5">
            <i class="fa-solid fa-circle-check mt-[0.1875rem] text-[0.5625rem] text-slate-400"></i>
            Huruf kecil (a&ndash;z)
        </li>

        <li class="flex items-start gap-1.5">
            <i class="fa-solid fa-circle-check mt-[0.1875rem] text-[0.5625rem] text-slate-400"></i>
            Angka (0&ndash;9)
        </li>

        <li class="flex items-start gap-1.5">
            <i class="fa-solid fa-circle-check mt-[0.1875rem] text-[0.5625rem] text-slate-400"></i>
            Karakter khusus (contoh: <span class="font-mono">! &#64; # $ %</span>)
        </li>

    </ul>

    @unless($compact)
        <p class="mt-2 text-[0.6875rem] text-slate-500">
            Contoh: <span class="font-mono font-semibold text-slate-700">Gps#Tracker2026</span>
        </p>
    @endunless

</div>
