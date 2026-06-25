<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiRequestLog;
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
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        $stats = [
            'total'       => ApiRequestLog::count(),
            'today'       => ApiRequestLog::whereDate('created_at', today())->count(),
            'errors_24h'  => ApiRequestLog::where('created_at', '>=', now()->subDay())
                                ->where('status_code', '>=', 400)->count(),
            'avg_latency' => (int) round(ApiRequestLog::where('created_at', '>=', now()->subDay())->avg('latency_ms') ?? 0),
        ];

        return view('admin.api-logs', compact('logs', 'stats'));
    }
}
