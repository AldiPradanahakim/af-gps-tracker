<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('devices.vehicle')->where('is_admin', false);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        if ($user->is_admin) {
            return redirect()->route('admin.users.index')->with('error', 'Tidak bisa menghapus akun admin.');
        }

        // When a user is deleted, their devices should be detached (user_id = null)
        // Check how it's defined in the migration: it says nullOnDelete() for user_id on devices.
        // So we can just safely delete the user.
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
