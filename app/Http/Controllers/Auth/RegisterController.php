<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TurnstileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'cf-turnstile-response' => 'required',
        ], [
            'email.unique'                  => 'Email ini sudah terdaftar. Silakan login.',
            'password.confirmed'            => 'Konfirmasi password tidak cocok.',
            'cf-turnstile-response.required'=> 'Silakan selesaikan verifikasi Captcha Cloudflare.',
        ]);

        // Verify Cloudflare Turnstile Captcha
        $captchaPass = TurnstileService::verify($request->input('cf-turnstile-response'));
        if (!$captchaPass) {
            return back()->withInput()->with('error', 'Verifikasi Captcha Cloudflare gagal. Silakan coba lagi.');
        }

        // Check if email is admin email
        $adminEmails = array_map('trim', explode(',', env('ADMIN_EMAILS', '')));
        $role = in_array($request->email, $adminEmails) ? 'admin' : 'customer';

        $user = User::create([
            'name'              => $request->name,
            'email'             => strtolower($request->email),
            'password'          => Hash::make($request->password),
            'role'              => $role,
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        Auth::login($user);

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', 'Registrasi Admin berhasil!');
        }

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat! Selamat berbelanja.');
    }
}
