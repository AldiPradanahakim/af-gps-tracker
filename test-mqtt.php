
<?php

require __DIR__ . '/vendor/autoload.php';

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

$client = new MqttClient(
    'broker.emqx.io',
    1883,
    'laravel-test-' . rand(1000, 9999)
);

$settings = (new ConnectionSettings)
    ->setKeepAliveInterval(60)
    ->setConnectTimeout(10)
    ->setReconnectAutomatically(false);

echo "Mencoba connect ke EMQX..." . PHP_EOL;

try {
    $client->connect($settings, true);

    echo "BERHASIL CONNECT KE EMQX!" . PHP_EOL;

    $client->subscribe(
        'gps/testing/fakih',
        function (string $topic, string $message) {
            echo PHP_EOL;
            echo "========================================" . PHP_EOL;
            echo "PESAN DITERIMA!" . PHP_EOL;
            echo "Topic   : " . $topic . PHP_EOL;
            echo "Message : " . $message . PHP_EOL;
            echo "========================================" . PHP_EOL;
        },
        1
    );

    echo "BERHASIL SUBSCRIBE!" . PHP_EOL;
    echo "Menunggu pesan..." . PHP_EOL;

    $client->loop(true);

} catch (Throwable $e) {

    echo PHP_EOL;
    echo "ERROR:" . PHP_EOL;
    echo get_class($e) . PHP_EOL;
    echo $e->getMessage() . PHP_EOL;
}
