<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = Auth::user();
        assert($user instanceof User);

        if (! $user->devices()->exists()) {
            return redirect()->route('devices.create');
        }

        $device = $user->devices()->first();

        if (! $device->vehicle()->exists()) {
            return redirect()->route('vehicles.create');
        }

        return redirect()->route('home');
    }
}
