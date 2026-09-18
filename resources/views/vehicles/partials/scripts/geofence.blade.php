<script>

window.VehicleGeofence = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    layers: {},

    drawing: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    escapeHtml(value) {

        if (value === null || value === undefined) {
            return '';
        }

        return String(value).replace(/[&<>"']/g, function (char) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
            })[char];
        });

    },

    init(state) {

        this.state = state;

        this.bindEvents();

        this.renderMap();

    },

    /*
    |--------------------------------------------------------------------------
    | Map Visibility (checkbox)
    |--------------------------------------------------------------------------
    */

    isTypeVisible(type) {

        const ids = {
            radius: 'toggleRadiusMap',
            administrative: 'toggleAdministrativeMap',
            custom: 'togglePolygonMap',
        };

        const checkbox = document.getElementById(ids[type]);

        return checkbox ? checkbox.checked : true;

    },

    /*
    |--------------------------------------------------------------------------
    | Render Map
    |--------------------------------------------------------------------------
    */

    clearLayers() {

        Object.keys(this.layers).forEach(id => {

            VehicleMap.removeOverlay(`geofence-${id}`);

        });

        this.layers = {};

    },

    renderMap() {

        this.clearLayers();

        (this.state.geofences ?? []).forEach(geofence => {

            this.renderOne(geofence);

        });

    },

    renderOne(geofence) {

        const layer = geofence.type === 'radius'
            ? this.buildRadiusLayer(geofence)
            : this.buildGeoJsonLayer(geofence);

        if (!layer) {
            return;
        }

        this.layers[geofence.id] = layer;

        if (this.isTypeVisible(geofence.type)) {

            VehicleMap.addOverlay(`geofence-${geofence.id}`, layer);

        }

    },

    buildRadiusLayer(geofence) {

        const center = geofence.config?.center;

        if (
            !center ||
            center.lat == null ||
            center.lng == null ||
            geofence.config.radius == null
        ) {
            return null;
        }

        const circle = L.circle(
            [Number(center.lat), Number(center.lng)],
            {
                radius: Number(geofence.config.radius),
                color: '#2563eb',
                weight: 2,
                fillColor: '#3b82f6',
                fillOpacity: 0.15,
            }
        );

        circle.bindPopup(
            `<strong>${this.escapeHtml(geofence.name)}</strong><br>Radius: ${Number(geofence.config.radius).toLocaleString()} m`
        );

        return circle;

    },

    buildGeoJsonLayer(geofence) {

        const geometry = geofence.config?.geometry;

        if (!geometry) {
            return null;
        }

        const isAdministrative = geofence.type === 'administrative';

        const layer = L.geoJSON(geometry, {
            style: {
                color: isAdministrative ? '#16a34a' : '#7c3aed',
                weight: 2,
                fillColor: isAdministrative ? '#22c55e' : '#8b5cf6',
                fillOpacity: 0.15,
            },
        });

        layer.bindPopup(`<strong>${this.escapeHtml(geofence.name)}</strong>`);

        return layer;

    },

    refreshLayerVisibility() {

        (this.state.geofences ?? []).forEach(geofence => {

            const layer = this.layers[geofence.id];

            if (!layer) {
                return;
            }

            if (this.isTypeVisible(geofence.type)) {
                VehicleMap.addOverlay(`geofence-${geofence.id}`, layer);
            } else {
                VehicleMap.removeOverlay(`geofence-${geofence.id}`);
            }

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Bind Events
    |--------------------------------------------------------------------------
    */

    bindEvents() {

        this.bindCheckboxes();

        this.bindRadius();

        this.bindAdministrative();

        this.bindPolygon();

        this.bindDelete();

        this.bindNotificationEvents();

        this.bindRepeatSetting();

        this.bindHistory();

    },

    /*
    |--------------------------------------------------------------------------
    | Pengingat "Masih di Luar Area"
    |--------------------------------------------------------------------------
    */

    bindRepeatSetting() {

        const toggle = document.getElementById('geofenceRepeatEnabled');

        const options = document.getElementById('geofenceRepeatOptions');

        toggle?.addEventListener('change', () => {

            options?.classList.toggle('hidden', !toggle.checked);

            /*
            | Mematikan pengingat langsung disimpan supaya pengguna tidak
            | perlu menekan Simpan hanya untuk berhenti menerima pesan.
            | Menyalakannya menunggu Simpan, karena jedanya ikut dikirim -
            | jadi begitu dinyalakan kolom jeda dibuka supaya pengguna bisa
            | langsung mengisinya.
            */

            if (toggle.checked) {

                this.setRepeatEditing(true);

                return;

            }

            this.setRepeatEditing(false);

            this.saveRepeatSetting();

        });

        document.getElementById('editGeofenceRepeatSetting')
            ?.addEventListener('click', () => this.setRepeatEditing(true));

        document.getElementById('cancelGeofenceRepeatSetting')
            ?.addEventListener('click', () => {

                const minutesInput = document.getElementById('geofenceRepeatMinutes');

                /*
                | Kembalikan ke nilai yang benar-benar tersimpan, bukan
                | sekadar mengunci kolomnya - kalau tidak, angka hasil
                | ketikan yang dibatalkan tetap terpampang seolah berlaku.
                */

                if (minutesInput) {

                    minutesInput.value = this.repeatMinutesSaved ?? minutesInput.value;

                }

                this.setRepeatEditing(false);

            });

        document.getElementById('saveGeofenceRepeatSetting')
            ?.addEventListener('click', () => this.saveRepeatSetting());

        /*
        | Nilai awal dari server dianggap sudah tersimpan: kolom terkunci,
        | hanya tombol "Ubah" yang terlihat.
        */

        this.repeatMinutesSaved =
            document.getElementById('geofenceRepeatMinutes')?.value ?? null;

        this.setRepeatEditing(false);

    },

    /*
    |--------------------------------------------------------------------------
    | Kunci / Buka Kolom Jeda Pengingat
    |--------------------------------------------------------------------------
    |
    | Mode terkunci = nilai yang sedang berlaku, tidak bisa diketik.
    | Mode edit     = kolom terbuka, tombol Simpan & Batal muncul.
    */

    setRepeatEditing(editing) {

        const minutesInput = document.getElementById('geofenceRepeatMinutes');

        const editButton = document.getElementById('editGeofenceRepeatSetting');

        const saveButton = document.getElementById('saveGeofenceRepeatSetting');

        const cancelButton = document.getElementById('cancelGeofenceRepeatSetting');

        if (minutesInput) {

            minutesInput.readOnly = !editing;

        }

        editButton?.classList.toggle('hidden', editing);

        saveButton?.classList.toggle('hidden', !editing);

        cancelButton?.classList.toggle('hidden', !editing);

        if (editing) {

            minutesInput?.focus();

            minutesInput?.select();

        }

    },

    async saveRepeatSetting() {

        const toggle = document.getElementById('geofenceRepeatEnabled');

        const minutesInput = document.getElementById('geofenceRepeatMinutes');

        const button = document.getElementById('saveGeofenceRepeatSetting');

        try {

            if (button) {
                button.disabled = true;
            }

            const response = await VehicleApi.updateGeofenceSetting({
                repeat_enabled: Boolean(toggle?.checked),
                repeat_minutes: Number(minutesInput?.value ?? 15),
            });

            if (!response.success) {

                const message = response.errors
                    ? Object.values(response.errors).flat().join('\n')
                    : (response.message ?? 'Gagal menyimpan pengaturan pengingat.');

                this.toast('error', 'Gagal', message);

                return;

            }

            if (minutesInput && response.data?.repeat_minutes) {

                minutesInput.value = response.data.repeat_minutes;

            }

            /*
            | Server yang menentukan nilai final (dibatasi 5-180 menit di
            | GeofenceEventService), jadi yang diingat sebagai "tersimpan"
            | adalah jawabannya - bukan angka yang diketik pengguna.
            */

            this.repeatMinutesSaved = minutesInput?.value ?? this.repeatMinutesSaved;

            this.setRepeatEditing(false);

            this.toast(
                'success',
                'Berhasil',
                response.message ?? 'Pengaturan pengingat berhasil disimpan.'
            );

        } catch (error) {

            console.error(error);

            this.toast('error', 'Gagal', 'Terjadi kesalahan pada server.');

        } finally {

            if (button) {
                button.disabled = false;
            }

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Riwayat Masuk/Keluar Geofence
    |--------------------------------------------------------------------------
    */

    MAX_HISTORY_ROWS: 200,

    historyLoaded: false,

    bindHistory() {

        document.getElementById('geofenceHistoryRefreshButton')
            ?.addEventListener('click', () => this.loadHistory());

        document.getElementById('geofenceHistoryApplyButton')
            ?.addEventListener('click', () => this.loadHistory());

        document.getElementById('geofenceHistoryResetButton')
            ?.addEventListener('click', () => {

                const start = document.getElementById('geofenceHistoryStartDate');
                const end = document.getElementById('geofenceHistoryEndDate');
                const event = document.getElementById('geofenceHistoryEvent');

                if (start) start.value = '';
                if (end) end.value = '';
                if (event) event.value = '';

                this.loadHistory();

            });

    },

    /*
    | Dipanggil saat section Geofence pertama kali dibuka, supaya riwayat
    | tidak diambil dari server sebelum tabnya benar-benar dilihat.
    */

    activate() {

        if (this.historyLoaded) {

            return;

        }

        this.historyLoaded = true;

        this.loadHistory();

    },

    async loadHistory() {

        try {

            const response = await VehicleApi.geofenceHistory({
                start_date: document.getElementById('geofenceHistoryStartDate')?.value,
                end_date: document.getElementById('geofenceHistoryEndDate')?.value,
                event: document.getElementById('geofenceHistoryEvent')?.value,
            });

            this.renderHistory(
                response?.data ?? [],
                response?.summary ?? {}
            );

        } catch (error) {

            console.error(error);

            this.renderHistory([], {});

        }

    },

    renderHistory(items, summary) {

        const list = document.getElementById('geofenceHistoryList');

        const empty = document.getElementById('geofenceHistoryEmpty');

        const setText = (id, value) => {
            const el = document.getElementById(id);
            if (el) el.textContent = value ?? 0;
        };

        setText('geofenceHistoryTotal', summary.total);
        setText('geofenceHistoryTotalExit', summary.total_exit);
        setText('geofenceHistoryTotalEnter', summary.total_enter);
        setText('geofenceHistoryToday', summary.today);

        if (!list) {
            return;
        }

        if (!items.length) {

            list.innerHTML = '';

            empty?.classList.remove('hidden');

            return;

        }

        empty?.classList.add('hidden');

        list.innerHTML = items
            .slice(0, this.MAX_HISTORY_ROWS)
            .map(item => this.historyRow(item))
            .join('');

        list.querySelectorAll('.geofence-history-focus').forEach(button => {

            button.addEventListener('click', () => {

                const lat = Number(button.dataset.lat);
                const lng = Number(button.dataset.lng);

                if (Number.isFinite(lat) && Number.isFinite(lng)) {

                    VehicleMap.flyTo(lat, lng);

                }

            });

        });

    },

    historyRow(item) {

        const isExit = item.event === 'exit';

        const hasPoint = item.lat !== null && item.lng !== null;

        /*
        | Durasi pada baris "Keluar" berarti lama kendaraan berada DI DALAM
        | area sebelum keluar; pada baris "Masuk" berarti lama di LUAR area.
        */

        const durationLabel = item.duration_label && item.duration_label !== '-'
            ? `${isExit ? 'Di dalam area selama' : 'Di luar area selama'} ${this.escapeHtml(item.duration_label)}`
            : '';

        return `
            <div class="flex flex-wrap items-start gap-4 px-6 py-4 transition hover:bg-slate-50">

                <span class="mt-0.5 inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full ${isExit ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600'}">
                    <i class="fa-solid ${isExit ? 'fa-right-from-bracket' : 'fa-right-to-bracket'} text-[0.8125rem]"></i>
                </span>

                <div class="min-w-[12.5rem] flex-1">

                    <div class="flex flex-wrap items-center gap-2">

                        <span class="inline-flex items-center rounded-full ${isExit ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600'} px-3 py-1 text-[0.6875rem] font-semibold">
                            ${isExit ? 'Keluar' : 'Masuk'}
                        </span>

                        <span class="text-[0.8125rem] font-semibold text-slate-900">
                            ${this.escapeHtml(item.geofence_name)}
                        </span>

                        <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[0.6875rem] font-medium text-slate-600">
                            ${this.escapeHtml(item.geofence_type_label)}
                        </span>

                    </div>

                    <p class="mt-1.5 text-[0.75rem] text-slate-500">
                        ${this.escapeHtml(item.address ?? '-')}
                    </p>

                    ${durationLabel ? `<p class="mt-1 text-[0.75rem] font-medium text-slate-600">${durationLabel}</p>` : ''}

                </div>

                <div class="text-right">

                    <p class="text-[0.75rem] font-medium text-slate-700">
                        ${this.escapeHtml(item.occurred_at ?? '-')}
                    </p>

                    ${hasPoint ? `
                        <button
                            type="button"
                            class="geofence-history-focus mt-2 rounded-lg border border-slate-300 px-3 py-1.5 text-[0.6875rem] font-semibold text-slate-700 transition hover:bg-slate-100"
                            data-lat="${item.lat}"
                            data-lng="${item.lng}"
                        >
                            Lihat di Peta
                        </button>
                    ` : ''}

                </div>

            </div>
        `;

    },

    /*
    |--------------------------------------------------------------------------
    | Notifikasi Geofence (auto-save saat toggle diubah)
    |--------------------------------------------------------------------------
    */

    bindNotificationEvents() {

        document.getElementById('geofenceEmailNotification')
            ?.addEventListener('change', () => this.saveNotificationSetting());

        document.getElementById('geofenceWhatsappNotification')
            ?.addEventListener('change', () => this.saveNotificationSetting());

    },

    async saveNotificationSetting() {

        const emailInput = document.getElementById('geofenceEmailNotification');

        const whatsappInput = document.getElementById('geofenceWhatsappNotification');

        try {

            const response = await fetch(

                `/vehicles/${this.state.device.id}/notification-setting`,

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

                    body: JSON.stringify({

                        email_notification: Boolean(emailInput?.checked),

                        whatsapp_notification: Boolean(whatsappInput?.checked),

                    }),

                }

            );

            const json = await response.json();

            if (!response.ok || !json.success) {

                const message = json.errors
                    ? Object.values(json.errors).flat().join('\n')
                    : (json.message ?? 'Gagal menyimpan pengaturan notifikasi.');

                this.toast('error', 'Gagal', message);

                return;

            }

            this.toast(
                'success',
                'Berhasil',
                json.message ?? 'Pengaturan notifikasi berhasil disimpan.'
            );

        } catch (error) {

            console.error(error);

            this.toast('error', 'Gagal', 'Terjadi kesalahan pada server.');

        }

    },

    bindCheckboxes() {

        const all = document.getElementById('toggleAllGeofenceMap');
        const radius = document.getElementById('toggleRadiusMap');
        const administrative = document.getElementById('toggleAdministrativeMap');
        const polygon = document.getElementById('togglePolygonMap');

        const syncAll = () => {

            if (!all) return;

            all.checked =
                Boolean(radius?.checked) &&
                Boolean(administrative?.checked) &&
                Boolean(polygon?.checked);

        };

        [radius, administrative, polygon].forEach(checkbox => {

            checkbox?.addEventListener('change', () => {
                this.refreshLayerVisibility();
                syncAll();
            });

        });

        all?.addEventListener('change', () => {

            const checked = all.checked;

            if (radius) radius.checked = checked;
            if (administrative) administrative.checked = checked;
            if (polygon) polygon.checked = checked;

            this.refreshLayerVisibility();

        });

    },

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    showModal(id) {
        document.getElementById(id)?.classList.remove('hidden');
        document.getElementById(id)?.classList.add('flex');
    },

    hideModal(id) {
        document.getElementById(id)?.classList.add('hidden');
        document.getElementById(id)?.classList.remove('flex');
    },

    toast(type, title, message) {
        GPSTracker.showToast(type, title, message);
    },

    reloadAfter(delay = 800) {

        /*
        |--------------------------------------------------------------------------
        | Pastikan setelah reload halaman tetap membuka section Geofence,
        | bukan balik ke "Informasi Kendaraan" (default section).
        |--------------------------------------------------------------------------
        */

        history.replaceState(null, '', '#geofence');

        setTimeout(() => window.location.reload(), delay);
    },

    async submitCreate(payload, label) {

        try {

            const response = await VehicleApi.storeGeofence(payload);

            if (!response.success) {

                const message = response.errors
                    ? Object.values(response.errors).flat().join('\n')
                    : (response.message ?? 'Gagal menyimpan geofence.');

                this.toast('error', 'Gagal', message);

                return;

            }

            this.toast(
                'success',
                'Berhasil',
                response.message ?? `${label} geofence berhasil ditambahkan.`
            );

            this.reloadAfter();

        } catch (error) {

            console.error(error);

            this.toast('error', 'Gagal', 'Terjadi kesalahan pada server.');

        }

    },

    async submitUpdate(id, payload, label) {

        try {

            const response = await VehicleApi.updateGeofence(id, payload);

            if (!response.success) {

                const message = response.errors
                    ? Object.values(response.errors).flat().join('\n')
                    : (response.message ?? 'Gagal memperbarui geofence.');

                this.toast('error', 'Gagal', message);

                return;

            }

            this.toast(
                'success',
                'Berhasil',
                response.message ?? `${label} geofence berhasil diperbarui.`
            );

            this.reloadAfter();

        } catch (error) {

            console.error(error);

            this.toast('error', 'Gagal', 'Terjadi kesalahan pada server.');

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Radius
    |--------------------------------------------------------------------------
    */

    bindRadius() {

        document.getElementById('createRadiusButton')
            ?.addEventListener('click', () => this.showModal('createRadiusModal'));

        document.getElementById('closeCreateRadiusModal')
            ?.addEventListener('click', () => this.hideModal('createRadiusModal'));

        document.getElementById('cancelCreateRadius')
            ?.addEventListener('click', () => this.hideModal('createRadiusModal'));

        document.getElementById('createRadiusForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const name = document.getElementById('createRadiusName').value.trim();

                if (!name) {
                    this.toast('error', 'Peringatan', 'Nama Radius wajib diisi.');
                    return;
                }

                await this.submitCreate({
                    device_id: String(this.state.device.id),
                    type: 'radius',
                    name,
                    radius_source: document.getElementById('createRadiusSource').value,
                    radius: document.getElementById('createRadiusValue').value,
                    radius_unit: 'meter',
                    status: document.getElementById('createRadiusStatus').value,
                }, 'Radius');

            });

        document.getElementById('editRadiusButton')
            ?.addEventListener('click', () => {
                document.getElementById('radiusReadContainer')?.classList.add('hidden');
                document.getElementById('radiusEditContainer')?.classList.remove('hidden');
            });

        document.getElementById('cancelRadiusEdit')
            ?.addEventListener('click', () => {
                document.getElementById('radiusEditContainer')?.classList.add('hidden');
                document.getElementById('radiusReadContainer')?.classList.remove('hidden');
            });

        document.getElementById('editRadiusForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const id = document.getElementById('editRadiusId').value;

                const source = document.getElementById('editRadiusSource').value;

                /*
                |--------------------------------------------------------------------------
                | Titik pusat TIDAK lagi dihitung di sini. Sumbernya (Home
                | Location / GPS terakhir) dikirim apa adanya, dan backend
                | yang mengambil koordinat terbaru dari database.
                |
                | Sebelumnya koordinat diambil dari state di browser, sehingga
                | mengedit radius tepat setelah mengubah Lokasi Rumah (tanpa
                | reload) salah membaca state dan memunculkan peringatan
                | "Lokasi Rumah belum diatur".
                |--------------------------------------------------------------------------
                */

                const payload = {
                    name: document.getElementById('editRadiusName').value,
                    status: document.getElementById('editRadiusStatus').value,
                    radius: document.getElementById('editRadiusValue').value,
                    radius_source: source,
                };

                /*
                |--------------------------------------------------------------------------
                | "Pertahankan Titik Saat Ini" sengaja tidak mengirim koordinat
                | apa pun. Titik yang tersimpan di server adalah yang paling
                | benar -- input hidden di halaman ini bisa basi kalau Home
                | Location sudah digeser sejak halaman dimuat.
                |--------------------------------------------------------------------------
                */

                await this.submitUpdate(id, payload, 'Radius');

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Administrative
    |--------------------------------------------------------------------------
    */

    bindAdministrative() {

        document.getElementById('createAdministrativeButton')
            ?.addEventListener('click', () => this.showModal('createAdministrativeModal'));

        document.getElementById('closeCreateAdministrativeModal')
            ?.addEventListener('click', () => this.hideModal('createAdministrativeModal'));

        document.getElementById('cancelCreateAdministrative')
            ?.addEventListener('click', () => this.hideModal('createAdministrativeModal'));

        this.bindAdministrativeSearch(
            'createAdministrativeSearch',
            'createAdministrativeResult',
            (item, geometry) => {

                document.getElementById('createAdministrativeGeojson').value = JSON.stringify(geometry);
                document.getElementById('createAdministrativeDisplayName').value = item.name;
                document.getElementById('createAdministrativeAreaType').value = item.level;

                const selected = document.getElementById('createAdministrativeSelected');

                if (selected) {
                    selected.textContent = `Terpilih: ${item.name} (${item.type})`;
                    selected.classList.remove('hidden');
                }

            }
        );

        document.getElementById('createAdministrativeForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const name = document.getElementById('createAdministrativeName').value.trim();

                const geojson = document.getElementById('createAdministrativeGeojson').value;

                if (!name) {
                    this.toast('error', 'Peringatan', 'Nama geofence wajib diisi.');
                    return;
                }

                if (!geojson) {
                    this.toast('error', 'Peringatan', 'Silakan pilih wilayah terlebih dahulu.');
                    return;
                }

                await this.submitCreate({
                    device_id: String(this.state.device.id),
                    type: 'administrative',
                    name,
                    status: document.getElementById('createAdministrativeStatus').value,
                    geojson,
                    display_name: document.getElementById('createAdministrativeDisplayName').value,
                    administrative_type: document.getElementById('createAdministrativeAreaType').value,
                }, 'Administratif');

            });

        document.getElementById('editAdministrativeButton')
            ?.addEventListener('click', () => {
                document.getElementById('administrativeReadContainer')?.classList.add('hidden');
                document.getElementById('administrativeEditContainer')?.classList.remove('hidden');
            });

        document.getElementById('cancelAdministrativeEdit')
            ?.addEventListener('click', () => {
                document.getElementById('administrativeEditContainer')?.classList.add('hidden');
                document.getElementById('administrativeReadContainer')?.classList.remove('hidden');
            });

        this.bindAdministrativeSearch(
            'editAdministrativeSearch',
            'editAdministrativeResult',
            (item, geometry) => {

                document.getElementById('editAdministrativeGeojson').value = JSON.stringify(geometry);
                document.getElementById('editAdministrativeDisplayName').value = item.name;
                document.getElementById('editAdministrativeAreaType').value = item.level;

            }
        );

        document.getElementById('editAdministrativeForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const id = document.getElementById('editAdministrativeId').value;

                const payload = {
                    name: document.getElementById('editAdministrativeName').value,
                    status: document.getElementById('editAdministrativeStatus').value,
                };

                const geojson = document.getElementById('editAdministrativeGeojson').value;

                if (geojson) {
                    payload.geojson = geojson;
                    payload.display_name = document.getElementById('editAdministrativeDisplayName').value;
                    payload.administrative_type = document.getElementById('editAdministrativeAreaType').value;
                }

                await this.submitUpdate(id, payload, 'Administratif');

            });

    },

    bindAdministrativeSearch(inputId, resultId, onSelect) {

        const input = document.getElementById(inputId);
        const result = document.getElementById(resultId);

        if (!input || !result) {
            return;
        }

        let debounce = null;

        let cachedDistricts = null;

        let cachedProvinces = null;

        let cachedRegencies = null;

        input.addEventListener('input', () => {

            clearTimeout(debounce);

            const keyword = input.value.trim();

            if (keyword.length < 3) {
                result.innerHTML = '';
                result.classList.add('hidden');
                return;
            }

            debounce = setTimeout(async () => {

                try {

                    const [districtResponse, provinceResponse, regencyResponse] = await Promise.all([
                        cachedDistricts ? null : VehicleApi.administrativeDistricts(),
                        cachedProvinces ? null : VehicleApi.administrativeProvinces(),
                        cachedRegencies ? null : VehicleApi.administrativeRegencies(),
                    ]);

                    if (!cachedDistricts) {
                        cachedDistricts = districtResponse.data ?? [];
                    }

                    if (!cachedProvinces) {
                        cachedProvinces = provinceResponse.data ?? [];
                    }

                    if (!cachedRegencies) {
                        cachedRegencies = regencyResponse.data ?? [];
                    }

                    const lower = keyword.toLowerCase();

                    const matches = [];

                    cachedProvinces.forEach(province => {

                        if (province.name.toLowerCase().includes(lower)) {

                            matches.push({
                                level: 'province',
                                code: province.code,
                                name: province.name,
                                type: 'Provinsi',
                            });

                        }

                    });

                    cachedRegencies.forEach(regency => {

                        if (regency.name.toLowerCase().includes(lower)) {

                            matches.push({
                                level: 'regency',
                                code: regency.code,
                                name: regency.name,
                                type: 'Kabupaten/Kota',
                            });

                        }

                    });

                    cachedDistricts.forEach(district => {

                        if (district.name.toLowerCase().includes(lower)) {

                            matches.push({
                                level: 'district',
                                code: district.code,
                                name: district.name,
                                type: 'Kecamatan',
                            });

                        }

                        (district.villages ?? []).forEach(village => {

                            if (village.name.toLowerCase().includes(lower)) {

                                matches.push({
                                    level: 'village',
                                    code: village.code,
                                    name: village.name,
                                    type: 'Kelurahan',
                                    district_name: district.name,
                                });

                            }

                        });

                    });

                    this.renderAdministrativeResult(
                        result,
                        matches,
                        item => this.selectAdministrativeItem(item, input, result, onSelect)
                    );

                } catch (error) {

                    console.error(error);

                }

            }, 400);

        });

    },

    renderAdministrativeResult(container, items, onClick) {

        container.innerHTML = '';

        if (!items.length) {

            container.innerHTML =
                '<div class="p-4 text-[0.8125rem] text-slate-500">Wilayah tidak ditemukan.</div>';

            container.classList.remove('hidden');

            return;

        }

        items.forEach(item => {

            const button = document.createElement('button');

            button.type = 'button';

            button.className =
                'block w-full border-b border-slate-100 px-4 py-3 text-left text-[0.8125rem] hover:bg-slate-50 last:border-0';

            button.innerHTML = `
                <div class="font-semibold text-slate-800">${this.escapeHtml(item.name)}</div>
                <div class="mt-1 text-[0.6875rem] text-slate-500">${this.escapeHtml(item.type)}${item.district_name ? ' &middot; ' + this.escapeHtml(item.district_name) : ''}</div>
            `;

            button.addEventListener('click', () => onClick(item));

            container.appendChild(button);

        });

        container.classList.remove('hidden');

    },

    async selectAdministrativeItem(item, input, result, onSelect) {

        try {

            const response = await VehicleApi.administrativeGeoJson(item.level, item.code);

            const geometry = response.data;

            input.value = item.name;

            result.classList.add('hidden');

            onSelect(item, geometry);

            this.toast('success', 'Wilayah Terpilih', item.name);

        } catch (error) {

            console.error(error);

            this.toast('error', 'Gagal', 'Gagal mengambil data wilayah.');

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Polygon
    |--------------------------------------------------------------------------
    */

    bindPolygon() {

        document.getElementById('createPolygonButton')
            ?.addEventListener('click', () => this.showModal('createPolygonModal'));

        document.getElementById('closeCreatePolygonModal')
            ?.addEventListener('click', () => this.hideModal('createPolygonModal'));

        document.getElementById('cancelCreatePolygon')
            ?.addEventListener('click', () => this.hideModal('createPolygonModal'));

        document.getElementById('createPolygonForm')
            ?.addEventListener('submit', (event) => {

                event.preventDefault();

                const name = document.getElementById('createPolygonName').value.trim();

                if (!name) {
                    this.toast('error', 'Peringatan', 'Nama Poligon wajib diisi.');
                    return;
                }

                const status = document.getElementById('createPolygonStatus').value;

                this.hideModal('createPolygonModal');

                this.toast(
                    'info',
                    'Gambar Poligon',
                    'Klik pada peta untuk membuat titik, lalu double klik untuk menyelesaikan.'
                );

                this.startPolygonDrawing(async (geojson) => {

                    await this.submitCreate({
                        device_id: String(this.state.device.id),
                        type: 'custom',
                        name,
                        status,
                        geojson: JSON.stringify(geojson),
                    }, 'Poligon');

                });

            });

        document.getElementById('editPolygonButton')
            ?.addEventListener('click', () => {
                document.getElementById('polygonReadContainer')?.classList.add('hidden');
                document.getElementById('polygonEditContainer')?.classList.remove('hidden');
            });

        document.getElementById('cancelPolygonEdit')
            ?.addEventListener('click', () => {
                document.getElementById('polygonEditContainer')?.classList.add('hidden');
                document.getElementById('polygonReadContainer')?.classList.remove('hidden');
            });

        document.getElementById('editPolygonForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                const id = document.getElementById('editPolygonId').value;

                await this.submitUpdate(id, {
                    name: document.getElementById('editPolygonName').value,
                    status: document.getElementById('editPolygonStatus').value,
                }, 'Poligon');

            });

        document.getElementById('changePolygonArea')
            ?.addEventListener('click', () => {

                const id = document.getElementById('editPolygonId').value;

                const name = document.getElementById('editPolygonName').value;

                const status = document.getElementById('editPolygonStatus').value;

                this.toast(
                    'info',
                    'Gambar Ulang',
                    'Klik pada peta untuk membuat titik baru, lalu double klik untuk menyelesaikan.'
                );

                this.startPolygonDrawing(async (geojson) => {

                    await this.submitUpdate(id, {
                        name,
                        status,
                        geojson: JSON.stringify(geojson),
                    }, 'Poligon');

                }, () => {
                    const layer = this.layers[id];
                    if (layer) {
                        VehicleMap.removeOverlay(`geofence-${id}`);
                    }
                });

            });

    },

    startPolygonDrawing(onFinish, onFirstClick = null) {

        if (!window.VehicleMap?.map) {
            return;
        }

        this.cancelPolygonDrawing();

        const map = VehicleMap.map;

        const points = [];

        const markers = [];

        let polygon = null;

        map.doubleClickZoom.disable();

        const redraw = () => {

            if (polygon) {
                map.removeLayer(polygon);
                polygon = null;
            }

            if (points.length >= 2) {

                polygon = L.polygon(points, {
                    color: '#7c3aed',
                    weight: 2,
                    fillColor: '#8b5cf6',
                    fillOpacity: 0.15,
                    dashArray: '6',
                });

                polygon.addTo(map);

            }

        };

        const onClick = (event) => {

            if (points.length === 0 && typeof onFirstClick === 'function') {
                onFirstClick();
            }

            const point = event.latlng;

            points.push(point);

            const marker = L.circleMarker(point, {
                radius: 8,
                color: '#ffffff',
                weight: 3,
                fillColor: '#7c3aed',
                fillOpacity: 1,
            }).addTo(map);

            markers.push(marker);

            redraw();

        };

        const onDblClick = (event) => {

            L.DomEvent.stop(event);

            if (points.length < 3) {

                this.toast('error', 'Poligon Kurang Titik', 'Minimal 3 titik untuk membuat poligon.');

                return;

            }

            const geojson = polygon.toGeoJSON().geometry;

            this.cancelPolygonDrawing();

            onFinish(geojson);

        };

        map.on('click', onClick);

        map.on('dblclick', onDblClick);

        this.drawing = {

            map,

            onClick,

            onDblClick,

            getMarkers: () => markers,

            getPolygon: () => polygon,

        };

    },

    cancelPolygonDrawing() {

        if (!this.drawing) {
            return;
        }

        const { map, onClick, onDblClick } = this.drawing;

        map.off('click', onClick);

        map.off('dblclick', onDblClick);

        map.doubleClickZoom.enable();

        (this.drawing.getMarkers?.() ?? []).forEach(marker => map.removeLayer(marker));

        const polygon = this.drawing.getPolygon?.();

        if (polygon) {
            map.removeLayer(polygon);
        }

        this.drawing = null;

    },

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    bindDelete() {

        const buttons = [
            document.getElementById('deleteRadiusButton'),
            document.getElementById('deleteAdministrativeButton'),
            document.getElementById('deletePolygonButton'),
        ];

        const typeLabels = {

            deleteRadiusButton: 'Radius',

            deleteAdministrativeButton: 'Administratif',

            deletePolygonButton: 'Poligon',

        };

        buttons.forEach(button => {

            if (!button) return;

            button.addEventListener('click', () => {

                document.getElementById('deleteGeofenceId').value = button.dataset.id;

                document.getElementById('deleteGeofenceType').value = typeLabels[button.id];

                document.getElementById('deleteGeofenceTypeText').textContent = typeLabels[button.id];

                document.getElementById('deleteGeofenceName').textContent = button.dataset.name || '-';

                this.showModal('deleteGeofenceModal');

            });

        });

        document.getElementById('cancelDeleteGeofence')
            ?.addEventListener('click', () => this.hideModal('deleteGeofenceModal'));

        document.getElementById('confirmDeleteGeofence')
            ?.addEventListener('click', async () => {

                const id = document.getElementById('deleteGeofenceId').value;

                if (!id) {
                    return;
                }

                const button = document.getElementById('confirmDeleteGeofence');

                try {

                    button.disabled = true;

                    const response = await VehicleApi.deleteGeofence(id);

                    if (!response.success) {
                        this.toast('error', 'Gagal', response.message ?? 'Gagal menghapus geofence.');
                        return;
                    }

                    this.toast('success', 'Berhasil', response.message ?? 'Geofence berhasil dihapus.');

                    this.reloadAfter();

                } catch (error) {

                    console.error(error);

                    this.toast('error', 'Gagal', 'Terjadi kesalahan pada server.');

                } finally {

                    button.disabled = false;

                }

            });

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        this.cancelPolygonDrawing();

        this.clearLayers();

        this.state = null;

    },

};

</script>
