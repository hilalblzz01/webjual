<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TurnstileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'password'              => 'required',
            'cf-turnstile-response' => 'required',
        ], [
            'cf-turnstile-response.required' => 'Silakan selesaikan verifikasi Captcha Cloudflare.',
        ]);

        // Verify Cloudflare Turnstile Captcha
        $captchaPass = TurnstileService::verify($request->input('cf-turnstile-response'));
        if (!$captchaPass) {
            return back()->withInput()->with('error', 'Verifikasi Captcha Cloudflare gagal.');
        }

        if (Auth::attempt(['email' => strtolower($request->email), 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Akun Anda telah dinonaktifkan.');
            }

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('home'))->with('success', 'Selamat datang kembali, ' . $user->name);
        }

        return back()->withInput()->with('error', 'Email atau password salah.');
    }
}
