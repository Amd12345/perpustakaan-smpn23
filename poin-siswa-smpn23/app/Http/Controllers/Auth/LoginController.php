<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        if (! $user->is_active) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun Anda belum aktif. Hubungi administrator.']);
        }

        Auth::login($user, $request->boolean('remember'));

        $user->update(['last_login_at' => now()]);

        ActivityLogService::log('login', $user, 'Login ke sistem');

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            ActivityLogService::log('logout', $user, 'Keluar dari sistem');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
