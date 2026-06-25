<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminInsightsController extends Controller
{
    /** Dedicated companies list (the dashboard is the platform overview). */
    public function companies(Request $request)
    {
        $companies = Company::withCount('users')
            ->with(['subscriptions' => fn ($q) => $q->latest()])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")
                ->orWhere('niu', 'like', "%{$s}%"))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.companies', compact('companies'));
    }

    public function subscriptions(Request $request)
    {
        $subscriptions = Subscription::with('company')
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'active'    => Subscription::where('status', 'ACTIVE')->count(),
            'suspended' => Subscription::where('status', 'SUSPENDED')->count(),
            'cancelled' => Subscription::where('status', 'CANCELLED')->count(),
        ];

        return view('admin.subscriptions', compact('subscriptions', 'stats'));
    }

    public function billing()
    {
        $mrr = (float) Subscription::where('status', 'ACTIVE')->sum('amount_xaf');

        // Revenue by month (last 6 months) — DB-agnostic via PHP grouping.
        $rows = Subscription::where('status', 'ACTIVE')
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->get(['amount_xaf', 'created_at']);

        $byMonth = [];
        foreach ($rows as $r) {
            $k = $r->created_at?->format('Y-m') ?? now()->format('Y-m');
            $byMonth[$k] = ($byMonth[$k] ?? 0) + (float) $r->amount_xaf;
        }
        ksort($byMonth);

        $byPlan = Subscription::where('status', 'ACTIVE')
            ->select('plan', DB::raw('COUNT(*) as n'), DB::raw('SUM(amount_xaf) as total'))
            ->groupBy('plan')->get();

        $recent = Subscription::with('company')->latest()->limit(15)->get();

        $metrics = [
            'mrr'           => $mrr,
            'arr'           => $mrr * 12,
            'active'        => Subscription::where('status', 'ACTIVE')->count(),
            'avg_per_co'    => Subscription::where('status', 'ACTIVE')->avg('amount_xaf') ?? 0,
        ];

        return view('admin.billing', compact('metrics', 'byMonth', 'byPlan', 'recent'));
    }

    public function audit(Request $request)
    {
        $logs = AuditLog::with(['user', 'company'])
            ->when($request->search, fn ($q, $s) => $q->where('action', 'like', "%{$s}%")
                ->orWhere('model_type', 'like', "%{$s}%"))
            ->when($request->action, fn ($q, $a) => $q->where('action', $a))
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('admin.audit', compact('logs'));
    }

    public function system()
    {
        $services = [
            'Database'     => $this->checkDatabase(),
            'Cache'        => $this->checkCache(),
            'Queue'        => ['ok' => Schema::hasTable('jobs'), 'detail' => Schema::hasTable('jobs') ? (DB::table('jobs')->count() . ' pending') : 'no jobs table'],
            'Storage'      => ['ok' => is_writable(storage_path()), 'detail' => is_writable(storage_path()) ? 'writable' : 'not writable'],
        ];

        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;

        $counts = [
            'companies'     => Company::count(),
            'users'         => User::count(),
            'api_keys'      => ApiKey::where('status', 'ACTIVE')->count(),
            'api_calls_24h' => ApiRequestLog::where('created_at', '>=', now()->subDay())->count(),
        ];

        return view('admin.system', compact('services', 'failedJobs', 'counts'));
    }

    private function checkDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            return ['ok' => true, 'detail' => round((microtime(true) - $start) * 1000, 1) . ' ms'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'detail' => 'unreachable'];
        }
    }

    private function checkCache(): array
    {
        try {
            cache()->put('__health', 1, 5);
            return ['ok' => cache()->get('__health') === 1, 'detail' => 'operational'];
        } catch (\Throwable $e) {
            return ['ok' => false, 'detail' => 'error'];
        }
    }
}
