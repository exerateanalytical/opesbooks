<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * POST /api/v1/auth/register
     * Creates a company + owner user in a single atomic operation.
     */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'company_name'           => 'required|string|max:255',
            'company_niu'            => 'required|string|unique:companies,niu',
            'company_rccm'           => 'required|string|unique:companies,rccm',
            'company_tax_regime'     => ['required', Rule::in(['REEL', 'SIMPLIFIE', 'LIBERATOIRE'])],
            'company_tax_center'     => 'required|string|max:100',
            'company_country_code'   => 'nullable|string|size:2|exists:country_configs,country_code',
            'company_phone'          => 'nullable|string|max:20',
            'company_email'          => 'nullable|email|max:255',
            'company_address'        => 'nullable|string|max:500',
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email|unique:users,email',
            'password'               => ['required', 'confirmed', new \App\Rules\StrongPassword],
        ]);

        $company = Company::create([
            'name'                   => $data['company_name'],
            'niu'                    => strtoupper($data['company_niu']),
            'rccm'                   => strtoupper($data['company_rccm']),
            'tax_regime'             => $data['company_tax_regime'],
            'tax_center'             => $data['company_tax_center'],
            'country_code'           => strtoupper($data['company_country_code'] ?? 'CM'),
            'phone'                  => $data['company_phone'] ?? null,
            'email'                  => $data['company_email'] ?? null,
            'address'                => $data['company_address'] ?? null,
            'vat_prorata_coefficient'=> 100.00,
            'subscription_status'    => 'ACTIVE',
        ]);

        $user = User::create([
            'company_id' => $company->id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => Hash::make($data['password']),
            'role'       => 'OWNER',
        ]);
        $user->companies()->attach($company->id, ['role' => 'OWNER', 'is_default' => true]);

        // In-app welcome (always) + email (best-effort; needs SMTP).
        app(\App\Services\NotificationService::class)->push($company, [
            'type'         => 'welcome',
            'title'        => 'Bienvenue sur OPESBooks',
            'body'         => 'Configurez votre espace en quelques minutes.',
            'icon'         => 'sparkles',
            'action_url'   => '/onboarding',
            'action_label' => 'Commencer',
        ], $user);
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
        } catch (\Throwable $e) {
            // SMTP not configured in this environment — non-fatal.
        }

        $token = $user->createToken('opes-api')->plainTextToken;

        return response()->json([
            'message' => 'Registration successful. Welcome to Opes Books.',
            'token'   => $token,
            'user'    => $this->userPayload($user),
            'company' => $company,
        ], 201);
    }

    /**
     * POST /api/v1/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Two-factor challenge: password is correct, but a valid TOTP or
        // recovery code is required before a token is issued.
        if ($user->hasTwoFactorEnabled()) {
            $code = trim((string) $request->input('code', ''));
            if ($code === '') {
                return response()->json(['two_factor_required' => true, 'message' => 'Code 2FA requis.'], 422);
            }
            if (! $this->verifyTwoFactor($user, $code)) {
                return response()->json(['two_factor_required' => true, 'message' => 'Code 2FA invalide.'], 422);
            }
        }

        // Revoke all previous tokens to enforce single-session discipline
        $user->tokens()->delete();
        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->save();
        $token = $user->createToken('opes-api')->plainTextToken;

        $company = $user->company;
        $companyData = $company ? $company->toArray() : null;
        if ($company?->logo_path) {
            $companyData['logo_url'] = \Storage::url($company->logo_path);
        }
        if ($company) {
            $companyData['country_config'] = $company->countryConfig;
        }

        return response()->json([
            'message' => 'Login successful.',
            'token'   => $token,
            'user'    => $this->userPayload($user),
            'company' => $companyData,
        ]);
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * GET /api/v1/auth/me
     */
    public function me(Request $request): JsonResponse
    {
        abort_unless($request->user(), 401, 'Unauthenticated.');
        $user    = $request->user()->load('company');
        $company = $user->company;

        $companyData = $company ? $company->toArray() : null;
        if ($company && $company->logo_path) {
            $companyData['logo_url'] = \Storage::url($company->logo_path);
        }
        if ($company) {
            $companyData['country_config'] = $company->countryConfig;
        }

        return response()->json([
            'user'           => $this->userPayload($user),
            'company'        => $companyData,
            'fiscal_modules' => $company
                ? app(\App\Services\FiscalGeographyRouter::class)->getActiveFiscalModules($company)
                : null,
        ]);
    }

    /**
     * POST /api/v1/auth/users  (OWNER only — invite a team member)
     */
    public function invite(Request $request): JsonResponse
    {
        $this->authorizeOwner($request);

        // Plan limit: max users per plan.
        $company = $request->user()->company;
        if ($company && ! app(\App\Services\PlanLimitService::class)->canAddUser($company)) {
            return response()->json(
                app(\App\Services\PlanLimitService::class)->limitReached($company, 'utilisateurs'),
                402
            );
        }

        $data = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email',
            'password'             => 'required|string|min:8',
            'role'                 => ['required', Rule::in(['ACCOUNTANT', 'CLERK'])],
            'assigned_caisse_code' => 'nullable|string|max:10',
        ]);

        $user = User::create([
            'company_id'           => $request->user()->company_id,
            'name'                 => $data['name'],
            'email'                => $data['email'],
            'password'             => Hash::make($data['password']),
            'role'                 => $data['role'],
            'assigned_caisse_code' => $data['assigned_caisse_code'] ?? null,
        ]);
        $user->companies()->attach($request->user()->company_id, [
            'role'       => $data['role'],
            'is_default' => true,
        ]);

        return response()->json([
            'message' => 'Team member invited.',
            'user'    => $this->userPayload($user),
        ], 201);
    }

    /**
     * GET /api/v1/auth/users  (OWNER/ACCOUNTANT — list team)
     */
    public function team(Request $request): JsonResponse
    {
        $users = User::where('company_id', $request->user()->company_id)
            ->get()
            ->map(fn ($u) => $this->userPayload($u));

        return response()->json($users);
    }

    // -------------------------------------------------------------------------

    /** POST /api/v1/auth/logout-all — revoke every token (log out all devices). */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Toutes les sessions ont été déconnectées.']);
    }

    private function userPayload(User $user): array
    {
        return [
            'id'                   => $user->id,
            'name'                 => $user->name,
            'email'                => $user->email,
            'role'                 => $user->role,
            'company_id'           => $user->company_id,
            'assigned_caisse_code' => $user->assigned_caisse_code,
            'two_factor_enabled'   => $user->hasTwoFactorEnabled(),
            'last_login_at'        => optional($user->last_login_at)->toIso8601String(),
            'last_login_ip'        => $user->last_login_ip,
        ];
    }

    /** Verify a TOTP code or consume a one-time recovery code. */
    private function verifyTwoFactor(User $user, string $code): bool
    {
        if ($user->two_factor_secret
            && (new \PragmaRX\Google2FAQRCode\Google2FA())->verifyKey($user->two_factor_secret, $code)) {
            return true;
        }

        $recovery = $user->two_factor_recovery_codes ?? [];
        if (in_array($code, $recovery, true)) {
            $user->forceFill([
                'two_factor_recovery_codes' => array_values(array_diff($recovery, [$code])),
            ])->save();
            return true;
        }

        return false;
    }

    private function authorizeOwner(Request $request): void
    {
        if ($request->user()->role !== 'OWNER') {
            abort(403, 'Only OWNER accounts may manage team members.');
        }
    }
}
