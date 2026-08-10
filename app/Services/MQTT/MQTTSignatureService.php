<?php

namespace App\Services\MQTT;

use App\Models\Device;
use InvalidArgumentException;

class MQTTSignatureService
{
    /**
     * Field payload (urutan tetap) yang ikut ditandatangani firmware.
     * device_id tidak diikutkan karena sudah jadi bagian penentu device
     * mana yang dipakai untuk verifikasi.
     */
    private const SIGNED_FIELDS = [
        'message_id',
        'lat',
        'lng',
        'speed',
        'heading',
        'battery',
        'satellite',
        'received_at',
    ];

    /**
     * Verifikasi bahwa payload benar-benar ditandatangani oleh device
     * pemilik $device->mqtt_secret, bukan sekadar mencantumkan device_id
     * miliknya di JSON (siapa pun yang tahu kredensial broker MQTT bisa
     * melakukan itu). Signature = HMAC-SHA256(secret, canonical string),
     * dikirim device via field "signature" (hex).
     *
     * @throws InvalidArgumentException
     */
    public function verify(Device $device, array $payload): void
    {
        if (! isset($payload['signature']) || ! is_string($payload['signature']) || $payload['signature'] === '') {

            throw new InvalidArgumentException('signature is required.');
        }

        $expected = $this->sign($device, $payload);

        if (! hash_equals($expected, strtolower($payload['signature']))) {

            throw new InvalidArgumentException('signature is invalid.');
        }
    }

    /**
     * Hitung signature yang diharapkan untuk sebuah payload. Dipakai juga
     * oleh tooling (mis. simulator/testing) untuk membuat payload valid.
     */
    public function sign(Device $device, array $payload): string
    {
        return hash_hmac(
            'sha256',
            $this->canonicalize($payload),
            (string) $device->mqtt_secret
        );
    }

    /**
     * Susun string kanonik dari field yang wajib ditandatangani, urutan
     * tetap sesuai SIGNED_FIELDS supaya server & firmware selalu
     * menghasilkan string yang identik.
     */
    protected function canonicalize(array $payload): string
    {
        $parts = [];

        foreach (self::SIGNED_FIELDS as $field) {
            $parts[] = $field . '=' . ($payload[$field] ?? '');
        }

        return implode('&', $parts);
    }
}
