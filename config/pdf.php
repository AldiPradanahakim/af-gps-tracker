<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batas Baris Export PDF
    |--------------------------------------------------------------------------
    |
    | Kosongkan (0) supaya batasnya dihitung otomatis dari memory_limit
    | PHP yang berlaku di server - server yang lebih besar otomatis bisa
    | mencetak lebih banyak baris. Isi dengan angka untuk memaksa batas
    | tertentu (mis. saat ingin laporan yang pendek dan ringkas).
    |
    | Lihat App\Services\Vehicle\PdfExportService::maxRows().
    |
    */

    'max_rows' => (int) env('PDF_MAX_ROWS', 0),

];
