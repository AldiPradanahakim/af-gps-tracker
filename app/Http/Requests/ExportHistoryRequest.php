<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidatorContract;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class ExportHistoryRequest extends FormRequest
{
    /**
     * --------------------------------------------------------------------------
     * Batas Rentang Tanggal Export
     * --------------------------------------------------------------------------
     * PDF generation jauh lebih berat dibanding response JSON biasa, jadi
     * rentang tanggal yang diminta tidak boleh tak terbatas (mis. "from"
     * bertahun-tahun lalu) - selain query di repository sudah dibatasi
     * MAX_HISTORY_ROWS, rentang tanggal juga dibatasi di sini supaya PDF
     * yang dihasilkan tetap wajar ukurannya.
     * --------------------------------------------------------------------------
     */
    private const MAX_RANGE_DAYS = 366;

    /**
     * Determine if the user is authorized.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation Rules
     */
    public function rules(): array
    {
        return [

            'from' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:from',
            ],

        ];
    }

    /**
     * Validation Messages
     */
    public function messages(): array
    {
        return [

            'from.date_format' =>
            'Format tanggal mulai tidak valid.',

            'to.date_format' =>
            'Format tanggal selesai tidak valid.',

            'to.after_or_equal' =>
            'Tanggal selesai tidak boleh sebelum tanggal mulai.',

        ];
    }

    /**
     * Validasi tambahan: batasi lebar rentang tanggal.
     */
    public function withValidator(ValidatorContract $validator): void
    {
        $validator->after(function (ValidatorContract $validator) {

            $from = $this->input('from');

            $to = $this->input('to');

            if (! $from || ! $to) {

                return;
            }

            $days = Carbon::parse($from)->diffInDays(
                Carbon::parse($to)
            );

            if ($days > self::MAX_RANGE_DAYS) {

                $validator->errors()->add(
                    'to',
                    'Rentang tanggal export maksimal ' . self::MAX_RANGE_DAYS . ' hari.'
                );
            }

        });
    }
}
