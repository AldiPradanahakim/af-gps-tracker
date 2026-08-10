<script>

window.VehicleSpeed = {

    /*
    |--------------------------------------------------------------------------
    | Properties
    |--------------------------------------------------------------------------
    */

    state: null,

    /*
    |--------------------------------------------------------------------------
    | Initialize
    |--------------------------------------------------------------------------
    */

    init(state) {

        this.state = state;

        this.bindSettingEvents();

    },

    /*
    |--------------------------------------------------------------------------
    | Setting: Bind Events
    |--------------------------------------------------------------------------
    */

    bindSettingEvents() {

        document.getElementById('editSpeedSettingButton')
            ?.addEventListener('click', () => this.showEdit());

        document.getElementById('cancelSpeedSettingEdit')
            ?.addEventListener('click', () => this.hideEdit());

        document.getElementById('speedSettingForm')
            ?.addEventListener('submit', async (event) => {

                event.preventDefault();

                await this.saveSetting();

            });

    },

    showEdit() {

        document.getElementById('speedSettingReadContainer')?.classList.add('hidden');

        document.getElementById('speedSettingStatusCard')?.classList.add('hidden');

        document.getElementById('speedSettingEditContainer')?.classList.remove('hidden');

    },

    hideEdit() {

        document.getElementById('speedSettingEditContainer')?.classList.add('hidden');

        document.getElementById('speedSettingReadContainer')?.classList.remove('hidden');

        document.getElementById('speedSettingStatusCard')?.classList.remove('hidden');

    },

    async saveSetting() {

        const button = document.getElementById('saveSpeedSetting');

        try {

            if (button) {
                button.disabled = true;
            }

            const payload = {

                enabled: document.getElementById('speedSettingEnabled').checked,

                limit_kmh: document.getElementById('speedLimitKmh').value,

                email_notification: document.getElementById('speedEmailNotification').checked,

                whatsapp_notification: document.getElementById('speedWhatsappNotification').checked,

            };

            const response = await fetch(

                `/vehicles/${this.state.device.id}/speed-setting`,

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
                json.message ?? 'Pengaturan Batas Kecepatan berhasil disimpan.'
            );

            /*
            |--------------------------------------------------------------------------
            | Pastikan setelah reload tetap membuka section Batas Kecepatan.
            |--------------------------------------------------------------------------
            */

            history.replaceState(null, '', '#speed');

            setTimeout(() => window.location.reload(), 800);

        } catch (error) {

            console.error(error);

            GPSTracker.showToast('error', 'Error', 'Terjadi kesalahan pada server.');

        } finally {

            if (button) {
                button.disabled = false;
            }

        }

    },

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    destroy() {

        this.state = null;

    },

};

</script>
