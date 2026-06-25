<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
    // POST /auth/otp/generate — generate and email a 6-digit OTP
    public function generate(Request $request)
    {
        $user = $request->user();

        $code      = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expiresAt = now()->addMinutes(10);

        $user->update([
            'otp_code'       => $code,
            'otp_expires_at' => $expiresAt,
        ]);

        // Send OTP by email
        Mail::raw(
            "Votre code de vérification Opes Books : {$code}\n\nCe code expire dans 10 minutes.",
            fn($m) => $m
                ->to($user->email)
                ->subject("Code de vérification Opes Books — {$code}")
        );

        return response()->json(['message' => 'Code envoyé par email.', 'expires_at' => $expiresAt]);
    }

    // POST /auth/otp/verify — verify the OTP and enable 2FA
    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = $request->user();

        if (
            $user->otp_code !== $request->code ||
            ! $user->otp_expires_at ||
            now()->isAfter($user->otp_expires_at)
        ) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 422);
        }

        $user->update([
            'otp_code'        => null,
            'otp_expires_at'  => null,
            'two_fa_enabled'  => true,
        ]);

        return response()->json(['message' => 'Vérification réussie. 2FA activée.']);
    }

    // POST /auth/otp/disable — disable 2FA (requires current OTP confirmation)
    public function disable(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $user = $request->user();

        if (
            $user->otp_code !== $request->code ||
            ! $user->otp_expires_at ||
            now()->isAfter($user->otp_expires_at)
        ) {
            return response()->json(['message' => 'Code invalide ou expiré.'], 422);
        }

        $user->update([
            'otp_code'       => null,
            'otp_expires_at' => null,
            'two_fa_enabled' => false,
        ]);

        return response()->json(['message' => '2FA désactivée.']);
    }
}
