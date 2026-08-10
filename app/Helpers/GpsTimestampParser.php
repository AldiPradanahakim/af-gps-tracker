<?php

namespace App\Helpers;

use Illuminate\Support\Carbon;
use InvalidArgumentException;

class GpsTimestampParser
{
    /**
     * Parse a GPS device "received_at" value into Carbon.
     *
     * Perangkat GPS murah/embedded seringkali mengirim timestamp dalam
     * bentuk yang tidak konsisten: ISO8601, epoch detik (int),
     * epoch detik sebagai string (banyak firmware men-stringify semua
     * field JSON-nya), atau epoch milidetik (int/string). Carbon::parse()
     * bawaan hanya menangani ISO8601 dan epoch detik sebagai int - ini
     * menormalisasi semua bentuk itu supaya titik GPS tidak ditolak
     * hanya karena format timestamp firmware berbeda.
     */
    public static function parse(mixed $value): Carbon
    {
        if ($value === null || $value === '') {
            throw new InvalidArgumentException('received_at is invalid.');
        }

        if (is_numeric($value)) {

            $number = (float) $value;

            // Epoch milidetik (13 digit) vs epoch detik (10 digit).
            if ($number > 9999999999) {
                $number = $number / 1000;
            }

            return Carbon::createFromTimestamp((int) $number);
        }

        return Carbon::parse((string) $value);
    }
}
