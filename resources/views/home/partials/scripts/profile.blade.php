<script>

document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById('profileButton');

    const dropdown = document.getElementById('profileDropdown');

    if (!button || !dropdown) {

        return;

    }

    function renderProfile() {

        dropdown.innerHTML = `

            <div class="border-b border-slate-200 px-5 py-4">

                <div class="font-semibold text-slate-900">

                    {{ auth()->user()->name }}

                </div>

                <div class="mt-1 text-sm text-slate-500">

                    {{ auth()->user()->email }}

                </div>

            </div>

            <div class="py-2">

                <a
                    href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 px-5 py-3 text-sm text-slate-700 transition hover:bg-slate-50">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 text-[#2563EB]"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5.121 17.804A9 9 0 1 1 18.88 17.8M15 11a3 3 0 1 1-6 0a3 3 0 0 1 6 0m-9 9a9 9 0 0 1 18 0"/>

                    </svg>

                    Profil

                </a>

            </div>

            <div class="border-t border-slate-200 p-2">

                <form
                    method="POST"
                    action="{{ route('logout') }}">

                    @csrf

                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium text-red-600 transition hover:bg-red-50">

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
                                d="M17 16l4-4m0 0l-4-4m4 4H9m4 4v1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5a2 2 0 0 1 2 2v1"/>

                        </svg>

                        Keluar

                    </button>

                </form>

            </div>

        `;

    }

    button.addEventListener('click', function (event) {

        event.stopPropagation();

        renderProfile();

        const opened = !dropdown.classList.contains('hidden');

        GPSTracker.closeDropdowns();

        if (!opened) {

            dropdown.classList.remove('hidden');

            document
                .getElementById('profileArrow')
                ?.classList.add('rotate-180');

        }

    });

    document.addEventListener('click', function (event) {

        if (

            !dropdown.contains(event.target) &&

            !button.contains(event.target)

        ) {

            GPSTracker.closeDropdowns();

        }

    });

});

</script>