<script>

window.VehicleNotification = {

    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    state: null,

    notifications: [],

    dropdown: null,

    button: null,

    badge: null,

    opened: false,

    channel: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.notifications = Array.isArray(state.notifications)
            ? [...state.notifications]
            : [];

        this.dropdown = document.getElementById('vehicleNotificationDropdown');

        this.button = document.getElementById('vehicleNotificationButton');

        this.badge = document.getElementById('vehicleNotificationBadge');

        this.bindButton();

        this.bindDropdown();

        this.bindOutsideClick();

        this.bindKeyboard();

        this.render();

        this.subscribeRealtime();

    },

    /*
    |--------------------------------------------------------------------------
    | Data Helper
    |--------------------------------------------------------------------------
    */

    getTitle(notification) {

        return notification?.data?.title ?? 'Notifikasi';

    },

    getMessage(notification) {

        return notification?.data?.message ?? '-';

    },

    getVehicleName(notification) {

        return notification?.data?.vehicle_name ?? '-';

    },

    getTime(notification) {

        if (!notification?.created_at) {

            return '-';

        }

        const createdAt = new Date(notification.created_at);

        const diff = Math.floor((Date.now() - createdAt) / 1000);

        if (diff < 60) {

            return 'Baru saja';

        }

        if (diff < 3600) {

            return Math.floor(diff / 60) + ' menit lalu';

        }

        if (diff < 86400) {

            return Math.floor(diff / 3600) + ' jam lalu';

        }

        return createdAt.toLocaleDateString('id-ID', {

            day: '2-digit',

            month: 'short',

            year: 'numeric',

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    updateBadge() {

        if (!this.badge) {

            return;
        }

        const total = this.notifications.length;

        if (total <= 0) {

            this.badge.classList.add('hidden');

            this.badge.textContent = '';

            return;
        }

        this.badge.classList.remove('hidden');

        this.badge.textContent = total > 99 ? '99+' : total;

    },

    renderEmpty() {

        return `
            <div class="flex flex-col items-center justify-center px-6 py-10 text-center">
                <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <i class="fa-regular fa-bell text-[18px]"></i>
                </div>
                <div class="text-sm font-semibold text-slate-700">
                    Belum ada notifikasi
                </div>
                <div class="mt-1 text-xs leading-relaxed text-slate-500">
                    Aktivitas kendaraan ini akan muncul di sini.
                </div>
            </div>
        `;

    },

    renderItem(notification) {

        return `
            <button
                type="button"
                class="vehicle-notification-item flex w-full items-start gap-3 border-b border-slate-100 px-4 py-3 text-left transition duration-200 hover:bg-slate-50"
                data-id="${notification.id}">

                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-600">
                    <i class="fa-regular fa-bell text-[13px]"></i>
                </div>

                <div class="min-w-0 flex-1">

                    <div class="truncate text-sm font-semibold text-slate-900">
                        ${this.getTitle(notification)}
                    </div>

                    <div class="mt-1 text-xs font-medium text-blue-600">
                        ${this.getVehicleName(notification)}
                    </div>

                    <div class="mt-1 text-xs leading-relaxed text-slate-600">
                        ${this.getMessage(notification)}
                    </div>

                    <div class="mt-2 text-[11px] text-slate-400">
                        ${this.getTime(notification)}
                    </div>

                </div>

            </button>
        `;

    },

    render() {

        if (!this.dropdown) {

            return;
        }

        const header = `
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-900">Notifikasi</h3>
                <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">
                    ${this.notifications.length}
                </span>
            </div>
        `;

        const list = this.notifications.length

            ? `<div class="max-h-[420px] overflow-y-auto">${this.notifications
                .map(notification => this.renderItem(notification))
                .join('')}</div>`

            : this.renderEmpty();

        const footer = `
            <div class="space-y-2 border-t border-slate-200 p-3">
                ${this.notifications.length ? `
                <button
                    id="vehicleMarkAllNotificationRead"
                    type="button"
                    class="w-full rounded-xl bg-slate-100 px-4 py-2.5 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-200">
                    Tandai semua telah dibaca
                </button>
                ` : ''}
                <button
                    id="vehicleGoToMessages"
                    type="button"
                    class="block w-full rounded-xl px-4 py-2.5 text-center text-sm font-medium text-blue-600 transition duration-200 hover:bg-blue-50">
                    Lihat Semua Riwayat
                </button>
            </div>
        `;

        this.dropdown.innerHTML = header + list + footer;

        this.updateBadge();

    },

    /*
    |--------------------------------------------------------------------------
    | Dropdown
    |--------------------------------------------------------------------------
    */

    open() {

        if (!this.dropdown) {

            return;
        }

        this.dropdown.classList.remove('hidden');

        this.opened = true;

    },

    close() {

        if (!this.dropdown) {

            return;
        }

        this.dropdown.classList.add('hidden');

        this.opened = false;

    },

    toggle() {

        this.opened

            ? this.close()

            : this.open();

    },

    /*
    |--------------------------------------------------------------------------
    | Collection
    |--------------------------------------------------------------------------
    */

    add(notification) {

        if (!notification) {

            return;
        }

        const exists = this.notifications.some(

            item => String(item.id) === String(notification.id)

        );

        if (exists) {

            return;
        }

        this.notifications.unshift(notification);

        this.render();

    },

    remove(id) {

        this.notifications = this.notifications.filter(

            notification => String(notification.id) !== String(id)

        );

        this.render();

    },

    /*
    |--------------------------------------------------------------------------
    | Mark As Read
    |--------------------------------------------------------------------------
    |
    | Notification yang sudah dibaca langsung dihapus dari daftar
    | (tidak ditampilkan lagi setelah refresh/login/masuk halaman).
    |
    */

    markAsRead(id) {

        const notification = this.notifications.find(

            item => String(item.id) === String(id)

        );

        if (!notification) {

            return;
        }

        this.remove(id);

        VehicleApi.markNotificationRead(id).then(result => {

            if (!result || !result.success) {

                throw new Error('Mark as read failed.');
            }

        }).catch(() => {

            this.add(notification);

            window.GPSTracker?.showToast?.(

                'error',

                'Gagal',

                'Notifikasi gagal ditandai telah dibaca.'

            );

        });

    },

    markAllAsRead() {

        if (!this.notifications.length) {

            return;
        }

        const previous = [...this.notifications];

        this.notifications = [];

        this.render();

        VehicleApi.markAllNotificationsRead().then(result => {

            if (!result || !result.success) {

                throw new Error('Mark all as read failed.');
            }

        }).catch(() => {

            this.notifications = previous;

            this.render();

            window.GPSTracker?.showToast?.(

                'error',

                'Gagal',

                'Notifikasi gagal ditandai telah dibaca.'

            );

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Realtime (Pusher / Laravel Echo)
    |--------------------------------------------------------------------------
    */

    subscribeRealtime() {

        if (typeof window.Echo === 'undefined') {

            return;
        }

        if (!this.state?.userId || this.channel) {

            return;
        }

        this.channel = window.Echo

            .private(`user.${this.state.userId}`)

            .listen('.notification.created', payload => {

                this.add(payload);

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindButton() {

        if (!this.button || this.button.dataset.notificationBound === 'true') {

            return;
        }

        this.button.dataset.notificationBound = 'true';

        this.button.addEventListener('click', event => {

            event.preventDefault();

            event.stopPropagation();

            this.toggle();

        });

    },

    bindDropdown() {

        if (!this.dropdown || this.dropdown.dataset.notificationBound === 'true') {

            return;
        }

        this.dropdown.dataset.notificationBound = 'true';

        this.dropdown.addEventListener('click', event => {

            const markAll = event.target.closest('#vehicleMarkAllNotificationRead');

            if (markAll) {

                event.preventDefault();

                this.markAllAsRead();

                return;
            }

            const goToMessages = event.target.closest('#vehicleGoToMessages');

            if (goToMessages) {

                event.preventDefault();

                this.close();

                document.querySelector('.vehicle-menu-btn[data-section="messages"]')?.click();

                return;
            }

            const item = event.target.closest('.vehicle-notification-item');

            if (item) {

                this.markAsRead(item.dataset.id);

            }

        });

    },

    bindOutsideClick() {

        document.addEventListener('click', event => {

            if (!this.dropdown || !this.button) {

                return;
            }

            if (

                this.dropdown.contains(event.target) ||

                this.button.contains(event.target)

            ) {

                return;
            }

            this.close();

        });

    },

    bindKeyboard() {

        document.addEventListener('keydown', event => {

            if (event.key === 'Escape') {

                this.close();
            }

        });

    },

};

</script>
