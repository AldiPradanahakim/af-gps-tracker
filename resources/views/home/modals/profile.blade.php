<!-- PROFILE MODAL -->
<div
    id="profileModal"
    class="fixed inset-0 z-[99999] hidden items-center justify-center bg-black/40 backdrop-blur-sm">

    <div
        class="relative w-full max-w-2xl rounded-3xl bg-white shadow-2xl">

        {{-- HEADER --}}
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">

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
        <div class="space-y-6 px-6 py-6">

            {{-- AVATAR --}}
            <div class="flex flex-col items-center">

                <div
                    data-profile-avatar
                    class="flex h-20 w-20 items-center justify-center rounded-full bg-[#2563EB] text-3xl font-bold text-white">

                    {{ strtoupper(mb_substr(auth()->user()->name,0,1)) }}

                </div>

                <h3
                    data-profile-name
                    class="mt-4 text-lg font-semibold text-slate-900">
                    {{ auth()->user()->name }}
                </h3>

                <p
                    data-profile-email
                    class="text-sm text-slate-500">
                    {{ auth()->user()->email }}
                </p>

            </div>

            {{-- FORM --}}
            <form
                id="profileForm"
                class="space-y-5">

                @csrf

                @method('PATCH')

                {{-- NAME --}}
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Nama
                    </label>

                    <input
                        id="profileName"
                        name="name"
                        type="text"
                        value="{{ auth()->user()->name }}"
                        disabled
                        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-700">

                </div>

                {{-- EMAIL --}}
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Email
                    </label>

                    <input
                        id="profileEmail"
                        name="email"
                        type="email"
                        value="{{ auth()->user()->email }}"
                        disabled
                        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-700">

                </div>

                {{-- PHONE --}}
                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Nomor HP
                    </label>

                    <input
                        id="profilePhone"
                        name="phone"
                        type="text"
                        value="{{ auth()->user()->phone }}"
                        disabled
                        placeholder="-"
                        class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-700">

                </div>

                {{-- PASSWORD --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    <div>

                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Password Lama
                        </label>

                        <input
                            id="currentPassword"
                            name="current_password"
                            type="password"
                            disabled
                            class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm">

                    </div>

                    <div>

                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Password Baru
                        </label>

                        <input
                            id="newPassword"
                            name="password"
                            type="password"
                            disabled
                            class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm">

                    </div>

                    <div>

                        <label class="mb-2 block text-sm font-medium text-slate-700">
                            Konfirmasi
                        </label>

                        <input
                            id="confirmPassword"
                            name="password_confirmation"
                            type="password"
                            disabled
                            class="w-full rounded-xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm">

                    </div>

                </div>

            </form>

        </div>

        {{-- FOOTER --}}
        <div
            class="flex items-center justify-between rounded-b-3xl border-t border-slate-200 bg-slate-50 px-6 py-4">

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

                Edit Profil

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