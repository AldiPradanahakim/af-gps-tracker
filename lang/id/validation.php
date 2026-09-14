<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Terjemahan Bahasa Indonesia untuk pesan validasi bawaan Laravel, supaya
    | error yang tidak di-override lewat messages() pada FormRequest tetap
    | tampil dalam Bahasa Indonesia (bukan jatuh ke default Inggris).
    |
    */

    'accepted' => ':attribute harus disetujui.',
    'accepted_if' => ':attribute harus disetujui apabila :other bernilai :value.',
    'active_url' => ':attribute bukan URL yang valid.',
    'after' => ':attribute harus berupa tanggal setelah :date.',
    'after_or_equal' => ':attribute harus berupa tanggal setelah atau sama dengan :date.',
    'alpha' => ':attribute hanya boleh berisi huruf.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => ':attribute hanya boleh berisi huruf dan angka.',
    'any_of' => ':attribute tidak valid.',
    'array' => ':attribute harus berupa array.',
    'ascii' => ':attribute hanya boleh berisi karakter alfanumerik dan simbol satu byte.',
    'before' => ':attribute harus berupa tanggal sebelum :date.',
    'before_or_equal' => ':attribute harus berupa tanggal sebelum atau sama dengan :date.',
    'between' => [
        'array' => ':attribute harus memiliki antara :min dan :max item.',
        'file' => ':attribute harus berukuran antara :min dan :max kilobyte.',
        'numeric' => ':attribute harus bernilai antara :min dan :max.',
        'string' => ':attribute harus berisi antara :min dan :max karakter.',
    ],
    'boolean' => ':attribute harus bernilai true atau false.',
    'can' => ':attribute berisi nilai yang tidak diizinkan.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'contains' => ':attribute tidak berisi nilai yang wajib ada.',
    'current_password' => 'Kata sandi salah.',
    'date' => ':attribute bukan tanggal yang valid.',
    'date_equals' => ':attribute harus berupa tanggal yang sama dengan :date.',
    'date_format' => ':attribute tidak sesuai dengan format :format.',
    'decimal' => ':attribute harus memiliki :decimal angka desimal.',
    'declined' => ':attribute harus ditolak.',
    'declined_if' => ':attribute harus ditolak apabila :other bernilai :value.',
    'different' => ':attribute dan :other harus berbeda.',
    'digits' => ':attribute harus terdiri dari :digits digit.',
    'digits_between' => ':attribute harus terdiri dari :min sampai :max digit.',
    'dimensions' => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct' => ':attribute memiliki nilai yang duplikat.',
    'doesnt_contain' => ':attribute tidak boleh mengandung salah satu dari: :values.',
    'doesnt_end_with' => ':attribute tidak boleh diakhiri dengan salah satu dari: :values.',
    'doesnt_start_with' => ':attribute tidak boleh diawali dengan salah satu dari: :values.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'encoding' => ':attribute harus menggunakan enkoding :encoding.',
    'ends_with' => ':attribute harus diakhiri dengan salah satu dari: :values.',
    'enum' => ':attribute yang dipilih tidak valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'extensions' => ':attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file' => ':attribute harus berupa berkas.',
    'filled' => ':attribute wajib diisi.',
    'gt' => [
        'array' => ':attribute harus memiliki lebih dari :value item.',
        'file' => ':attribute harus lebih besar dari :value kilobyte.',
        'numeric' => ':attribute harus lebih besar dari :value.',
        'string' => ':attribute harus lebih besar dari :value karakter.',
    ],
    'gte' => [
        'array' => ':attribute harus memiliki :value item atau lebih.',
        'file' => ':attribute harus lebih besar atau sama dengan :value kilobyte.',
        'numeric' => ':attribute harus lebih besar atau sama dengan :value.',
        'string' => ':attribute harus lebih besar atau sama dengan :value karakter.',
    ],
    'hex_color' => ':attribute harus berupa kode warna heksadesimal yang valid.',
    'image' => ':attribute harus berupa gambar.',
    'in' => ':attribute yang dipilih tidak valid.',
    'in_array' => ':attribute tidak ada dalam :other.',
    'in_array_keys' => ':attribute harus berisi setidaknya salah satu dari: :values.',
    'integer' => ':attribute harus berupa angka bulat.',
    'ip' => ':attribute harus berupa alamat IP yang valid.',
    'ipv4' => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6' => ':attribute harus berupa alamat IPv6 yang valid.',
    'json' => ':attribute harus berupa string JSON yang valid.',
    'list' => ':attribute harus berupa daftar (list).',
    'lowercase' => ':attribute harus berupa huruf kecil.',
    'lt' => [
        'array' => ':attribute harus memiliki kurang dari :value item.',
        'file' => ':attribute harus lebih kecil dari :value kilobyte.',
        'numeric' => ':attribute harus lebih kecil dari :value.',
        'string' => ':attribute harus lebih kecil dari :value karakter.',
    ],
    'lte' => [
        'array' => ':attribute tidak boleh memiliki lebih dari :value item.',
        'file' => ':attribute harus lebih kecil atau sama dengan :value kilobyte.',
        'numeric' => ':attribute harus lebih kecil atau sama dengan :value.',
        'string' => ':attribute harus lebih kecil atau sama dengan :value karakter.',
    ],
    'mac_address' => ':attribute harus berupa alamat MAC yang valid.',
    'max' => [
        'array' => ':attribute tidak boleh memiliki lebih dari :max item.',
        'file' => ':attribute tidak boleh lebih besar dari :max kilobyte.',
        'numeric' => ':attribute tidak boleh lebih besar dari :max.',
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits' => ':attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes' => ':attribute harus berupa berkas bertipe: :values.',
    'mimetypes' => ':attribute harus berupa berkas bertipe: :values.',
    'min' => [
        'array' => ':attribute harus memiliki paling sedikit :min item.',
        'file' => ':attribute harus paling sedikit :min kilobyte.',
        'numeric' => ':attribute harus bernilai paling sedikit :min.',
        'string' => ':attribute harus berisi paling sedikit :min karakter.',
    ],
    'min_digits' => ':attribute harus memiliki paling sedikit :min digit.',
    'missing' => ':attribute wajib tidak ada.',
    'missing_if' => ':attribute wajib tidak ada apabila :other bernilai :value.',
    'missing_unless' => ':attribute wajib tidak ada kecuali :other bernilai :value.',
    'missing_with' => ':attribute wajib tidak ada apabila :values ada.',
    'missing_with_all' => ':attribute wajib tidak ada apabila :values semuanya ada.',
    'multiple_of' => ':attribute harus merupakan kelipatan dari :value.',
    'not_in' => ':attribute yang dipilih tidak valid.',
    'not_regex' => 'Format :attribute tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'password' => [
        'letters' => ':attribute harus mengandung minimal satu huruf.',
        'mixed' => ':attribute harus mengandung minimal satu huruf besar dan satu huruf kecil.',
        'numbers' => ':attribute harus mengandung minimal satu angka.',
        'symbols' => ':attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':attribute yang dimasukkan pernah muncul dalam kebocoran data. Silakan pilih :attribute lain.',
    ],
    'present' => ':attribute wajib ada.',
    'present_if' => ':attribute wajib ada apabila :other bernilai :value.',
    'present_unless' => ':attribute wajib ada kecuali :other bernilai :value.',
    'present_with' => ':attribute wajib ada apabila :values ada.',
    'present_with_all' => ':attribute wajib ada apabila :values semuanya ada.',
    'prohibited' => ':attribute tidak diperbolehkan diisi.',
    'prohibited_if' => ':attribute tidak diperbolehkan diisi apabila :other bernilai :value.',
    'prohibited_if_accepted' => ':attribute tidak diperbolehkan diisi apabila :other disetujui.',
    'prohibited_if_declined' => ':attribute tidak diperbolehkan diisi apabila :other ditolak.',
    'prohibited_unless' => ':attribute tidak diperbolehkan diisi kecuali :other ada dalam :values.',
    'prohibits' => ':attribute tidak memperbolehkan :other untuk diisi.',
    'regex' => 'Format :attribute tidak valid.',
    'required' => ':attribute wajib diisi.',
    'required_array_keys' => ':attribute wajib berisi entri untuk: :values.',
    'required_if' => ':attribute wajib diisi apabila :other bernilai :value.',
    'required_if_accepted' => ':attribute wajib diisi apabila :other disetujui.',
    'required_if_declined' => ':attribute wajib diisi apabila :other ditolak.',
    'required_unless' => ':attribute wajib diisi kecuali :other ada dalam :values.',
    'required_with' => ':attribute wajib diisi apabila :values ada.',
    'required_with_all' => ':attribute wajib diisi apabila :values semuanya ada.',
    'required_without' => ':attribute wajib diisi apabila :values tidak ada.',
    'required_without_all' => ':attribute wajib diisi apabila tidak satu pun dari :values ada.',
    'same' => ':attribute dan :other harus sama.',
    'size' => [
        'array' => ':attribute harus berisi :size item.',
        'file' => ':attribute harus berukuran :size kilobyte.',
        'numeric' => ':attribute harus bernilai :size.',
        'string' => ':attribute harus berisi :size karakter.',
    ],
    'starts_with' => ':attribute harus diawali dengan salah satu dari: :values.',
    'string' => ':attribute harus berupa teks.',
    'timezone' => ':attribute harus berupa zona waktu yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'uploaded' => ':attribute gagal diunggah.',
    'uppercase' => ':attribute harus berupa huruf besar.',
    'url' => ':attribute harus berupa URL yang valid.',
    'ulid' => ':attribute harus berupa ULID yang valid.',
    'uuid' => ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Nama field default (dipakai kalau FormRequest tidak punya attributes()
    | sendiri) supaya pesan generik ("device id field is required" dsb)
    | tetap terbaca alami dalam Bahasa Indonesia.
    |
    */

    'attributes' => [
        'name' => 'nama',
        'email' => 'email',
        'phone' => 'nomor WhatsApp',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'current_password' => 'kata sandi saat ini',
        'device_id' => 'ID Perangkat',
        'device_password' => 'password perangkat',
        'vehicle_name' => 'nama kendaraan',
        'vehicle_type' => 'jenis kendaraan',
        'plate_number' => 'nomor polisi',
        'marker_icon' => 'ikon marker',
        'marker_color' => 'warna marker',
    ],

];
