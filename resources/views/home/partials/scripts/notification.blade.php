<script>

document.addEventListener('gpstracker:map-ready', () => {

    /*
    |--------------------------------------------------------------------------
    | Notification State
    |--------------------------------------------------------------------------
    */

    GPSTracker.notifications ??= [];

    GPSTracker.notificationConfig = {

        dropdownId : 'notificationDropdown',

        buttonId : 'notificationButton',

        badgeId : 'notificationBadge',

        maxItems : 50,

        maxBadge : 99,

    };

    GPSTracker.notification = {

        dropdown : null,

        button : null,

        badge : null,

        opened : false,

        loading : false,

    };

    /*
    |--------------------------------------------------------------------------
    | Escape Helper
    |--------------------------------------------------------------------------
    */

    GPSTracker.escapeNotificationHtml = function (value) {

        if (value === null || value === undefined) {

            return '';

        }

        return String(value).replace(/[&<>"']/g, function (char) {

            return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[char];

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Collection Helper
    |--------------------------------------------------------------------------
    */

    GPSTracker.getNotifications = function () {

        return this.notifications;

    };

    GPSTracker.getNotification = function (id) {

        return this.notifications.find(

            notification => String(notification.id) === String(id)

        ) ?? null;

    };

    GPSTracker.hasNotification = function (id) {

        return this.notifications.some(

            notification => String(notification.id) === String(id)

        );

    };

    GPSTracker.addNotification = function (notification) {

        if (!notification) {

            return;

        }

        if (this.hasNotification(notification.id)) {

            return this.updateNotification(notification);

        }

        this.notifications.unshift(notification);

        if (this.notifications.length > this.notificationConfig.maxItems) {

            this.notifications = this.notifications.slice(

                0,

                this.notificationConfig.maxItems

            );

        }

        return notification;

    };

    GPSTracker.updateNotification = function (notification) {

        const index = this.notifications.findIndex(

            item => String(item.id) === String(notification.id)

        );

        if (index === -1) {

            this.notifications.unshift(notification);

        } else {

            this.notifications[index] = {

                ...this.notifications[index],

                ...notification,

            };

        }

        return notification;

    };

    GPSTracker.removeNotification = function (id) {

        this.notifications = this.notifications.filter(

            notification => String(notification.id) !== String(id)

        );

    };

    GPSTracker.clearNotifications = function () {

        this.notifications = [];

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Counter
    |--------------------------------------------------------------------------
    */

    GPSTracker.getNotificationCount = function () {

        return this.notifications.length;

    };

    GPSTracker.getUnreadNotificationCount = function () {

        return this.notifications.filter(

            notification => !notification.read_at

        ).length;

    };

    /*
    |--------------------------------------------------------------------------
    | Notification DOM
    |--------------------------------------------------------------------------
    */

    GPSTracker.getNotificationDropdown = function () {

        if (!this.notification.dropdown) {

            this.notification.dropdown = document.getElementById(

                this.notificationConfig.dropdownId

            );

        }

        return this.notification.dropdown;

    };

    GPSTracker.getNotificationButton = function () {

        if (!this.notification.button) {

            this.notification.button = document.getElementById(

                this.notificationConfig.buttonId

            );

        }

        return this.notification.button;

    };

    GPSTracker.getNotificationBadge = function () {

        if (!this.notification.badge) {

            this.notification.badge = document.getElementById(

                this.notificationConfig.badgeId

            );

        }

        return this.notification.badge;

    };
        /*
    |--------------------------------------------------------------------------
    | Notification Badge
    |--------------------------------------------------------------------------
    */

    GPSTracker.updateNotificationBadge = function () {

        const badge = this.getNotificationBadge();

        if (!badge) {

            return;

        }

        const total = this.getUnreadNotificationCount();

        if (total <= 0) {

            badge.classList.add('hidden');

            badge.textContent = '';

            return;

        }

        badge.classList.remove('hidden');

        badge.textContent = total > this.notificationConfig.maxBadge
            ? this.notificationConfig.maxBadge + '+'
            : total;

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Helper
    |--------------------------------------------------------------------------
    */

    GPSTracker.getNotificationType = function (notification) {

        return (
            notification.type ??
            notification.data?.type ??
            'notification'
        );

    };

    GPSTracker.getNotificationTitle = function (notification) {

        if (notification.title) {

            return notification.title;

        }

        if (notification.data?.title) {

            return notification.data.title;

        }

        switch (this.getNotificationType(notification)) {

            case 'device_online':

                return 'Perangkat Terhubung';

            case 'device_offline':

                return 'Perangkat Terputus';

            case 'geofence_enter':

                return 'Masuk Geofence';

            case 'geofence_exit':

                return 'Keluar Geofence';

            case 'overspeed':

                return 'Kecepatan Berlebih';

            case 'low_battery':

                return 'Baterai Perangkat Lemah';

            case 'stop':
            case 'stop_detection':

                return 'Kendaraan Berhenti';

            case 'home_location':

                return 'Lokasi Rumah';

            default:

                return 'Notifikasi';

        }

    };

    GPSTracker.getNotificationMessage = function (notification) {

        return (
            notification.message ??
            notification.data?.message ??
            '-'
        );

    };

    GPSTracker.getNotificationVehicle = function (notification) {

        return (
            notification.vehicle_name ??
            notification.data?.vehicle_name ??
            '-'
        );

    };

    GPSTracker.getNotificationDevice = function (notification) {

        return (
            notification.device_id ??
            notification.data?.device_id ??
            null
        );

    };

    GPSTracker.isNotificationRead = function (notification) {

        return Boolean(notification.read_at);

    };

    GPSTracker.getNotificationIcon = function (notification) {

        switch (this.getNotificationType(notification)) {

            case 'device_online':

                return 'text-emerald-600 bg-emerald-100';

            case 'device_offline':

                return 'text-slate-600 bg-slate-200';

            case 'geofence_enter':

                return 'text-green-600 bg-green-100';

            case 'geofence_exit':

                return 'text-red-600 bg-red-100';

            case 'overspeed':

                return 'text-orange-600 bg-orange-100';

            case 'low_battery':

                return 'text-red-600 bg-red-100';

            case 'stop':
            case 'stop_detection':

                return 'text-amber-600 bg-amber-100';

            case 'home_location':

                return 'text-blue-600 bg-blue-100';

            default:

                return 'text-sky-600 bg-sky-100';

        }

    };

    GPSTracker.getNotificationTime = function (notification) {

        if (!notification.created_at) {

            return '-';

        }

        const createdAt = new Date(notification.created_at);

        const now = new Date();

        const diff = Math.floor((now - createdAt) / 1000);

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

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Header
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderNotificationHeader = function () {

        return `

            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                <div>

                    <h3 class="text-sm font-semibold text-slate-900">

                        Notifikasi

                    </h3>

                    <p class="mt-1 text-xs text-slate-500">

                        Aktivitas kendaraan terbaru

                    </p>

                </div>

                <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">

                    ${this.getUnreadNotificationCount()}

                </span>

            </div>

        `;

    };
        /*
    |--------------------------------------------------------------------------
    | Notification Empty State
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderNotificationEmpty = function () {

        return `

            <div class="flex flex-col items-center justify-center px-8 py-12">

                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-7 w-7 text-slate-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="1.8">

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .53-.21 1.04-.59 1.4L4 17h5"/>

                    </svg>

                </div>

                <div class="text-sm font-semibold text-slate-700">

                    Belum ada notifikasi

                </div>

                <div class="mt-2 text-center text-xs leading-5 text-slate-500">

                    Semua aktivitas kendaraan akan muncul
                    secara realtime pada halaman ini.

                </div>

            </div>

        `;

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Footer
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderNotificationFooter = function () {

        return `

            <div class="space-y-2 border-t border-slate-200 p-3">

                ${this.notifications.length ? `
                <button
                    id="markAllNotificationRead"
                    type="button"
                    class="w-full rounded-xl bg-slate-100 px-4 py-3 text-sm font-medium text-slate-700 transition duration-200 hover:bg-slate-200">

                    Tandai semua telah dibaca

                </button>
                ` : ''}

                <button
                    id="goToMessagesModal"
                    type="button"
                    class="block w-full rounded-xl px-4 py-3 text-center text-sm font-medium text-blue-600 transition duration-200 hover:bg-blue-50">

                    Lihat Semua Riwayat

                </button>

            </div>

        `;

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Item
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderNotificationItem = function (notification) {

        const unread = this.isNotificationRead(notification)
            ? ''
            : `
                <span class="ml-2 h-2.5 w-2.5 rounded-full bg-blue-500"></span>
            `;

        return `

            <button
                type="button"
                class="notification-item flex w-full items-start gap-4 border-b border-slate-100 px-5 py-4 text-left transition duration-200 hover:bg-slate-50"
                data-id="${notification.id}"
                data-device="${this.getNotificationDevice(notification)}">

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl ${this.getNotificationIcon(notification)}">

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
                            d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2c0 .53-.21 1.04-.59 1.4L4 17h5"/>

                    </svg>

                </div>

                <div class="min-w-0 flex-1">

                    <div class="flex items-center justify-between">

                        <div class="truncate text-sm font-semibold text-slate-900">

                            ${this.escapeNotificationHtml(this.getNotificationTitle(notification))}

                        </div>

                        ${unread}

                    </div>

                    <div class="mt-1 text-xs font-medium text-blue-600">

                        ${this.escapeNotificationHtml(this.getNotificationVehicle(notification))}

                    </div>

                    <div class="mt-2 text-sm leading-relaxed text-slate-600">

                        ${this.escapeNotificationHtml(this.getNotificationMessage(notification))}

                    </div>

                    <div class="mt-3 flex items-center justify-between">

                        <span class="text-xs text-slate-400">

                            ${this.getNotificationTime(notification)}

                        </span>

                    </div>

                </div>

            </button>

        `;

    };

    /*
    |--------------------------------------------------------------------------
    | Notification List
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderNotificationList = function () {

        if (!this.notifications.length) {

            return this.renderNotificationEmpty();

        }

        return `

            <div
                id="notificationList"
                class="max-h-[30rem] overflow-y-auto">

                ${this.notifications
                    .map(notification => this.renderNotificationItem(notification))
                    .join('')}

            </div>

        `;

    };

    /*
    |--------------------------------------------------------------------------
    | Render Notification
    |--------------------------------------------------------------------------
    */

    GPSTracker.renderNotifications = function () {

        const dropdown = this.getNotificationDropdown();

        if (!dropdown) {

            return;

        }

        dropdown.innerHTML =

            this.renderNotificationHeader() +

            this.renderNotificationList() +

            this.renderNotificationFooter();

        this.updateNotificationBadge();

    };
        /*
    |--------------------------------------------------------------------------
    | Notification Dropdown
    |--------------------------------------------------------------------------
    */

    GPSTracker.openNotificationDropdown = function () {

        const dropdown = this.getNotificationDropdown();

        if (!dropdown) {

            return;

        }

        dropdown.classList.remove('hidden');

        this.notification.opened = true;

    };

    GPSTracker.closeNotificationDropdown = function () {

        const dropdown = this.getNotificationDropdown();

        if (!dropdown) {

            return;

        }

        dropdown.classList.add('hidden');

        this.notification.opened = false;

    };

    GPSTracker.toggleNotificationDropdown = function () {

        if (this.notification.opened) {

            this.closeNotificationDropdown();

            return;

        }

        this.openNotificationDropdown();

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Read
    |--------------------------------------------------------------------------
    */

    GPSTracker.notificationRequest = function (

        url

    ) {

        return fetch(url, {

            method: 'PATCH',

            headers: {

                'Accept': 'application/json',

                'X-Requested-With': 'XMLHttpRequest',

                'X-CSRF-TOKEN': document.querySelector(

                    'meta[name="csrf-token"]'

                )?.content,

            },

        }).then(response => {

            if (!response.ok) {

                throw new Error('Notification request failed.');

            }

            return response.json();

        });

    };

    GPSTracker.notificationErrorToast = function () {

        if (typeof this.showToast === 'function') {

            this.showToast(

                'error',

                'Gagal',

                'Notifikasi gagal ditandai telah dibaca.'

            );

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Notification sudah dibaca tidak ditampilkan lagi (dihapus dari
    | daftar), bukan hanya ditandai secara visual.
    |--------------------------------------------------------------------------
    */

    GPSTracker.markNotificationAsRead = function (

        id

    ) {

        const notification = this.getNotification(

            id

        );

        if (!notification) {

            return;

        }

        this.removeNotification(id);

        this.renderNotifications();

        this.notificationRequest(`/notifications/${id}/read`).catch(() => {

            this.addNotification(notification);

            this.renderNotifications();

            this.notificationErrorToast();

        });

    };

    GPSTracker.markAllNotificationAsRead = function () {

        if (!this.notifications.length) {

            return;

        }

        const previous = [...this.notifications];

        this.clearNotifications();

        this.renderNotifications();

        this.notificationRequest('/notifications/mark-all-read').catch(() => {

            this.notifications = previous;

            this.renderNotifications();

            this.notificationErrorToast();

        });

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Click Handler
    |--------------------------------------------------------------------------
    */

    GPSTracker.handleNotificationClick = function (

        id

    ) {

        const notification = this.getNotification(

            id

        );

        if (!notification) {

            return;

        }

        this.markNotificationAsRead(

            id

        );

        this.closeNotificationDropdown();

        const deviceId = this.getNotificationDevice(

            notification

        );

        if (

            deviceId &&

            typeof this.focusVehicle === 'function'

        ) {

            this.focusVehicle(

                deviceId

            );

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Button Event
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindNotificationButton = function () {

        const button = this.getNotificationButton();

        if (!button) {

            return;

        }

        if (button.dataset.notificationBound === 'true') {

            return;

        }

        button.dataset.notificationBound = 'true';

        button.addEventListener(

            'click',

            event => {

                event.preventDefault();

                event.stopPropagation();

                this.toggleNotificationDropdown();

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Dropdown Event
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindNotificationDropdown = function () {

        const dropdown = this.getNotificationDropdown();

        if (!dropdown) {

            return;

        }

        if (dropdown.dataset.notificationBound === 'true') {

            return;

        }

        dropdown.dataset.notificationBound = 'true';

        dropdown.addEventListener(

            'click',

            event => {

                const item = event.target.closest(

                    '.notification-item'

                );

                if (!item) {

                    return;

                }

                this.handleNotificationClick(

                    item.dataset.id

                );

            }

        );

        dropdown.addEventListener(

            'click',

            event => {

                const markAll = event.target.closest(

                    '#markAllNotificationRead'

                );

                if (!markAll) {

                    return;

                }

                event.preventDefault();

                this.markAllNotificationAsRead();

            }

        );

        dropdown.addEventListener(

            'click',

            event => {

                const goToMessages = event.target.closest(

                    '#goToMessagesModal'

                );

                if (!goToMessages) {

                    return;

                }

                event.preventDefault();

                this.closeNotificationDropdown();

                this.openMessagesModal?.();

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Outside Click
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindNotificationOutsideClick = function () {

        if (

            this.notification.outsideClickBound

        ) {

            return;

        }

        this.notification.outsideClickBound = true;

        document.addEventListener(

            'click',

            event => {

                const dropdown = this.getNotificationDropdown();

                const button = this.getNotificationButton();

                if (

                    !dropdown ||

                    !button

                ) {

                    return;

                }

                if (

                    dropdown.contains(

                        event.target

                    ) ||

                    button.contains(

                        event.target

                    )

                ) {

                    return;

                }

                this.closeNotificationDropdown();

            }

        );

    };
        /*
    |--------------------------------------------------------------------------
    | Keyboard Event
    |--------------------------------------------------------------------------
    */

    GPSTracker.bindNotificationKeyboard = function () {

        if (

            this.notification.keyboardBound

        ) {

            return;

        }

        this.notification.keyboardBound = true;

        document.addEventListener(

            'keydown',

            event => {

                if (

                    event.key !== 'Escape'

                ) {

                    return;

                }

                this.closeNotificationDropdown();

            }

        );

    };

    /*
    |--------------------------------------------------------------------------
    | Notification State Helper
    |--------------------------------------------------------------------------
    */

    GPSTracker.isNotificationOpened = function () {

        return this.notification.opened;

    };

    GPSTracker.setNotificationLoading = function (state = true) {

        this.notification.loading = state;

    };

    GPSTracker.isNotificationLoading = function () {

        return this.notification.loading;

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Refresh
    |--------------------------------------------------------------------------
    */

    GPSTracker.refreshNotifications = function () {

        this.renderNotifications();

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Destroy
    |--------------------------------------------------------------------------
    */

    GPSTracker.destroyNotifications = function () {

        this.notification.dropdown = null;

        this.notification.button = null;

        this.notification.badge = null;

        this.notification.opened = false;

        this.notification.loading = false;

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Initialize
    |--------------------------------------------------------------------------
    */

    GPSTracker.initNotifications = function () {

        this.renderNotifications();

        this.bindNotificationButton();

        this.bindNotificationDropdown();

        this.bindNotificationOutsideClick();

        this.bindNotificationKeyboard();

        this.updateNotificationBadge();

    };

    /*
    |--------------------------------------------------------------------------
    | Notification Ready
    |--------------------------------------------------------------------------
    */

    GPSTracker.initNotifications();

    /*
    |--------------------------------------------------------------------------
    | End Notification Module
    |--------------------------------------------------------------------------
    */

});

</script>