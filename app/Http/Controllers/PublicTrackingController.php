<?php

namespace App\Http\Controllers;

use App\Models\Notification;
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
        $notification->load('device.vehicle');

        $device = $notification->device;

        return view('public.tracking', [

            'device' => $device,

            'notification' => $notification,

            'vehicle' => $device?->vehicle,

            'latitude' => data_get($notification->data, 'location.lat'),

            'longitude' => data_get($notification->data, 'location.lng'),

            'address' => data_get($notification->data, 'search_address'),

            'receivedAt' => $notification->created_at,

        ]);
    }
}
