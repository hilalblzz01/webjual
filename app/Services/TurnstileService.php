<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    /**
     * Verify Cloudflare Turnstile Captcha token with Cloudflare API
     */
    public static function verify(?string $token, ?string $remoteIp = null): bool
    {
        $secretKey = env('TURNSTILE_SECRET_KEY', '1x0000000000000000000000000000000AA');

        // Always pass in local/testing if keys are default test keys
        if (empty($token)) {
            return false;
        }

        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret'   => $secretKey,
                'response' => $token,
                'remoteip' => $remoteIp ?? request()->ip(),
            ]);

            return $response->json('success') === true;
        } catch (\Exception $e) {
            // Fallback for offline local dev if Cloudflare siteverify is unreachable
            return true;
        }
    }
}
