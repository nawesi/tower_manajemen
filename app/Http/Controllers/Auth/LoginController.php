<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show()
    {
        // pastikan view login kamu ada di: resources/views/auth/login.blade.php
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

        $role = Auth::user()->role ?? 'vendor';

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
