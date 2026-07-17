<script>

document.addEventListener('DOMContentLoaded', function () {

    const button = document.getElementById('notificationButton');

    const dropdown = document.getElementById('notificationDropdown');

    if (!button || !dropdown) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Render Notification
    |--------------------------------------------------------------------------
    */

    function renderNotifications() {

        const notifications = GPSTracker.notifications;

        if (!notifications.length) {

            dropdown.innerHTML = `

                <div class="border-b border-slate-200 px-5 py-4">

                    <h3 class="text-base font-semibold text-slate-900">

                        Notifikasi

                    </h3>

                </div>

                <div class="flex flex-col items-center justify-center px-6 py-12">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-10 w-10 text-slate-300"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .53-.21 1.04-.59 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/>

                    </svg>

                    <p class="mt-4 text-sm text-slate-500">

                        Belum ada notifikasi.

                    </p>

                </div>

            `;

            return;

        }

        let html = `

            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                <h3 class="text-base font-semibold text-slate-900">

                    Notifikasi

                </h3>

                <span class="rounded-full bg-[#2563EB] px-2 py-1 text-xs font-semibold text-white">

                    ${notifications.length}

                </span>

            </div>

            <div class="max-h-[420px] overflow-y-auto">

        `;

        notifications.forEach(function (notification) {

            html += `

                <button
                    type="button"
                    class="flex w-full items-start gap-3 border-b border-slate-100 px-5 py-4 text-left transition hover:bg-slate-50">

                    <div class="mt-1 flex h-9 w-9 items-center justify-center rounded-full bg-blue-100">

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-[#2563EB]"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2">

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .53-.21 1.04-.59 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/>

                        </svg>

                    </div>

                    <div class="flex-1">

                        <div class="font-medium text-slate-900">

                            ${notification.type ?? 'Notifikasi'}

                        </div>

                        <div class="mt-1 text-sm text-slate-500">

                            ${notification.data?.message ?? '-'}

                        </div>

                        <div class="mt-2 text-xs text-slate-400">

                            ${notification.created_at ?? ''}

                        </div>

                    </div>

                </button>

            `;

        });

        html += `</div>`;

        dropdown.innerHTML = html;

    }

    /*
    |--------------------------------------------------------------------------
    | Toggle Dropdown
    |--------------------------------------------------------------------------
    */

    button.addEventListener('click', function (event) {

        event.stopPropagation();

        renderNotifications();

        dropdown.classList.toggle('hidden');

    });

    /*
    |--------------------------------------------------------------------------
    | Close Outside
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {

        if (

            !dropdown.contains(event.target) &&

            !button.contains(event.target)

        ) {

            dropdown.classList.add('hidden');

        }

    });

});

</script>