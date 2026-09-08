<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MQTT Broker
    |--------------------------------------------------------------------------
    */

    'host' => env(
        'MQTT_HOST',
        '127.0.0.1'
    ),

    'port' => (int) env(
        'MQTT_PORT',
        1883
    ),

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    'username' => env(
        'MQTT_USERNAME'
    ) ?: null,

    'password' => env(
        'MQTT_PASSWORD'
    ) ?: null,

    /*
    |--------------------------------------------------------------------------
    | Client
    |--------------------------------------------------------------------------
    */

    'client_id' => env(
        'MQTT_CLIENT_ID',
        'laravel-gps-tracker'
    ),

    // Wajib false - library php-mqtt/client menolak kombinasi clean
    // session + auto-reconnect (lempar ConfigurationInvalidException),
    // dan auto-reconnect di bawah aktif secara default.
    'clean_session' => env(
        'MQTT_CLEAN_SESSION',
        false
    ),

    /*
    |--------------------------------------------------------------------------
    | Connection
    |--------------------------------------------------------------------------
    */

    'keep_alive' => (int) env(
        'MQTT_KEEP_ALIVE',
        60
    ),

    'timeout' => (int) env(
        'MQTT_TIMEOUT',
        5
    ),

    'qos' => (int) env(
        'MQTT_QOS',
        1
    ),

    /*
    |--------------------------------------------------------------------------
    | Reconnect
    |--------------------------------------------------------------------------
    |
    | Koneksi MQTT ke perangkat GPS lapangan rentan putus (jaringan
    | seluler, restart broker, dst). Tanpa auto-reconnect, sekali
    | koneksi putus, mqtt:subscribe akan crash dan proses ingestion
    | GPS berhenti total sampai ada yang restart manual.
    |--------------------------------------------------------------------------

    */

    'reconnect' => [

        'automatic' => (bool) env(
            'MQTT_RECONNECT_AUTOMATIC',
            true
        ),

        'max_attempts' => (int) env(
            'MQTT_RECONNECT_MAX_ATTEMPTS',
            10
        ),

        'delay_ms' => (int) env(
            'MQTT_RECONNECT_DELAY_MS',
            3000
        ),

    ],

    /*
    |--------------------------------------------------------------------------
    | Topics
    |--------------------------------------------------------------------------
    */

    'topics' => [

        'gps' => env(
            'MQTT_TOPIC',
            'gps/+/location'
        ),

    ],

    /*
    |--------------------------------------------------------------------------
    | Signature
    |--------------------------------------------------------------------------
    |
    | Wajib true di production - mencegah device_id spoofing lewat payload
    | HMAC per-device (lihat MQTTSignatureService). Set false hanya untuk
    | testing/bring-up sebelum firmware mendukung signing.
    |--------------------------------------------------------------------------

    */

    'require_signature' => (bool) env(
        'MQTT_REQUIRE_SIGNATURE',
        true
    ),

    /*
    |--------------------------------------------------------------------------
    | Position Filter
    |--------------------------------------------------------------------------
    |
    | Minimum perpindahan posisi GPS yang diperlukan sebelum payload
    | disimpan sebagai DeviceLog dan TravelHistory.
    |
    | Unit: meter
    |
    | Contoh:
    |
    | MQTT_POSITION_THRESHOLD=0.5
    |
    | distance < 0.5 m
    |     -> tidak INSERT DeviceLog
    |     -> tidak INSERT TravelHistory
    |
    | distance >= 0.5 m
    |     -> INSERT DeviceLog
    |     -> INSERT TravelHistory
    |
    | Catatan:
    | Threshold ini HANYA memengaruhi penyimpanan histori.
    | MQTT payload tetap diproses untuk heartbeat, battery,
    | stop detection, overspeed, geofence, dan realtime.
    |
    |--------------------------------------------------------------------------
    */

    'position_threshold' => (float) env(
        'MQTT_POSITION_THRESHOLD',
        0.5
    ),

    /*
    |--------------------------------------------------------------------------
    | Alerts
    |--------------------------------------------------------------------------
    */

    'alerts' => [

        // Persentase baterai - di bawah ini device dianggap "baterai lemah".
        'low_battery_threshold' => (int) env(
            'LOW_BATTERY_THRESHOLD',
            20
        ),

        // Menit sejak last_heartbeat - dipakai DeviceHealthCheckCommand,
        // selaras dengan Device::ONLINE_THRESHOLD_MINUTES (is_online).
        'offline_threshold_minutes' => (int) env(
            'OFFLINE_ALERT_THRESHOLD_MINUTES',
            5
        ),

    ],

    /*
    |--------------------------------------------------------------------------
    | TLS
    |--------------------------------------------------------------------------
    */

    'tls' => [

        'enabled' => (bool) env(
            'MQTT_USE_TLS',
            false
        ),

        'ca_file' => env(
            'MQTT_CA_FILE'
        ),

        'client_certificate' => env(
            'MQTT_CLIENT_CERT'
        ),

        'client_key' => env(
            'MQTT_CLIENT_KEY'
        ),

    ],

];