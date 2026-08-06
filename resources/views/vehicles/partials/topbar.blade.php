<header
    id="vehicleTopbar"
    class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur-md"
>

    <div
        class="flex h-16 items-center justify-between px-6"
    >

        {{-- ========================================================= --}}
        {{-- LEFT --}}
        {{-- ========================================================= --}}

        <div
            class="flex items-center"
        >

            <a
                href="{{ route('home') }}"
                class="flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-[13px] font-semibold text-slate-700 transition-all duration-200 hover:border-blue-300 hover:text-blue-600"
            >

                <i
                    class="fa-solid fa-arrow-left text-[12px]"
                ></i>

                <span>

                    Kembali ke Monitoring

                </span>

            </a>

        </div>

        {{-- ========================================================= --}}
        {{-- CENTER SEARCH --}}
        {{-- ========================================================= --}}

        <div
            class="w-full max-w-[560px]"
        >

            <div
                class="relative"
            >

                <i
                    class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-[13px] text-slate-400"
                ></i>

                <input

                    id="vehicleSearch"

                    type="text"

                    autocomplete="off"

                    placeholder="Cari lokasi atau alamat..."

                    class="h-10 w-full rounded-full border border-slate-200 bg-white pl-11 pr-4 text-[13px] outline-none transition-all duration-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100"

                >

                <div

                    id="vehicleSearchResult"

                    class="absolute left-0 right-0 top-full z-[9999] mt-2 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"

                ></div>

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- RIGHT --}}
        {{-- ========================================================= --}}

        <div
            class="flex items-center gap-3"
        >

                    {{-- ========================================================= --}}
            {{-- Notification --}}
            {{-- ========================================================= --}}

            <button

                id="vehicleNotificationButton"

                type="button"

                class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition-all duration-200 hover:border-blue-300 hover:text-blue-600"

            >

                <i
                    class="fa-regular fa-bell text-[15px]"
                ></i>

                <span

                    id="vehicleNotificationBadge"

                    class="absolute -right-1 -top-1 flex h-4 min-w-[16px] items-center justify-center rounded-full bg-red-500 px-1 text-[9px] font-bold leading-none text-white"

                >

                    0

                </span>

            </button>

            {{-- ========================================================= --}}
            {{-- Profile --}}
            {{-- ========================================================= --}}

            <div class="relative">

                <button
                    id="profileButton"
                    type="button"
                    class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 transition hover:bg-slate-50">

                    <div
                        data-profile-avatar
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-[#2563EB] text-sm font-bold text-white">

                        {{ strtoupper(mb_substr(auth()->user()->name,0,1)) }}

                    </div>

                    <div class="text-left">

                        <div class="text-sm font-semibold text-slate-900">

                            <span data-profile-name>

                                {{ auth()->user()->name }}

                            </span>

                        </div>

                        <div class="text-xs text-slate-500">

                            <span data-profile-email>

                                {{ auth()->user()->email }}

                            </span>
                        </div>

                    </div>

                    <svg
                        id="profileArrow"
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-slate-500 transition-transform duration-200"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m6 9 6 6 6-6"/>

                    </svg>

                </button>

                <div
                    id="profileDropdown"
                    class="absolute right-0 z-[9999] mt-3 hidden w-64 rounded-2xl border border-slate-200 bg-white shadow-2xl">

                </div>

            </div>


        </div>

    </div>

</header>