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
            'company_phone'          => 'nullable|string|max:20',
            'company_email'          => 'nullable|email|max:255',
            'company_address'        => 'nullable|string|max:500',
            'name'                   => 'required|string|max:255',
            'email'                  => 'required|email|unique:users,email',
            'password'               => 'required|string|min:8|confirmed',
        ]);

        $company = Company::create([
            'name'                   => $data['company_name'],
            'niu'                    => strtoupper($data['company_niu']),
            'rccm'                   => strtoupper($data['company_rccm']),
            'tax_regime'             => $data['company_tax_regime'],
            'tax_center'             => $data['company_tax_center'],
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

        // Revoke all previous tokens to enforce single-session discipline
        $user->tokens()->delete();
        $token = $user->createToken('opes-api')->plainTextToken;

        $company = $user->company;
        $companyData = $company ? $company->toArray() : null;
        if ($company?->logo_path) {
            $companyData['logo_url'] = \Storage::url($company->logo_path);
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

    private function userPayload(User $user): array
    {
        return [
            'id'                   => $user->id,
            'name'                 => $user->name,
            'email'                => $user->email,
            'role'                 => $user->role,
            'company_id'           => $user->company_id,
            'assigned_caisse_code' => $user->assigned_caisse_code,
        ];
    }

    private function authorizeOwner(Request $request): void
    {
        if ($request->user()->role !== 'OWNER') {
            abort(403, 'Only OWNER accounts may manage team members.');
        }
    }
}
