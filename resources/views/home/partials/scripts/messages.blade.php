<script>

document.addEventListener('gpstracker:map-ready', () => {

    GPSTracker.messages ??= {

        loaded: false,

        currentUrl: '/notifications?per_page=15',

    };

    /*
    |--------------------------------------------------------------------------
    | Icon Per Tipe
    |--------------------------------------------------------------------------
    */

    GPSTracker.getMessageIconConfig = function (type) {

        const config = {

            geofence_enter: { icon: 'fa-solid fa-right-to-bracket', color: 'text-green-600 bg-green-100' },
            geofence_exit: { icon: 'fa-solid fa-right-from-bracket', color: 'text-red-600 bg-red-100' },
            stop: { icon: 'fa-solid fa-pause', color: 'text-amber-600 bg-amber-100' },
            device_online: { icon: 'fa-solid fa-plug-circle-check', color: 'text-emerald-600 bg-emerald-100' },
            device_offline: { icon: 'fa-solid fa-plug-circle-xmark', color: 'text-slate-600 bg-slate-200' },
            overspeed: { icon: 'fa-solid fa-gauge-high', color: 'text-orange-600 bg-orange-100' },
            low_battery: { icon: 'fa-solid fa-battery-quarter', color: 'text-red-600 bg-red-100' },

        };

        return config[type] ?? { icon: 'fa-regular fa-bell', color: 'text-sky-600 bg-sky-100' };

    };

    GPSTracker.formatMessageTime = function (value) {

        if (!value) {

            return '-';
        }

        return new Date(value).toLocaleString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta',
        }) + ' WIB';

    };

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    GPSTracker.escapeMessageAttribute = function (value = '') {

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

    };

    GPSTracker.renderMessageItem = function (notification) {

        const iconConfig = this.getMessageIconConfig(notification.type);

        const title = notification.data?.title ?? 'Notifikasi';
        const message = notification.data?.message ?? '-';
        const vehicleName = notification.data?.vehicle_name ?? notification.device?.vehicle?.vehicle_name ?? '-';
        const address = notification.data?.search_address ?? '';
        const isRead = Boolean(notification.read_at);

        const location = notification.data?.location ?? null;

        const hasLocation =
            location &&
            location.lat != null &&
            location.lng != null;

        return `
            <div
                class="flex items-start gap-4 px-6 py-4 ${isRead ? '' : 'bg-blue-50/40'} ${hasLocation ? 'cursor-pointer transition hover:bg-slate-50' : ''}"
                ${hasLocation ? `
                    data-message-lat="${location.lat}"
                    data-message-lng="${location.lng}"
                    data-message-title="${this.escapeMessageAttribute(title)}"
                ` : ''}
            >

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl ${iconConfig.color}">
                    <i class="${iconConfig.icon} text-[15px]"></i>
                </div>

                <div class="min-w-0 flex-1">

                    <div class="flex items-center justify-between gap-2">
                        <div class="truncate text-sm font-semibold text-slate-900">${this.escapeMessageAttribute(title)}</div>
                        ${isRead
                            ? '<span class="shrink-0 text-[10px] font-medium text-slate-400">Dibaca</span>'
                            : '<span class="shrink-0 h-2 w-2 rounded-full bg-blue-500"></span>'
                        }
                    </div>

                    <div class="mt-1 text-xs font-medium text-blue-600">${this.escapeMessageAttribute(vehicleName)}</div>

                    <div class="mt-1 text-sm leading-relaxed text-slate-600">${this.escapeMessageAttribute(message)}</div>

                    ${address ? `<div class="mt-1 text-xs text-slate-400">${this.escapeMessageAttribute(address)}</div>` : ''}

                    <div class="mt-2 flex items-center justify-between gap-2">

                        <span class="text-xs text-slate-400">${this.formatMessageTime(notification.created_at)}</span>

                        ${hasLocation ? `
                            <span class="text-[11px] font-semibold text-blue-600">
                                <i class="fa-solid fa-location-dot mr-1"></i>Lihat titik di peta
                            </span>
                        ` : ''}

                    </div>

                </div>

            </div>
        `;

    };

    GPSTracker.renderMessageEmpty = function () {

        return `
            <div class="flex flex-col items-center justify-center px-6 py-14 text-center">
                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <i class="fa-regular fa-bell text-[22px]"></i>
                </div>
                <div class="text-sm font-semibold text-slate-700">Belum ada pesan</div>
            </div>
        `;

    };

    /*
    |--------------------------------------------------------------------------
    | Load (dari database lewat GET /notifications)
    |--------------------------------------------------------------------------
    */

    GPSTracker.loadMessages = async function (url) {

        url = url ?? this.messages.currentUrl;

        const list = document.getElementById('messagesModalList');

        const prevBtn = document.getElementById('messagesModalPrev');
        const nextBtn = document.getElementById('messagesModalNext');
        const pageInfo = document.getElementById('messagesModalPageInfo');

        if (!list) {

            return;
        }

        list.innerHTML = '<div class="px-6 py-14 text-center text-slate-500">Memuat pesan...</div>';

        try {

            const response = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (!response.ok) {

                throw new Error('Request failed');
            }

            const result = await response.json();

            if (!result.success) {

                throw new Error('Request unsuccessful');
            }

            const paginator = result.data;

            const items = paginator.data ?? [];

            list.innerHTML = items.length
                ? items.map(notification => this.renderMessageItem(notification)).join('')
                : this.renderMessageEmpty();

            this.messages.currentUrl = url;

            if (prevBtn) {

                prevBtn.disabled = !paginator.prev_page_url;
                prevBtn.dataset.url = paginator.prev_page_url ?? '';
            }

            if (nextBtn) {

                nextBtn.disabled = !paginator.next_page_url;
                nextBtn.dataset.url = paginator.next_page_url ?? '';
            }

            if (pageInfo) {

                pageInfo.textContent = `Halaman ${paginator.current_page} dari ${paginator.last_page}`;
            }

        } catch (error) {

            list.innerHTML = `
                <div class="flex flex-col items-center justify-center px-6 py-14 text-center">
                    <div class="text-sm font-semibold text-slate-700">Gagal memuat pesan.</div>
                    <button id="messagesModalRetry" class="mt-3 rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">Coba Lagi</button>
                </div>
            `;

            document.getElementById('messagesModalRetry')?.addEventListener('click', () => this.loadMessages());

            this.showToast?.('error', 'Gagal', 'Pesan gagal dimuat.');

        }

    };

    /*
    |--------------------------------------------------------------------------
    | Modal Open/Close
    |--------------------------------------------------------------------------
    */

    GPSTracker.openMessagesModal = function () {

        const modal = document.getElementById('messagesModal');

        if (!modal) {

            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if (!this.messages.loaded) {

            this.messages.loaded = true;

            this.loadMessages();
        }

    };

    GPSTracker.closeMessagesModal = function () {

        const modal = document.getElementById('messagesModal');

        if (!modal) {

            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');

    };

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    document.getElementById('messagesModalClose')?.addEventListener('click', () => {

        GPSTracker.closeMessagesModal();

    });

    document.getElementById('messagesModal')?.addEventListener('click', event => {

        if (event.target.id === 'messagesModal') {

            GPSTracker.closeMessagesModal();
        }

    });

    document.getElementById('messagesModalPrev')?.addEventListener('click', event => {

        const url = event.currentTarget.dataset.url;

        if (url) {

            GPSTracker.loadMessages(url);
        }

    });

    document.getElementById('messagesModalNext')?.addEventListener('click', event => {

        const url = event.currentTarget.dataset.url;

        if (url) {

            GPSTracker.loadMessages(url);
        }

    });

    /*
    |--------------------------------------------------------------------------
    | Klik Kartu Riwayat -> Tampilkan Titik di Peta
    |--------------------------------------------------------------------------
    */

    document.getElementById('messagesModalList')?.addEventListener('click', event => {

        const item = event.target.closest('[data-message-lat]');

        if (!item) {

            return;
        }

        const lat = Number(item.dataset.messageLat);
        const lng = Number(item.dataset.messageLng);

        if (Number.isNaN(lat) || Number.isNaN(lng)) {

            return;
        }

        GPSTracker.closeMessagesModal();

        GPSTracker.flyToLocation(lat, lng, 17);

        GPSTracker.showTemporaryMarker(lat, lng, item.dataset.messageTitle ?? '');

    });

});

</script>
