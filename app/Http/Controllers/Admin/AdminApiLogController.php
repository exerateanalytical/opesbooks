<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiRequestLog;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminApiLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ApiRequestLog::with(['apiKey', 'company'])
            ->when($request->search, fn ($q, $s) =>
                $q->where('endpoint', 'like', "%{$s}%"))
            ->when($request->status, function ($q, $status) {
                return match ($status) {
                    '2xx' => $q->whereBetween('status_code', [200, 299]),
                    '4xx' => $q->whereBetween('status_code', [400, 499]),
                    '5xx' => $q->whereBetween('status_code', [500, 599]),
                    default => $q,
                };
            })
            ->when($request->method, fn ($q, $m) => $q->where('method', $m))
            ->when($request->company_id, fn ($q, $id) => $q->where('company_id', $id))
            ->when($request->api_key_id, fn ($q, $id) => $q->where('api_key_id', $id))
            ->when($request->from, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->to, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        $stats = [
            'total'        => ApiRequestLog::count(),
            'today'        => ApiRequestLog::whereDate('created_at', today())->count(),
            // Server faults only (5xx) — client errors (4xx) are not platform-health.
            'errors_24h'   => ApiRequestLog::where('created_at', '>=', now()->subDay())
                                ->where('status_code', '>=', 500)->count(),
            'client_4xx_24h' => ApiRequestLog::where('created_at', '>=', now()->subDay())
                                ->whereBetween('status_code', [400, 499])->count(),
            'avg_latency'  => (int) round(ApiRequestLog::where('created_at', '>=', now()->subDay())->avg('latency_ms') ?? 0),
        ];

        $companies = Company::orderBy('name')->get(['id', 'name']);

        return view('admin.api-logs', compact('logs', 'stats', 'companies'));
    }
}
