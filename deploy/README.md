# Menjalankan Proses Background — Development vs Production

Aplikasi ini butuh beberapa proses berjalan terus di belakang layar selain web
server itu sendiri. Cara menjalankannya **beda antara development lokal dan
production** — jangan pakai cara development di server production.

## Ringkasan

| Proses | Development (lokal) | Production |
|---|---|---|
| Web server | `php artisan serve` | Nginx/Apache + PHP-FPM (bukan `artisan serve`) |
| Asset frontend | `npm run dev` (Vite, live-reload) | `npm run build` sekali saat deploy, hasilnya disajikan langsung sebagai file statis |
| Queue worker | `php artisan queue:listen` | `php artisan queue:work`, dijaga **Supervisor** |
| Scheduler (`device:health-check`) | `php artisan schedule:work` | cron OS asli (`php artisan schedule:run` tiap menit) |
| MQTT subscriber | `php artisan mqtt:subscribe` | `php artisan mqtt:subscribe`, dijaga **Supervisor** |

## Kenapa dipisah di production, padahal di lokal digabung satu command?

`composer run dev` (lihat `composer.json`) menjalankan semuanya sekaligus lewat
`concurrently` — praktis untuk development karena satu command, satu
terminal, gampang dimatikan. Tapi ini **tidak cocok untuk production**:

1. **Tidak ada auto-restart per proses yang andal.** Kalau `mqtt:subscribe`
   crash di tengah malam (broker restart, jaringan putus), tidak ada yang
   menghidupkannya lagi. Supervisor secara native menangani ini.
2. **Butuh Node.js di server production** cuma untuk menjalankan proses PHP
   background — dependency yang tidak perlu ada di server yang seharusnya
   cuma menjalankan PHP.
3. **Log tercampur** jadi satu aliran, menyulitkan debug proses mana yang
   bermasalah.
4. **Kebutuhan restart berbeda per proses** — queue worker perlu di-restart
   tiap ada deploy kode baru (`php artisan queue:restart`), sedangkan
   `mqtt:subscribe` idealnya tidak pernah terganggu sama sekali kecuali
   benar-benar crash. Digabung menyulitkan mengelola ini secara terpisah.

## Development lokal

Cukup jalankan:

```bash
composer run dev
```

Ini menyalakan 5 proses sekaligus (server, queue, vite, scheduler, MQTT
subscriber) dalam satu terminal dengan warna/label berbeda. Catatan: `mqtt`
butuh broker MQTT aktif (mis. Mosquitto lokal) — kalau brokernya belum
jalan, panel `mqtt` di terminal akan menampilkan error tapi 4 proses lainnya
tetap jalan normal (tidak saling mematikan).

## Production (server Linux + Supervisor)

Berkas konfigurasi contoh ada di folder ini:

- `supervisor/af-gps-tracker-queue.conf` — konfigurasi Supervisor untuk queue worker.
- `supervisor/af-gps-tracker-mqtt.conf` — konfigurasi Supervisor untuk MQTT subscriber.
- `cron/af-gps-tracker` — baris cron untuk scheduler.

### Langkah instalasi

1. **Pasang Supervisor** (kalau belum ada): `sudo apt install supervisor`
2. **Salin & sesuaikan** kedua file `.conf` — ganti `/path/to/af-gps-tracker`
   dengan path project sesungguhnya di server, dan `user=www-data` dengan
   user yang menjalankan aplikasi:
   ```bash
   sudo cp deploy/supervisor/af-gps-tracker-queue.conf /etc/supervisor/conf.d/
   sudo cp deploy/supervisor/af-gps-tracker-mqtt.conf /etc/supervisor/conf.d/
   sudo supervisorctl reread
   sudo supervisorctl update
   sudo supervisorctl start af-gps-tracker-queue:* af-gps-tracker-mqtt
   ```
3. **Pasang cron scheduler** — salin & sesuaikan path/user di dalamnya:
   ```bash
   sudo cp deploy/cron/af-gps-tracker /etc/cron.d/af-gps-tracker
   sudo chmod 644 /etc/cron.d/af-gps-tracker
   ```
4. **Cek status**:
   ```bash
   sudo supervisorctl status
   ```
   Harus terlihat `af-gps-tracker-queue:af-gps-tracker-queue_00` dan
   `af-gps-tracker-mqtt` berstatus `RUNNING`.

### Checklist setiap kali deploy kode baru

- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `npm run build`
- [ ] `php artisan migrate --force`
- [ ] `php artisan config:cache && php artisan route:cache && php artisan view:cache`
- [ ] `php artisan queue:restart` — **wajib**, supaya queue worker pakai kode
      terbaru (proses lama tetap pakai kode versi lama di memori sampai
      di-restart). `mqtt:subscribe` tidak perlu restart manual untuk
      perubahan kode di luar dirinya sendiri, tapi restart lewat
      `supervisorctl restart af-gps-tracker-mqtt` kalau yang berubah
      menyentuh langsung pipeline ingest GPS (`GPSProcessingService` dan
      layanan-layanan yang dipanggilnya).

### Membuat akun admin pertama (sekali saja, bukan setiap deploy)

`DatabaseSeeder` sengaja tidak membuat akun admin di luar environment
`local` (repositori ini publik - jangan sampai ada akun dengan kata
sandi yang tertulis di kode terbuat otomatis di produksi). Setelah
deploy pertama, buat akun admin dengan:

```bash
php artisan db:seed --class=AdminSeeder
```

Isi `ADMIN_EMAIL`/`ADMIN_PASSWORD` di `.env` server dulu kalau ingin
kredensial tertentu; kalau dikosongkan, seeder mencetak kata sandi acak
sekali ke layar - catat saat itu juga, tidak disimpan di mana pun.
Aman dijalankan ulang: kalau akun dengan email itu sudah ada, seeder
tidak melakukan apa-apa (tidak menimpa kata sandi yang sudah diganti
lewat aplikasi).

### Variabel lingkungan yang wajib diisi di server production

Lihat `.env.example` untuk daftar lengkap. Yang paling gampang lupa saat
pindah dari lokal ke production:

- `MQTT_HOST`, `MQTT_PORT`, `MQTT_USERNAME`, `MQTT_PASSWORD` — broker MQTT
  produksi, bukan broker lokal.
- `MQTT_REQUIRE_SIGNATURE=true` — **jangan pernah** `false` di production,
  ini yang mencegah pemalsuan `device_id` (lihat `MQTTSignatureService`).
- `QUEUE_CONNECTION=database` (atau redis kalau tersedia) — bukan `sync`,
  karena `sync` membuat email/WhatsApp/broadcast realtime dikirim secara
  blocking di tengah request/proses ingest, meniadakan seluruh manfaat
  queue.
- `SESSION_SECURE_COOKIE=true` — wajib di production (HTTPS).
- `LOW_BATTERY_THRESHOLD`, `OFFLINE_ALERT_THRESHOLD_MINUTES` — sesuaikan
  kalau ambang batas default (20%, 5 menit) tidak sesuai karakteristik
  perangkat/armada.
