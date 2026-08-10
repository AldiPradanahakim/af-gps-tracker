<script>

window.VehicleMessages = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    loaded: false,

    currentUrl: '/notifications?per_page=15',

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.bindPagination();

    },

    /*
    |--------------------------------------------------------------------------
    | Activate (lazy load - hanya sekali saat tab "Pesan" pertama dibuka)
    |--------------------------------------------------------------------------
    */

    activate() {

        if (this.loaded) {

            return;
        }

        this.loaded = true;

        this.load();

    },

    /*
    |--------------------------------------------------------------------------
    | Icon Per Tipe
    |--------------------------------------------------------------------------
    */

    getIconConfig(type) {

        const config = {

            geofence_enter: { icon: 'fa-solid fa-right-to-bracket', color: 'text-green-600 bg-green-100' },
            geofence_exit: { icon: 'fa-solid fa-right-from-bracket', color: 'text-red-600 bg-red-100' },
            stop: { icon: 'fa-solid fa-pause', color: 'text-amber-600 bg-amber-100' },
            device_online: { icon: 'fa-solid fa-plug-circle-check', color: 'text-emerald-600 bg-emerald-100' },
            device_offline: { icon: 'fa-solid fa-plug-circle-xmark', color: 'text-slate-600 bg-slate-200' },

        };

        return config[type] ?? { icon: 'fa-regular fa-bell', color: 'text-sky-600 bg-sky-100' };

    },

    formatTime(value) {

        if (!value) {

            return '-';
        }

        return new Date(value).toLocaleString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Jakarta',
        }) + ' WIB';

    },

    /*
    |--------------------------------------------------------------------------
    | Render
    |--------------------------------------------------------------------------
    */

    escapeAttribute(value = '') {

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');

    },

    renderItem(notification) {

        const iconConfig = this.getIconConfig(notification.type);

        const title = notification.data?.title ?? 'Notifikasi';
        const message = notification.data?.message ?? '-';
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
                    data-message-type="${notification.type ?? ''}"
                    data-message-title="${this.escapeAttribute(title)}"
                    data-message-body="${this.escapeAttribute(message)}"
                    data-message-address="${this.escapeAttribute(address)}"
                    data-message-time="${notification.created_at ?? ''}"
                ` : ''}
            >

                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl ${iconConfig.color}">
                    <i class="${iconConfig.icon} text-[15px]"></i>
                </div>

                <div class="min-w-0 flex-1">

                    <div class="flex items-center justify-between gap-2">
                        <div class="truncate text-sm font-semibold text-slate-900">${title}</div>
                        ${isRead
                            ? '<span class="shrink-0 text-[10px] font-medium text-slate-400">Dibaca</span>'
                            : '<span class="shrink-0 h-2 w-2 rounded-full bg-blue-500"></span>'
                        }
                    </div>

                    <div class="mt-1 text-sm leading-relaxed text-slate-600">${message}</div>

                    ${address ? `<div class="mt-1 text-xs text-slate-400">${address}</div>` : ''}

                    <div class="mt-2 flex items-center justify-between gap-2">

                        <span class="text-xs text-slate-400">${this.formatTime(notification.created_at)}</span>

                        ${hasLocation ? `
                            <span class="text-[11px] font-semibold text-blue-600">
                                <i class="fa-solid fa-location-dot mr-1"></i>Lihat titik di peta
                            </span>
                        ` : ''}

                    </div>

                </div>

            </div>
        `;

    },

    renderEmpty() {

        return `
            <div class="flex flex-col items-center justify-center px-6 py-14 text-center">
                <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <i class="fa-regular fa-bell text-[22px]"></i>
                </div>
                <div class="text-sm font-semibold text-slate-700">Belum ada pesan</div>
            </div>
        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Load (dari database lewat GET /notifications)
    |--------------------------------------------------------------------------
    */

    async load(url) {

        url = url ?? this.currentUrl;

        const list = document.getElementById('vehicleMessagesList');

        const prevBtn = document.getElementById('vehicleMessagesPrev');
        const nextBtn = document.getElementById('vehicleMessagesNext');
        const pageInfo = document.getElementById('vehicleMessagesPageInfo');

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
                ? items.map(notification => this.renderItem(notification)).join('')
                : this.renderEmpty();

            this.currentUrl = url;

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
                    <button id="vehicleMessagesRetry" class="mt-3 rounded-xl bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200">Coba Lagi</button>
                </div>
            `;

            document.getElementById('vehicleMessagesRetry')?.addEventListener('click', () => this.load());

            window.GPSTracker?.showToast?.('error', 'Gagal', 'Pesan gagal dimuat.');

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    bindPagination() {

        document.getElementById('vehicleMessagesPrev')?.addEventListener('click', (event) => {

            const url = event.currentTarget.dataset.url;

            if (url) {

                this.load(url);
            }

        });

        document.getElementById('vehicleMessagesNext')?.addEventListener('click', (event) => {

            const url = event.currentTarget.dataset.url;

            if (url) {

                this.load(url);
            }

        });

        document.getElementById('vehicleMessagesList')?.addEventListener('click', (event) => {

            const item = event.target.closest('[data-message-lat]');

            if (!item) {

                return;
            }

            const latitude = Number(item.dataset.messageLat);
            const longitude = Number(item.dataset.messageLng);

            if (Number.isNaN(latitude) || Number.isNaN(longitude)) {

                return;
            }

            window.VehicleEventFocus?.focusOn({
                type: item.dataset.messageType,
                title: item.dataset.messageTitle,
                message: item.dataset.messageBody,
                address: item.dataset.messageAddress,
                time: item.dataset.messageTime,
                latitude,
                longitude,
            });

        });

    },

};

</script>
