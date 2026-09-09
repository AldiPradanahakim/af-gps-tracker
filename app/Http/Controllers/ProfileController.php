<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use App\Services\Profile\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        protected ProfileService $profileService
    ) {}

    /**
     * Menampilkan halaman Lengkapi Profil
     */
    public function edit(): View
    {
        abort_unless(session()->has('activated_device_id'), 403);

        return view('profile.edit');
    }

    /**
     * Menyimpan akun baru setelah aktivasi device
     */
    public function store(ProfileUpdateRequest $request): RedirectResponse
    {
        abort_unless(session()->has('activated_device_id'), 403);

        $user = $this->profileService->createUser(
            $request->validated(),
            session('activated_device_id')
        );

        Auth::login($user);

        session()->forget('activated_device_id');

        return redirect()->route('vehicles.create');
    }

    /**
     * Update profil setelah user login
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();

        assert($user instanceof User);

        $user = $this->profileService->update(
            $user,
            $request->validated()
        );

        if ($request->expectsJson()) {

            return response()->json([

                'success' => true,

                'message' => 'Profil berhasil diperbarui.',

                'user' => [

                    'name' => $user->name,

                    'email' => $user->email,

                    'phone' => $user->phone,

                    'timezone' => $user->displayTimezone(),

                ],

            ]);
        }

        return back()->with(
            'success',
            'Profil berhasil diperbarui.'
        );
    }

    /**
     * Hapus akun
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        if ($user instanceof User) {
            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login');
    }
}
