<div class="w-full max-w-[460px] rounded-[24px] bg-white p-8 shadow-xl border border-[#E5E7EB]">

    <div class="text-center">

        <div class="mx-auto mb-4 flex h-[72px] w-[72px] items-center justify-center rounded-full bg-[#EFF6FF] shadow-sm">
            <img src="{{ asset('images/LOGO GPS.png') }}"
                alt="GPS TRACKER"
                class="h-[42px] w-[42px] object-contain">
        </div>

        <h1 class="text-[30px] font-semibold tracking-[-0.03em] text-slate-950">
            Lengkapi Profil
        </h1>

        <p class="mt-3 text-sm leading-6 text-slate-500">
            Lengkapi informasi pribadi Anda untuk melanjutkan proses aktivasi perangkat GPS.
        </p>

    </div>

    @if ($errors->any())
        <div class="mt-6 rounded-xl border border-red-200 bg-red-50 p-4">

            <ul class="space-y-1 text-sm text-red-600">

                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach

            </ul>

        </div>
    @endif

    <form method="POST"
        action="{{ route('profile.store') }}"
        class="mt-8 space-y-5">

        @csrf

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Nama Lengkap
            </label>

            <input
                type="text"
                name="name"
                value="{{ old('name') }}"
                placeholder="Masukkan nama lengkap"
                required
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Email
            </label>

            <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email"
                required
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Nomor WhatsApp
            </label>

            <input
                type="text"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="08xxxxxxxxxx"
                required
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Kata Sandi
            </label>

            <input
                type="password"
                name="password"
                placeholder="Minimal 8 karakter"
                required
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

        </div>

        <div>

            <label class="mb-2 block text-sm font-semibold text-slate-700">
                Konfirmasi Kata Sandi
            </label>

            <input
                type="password"
                name="password_confirmation"
                placeholder="Masukkan ulang kata sandi"
                required
                class="h-[52px] w-full rounded-xl border border-slate-300 px-4 text-sm outline-none transition focus:border-[#2563EB] focus:ring-2 focus:ring-[#2563EB]/20">

        </div>

        <button
            type="submit"
            class="flex h-[52px] w-full items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] to-[#3B82F6] text-sm font-semibold text-white shadow-lg transition hover:brightness-105">

            Simpan & Lanjutkan

        </button>

    </form>

</div>