<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\Notification\NotificationPresenter;
use Illuminate\View\View;

class PublicTrackingController extends Controller
{
    /**
     * --------------------------------------------------------------------------
     * Halaman Pelacakan Publik (Tanpa Login)
     * --------------------------------------------------------------------------
     *
     * Diakses lewat link ber-signature (lihat routes/web.php, middleware
     * "signed") yang dikirim ke Email/WhatsApp. Titik yang ditampilkan
     * SELALU lokasi yang tersimpan pada notification itu sendiri (kolom
     * `data` di database) - bukan lokasi terkini device - supaya peta
     * selalu konsisten dengan pesan Email/WhatsApp yang dikirim.
     */
    public function show(Notification $notification): View
    {
        $notification->load(['device.vehicle', 'device.user']);

        $device = $notification->device;

        /*
        |--------------------------------------------------------------------------
        | Lokasi & Alamat
        |--------------------------------------------------------------------------
        |
        | Sebagian tipe notification (baterai lemah, perangkat offline/
        | online) tidak membawa koordinat, dan alamatnya bisa saja masih
        | berupa placeholder "Memuat alamat..." kalau cache reverse-
        | geocoding sedang miss saat notification dibuat. Request HTTP
        | biasa boleh menunggu, jadi keduanya di-resolve (dan disimpan)
        | di sini supaya peta tidak pernah kosong dan alamat yang tampil
        | selalu alamat lengkap.
        |
        */

        $location = NotificationPresenter::resolveLocation($notification);

        $address = NotificationPresenter::resolveAddress($notification);

        return view('public.tracking', [

            'device' => $device,

            'notification' => $notification,

            'vehicle' => $device?->vehicle,

            'latitude' => $location['lat'],

            'longitude' => $location['lng'],

            'address' => $address,

            'receivedAt' => $notification->created_at,

            /*
            |------------------------------------------------------------------
            | Halaman ini dibuka tanpa login (lewat link bertanda tangan di
            | Email/WhatsApp), jadi zona waktunya tidak bisa diambil dari
            | sesi - dipakai zona waktu PEMILIK kendaraan supaya jam yang
            | tampil sama persis dengan jam di pesan yang dia terima.
            |------------------------------------------------------------------
            */

            'updatedAtLabel' => NotificationPresenter::formatDateTime(
                $notification->created_at,
                $device?->user
            ),

        ]);
    }
}
