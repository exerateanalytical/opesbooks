<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use App\Models\ApiRequestLog;
use App\Models\AuditLog;
use App\Models\Company;
use App\Models\Payment;
use App\Models\PlanConfig;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
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

        // Realized revenue by month (last 6 months) = completed payments, not
        // sub created_at — DB-agnostic via PHP grouping.
        $rows = Payment::where('status', 'completed')
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

        // Payments by method (for the billing chart) + recent payments table.
        $byMethod = Payment::where('status', 'completed')
            ->select('payment_method', DB::raw('SUM(amount_xaf) as total'))
            ->groupBy('payment_method')->pluck('total', 'payment_method')->toArray();
        $payments = Payment::with('company')->latest()->limit(20)->get();

        $metrics = [
            'mrr'           => $mrr,
            'arr'           => $mrr * 12,
            'active'        => Subscription::where('status', 'ACTIVE')->count(),
            'avg_per_co'    => Subscription::where('status', 'ACTIVE')->avg('amount_xaf') ?? 0,
            'churn_rate'    => $this->churnRate(),
            // Realized cash collected this calendar month (completed payments).
            'realized_mtd'  => (float) Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)
                ->sum('amount_xaf'),
        ];

        return view('admin.billing', compact('metrics', 'byMonth', 'byPlan', 'recent', 'byMethod', 'payments'));
    }

    private function churnRate(): float
    {
        $startActive = Subscription::where('status', 'ACTIVE')
            ->where('created_at', '<', now()->startOfMonth())->count();
        $churned = SubscriptionEvent::where('event_type', 'cancelled')
            ->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        return $startActive > 0 ? round($churned / $startActive * 100, 1) : 0;
    }

    /** POST /admin/companies/{company}/payments */
    public function recordPayment(Request $request, Company $company)
    {
        $data = $request->validate([
            'amount_xaf'     => 'required|integer|min:0',
            'payment_method' => 'required|in:orange_money,mtn_momo,bank_transfer,cash,manual,stripe',
            'reference'      => 'nullable|string|max:120',
            'period_start'   => 'nullable|date',
            'period_end'     => 'nullable|date|after_or_equal:period_start',
            'notes'          => 'nullable|string|max:500',
        ]);

        $payment = Payment::create(array_merge($data, [
            'company_id'     => $company->id,
            'plan_slug'      => $company->plan_slug ?? 'free',
            'currency'       => 'XAF',
            'status'         => 'completed',
            'receipt_number' => Payment::nextReceiptNumber(),
        ]));

        SubscriptionEvent::create([
            'company_id'    => $company->id,
            'admin_user_id' => $request->user()->id,
            'event_type'    => 'payment_received',
            'to_plan'       => $company->plan_slug,
            'amount_xaf'    => $data['amount_xaf'],
            'created_at'    => now(),
        ]);

        $company->update([
            'subscription_status'     => 'ACTIVE',
            'subscription_renewed_at' => now(),
            'next_billing_at'         => $data['period_end'] ?? now()->addMonth(),
        ]);

        return back()->with('success', "Paiement enregistré · Reçu {$payment->receipt_number}");
    }

    /** GET /admin/payments/{payment}/receipt */
    public function receipt(Payment $payment)
    {
        $payment->load('company');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.receipt', compact('payment'));
        return $pdf->stream("{$payment->receipt_number}.pdf");
    }

    /** POST /admin/payments/{payment}/refund — reverse a completed payment. */
    public function refundPayment(Request $request, Payment $payment)
    {
        if ($payment->status !== 'completed') {
            return back()->withErrors(['payment' => 'Seuls les paiements complétés peuvent être remboursés.']);
        }

        $payment->update(['status' => 'refunded']);

        SubscriptionEvent::create([
            'company_id'    => $payment->company_id,
            'admin_user_id' => $request->user()->id,
            'event_type'    => 'refund',
            'to_plan'       => $payment->plan_slug,
            'amount_xaf'    => -1 * (int) $payment->amount_xaf,
            'notes'         => 'Remboursement de ' . $payment->receipt_number,
            'created_at'    => now(),
        ]);

        return back()->with('success', "Paiement {$payment->receipt_number} remboursé.");
    }

    /** GET /admin/companies/{company}/invoice — proforma platform invoice (Opesware → tenant). */
    public function platformInvoice(Company $company)
    {
        $plan   = PlanConfig::where('slug', $company->plan_slug)->first();
        $amount = (int) ($company->custom_price_xaf ?: ($plan?->price_xaf_monthly ?? 0));
        $invoiceNumber = 'PF-' . now()->format('Y') . '-' . str_pad((string) $company->id, 5, '0', STR_PAD_LEFT);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.platform_invoice', compact('company', 'plan', 'amount', 'invoiceNumber'));
        return $pdf->stream("{$invoiceNumber}.pdf");
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

    /** POST /admin/system/retry-jobs */
    public function retryFailedJobs()
    {
        \Illuminate\Support\Facades\Artisan::call('queue:retry', ['id' => ['all']]);
        return back()->with('success', 'Jobs échoués relancés.');
    }

    /** POST /admin/system/flush-jobs */
    public function flushFailedJobs()
    {
        \Illuminate\Support\Facades\Artisan::call('queue:flush');
        return back()->with('success', 'File des jobs échoués vidée.');
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
