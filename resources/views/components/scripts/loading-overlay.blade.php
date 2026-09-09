<script>

/*
|--------------------------------------------------------------------------
| GPSLoading
|--------------------------------------------------------------------------
|
| API kecil untuk overlay loading global (lihat components/loading-overlay).
|
|   GPSLoading.show('Memuat riwayat', 'Mengambil data dari server...');
|   GPSLoading.hide();
|
| Atau bungkus langsung sebuah promise - overlay pasti tertutup walau
| request-nya gagal:
|
|   await GPSLoading.wrap(fetchSesuatu(), 'Memuat riwayat');
|
| Catatan desain:
|
| - Pakai penghitung (depth), bukan boolean. Beberapa request sering
|   berjalan bersamaan (mis. titik GPS + perjalanan yang dikelompokkan);
|   tanpa penghitung, request pertama yang selesai akan menutup overlay
|   padahal yang lain masih berjalan.
|
| - Ada jeda tampil singkat. Request yang selesai di bawah ~180 ms tidak
|   perlu overlay sama sekali - kalau dipaksa tampil, yang terlihat hanya
|   kedipan yang justru mengganggu.
|
| - Ada batas waktu pengaman. Kalau ada pemanggil yang lupa memanggil
|   hide(), overlay tidak boleh mengunci halaman selamanya.
|
*/

window.GPSLoading = (function () {

    const SHOW_DELAY_MS = 180;

    const SAFETY_TIMEOUT_MS = 30000;

    let depth = 0;

    let showTimer = null;

    let safetyTimer = null;

    function element() {

        return document.getElementById('appLoading');

    }

    function paint(title, message) {

        const titleEl = document.getElementById('appLoadingTitle');

        const messageEl = document.getElementById('appLoadingMessage');

        if (titleEl && title) {

            titleEl.textContent = title;

        }

        if (messageEl) {

            messageEl.textContent = message || 'Mohon tunggu sebentar…';

        }

    }

    function reveal() {

        const overlay = element();

        if (!overlay) {

            return;

        }

        overlay.classList.add('is-visible');

        overlay.setAttribute('aria-hidden', 'false');

    }

    function conceal() {

        const overlay = element();

        if (!overlay) {

            return;

        }

        overlay.classList.remove('is-visible');

        overlay.setAttribute('aria-hidden', 'true');

    }

    function clearTimers() {

        if (showTimer) {

            clearTimeout(showTimer);

            showTimer = null;

        }

        if (safetyTimer) {

            clearTimeout(safetyTimer);

            safetyTimer = null;

        }

    }

    return {

        show(title, message) {

            depth += 1;

            /*
            | Hanya pemanggil TERLUAR yang menentukan teksnya. Request
            | dalam sering bersarang (mis. VehicleHistory.load() yang di
            | dalamnya memanggil VehicleApi.history()); kalau setiap
            | lapisan menimpa judul, teksnya berkedip-ganti tanpa guna.
            */

            if (depth > 1) {

                return;

            }

            paint(title, message);

            showTimer = setTimeout(reveal, SHOW_DELAY_MS);

            safetyTimer = setTimeout(() => {

                depth = 0;

                clearTimers();

                conceal();

            }, SAFETY_TIMEOUT_MS);

        },

        hide() {

            depth = Math.max(0, depth - 1);

            if (depth > 0) {

                return;

            }

            clearTimers();

            conceal();

        },

        /**
         * Paksa tutup - dipakai saat berpindah halaman atau ketika
         * keadaan sudah tidak jelas.
         */
        reset() {

            depth = 0;

            clearTimers();

            conceal();

        },

        /**
         * Bungkus sebuah promise. Overlay selalu tertutup, baik promise
         * itu sukses maupun gagal.
         */
        async wrap(promise, title, message) {

            this.show(title, message);

            try {

                return await promise;

            } finally {

                this.hide();

            }

        },

    };

})();

/*
| Kalau pengguna menekan tombol back/forward, halaman bisa dipulihkan
| dari bfcache dengan overlay masih menyala - pastikan selalu bersih.
*/

window.addEventListener('pageshow', function () {

    window.GPSLoading.reset();

});

</script>
