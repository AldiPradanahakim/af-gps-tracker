<script>

document.addEventListener('DOMContentLoaded', () => {

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const addButton = document.getElementById('sidebarAddVehicleButton');

    const activateModal = document.getElementById('activateDeviceModal');

    const vehicleModal = document.getElementById('vehicleInformationModal');

    const activateForm = document.getElementById('activateDeviceForm');

    const vehicleForm = document.getElementById('vehicleInformationForm');

    const deleteButton = document.getElementById('sidebarDeleteVehicleButton');

    const deleteModal = document.getElementById('deleteVehicleModal');

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    function openModal(modal) {
        modal?.classList.remove('hidden');
        modal?.classList.add('flex');
    }

    function closeModal(modal) {
        modal?.classList.add('hidden');
        modal?.classList.remove('flex');
    }

    function csrfToken() {
        return document
            .querySelector('meta[name="csrf-token"]')
            ?.content;
    }

    function toast(type, title, message) {
        GPSTracker.showToast(type, title, message);
    }

    /*
    |--------------------------------------------------------------------------
    | Tambah Kendaraan: Buka Modal Aktivasi
    |--------------------------------------------------------------------------
    */

    addButton?.addEventListener('click', () => openModal(activateModal));

    document.getElementById('closeActivateDevice')
        ?.addEventListener('click', () => closeModal(activateModal));

    document.getElementById('cancelActivateDevice')
        ?.addEventListener('click', () => closeModal(activateModal));

    document.getElementById('cancelVehicleInformation')
        ?.addEventListener('click', () => closeModal(vehicleModal));

    /*
    |--------------------------------------------------------------------------
    | Tambah Kendaraan: Aktivasi Device
    |--------------------------------------------------------------------------
    */

    activateForm?.addEventListener('submit', async (event) => {

        event.preventDefault();

        const button = activateForm.querySelector('button[type="submit"]');

        try {

            if (button) button.disabled = true;

            const response = await fetch('/home/devices/activate', {

                method: 'POST',

                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },

                body: new FormData(activateForm),

            });

            const result = await response.json();

            if (!response.ok || !result.success) {

                const message = result.errors
                    ? Object.values(result.errors).flat().join('\n')
                    : (result.message ?? 'Aktivasi perangkat gagal.');

                toast('error', 'Gagal', message);

                return;

            }

            toast(
                'success',
                'Berhasil',
                result.message ?? 'Perangkat berhasil diaktivasi.'
            );

            activateForm.reset();

            closeModal(activateModal);

            openModal(vehicleModal);

        } catch (error) {

            console.error(error);

            toast('error', 'Gagal', 'Terjadi kesalahan pada server.');

        } finally {

            if (button) button.disabled = false;

        }

    });

    /*
    |--------------------------------------------------------------------------
    | Tambah Kendaraan: Simpan Informasi Kendaraan
    |--------------------------------------------------------------------------
    */

    vehicleForm?.addEventListener('submit', async (event) => {

        event.preventDefault();

        const button = vehicleForm.querySelector('button[type="submit"]');

        try {

            if (button) button.disabled = true;

            const response = await fetch('/home/vehicles', {

                method: 'POST',

                headers: {
                    'X-CSRF-TOKEN': csrfToken(),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },

                body: new FormData(vehicleForm),

            });

            const result = await response.json();

            if (!response.ok || !result.success) {

                const message = result.errors
                    ? Object.values(result.errors).flat().join('\n')
                    : (result.message ?? 'Kendaraan gagal disimpan.');

                toast('error', 'Gagal', message);

                return;

            }

            toast(
                'success',
                'Berhasil',
                'Kendaraan berhasil ditambahkan.'
            );

            vehicleForm.reset();

            closeModal(vehicleModal);

            /*
            |--------------------------------------------------------------------------
            | Reload supaya sidebar menampilkan kendaraan baru.
            |--------------------------------------------------------------------------
            */

            setTimeout(() => window.location.reload(), 1000);

        } catch (error) {

            console.error(error);

            toast('error', 'Gagal', 'Terjadi kesalahan pada server.');

        } finally {

            if (button) button.disabled = false;

        }

    });

    /*
    |--------------------------------------------------------------------------
    | Hapus Kendaraan
    |--------------------------------------------------------------------------
    */

    deleteButton?.addEventListener('click', () => {

        const name = document.getElementById('deleteVehicleName');

        if (name) {
            name.textContent = deleteButton.dataset.vehicleName || '-';
        }

        openModal(deleteModal);

    });

    document.getElementById('cancelDeleteVehicle')
        ?.addEventListener('click', () => closeModal(deleteModal));

    document.getElementById('confirmDeleteVehicle')
        ?.addEventListener('click', async () => {

            const id = deleteButton?.dataset.deviceId;

            if (!id) {
                return;
            }

            const button = document.getElementById('confirmDeleteVehicle');

            /*
            | Menandai bahwa halaman akan berpindah. Kalau true, overlay
            | sengaja DIBIARKAN menyala sampai perpindahan benar-benar
            | terjadi - menutupnya lebih dulu membuat halaman detail yang
            | datanya sudah hilang sempat terlihat kosong.
            */

            let willNavigate = false;

            try {

                button.disabled = true;

                GPSLoading.show(
                    'Menghapus kendaraan',
                    'Menghapus data dan melepas perangkat…'
                );

                const response = await fetch(`/vehicles/${id}`, {

                    method: 'DELETE',

                    headers: {
                        'X-CSRF-TOKEN': csrfToken(),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },

                });

                const result = await response.json();

                if (!response.ok || !result.success) {

                    toast(
                        'error',
                        'Gagal',
                        result.message ?? 'Kendaraan gagal dihapus.'
                    );

                    return;

                }

                toast(
                    'success',
                    'Berhasil',
                    result.message ?? 'Kendaraan berhasil dihapus.'
                );

                /*
                |--------------------------------------------------------------------------
                | Halaman device ini sudah tidak ada lagi -> kembali ke Home.
                | Pakai replace() (bukan href) supaya halaman yang sudah
                | dihapus tidak tersimpan di history dan tidak bisa
                | dibuka lagi lewat tombol back browser.
                |--------------------------------------------------------------------------
                */

                willNavigate = true;

                GPSLoading.show(
                    'Mengalihkan ke Halaman Utama',
                    'Sebentar lagi…'
                );

                setTimeout(() => {
                    window.location.replace('/home');
                }, 800);

            } catch (error) {

                console.error(error);

                toast('error', 'Gagal', 'Terjadi kesalahan pada server.');

            } finally {

                button.disabled = false;

                if (!willNavigate) {

                    // Termasuk jalur gagal yang keluar lebih awal lewat
                    // return - tanpa ini overlay bisa mengunci halaman.
                    GPSLoading.reset();

                }

            }

        });

});

</script>
