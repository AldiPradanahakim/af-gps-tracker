#pragma once

/*
 * Salin file ini menjadi secrets.h di folder yang sama, lalu isi nilainya.
 *
 * secrets.h sengaja tidak ikut ke git. Ambil nilainya dari:
 *
 *   php artisan device:mqtt-secret GPS-AF-0001    -> AFGPS_MQTT_SECRET
 *
 * Username dan password broker per-perangkat dipegang Aldi.
 */

#define AFGPS_DEVICE_ID   "GPS-AF-0001"
#define AFGPS_MQTT_USER   "gps-af-0001"
#define AFGPS_MQTT_PASS   "ganti-dengan-password-broker"
#define AFGPS_MQTT_TOPIC  "gps/GPS-AF-0001/location"
#define AFGPS_MQTT_SECRET "ganti-dengan-mqtt-secret-64-hex"
