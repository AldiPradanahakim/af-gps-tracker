<!-- PROFILE MODAL -->
<div
    id="profileModal"
    class="fixed inset-0 z-[99999] hidden items-center justify-center bg-black/40 p-4 backdrop-blur-sm">

    {{--
        Tinggi dibatasi ke layar dan isinya yang menggulir, bukan halaman -
        tanpa ini seluruh formulir memaksa modal lebih tinggi dari viewport
        dan tombol "Simpan Perubahan" di footer tidak pernah terlihat.
    --}}
    <div
        class="relative flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">

        {{-- HEADER --}}
        <div class="flex shrink-0 items-center justify-between border-b border-slate-200 px-6 py-4">

            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Profil
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Informasi akun Anda
                </p>
            </div>

            <button
                id="closeProfileModal"
                type="button"
                class="rounded-xl p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"/>

                </svg>

            </button>

        </div>

        {{-- BODY --}}
        <div class="flex-1 space-y-4 overflow-y-auto px-6 py-5">

            {{--
                Avatar disusun mendatar (bukan bertumpuk di tengah) supaya
                blok identitas ini memakan satu baris saja - versi tumpuk
                menghabiskan tinggi yang dibutuhkan formulir di bawahnya.
            --}}
            <div class="flex items-center gap-3">

                <div
                    data-profile-avatar
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#2563EB] text-lg font-bold text-white">

                    {{ strtoupper(mb_substr(auth()->user()->name,0,1)) }}

                </div>

                <div class="min-w-0">

                    <h3
                        data-profile-name
                        class="truncate font-semibold text-slate-900">
                        {{ auth()->user()->name }}
                    </h3>

                    <p
                        data-profile-email
                        class="truncate text-sm text-slate-500">
                        {{ auth()->user()->email }}
                    </p>

                </div>

            </div>

            {{--
                FORM

                Dua kolom di layar sedang ke atas: tinggi formulir turun
                separuh, jadi seluruh isinya (termasuk syarat kata sandi)
                muat tanpa menggulir di layar laptop.
            --}}
            <form
                id="profileForm"
                class="grid grid-cols-1 gap-x-4 gap-y-3 md:grid-cols-2">

                @csrf

                @method('PATCH')

                {{-- NAME --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Nama
                    </label>

                    <input
                        id="profileName"
                        name="name"
                        type="text"
                        value="{{ auth()->user()->name }}"
                        disabled
                        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-700">

                </div>

                {{-- EMAIL --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Email
                    </label>

                    <input
                        id="profileEmail"
                        name="email"
                        type="email"
                        value="{{ auth()->user()->email }}"
                        disabled
                        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-700">

                </div>

                {{-- PHONE --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Nomor HP
                    </label>

                    <input
                        id="profilePhone"
                        name="phone"
                        type="text"
                        value="{{ auth()->user()->phone }}"
                        disabled
                        placeholder="-"
                        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-700">

                </div>

                {{-- ZONA WAKTU --}}
                {{--
                    Menentukan jam yang dilihat pengguna ini di SELURUH
                    aplikasi: halaman, export PDF, dan notifikasi Email/
                    WhatsApp. Data tetap disimpan apa adanya - hanya
                    tampilannya yang dikonversi, jadi tidak ada biaya
                    query tambahan.
                --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                        Zona Waktu
                    </label>

                    <select
                        id="profileTimezone"
                        name="timezone"
                        disabled
                        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm text-slate-700">

                        @foreach(\App\Models\User::TIMEZONE_OPTIONS as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(auth()->user()->displayTimezone() === $value)>
                                {{ $label }}
                            </option>
                        @endforeach

                    </select>

                    <p class="mt-1.5 text-[0.6875rem] text-slate-500">
                        Dipakai untuk semua tanggal &amp; jam yang Anda lihat &mdash;
                        termasuk export PDF dan notifikasi Email/WhatsApp.
                    </p>

                </div>

                {{-- PASSWORD --}}
                <div class="grid grid-cols-1 gap-3 md:col-span-2 md:grid-cols-3">

                    <div>

                        <label class="mb-1.5 block text-sm font-medium text-slate-700">
                            Kata Sandi Lama
                        </label>

                        <input
                            id="currentPassword"
                            name="current_password"
                            type="password"
                            disabled
                            class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm">

                    </div>

                    <div>

                        <label class="mb-1.5 block text-sm font-medium text-slate-700">
                            Kata Sandi Baru
                        </label>

                        <input
                            id="newPassword"
                            name="password"
                            type="password"
                            placeholder="Contoh: Gps#Tracker2026"
                            disabled
                            class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm">

                    </div>

                    <div>

                        <label class="mb-1.5 block text-sm font-medium text-slate-700">
                            Konfirmasi
                        </label>

                        <input
                            id="confirmPassword"
                            name="password_confirmation"
                            type="password"
                            disabled
                            class="w-full rounded-xl border border-slate-300 bg-slate-100 px-3 py-2 text-sm">

                    </div>

                </div>

                {{-- Syarat kata sandi (hanya relevan saat mode edit aktif) --}}
                <x-password-requirements id="passwordRequirements" class="hidden md:col-span-2" />

            </form>

        </div>

        {{-- FOOTER --}}
        <div
            class="flex shrink-0 items-center justify-between border-t border-slate-200 bg-slate-50 px-6 py-3">

            <button
                id="editProfileButton"
                type="button"
                class="inline-flex items-center gap-2 rounded-xl border border-[#2563EB] px-5 py-2.5 text-sm font-semibold text-[#2563EB] transition hover:bg-blue-50">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2">

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M16.862 3.487a2.25 2.25 0 1 1 3.182 3.182L8.25 18.463 3 20l1.537-5.25L16.862 3.487Z"/>

                </svg>

                Ubah Profil

            </button>

            <div class="flex items-center gap-3">

                <button
                    id="cancelProfileButton"
                    type="button"
                    class="hidden rounded-xl border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100">

                    Batal

                </button>

                <button
                    id="saveProfileButton"
                    type="submit"
                    form="profileForm"
                    class="hidden rounded-xl bg-[#2563EB] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">

                    Simpan Perubahan

                </button>

            </div>

        </div>

    </div>

</div>