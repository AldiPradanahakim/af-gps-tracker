<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Menolak domain email yang kemungkinan besar salah ketik dari penyedia
 * populer (mis. "gmai.com", "gmial.com" alih-alih "gmail.com").
 *
 * Rule bawaan `email:rfc,dns` tidak cukup untuk kasus ini: banyak domain
 * hasil typo (termasuk "gmai.com") ternyata benar-benar terdaftar/
 * di-parkir oleh pihak lain, sehingga tetap punya record DNS/MX yang
 * valid dan lolos dari pengecekan DNS. Rule ini menambah lapisan
 * pengecekan jarak-edit (Levenshtein) terhadap daftar domain populer.
 */
class NotTypoEmailDomain implements ValidationRule
{
    private const KNOWN_DOMAINS = [
        'gmail.com',
        'yahoo.com',
        'yahoo.co.id',
        'outlook.com',
        'hotmail.com',
        'live.com',
        'icloud.com',
        'protonmail.com',
        'aol.com',
        'ymail.com',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! str_contains($value, '@')) {
            return;
        }

        $domain = strtolower(trim(substr($value, strrpos($value, '@') + 1)));

        if ($domain === '' || in_array($domain, self::KNOWN_DOMAINS, true)) {
            return;
        }

        foreach (self::KNOWN_DOMAINS as $known) {

            $distance = levenshtein($domain, $known);

            if ($distance > 0 && $distance <= 2) {

                $fail("Domain email \"{$domain}\" sepertinya salah ketik. Mungkin maksud Anda \"{$known}\"?");

                return;
            }
        }
    }
}
