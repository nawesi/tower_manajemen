<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput()
                ->withErrors(['username' => 'Username / password salah.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // === Cek status akun (aktif/nonaktif) ===
        if (($user->account_status ?? 'active') !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput()
                ->withErrors(['username' => 'Akun Anda nonaktif. Hubungi admin.']);
        }

        // === Cek batas akses (expired) ===
        $expired = !empty($user->access_expires_at) && $user->access_expires_at->isPast();

        if ($expired) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput()
                ->withErrors(['username' => 'Akun Anda sudah melewati batas akses. Hubungi admin untuk aktifkan kembali.']);
        }

        // === Redirect berdasarkan role ===
        $role = $user->role ?? 'vendor';

        return $role === 'admin'
            ? redirect()->route('admin.welcome')
            : redirect()->route('vendor.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}