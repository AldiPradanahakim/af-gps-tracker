<script>

window.VehicleStop = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    stops: [],

    loaded: false,

    /*
    |--------------------------------------------------------------------------
    | Batas Render
    |--------------------------------------------------------------------------
    |
    | Sama seperti VehicleHistory.MAX_TIMELINE_ROWS - tanpa batas ini,
    | perangkat yang sudah lama berhenti berkali-kali (riwayat berhenti
    | sekarang selalu tercatat, tidak lagi tergantung notifikasi
    | diaktifkan) bisa mengirim ribuan baris yang seluruhnya dirender
    | sekaligus ke tabel dan membuat tab browser tidak responsif.
    |--------------------------------------------------------------------------
    */

    MAX_TABLE_ROWS: 200,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.bindSettingEvents();

        this.bindHistoryEvents();

    },

    /*
    |--------------------------------------------------------------------------
    | Activate (lazy load)
    |--------------------------------------------------------------------------
    | Dipanggil saat tab "Kendaraan Berhenti" pertama kali dibuka, supaya
    | data tidak diambil dari server sebelum tab-nya benar-benar dilihat.
    |--------------------------------------------------------------------------
    */

    activate() {

        if (this.loaded) {

            return;

        }

        this.loadHistory();

    },

    /*
    |--------------------------------------------------------------------------
    | Setting: Bind Events
    |--------------------------------------------------------------------------
    */

    bindSettingEvents() {

        document.getElementById('editStopDetectionButton')
            ?.addEventListener('click', () => this.showEdit());

        document.getElementById('cancelStopDetectionEdit')
            ?.addEventListener('click', () => this.hideEdit());

        document.getElementById('stopDetectionForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                await this.saveSetting();

            });

    },

    showEdit() {

        document.getElementById('stopDetectionReadContainer')?.classList.add('hidden');

        document.getElementById('stopDetectionStatusCard')?.classList.add('hidden');

        document.getElementById('stopDetectionEditContainer')?.classList.remove('hidden');

    },

    hideEdit() {

        document.getElementById('stopDetectionEditContainer')?.classList.add('hidden');

        document.getElementById('stopDetectionReadContainer')?.classList.remove('hidden');

        document.getElementById('stopDetectionStatusCard')?.classList.remove('hidden');

    },

    async saveSetting() {

        const button = document.getElementById('saveStopDetection');

        try {

            if (button) {
                button.disabled = true;
            }

            const payload = {

                enabled: document.getElementById('stopDetectionEnabled').checked,

                stop_minutes: document.getElementById('stopMinutes').value,

                email_notification: document.getElementById('emailNotification').checked,

                whatsapp_notification: document.getElementById('whatsappNotification').checked,

            };

            const response = await fetch(

                `/vehicles/${this.state.device.id}/stop-setting`,

                {

                    method: 'PATCH',

                    headers: {

                        'Content-Type': 'application/json',

                        'Accept': 'application/json',

                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content,

                        'X-Requested-With': 'XMLHttpRequest',

                    },

                    body: JSON.stringify(payload),

                }

            );

            const json = await response.json();

            if (!response.ok || !json.success) {

                const message = json.errors
                    ? Object.values(json.errors).flat().join('\n')
                    : (json.message ?? 'Gagal menyimpan pengaturan.');

                GPSTracker.showToast('error', 'Gagal', message);

                return;

            }

            GPSTracker.showToast(
                'success',
                'Berhasil',
                json.message ?? 'Pengaturan Deteksi Berhenti berhasil disimpan.'
            );

            /*
            |--------------------------------------------------------------------------
            | Pastikan setelah reload tetap membuka section Stop Detection.
            |--------------------------------------------------------------------------
            */

            history.replaceState(null, '', '#stop');

            setTimeout(() => window.location.reload(), 800);

        } catch (error) {

            console.error(error);

            GPSTracker.showToast('error', 'Gagal', 'Terjadi kesalahan pada server.');

        } finally {

            if (button) {
                button.disabled = false;
            }

        }

    },

    /*
    |--------------------------------------------------------------------------
    | History: Bind Events
    |--------------------------------------------------------------------------
    */

    bindHistoryEvents() {

        const refreshButton = document.getElementById('refreshStopHistory');

        refreshButton?.addEventListener('click', async () => {

            /*
            |--------------------------------------------------------------
            | Tombol dikunci + ikon berputar selama request berlangsung,
            | lalu ditutup toast. Tanpa umpan balik ini tombol terasa
            | "tidak jalan" walau datanya sebenarnya sudah diperbarui.
            |--------------------------------------------------------------
            */

            this.setRefreshLoading(refreshButton, true);

            GPSLoading.show(
                'Memuat riwayat berhenti',
                'Mengambil data dari server…'
            );

            try {

                await this.loadHistory();

                GPSTracker.showToast(
                    'success',
                    'Diperbarui',
                    'Riwayat kendaraan berhenti berhasil dimuat ulang.'
                );

            }

            finally {

                GPSLoading.hide();

                this.setRefreshLoading(refreshButton, false);

            }

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Refresh Loading State
    |--------------------------------------------------------------------------
    */

    setRefreshLoading(button, isLoading) {

        if (!button) {

            return;

        }

        const icon = button.querySelector('i');

        button.disabled = isLoading;

        button.classList.toggle('opacity-60', isLoading);

        button.classList.toggle('cursor-not-allowed', isLoading);

        icon?.classList.toggle('fa-spin', isLoading);

    },

    async loadHistory() {

        const table = document.getElementById('stopHistoryTable');

        if (!table) {
            return;
        }

        this.loaded = true;

        try {

            const response = await fetch(

                `/vehicles/${this.state.device.id}/stop`,

                {

                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },

                }

            );

            const json = await response.json();

            if (!json.success) {

                this.stops = [];

                this.renderHistory();

                return;

            }

            this.stops = json.data ?? [];

            this.renderHistory();

        } catch (error) {

            console.error('[VehicleStop]', error);

            this.stops = [];

            this.renderHistory();

        }

    },

    renderHistory() {

        const table = document.getElementById('stopHistoryTable');

        const empty = document.getElementById('stopHistoryEmpty');

        if (!table) {
            return;
        }

        if (!this.stops.length) {

            table.innerHTML = '';

            empty?.classList.remove('hidden');

            return;

        }

        empty?.classList.add('hidden');

        const visible = this.stops.slice(0, this.MAX_TABLE_ROWS);

        table.innerHTML = visible.map(stop => this.row(stop)).join('');

        const notice = document.getElementById('stopHistoryTruncatedNotice');

        if (notice) {

            notice.classList.toggle('hidden', this.stops.length <= visible.length);

            notice.textContent = `Menampilkan ${visible.length} dari ${this.stops.length} riwayat berhenti terbaru. Gunakan Unduh PDF untuk data lengkap.`;

        }

        this.bindRowActions();

    },

    row(stop) {

        const isOngoing = !stop.ended_at;

        const duration = this.formatDuration(stop.duration_seconds ?? 0);

        return `
            <tr class="hover:bg-slate-50">
                <td class="px-6 py-4 text-[13px] text-slate-700">${stop.started_at ?? '-'}</td>
                <td class="px-6 py-4 text-[13px] text-slate-700">${stop.ended_at ?? '-'}</td>
                <td class="px-6 py-4 text-[13px] font-semibold text-slate-900">${duration}</td>
                <td class="px-6 py-4 text-[13px] text-slate-700">${stop.address ?? '-'}</td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center gap-2 rounded-full ${isOngoing ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600'} px-3 py-1 text-[11px] font-semibold">
                        <span class="h-2 w-2 rounded-full ${isOngoing ? 'bg-amber-500' : 'bg-emerald-500'}"></span>
                        ${isOngoing ? 'Sedang Berhenti' : 'Selesai'}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <button
                        type="button"
                        class="stop-focus rounded-lg border border-slate-300 px-4 py-2 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-100"
                        data-id="${stop.id}"
                    >
                        Lihat di Peta
                    </button>
                </td>
            </tr>
        `;

    },

    bindRowActions() {

        document.querySelectorAll('.stop-focus').forEach(button => {

            button.addEventListener('click', () => {

                const stop = this.stops.find(
                    item => String(item.id) === String(button.dataset.id)
                );

                if (!stop || stop.lat == null || stop.lng == null) {
                    return;
                }

                window.VehicleEventFocus?.focusOn({

                    type: 'stop',
                    title: 'Kendaraan Berhenti',
                    message: `Berhenti selama ${this.formatDuration(stop.duration_seconds ?? 0)}`,
                    address: stop.address,
                    latitude: stop.lat,
                    longitude: stop.lng,
                    time: stop.started_at,

                });

            });

        });

    },

    formatDuration(seconds) {

        seconds = Number(seconds) || 0;

        const hours = Math.floor(seconds / 3600);

        const minutes = Math.floor((seconds % 3600) / 60);

        if (hours > 0) {
            return `${hours}j ${minutes}m`;
        }

        return `${minutes}m`;

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        this.stops = [];

        this.state = null;

    },

};

</script>
