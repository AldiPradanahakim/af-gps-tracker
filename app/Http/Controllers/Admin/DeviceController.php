<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $query = Device::with('user');

        if ($request->filled('search')) {
            $query->where('device_id', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'used') {
                $query->whereNotNull('user_id');
            } elseif ($request->status === 'available') {
                $query->whereNull('user_id');
            }
        }

        $devices = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('admin.devices.index', compact('devices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jumlah_perangkat' => 'required|integer|min:1|max:100',
        ]);

        $jumlah = (int) $request->jumlah_perangkat;
        $prefix = 'GPS-AF-';

        // Cari device_id terakhir yang sesuai prefix
        $lastDevice = Device::where('device_id', 'like', $prefix . '%')
            ->get()
            ->sortByDesc(function ($device) use ($prefix) {
                return (int) str_replace($prefix, '', $device->device_id);
            })
            ->first();

        $lastNumber = 0;
        if ($lastDevice) {
            $numberPart = str_replace($prefix, '', $lastDevice->device_id);
            if (is_numeric($numberPart)) {
                $lastNumber = (int) $numberPart;
            }
        }

        $generatedDevices = [];

        for ($i = 0; $i < $jumlah; $i++) {
            $lastNumber++;
            // Format number to 3 digits (e.g., 001, 002, ... 100)
            $newDeviceId = $prefix . str_pad($lastNumber, 4, '0', STR_PAD_LEFT);
            $newPassword = Str::random(8);

            Device::create([
                'id' => Str::uuid(),
                'device_id' => $newDeviceId,
                'device_password' => $newPassword, // Model otomatis akan nge-hash
            ]);

            $generatedDevices[] = [
                'device_id' => $newDeviceId,
                'device_password' => $newPassword,
            ];
        }

        $cacheKey = 'generated_devices_' . Str::random(10);
        \Illuminate\Support\Facades\Cache::put($cacheKey, $generatedDevices, now()->addMinutes(10));

        return redirect()->route('admin.devices.index')
            ->with('success', $jumlah . ' Perangkat berhasil ditambahkan.')
            ->with('generated_devices_key', $cacheKey)
            ->with('generated_devices_data', $generatedDevices);
    }

    public function exportPdf($key)
    {
        $generatedDevices = \Illuminate\Support\Facades\Cache::get($key);

        if (!$generatedDevices) {
            return redirect()->route('admin.devices.index')->with('error', 'Sesi unduh kata sandi sudah kedaluwarsa atau tidak valid.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.devices.pdf', [
            'devices' => $generatedDevices,
            'date' => now()->format('d/m/Y H:i')
        ]);

        return $pdf->download('Device_Credentials_' . now()->format('YmdHis') . '.pdf');
    }

    public function destroy(Device $device)
    {
        if ($device->user_id !== null) {
            return redirect()->route('admin.devices.index')->with('error', 'Perangkat tidak bisa dihapus karena sedang digunakan oleh pengguna.');
        }

        $device->delete();

        return redirect()->route('admin.devices.index')->with('success', 'Perangkat berhasil dihapus.');
    }
}
