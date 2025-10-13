<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:technician,staff,supervisor'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'name.required' => 'Nama wajib diisi.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role tidak valid.'
        ]);

        $user = \App\Models\User::where('email', $request->email)
            ->where('name', $request->name)
            ->where('role', $request->role)
            ->first();
        if (!$user) {
            return back()->withInput($request->only('email', 'name', 'role'))
                ->withErrors(['email' => 'Data yang dimasukkan tidak cocok. Pastikan nama, email, dan role sudah benar.']);
        }

        // Generate token dan redirect ke halaman reset password (tanpa email)
        $token = app('auth.password.broker')->createToken($user);
        return redirect()->route('password.reset', ['token' => $token, 'email' => $user->email])
            ->with('status', 'Verifikasi berhasil! Silakan buat password baru.');
    }
}
