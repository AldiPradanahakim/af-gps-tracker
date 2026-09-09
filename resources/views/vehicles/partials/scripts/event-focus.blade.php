<script>

window.VehicleEventFocus = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    event: null,

    marker: null,

    mapReady: false,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    |
    | Dipakai untuk 2 hal:
    | 1. Auto-fokus saat halaman dibuka lewat link notification
    |    (?event= - lihat VehicleService::show()).
    | 2. API publik focusOn() yang dipanggil dari mana saja (klik item
    |    Riwayat Perjalanan, klik "Lihat di Peta" pada Kendaraan
    |    Berhenti, dst) supaya semuanya konsisten satu komponen.
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.bindResetButton();

        document.addEventListener(

            'vehicle.map.ready',

            () => {

                this.mapReady = true;

                const initialEvent = state.eventFocus ?? null;

                if (initialEvent && initialEvent.latitude != null && initialEvent.longitude != null) {

                    this.focusOn(initialEvent);
                }

                /*
                | ?event= hanya dipakai untuk auto-fokus SEKALI saat link
                | notification dibuka. Dibuang dari URL supaya refresh
                | (F5) berikutnya tidak menampilkan popup yang sama lagi.
                | Untuk melihat lokasinya lagi, buka lewat Riwayat
                | Perjalanan atau kartu Pesan (VehicleMessages).
                */
                this.clearEventFromUrl();

            }

        );

    },

    /*
    |--------------------------------------------------------------------------
    | Buang ?event= dari URL Setelah Ditampilkan
    |--------------------------------------------------------------------------
    */

    clearEventFromUrl() {

        const url = new URL(window.location.href);

        if (!url.searchParams.has('event')) {

            return;
        }

        url.searchParams.delete('event');

        window.history.replaceState({}, '', url);

    },

    /*
    |--------------------------------------------------------------------------
    | Focus On (API publik)
    |--------------------------------------------------------------------------
    |
    | eventData: { type, title, message, address, latitude, longitude, time }
    |--------------------------------------------------------------------------
    */

    focusOn(eventData) {

        if (!eventData || eventData.latitude == null || eventData.longitude == null) {

            return;
        }

        this.event = eventData;

        if (this.mapReady) {

            this.showEvent();
        }

        document.getElementById('vehicleMapContainer')?.scrollIntoView({

            behavior: 'smooth',

            block: 'start',

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Icon Per Tipe Event
    |--------------------------------------------------------------------------
    */

    getIconConfig() {

        const config = {

            geofence_enter: { color: '#22c55e', icon: 'fa-solid fa-right-to-bracket' },
            geofence_exit: { color: '#ef4444', icon: 'fa-solid fa-right-from-bracket' },
            stop: { color: '#f97316', icon: 'fa-solid fa-pause' },
            travel_moving: { color: '#2563eb', icon: 'fa-solid fa-location-dot' },
            travel_stopped: { color: '#f97316', icon: 'fa-solid fa-pause' },

        };

        return config[this.event.type] ?? { color: '#2563eb', icon: 'fa-solid fa-location-dot' };

    },

    createIcon() {

        const { color, icon } = this.getIconConfig();

        return L.divIcon({

            html: `
                <div style="width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
                    <div style="width:46px;height:46px;border-radius:50%;background:${color};border:4px solid white;box-shadow:0 6px 16px rgba(15,23,42,0.35);display:flex;align-items:center;justify-content:center;">
                        <i class="${icon}" style="font-size:18px;color:white;"></i>
                    </div>
                </div>
            `,

            className: '',

            iconSize: [56, 56],

            iconAnchor: [28, 28],

            popupAnchor: [0, -28],

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Format Waktu (WIB)
    |--------------------------------------------------------------------------
    */

    formatTime() {

        if (!this.event.time) {

            return '-';
        }

        return new Date(this.event.time).toLocaleString('id-ID', {

            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            timeZone: (window.AppTimezone?.name ?? 'Asia/Jakarta'),

        }) + ' ' + (window.AppTimezone?.label ?? 'WIB');

    },

    /*
    |--------------------------------------------------------------------------
    | Tampilkan Event
    |--------------------------------------------------------------------------
    */

    showEvent() {

        this.marker = L.marker(

            [this.event.latitude, this.event.longitude],

            { icon: this.createIcon() }

        );

        this.marker.bindPopup(this.createPopup());

        VehicleMap.addOverlay('event-focus-marker', this.marker);

        VehicleMap.flyTo(this.event.latitude, this.event.longitude, 17);

        setTimeout(() => this.marker.openPopup(), 400);

        this.showBanner();

    },

    createPopup() {

        return `
            <div class="min-w-[220px] rounded-[16px] bg-white p-3">
                <div class="text-sm font-semibold text-slate-900">${this.event.title ?? '-'}</div>
                <div class="mt-1 text-xs leading-relaxed text-slate-600">${this.event.message ?? ''}</div>
                ${this.event.address ? `<div class="mt-2 text-xs text-slate-500">${this.event.address}</div>` : ''}
                <div class="mt-2 text-[11px] text-slate-400">${this.formatTime()}</div>
            </div>
        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Banner
    |--------------------------------------------------------------------------
    */

    showBanner() {

        const banner = document.getElementById('vehicleEventFocusBanner');

        const title = document.getElementById('vehicleEventFocusTitle');

        const time = document.getElementById('vehicleEventFocusTime');

        if (!banner) {

            return;
        }

        if (title) {

            title.textContent = 'Menampilkan lokasi: ' + (this.event.title ?? '-');
        }

        if (time) {

            time.textContent = this.formatTime();
        }

        banner.classList.remove('hidden');

    },

    hideBanner() {

        document.getElementById('vehicleEventFocusBanner')?.classList.add('hidden');

    },

    /*
    |--------------------------------------------------------------------------
    | Reset Ke Lokasi Terkini
    |--------------------------------------------------------------------------
    */

    bindResetButton() {

        const button = document.getElementById('vehicleEventFocusReset');

        if (!button) {

            return;
        }

        button.addEventListener('click', () => {

            this.reset();

        });

    },

    reset() {

        if (this.marker) {

            VehicleMap.removeOverlay('event-focus-marker');

            this.marker = null;
        }

        this.hideBanner();

        if (window.VehicleMarker?.focus) {

            window.VehicleMarker.focus();
        }

    },

};

</script>
