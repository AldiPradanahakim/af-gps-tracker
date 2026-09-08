<?php

namespace App\Services\MQTT;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Throwable;

class MQTTSubscriberService
{
    public function __construct(
        protected GPSProcessingService $gpsProcessingService
    ) {}

    /**
     * Start MQTT Subscriber.
     */
    public function subscribe(): void
    {
        $client = new MqttClient(
            config('mqtt.host'),
            config('mqtt.port'),
            config('mqtt.client_id')
        );

        $settings = (new ConnectionSettings)

            ->setKeepAliveInterval(
                config('mqtt.keep_alive')
            )

            ->setConnectTimeout(
                config('mqtt.timeout')
            )

            ->setReconnectAutomatically(
                config('mqtt.reconnect.automatic')
            )

            ->setMaxReconnectAttempts(
                config('mqtt.reconnect.max_attempts')
            )

            ->setDelayBetweenReconnectAttempts(
                config('mqtt.reconnect.delay_ms')
            );

        /*
        |--------------------------------------------------------------------------
        | Username & Password
        |--------------------------------------------------------------------------
        */

        if (!empty(config('mqtt.username'))) {
            $settings = $settings->setUsername(
                config('mqtt.username')
            );
        }

        if (!empty(config('mqtt.password'))) {
            $settings = $settings->setPassword(
                config('mqtt.password')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TLS
        |--------------------------------------------------------------------------
        */

        if (config('mqtt.tls.enabled')) {

            $settings = $settings->setUseTls(true);

            if (config('mqtt.tls.ca_file')) {
                $settings = $settings->setTlsCertificateAuthorityFile(
                    config('mqtt.tls.ca_file')
                );
            }

            if (config('mqtt.tls.client_certificate')) {
                $settings = $settings->setTlsClientCertificateFile(
                    config('mqtt.tls.client_certificate')
                );
            }

            if (config('mqtt.tls.client_key')) {
                $settings = $settings->setTlsClientCertificateKeyFile(
                    config('mqtt.tls.client_key')
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Debug Connection
        |--------------------------------------------------------------------------
        */

        echo PHP_EOL;
        echo "========================================" . PHP_EOL;
        echo " MQTT SUBSCRIBER" . PHP_EOL;
        echo "========================================" . PHP_EOL;
        echo "Broker : " . config('mqtt.host') . ":" . config('mqtt.port') . PHP_EOL;
        echo "Client : " . config('mqtt.client_id') . PHP_EOL;
        echo "Topic  : " . config('mqtt.topics.gps') . PHP_EOL;
        echo PHP_EOL;

        /*
        |--------------------------------------------------------------------------
        | Connect
        |--------------------------------------------------------------------------
        */

        echo "Connecting ke MQTT broker..." . PHP_EOL;

        $client->connect(
            $settings,
            config('mqtt.clean_session')
        );

        echo "BERHASIL CONNECT!" . PHP_EOL;

        /*
        |--------------------------------------------------------------------------
        | Subscribe
        |--------------------------------------------------------------------------
        */

        $client->subscribe(

            config('mqtt.topics.gps'),

            function (
                string $topic,
                string $message
            ) {

                echo PHP_EOL;
                echo "========================================" . PHP_EOL;
                echo "MQTT MESSAGE DITERIMA!" . PHP_EOL;
                echo "Topic   : " . $topic . PHP_EOL;
                echo "Message : " . $message . PHP_EOL;
                echo "========================================" . PHP_EOL;

                $this->handleMessage(
                    $topic,
                    $message
                );
            },

            config('mqtt.qos')
        );

        echo "BERHASIL SUBSCRIBE!" . PHP_EOL;
        echo "Menunggu pesan MQTT..." . PHP_EOL;

        /*
        |--------------------------------------------------------------------------
        | Listen Forever
        |--------------------------------------------------------------------------
        */

        $client->loop(true);

        $client->disconnect();
    }

    /**
     * Process Incoming MQTT Message.
     */
    protected function handleMessage(
        string $topic,
        string $message
    ): void {

        try {

            echo "Parsing JSON..." . PHP_EOL;

            $payload = json_decode(
                $message,
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            echo "JSON VALID!" . PHP_EOL;

            echo "Payload:" . PHP_EOL;

            print_r($payload);

            echo PHP_EOL;
            echo "Memproses GPS..." . PHP_EOL;

            $this->gpsProcessingService->process(
                $payload
            );

            echo "GPS BERHASIL DIPROSES!" . PHP_EOL;

        } catch (Throwable $exception) {

            echo PHP_EOL;
            echo "========================================" . PHP_EOL;
            echo "ERROR PROCESSING MQTT" . PHP_EOL;
            echo "========================================" . PHP_EOL;
            echo "Class   : " . get_class($exception) . PHP_EOL;
            echo "Message : " . $exception->getMessage() . PHP_EOL;
            echo "========================================" . PHP_EOL;

            report($exception);
        }
    }
}