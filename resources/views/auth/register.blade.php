@extends('layouts.app')

@section('title', 'Daftar Akun Baru')

@push('styles')
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
@endpush

@section('content')
<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm">

        <!-- Title -->
        <div class="text-center mb-6">
            <div class="w-12 h-12 gradient-primary rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-user-plus text-white text-xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Daftar Akun Baru</h1>
            <p class="text-xs text-gray-500 mt-1">Buat akun untuk mulai berbelanja produk digital</p>
        </div>

        <!-- Google OAuth Button -->
        <a href="{{ route('auth.google') }}"
           class="flex items-center justify-center gap-3 w-full py-3 px-4 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors mb-6 shadow-sm">
            <i class="fab fa-google text-red-500 text-lg"></i>
            Daftar Cepat dengan Google
        </a>

        <div class="relative flex items-center justify-center mb-6">
            <div class="border-t border-gray-200 w-full"></div>
            <span class="bg-white px-3 text-xs text-gray-400 absolute">atau daftar manual</span>
        </div>

        <!-- Form Register -->
        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition-colors @error('name') border-red-400 @enderror"
                           placeholder="Nama Anda">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Alamat Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition-colors @error('email') border-red-400 @enderror"
                           placeholder="email@example.com">
                    @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Password *</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition-colors @error('password') border-red-400 @enderror"
                           placeholder="Minimal 8 karakter">
                    @error('password')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-700 mb-1 block">Konfirmasi Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:border-primary-500 transition-colors"
                           placeholder="Ketik ulang password">
                </div>

                <!-- Cloudflare Turnstile Captcha Widget -->
                <div class="pt-2 flex flex-col items-center">
                    <div class="cf-turnstile" data-sitekey="{{ env('TURNSTILE_SITE_KEY', '1x00000000000000000000AA') }}" data-theme="light"></div>
                    @error('cf-turnstile-response')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit" class="w-full gradient-primary text-white font-bold py-3 rounded-xl mt-6 hover:opacity-90 transition-opacity">
                Daftar Akun Sekarang <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </form>

        <p class="text-xs text-center text-gray-500 mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-primary-500 font-bold hover:underline">Masuk di sini</a>
        </p>

    </div>
</div>
@endsection
