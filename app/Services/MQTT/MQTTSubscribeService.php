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

            ->setUsername(
                config('mqtt.username')
            )

            ->setPassword(
                config('mqtt.password')
            )

            ->setKeepAliveInterval(
                config('mqtt.keep_alive')
            )

            ->setConnectTimeout(
                config('mqtt.timeout')
            )

            ->setUseTls(
                config('mqtt.tls.enabled')
            );

        /*
        |--------------------------------------------------------------------------
        | TLS / SSL
        |--------------------------------------------------------------------------
        */

        if (config('mqtt.tls.enabled')) {

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
        | Connect
        |--------------------------------------------------------------------------
        */

        $client->connect(
            $settings,
            config('mqtt.clean_session')
        );

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

                $this->handleMessage(
                    $topic,
                    $message
                );
            },

            config('mqtt.qos')

        );

        /*
        |--------------------------------------------------------------------------
        | Listen Forever
        |--------------------------------------------------------------------------
        */

        $client->loop(
            true
        );

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

            $payload = json_decode(
                $message,
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $this->gpsProcessingService
                ->process(
                    $payload
                );
        } catch (Throwable $exception) {

            report($exception);
        }
    }
}
