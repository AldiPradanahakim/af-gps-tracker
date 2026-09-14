<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('is_admin', false)->count();
        $totalDevices = Device::count();
        $usedDevices = Device::whereNotNull('user_id')->count();
        $availableDevices = Device::whereNull('user_id')->count();

        // Get devices to calculate online status using the accessor
        $allDevices = Device::get();
        $onlineDevicesCount = $allDevices->filter(function ($device) {
            return $device->is_online;
        })->count();
        $offlineDevicesCount = $usedDevices - $onlineDevicesCount;

        // Chart Data (Device Status)
        $chartData = [
            'labels' => ['Terhubung', 'Terputus', 'Tersedia'],
            'data' => [$onlineDevicesCount, $offlineDevicesCount, $availableDevices],
            'colors' => ['#10B981', '#EF4444', '#94A3B8'], // Green, Red, Slate
        ];

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDevices',
            'usedDevices',
            'availableDevices',
            'onlineDevicesCount',
            'offlineDevicesCount',
            'chartData'
        ));
    }
}
