<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorController extends Controller
{
    private function engine(): Google2FA
    {
        return new Google2FA();
    }

    /** GET /api/v1/auth/two-factor — current 2FA state. */
    public function status(Request $request): JsonResponse
    {
        return response()->json(['enabled' => $request->user()->hasTwoFactorEnabled()]);
    }

    /** POST /api/v1/auth/two-factor/setup — generate secret + QR (not yet confirmed). */
    public function setup(Request $request): JsonResponse
    {
        $user = $request->user();
        $g    = $this->engine();
        $secret = $g->generateSecretKey();

        $user->forceFill([
            'two_factor_secret'       => $secret,
            'two_factor_confirmed_at' => null,
        ])->save();

        $qr = $g->getQRCodeInline(config('app.name', 'OPESBooks'), $user->email, $secret);

        return response()->json(['secret' => $secret, 'qr' => $qr]);
    }

    /** POST /api/v1/auth/two-factor/confirm — verify a code, activate 2FA, return recovery codes. */
    public function confirm(Request $request): JsonResponse
    {
        $data = $request->validate(['code' => 'required|string']);
        $user = $request->user();

        if (! $user->two_factor_secret || ! $this->engine()->verifyKey($user->two_factor_secret, $data['code'])) {
            return response()->json(['message' => 'Code invalide.'], 422);
        }

        $recovery = collect(range(1, 8))->map(fn () => Str::lower(Str::random(4) . '-' . Str::random(4)))->all();

        $user->forceFill([
            'two_factor_recovery_codes' => $recovery,
            'two_factor_confirmed_at'   => now(),
        ])->save();

        return response()->json(['ok' => true, 'recovery_codes' => $recovery]);
    }

    /** POST /api/v1/auth/two-factor/disable — requires password. */
    public function disable(Request $request): JsonResponse
    {
        $data = $request->validate(['password' => 'required|string']);
        $user = $request->user();

        if (! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Mot de passe incorrect.'], 422);
        }

        $user->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ])->save();

        return response()->json(['ok' => true]);
    }

    /** POST /api/v1/auth/two-factor/recovery-codes — regenerate (requires password). */
    public function regenerate(Request $request): JsonResponse
    {
        $data = $request->validate(['password' => 'required|string']);
        $user = $request->user();
        if (! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Mot de passe incorrect.'], 422);
        }
        $recovery = collect(range(1, 8))->map(fn () => Str::lower(Str::random(4) . '-' . Str::random(4)))->all();
        $user->forceFill(['two_factor_recovery_codes' => $recovery])->save();
        return response()->json(['ok' => true, 'recovery_codes' => $recovery]);
    }
}
