<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect ke Google OAuth.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google OAuth.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cari atau buat user baru
            $user = User::updateOrCreate(
                ['email' => $googleUser->email],
                [
                    'name'              => $googleUser->name,
                    'google_id'         => $googleUser->id,
                    'avatar'            => $googleUser->avatar,
                    'email_verified_at' => now(),
                ]
            );

            // Cek apakah email ini masuk daftar admin
            $adminEmails = explode(',', env('ADMIN_EMAILS', ''));
            $adminEmails = array_map('trim', $adminEmails);
            if (in_array($user->email, $adminEmails) && $user->role !== 'admin') {
                $user->update(['role' => 'admin']);
            }

            // Login user
            Auth::login($user);

            // Redirect berdasarkan role
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended(route('home'));

        } catch (\Exception $e) {
            return redirect()->route('home')
                ->with('error', 'Login dengan Google gagal. Silakan coba lagi.');
        }
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil logout.');
    }
}
