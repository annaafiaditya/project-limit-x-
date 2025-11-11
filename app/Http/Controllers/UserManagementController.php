<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class UserManagementController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->hasRole('supervisor')) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $query = User::query();

        if (request('search_name')) {
            $query->where('name', 'like', '%' . request('search_name') . '%');
        }

        if (request('search_email')) {
            $query->where('email', 'like', '%' . request('search_email') . '%');
        }

        if (request('search_role')) {
            $query->where('role', request('search_role'));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10)->appends(request()->except('page'));

        return view('user-management.index', compact('users'));
    }

    public function destroy(User $user)
    {
        // Jangan izinkan supervisor menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('user-management.index')->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('user-management.index')->with('success', 'Akun berhasil dihapus.');
    }
}
