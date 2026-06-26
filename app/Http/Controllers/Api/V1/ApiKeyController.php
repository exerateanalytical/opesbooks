<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    private const AVAILABLE_SCOPES = [
        'invoices:read', 'invoices:write',
        'journal:read',
        'tax:read',
        'reports:read',
        'webhooks:manage',
    ];

    public function index(Company $company): JsonResponse
    {
        $keys = ApiKey::where('company_id', $company->id)
            ->where('status', '!=', 'REVOKED')
            ->latest()
            ->get()
            ->map(fn ($k) => [
                'id'          => $k->id,
                'name'        => $k->name,
                'environment' => $k->environment,
                'key_prefix'  => $k->key_prefix . '••••',
                'scopes'      => $k->scopes,
                'status'      => $k->status,
                'last_used_at'=> $k->last_used_at,
                'expires_at'  => $k->expires_at,
                'created_at'  => $k->created_at,
            ]);

        return response()->json(['data' => $keys, 'available_scopes' => self::AVAILABLE_SCOPES]);
    }

    public function store(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'environment' => 'nullable|in:live,test',
            'scopes'      => 'required|array|min:1',
            'scopes.*'    => 'in:' . implode(',', self::AVAILABLE_SCOPES),
            'expires_at'  => 'nullable|date|after:today',
        ]);

        [$model, $plain] = ApiKey::issue([
            'company_id'  => $company->id,
            'name'        => $data['name'],
            'environment' => $data['environment'] ?? 'live',
            'scopes'      => $data['scopes'],
            'expires_at'  => $data['expires_at'] ?? null,
            'rate_limit'  => 1000,
            'created_by'  => $request->user()->id,
        ]);

        return response()->json([
            'id'      => $model->id,
            'key'     => $plain,
            'message' => 'Store this key — it will not be shown again.',
        ], 201);
    }

    public function revoke(Company $company, ApiKey $apiKey): JsonResponse
    {
        abort_if($apiKey->company_id !== $company->id, 404);
        $apiKey->update(['status' => 'REVOKED']);
        return response()->json(['message' => 'API key revoked.']);
    }

    public function logs(Company $company, ApiKey $apiKey): JsonResponse
    {
        abort_if($apiKey->company_id !== $company->id, 404);
        $logs = $apiKey->requestLogs()->latest()->limit(50)->get();
        return response()->json($logs);
    }
}
