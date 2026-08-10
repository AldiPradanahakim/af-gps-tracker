# Trackio

Platform pelacakan kendaraan GPS berbasis web, dibangun dengan Laravel. Menerima data lokasi dari perangkat GPS lewat MQTT, menampilkan posisi kendaraan secara real-time di peta, dan mendukung geofencing beserta notifikasinya.

## Fitur

- Aktivasi perangkat GPS per akun (device ID + password perangkat)
- Pemantauan lokasi kendaraan real-time di peta (Leaflet + OpenStreetMap)
- Riwayat perjalanan dan playback rute
- Geofence berbasis radius, wilayah administratif, atau polygon custom, dengan notifikasi saat kendaraan keluar area
- Deteksi kendaraan berhenti (stop detection) dengan durasi yang bisa diatur
- Notifikasi real-time lewat Pusher/Laravel Echo
- Autentikasi berbasis sesi, akun baru dibuat lewat alur aktivasi perangkat

## Teknologi

- **Backend**: Laravel 13, PHP 8.3
- **Database**: PostgreSQL 14
- **Realtime**: Pusher, Laravel Echo, Laravel Broadcasting
- **Ingest data GPS**: MQTT (php-mqtt/client)
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Vite
- **Peta**: Leaflet

## Instalasi Lokal

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

# sesuaikan koneksi database & broker MQTT di .env

php artisan migrate --seed
npm run build

php artisan serve
```

Jalankan queue worker dan MQTT subscriber di proses terpisah untuk pemrosesan data GPS dan notifikasi realtime:

```bash
php artisan queue:listen
php artisan mqtt:subscribe
```

### Menyambungkan perangkat GPS fisik

Setiap device menandatangani payload MQTT-nya dengan HMAC-SHA256 (`mqtt_secret`,
digenerate otomatis saat device dibuat) supaya `device_id` tidak bisa dipalsukan
oleh pengirim lain yang tahu kredensial broker. Lihat kunci dan contoh payload
yang sudah ditandatangani untuk device tertentu:

```bash
php artisan device:mqtt-secret GPS-AF-0001
```

Set `MQTT_REQUIRE_SIGNATURE=false` di `.env` hanya untuk testing sementara
sebelum firmware mendukung signing.
