<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollLine;
use App\Services\CnpsIrppService;
use App\Services\JournalPostingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PayrollController extends Controller
{
    public function __construct(
        private CnpsIrppService $cnps,
        private JournalPostingService $poster,
    ) {}

    // ── Employees ─────────────────────────────────────────────────────────────

    public function employees(Company $company): JsonResponse
    {
        return response()->json(
            Employee::where('company_id', $company->id)->where('is_active', true)->orderBy('name')->get()
        );
    }

    public function storeEmployee(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'cnps_number'      => 'nullable|string|max:30',
            'position'         => 'nullable|string|max:100',
            'gross_salary_xaf' => 'required|numeric|min:' . config('opes.smig_xaf', 36270),
            'hire_date'        => 'required|date',
        ]);
        $employee = Employee::create(['company_id' => $company->id, ...$data]);
        return response()->json($employee, 201);
    }

    public function updateEmployee(Request $request, Company $company, Employee $employee): JsonResponse
    {
        abort_if($employee->company_id !== $company->id, 404);
        $data = $request->validate([
            'name'             => 'sometimes|string|max:255',
            'cnps_number'      => 'nullable|string|max:30',
            'position'         => 'nullable|string|max:100',
            'gross_salary_xaf' => 'sometimes|numeric|min:' . config('opes.smig_xaf', 36270),
            'termination_date' => 'nullable|date',
            'is_active'        => 'sometimes|boolean',
        ]);
        $employee->update($data);
        return response()->json($employee);
    }

    // ── Payroll Periods ───────────────────────────────────────────────────────

    public function periods(Company $company): JsonResponse
    {
        return response()->json(
            PayrollPeriod::where('company_id', $company->id)
                ->orderByDesc('period_year')->orderByDesc('period_month')
                ->with('lines.employee:id,name,position')
                ->paginate(12)
        );
    }

    public function calculate(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'period_month' => 'required|integer|between:1,12',
            'period_year'  => 'required|integer|min:2020',
        ]);

        // Idempotent: return existing draft if already calculated
        $existing = PayrollPeriod::where('company_id', $company->id)
            ->where('period_month', $data['period_month'])
            ->where('period_year', $data['period_year'])
            ->where('status', 'DRAFT')
            ->with('lines.employee')
            ->first();

        if ($existing) return response()->json($existing);

        $employees = Employee::where('company_id', $company->id)->where('is_active', true)->get();

        $period = PayrollPeriod::create([
            'company_id'   => $company->id,
            'period_month' => $data['period_month'],
            'period_year'  => $data['period_year'],
            'status'       => 'DRAFT',
        ]);

        $totals = ['gross' => 0, 'cnps_e' => 0, 'cnps_r' => 0, 'irpp' => 0, 'cac' => 0, 'net' => 0, 'tsr' => 0];

        foreach ($employees as $emp) {
            $calc    = $this->cnps->calculate($emp->gross_salary_xaf);
            $tsrAmt  = round($emp->gross_salary_xaf * 0.01, 2); // TSR = 1% of gross, employer charge
            PayrollLine::create([
                'payroll_period_id' => $period->id,
                'employee_id'       => $emp->id,
                'gross_salary'      => $emp->gross_salary_xaf,
                'tsr_employer'      => $tsrAmt,
                ...$calc,
            ]);
            $totals['gross']  += $emp->gross_salary_xaf;
            $totals['cnps_e'] += $calc['cnps_employee'];
            $totals['cnps_r'] += $calc['cnps_employer'];
            $totals['irpp']   += $calc['irpp'];
            $totals['cac']    += $calc['cac_irpp'];
            $totals['net']    += $calc['net_salary'];
            $totals['tsr']    += $tsrAmt;
        }

        $period->update([
            'total_gross'          => $totals['gross'],
            'total_cnps_employee'  => $totals['cnps_e'],
            'total_cnps_employer'  => $totals['cnps_r'],
            'total_irpp'           => $totals['irpp'],
            'total_cac_irpp'       => $totals['cac'],
            'total_net'            => $totals['net'],
            'total_tsr'            => $totals['tsr'],
        ]);

        return response()->json($period->load('lines.employee'), 201);
    }

    public function post(Company $company, PayrollPeriod $period): JsonResponse
    {
        abort_if($period->company_id !== $company->id, 404);
        abort_if($period->status === 'POSTED', 422, 'Period already posted.');

        $label = sprintf('%02d/%d', $period->period_month, $period->period_year);

        // SYSCOHADA payroll entry:
        // Dr 661 (Rémunérations du personnel) — gross
        // Dr 664 (Charges sociales — part patronale CNPS)
        // Cr 421 (Personnel — rémunérations dues) — net à payer
        // Cr 431 (CNPS — part salariale)
        // Cr 432 (CNPS — part patronale)
        // Cr 447 (État — IRPP + CAC)
        $entry = $this->poster->post([
            'company_id'      => $company->id,
            'posting_date'    => now()->toDateString(),
            'reference_id'    => 'PAY-' . $label . '-' . strtoupper(Str::random(6)),
            'source_pipeline' => 'MANUAL_BANK',
            'memo'            => "Paie {$label} — {$period->lines->count()} salarié(s)",
            'posting_type'    => 'STANDARD',
        ], [
            ['account_code' => '661100', 'debit' => $period->total_gross, 'credit' => 0],
            ['account_code' => '664000', 'debit' => $period->total_cnps_employer, 'credit' => 0],
            ['account_code' => '664100', 'debit' => $period->total_tsr ?? 0, 'credit' => 0],
            ['account_code' => '421100', 'debit' => 0, 'credit' => $period->total_net],
            ['account_code' => '431000', 'debit' => 0, 'credit' => $period->total_cnps_employee + $period->total_cnps_employer],
            ['account_code' => '447000', 'debit' => 0, 'credit' => $period->total_irpp + $period->total_cac_irpp],
            ['account_code' => '447300', 'debit' => 0, 'credit' => $period->total_tsr ?? 0],
        ]);

        $period->update(['status' => 'POSTED', 'journal_entry_id' => $entry->id]);
        return response()->json($period->load('journalEntry'));
    }

    /**
     * GET /payroll/periods/{period}/payslip/{employee}
     * Printable pay slip (bulletin de paie) for one employee in a period.
     */
    public function payslip(Company $company, PayrollPeriod $period, Employee $employee)
    {
        abort_if($period->company_id !== $company->id, 404);
        abort_if($employee->company_id !== $company->id, 404);

        $line = PayrollLine::where('payroll_period_id', $period->id)
            ->where('employee_id', $employee->id)
            ->firstOrFail();

        $months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.payslip', [
            'company'     => $company,
            'employee'    => $employee,
            'line'        => $line,
            'periodLabel' => ($months[$period->period_month] ?? $period->period_month) . ' ' . $period->period_year,
        ])->setPaper('a4');

        return $pdf->stream("bulletin_{$employee->name}_{$period->period_month}_{$period->period_year}.pdf");
    }

    /**
     * GET /payroll/dipe?year= — DIPE (Déclaration des Informations sur le
     * Personnel Employé): yearly per-employee summary of gross, CNPS & IRPP.
     */
    public function dipe(Request $request, Company $company)
    {
        $year = (int) $request->input('year', date('Y'));

        $lines = PayrollLine::with('employee:id,name,cnps_number,position')
            ->whereHas('period', fn ($q) => $q->where('company_id', $company->id)->where('period_year', $year))
            ->get();

        $byEmp = [];
        foreach ($lines as $l) {
            $eid = $l->employee_id;
            $byEmp[$eid] ??= [
                'name' => $l->employee?->name, 'cnps' => $l->employee?->cnps_number,
                'position' => $l->employee?->position,
                'gross' => 0, 'cnps_emp' => 0, 'irpp' => 0, 'cac' => 0, 'months' => 0,
            ];
            $byEmp[$eid]['gross']    += (float) $l->gross_salary;
            $byEmp[$eid]['cnps_emp'] += (float) $l->cnps_employee;
            $byEmp[$eid]['irpp']     += (float) $l->irpp;
            $byEmp[$eid]['cac']      += (float) $l->cac_irpp;
            $byEmp[$eid]['months']++;
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payroll.dipe', [
            'company' => $company,
            'year'    => $year,
            'rows'    => array_values($byEmp),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream("dipe-{$year}.pdf");
    }
}
