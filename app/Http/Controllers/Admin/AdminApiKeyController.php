<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminApiKeyController extends Controller
{
    /** Scopes offered when issuing a key. */
    public const SCOPES = [
        'invoices:read', 'invoices:write',
        'journal:read', 'journal:write',
        'reports:read', 'accounts:read',
        'tax:read', 'clients:write',
        'webhooks:manage',
    ];

    public function index(Request $request)
    {
        $keys = ApiKey::with('company')
            ->when($request->search, fn ($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%{$s}%")))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $companies = Company::orderBy('name')->get(['id', 'name']);
        $scopes    = self::SCOPES;

        // Surface a freshly-generated plaintext key exactly once.
        $newKey = session('new_api_key');

        return view('admin.api-keys', compact('keys', 'companies', 'scopes', 'newKey'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_id'  => 'required|exists:companies,id',
            'name'        => 'required|string|max:100',
            'environment' => 'required|in:live,test',
            'scopes'      => 'required|array|min:1',
            'scopes.*'    => 'string|in:' . implode(',', self::SCOPES),
            'rate_limit'  => 'required|integer|min:10|max:100000',
            'expires_at'  => 'nullable|date|after:today',
        ]);

        [, $plain] = ApiKey::issue([
            'company_id'  => $data['company_id'],
            'name'        => $data['name'],
            'environment' => $data['environment'],
            'scopes'      => $data['scopes'] ?? [],
            'rate_limit'  => $data['rate_limit'],
            'expires_at'  => $data['expires_at'] ?? null,
            'created_by'  => $request->user()->id,
        ]);

        return back()->with('new_api_key', $plain)
                     ->with('success', 'API key generated — copy it now, it will not be shown again.');
    }

    public function revoke(ApiKey $apiKey)
    {
        $apiKey->update(['status' => 'REVOKED']);
        return back()->with('success', "API key “{$apiKey->name}” revoked.");
    }
}
