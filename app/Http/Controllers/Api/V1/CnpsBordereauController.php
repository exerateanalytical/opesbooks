<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * CNPS Bordereau Mensuel — monthly social security contribution declaration.
 * Employers in Cameroun must file this with CNPS (Caisse Nationale de Prévoyance Sociale).
 */
class CnpsBordereauController extends Controller
{
    public function generate(Request $request, Company $company)
    {
        $data = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020|max:2099',
        ]);

        $month = (int) $data['month'];
        $year  = (int) $data['year'];
        $from  = sprintf('%d-%02d-01', $year, $month);
        $to    = date('Y-m-t', strtotime($from));

        // Pull payroll entries for the period
        $payrolls = DB::select("
            SELECT p.employee_name, p.employee_id_number, p.gross_salary,
                   p.cnps_employee, p.cnps_employer, p.net_salary,
                   p.pay_period_start, p.pay_period_end
            FROM payrolls p
            WHERE p.company_id = ?
              AND p.pay_period_start >= ?
              AND p.pay_period_end <= ?
              AND p.status = 'PAID'
              AND p.deleted_at IS NULL
            ORDER BY p.employee_name
        ", [$company->id, $from, $to]);

        $totalGross    = array_sum(array_column((array)$payrolls, 'gross_salary'));
        $totalEmployee = array_sum(array_column((array)$payrolls, 'cnps_employee'));
        $totalEmployer = array_sum(array_column((array)$payrolls, 'cnps_employer'));
        $totalCnps     = $totalEmployee + $totalEmployer;

        $pdf = Pdf::loadView('certificates.cnps_bordereau', [
            'company'        => $company,
            'payrolls'       => $payrolls,
            'month'          => $month,
            'year'           => $year,
            'period_label'   => \Carbon\Carbon::create($year, $month)->translatedFormat('F Y'),
            'total_gross'    => $totalGross,
            'total_employee' => $totalEmployee,
            'total_employer' => $totalEmployer,
            'total_cnps'     => $totalCnps,
            'generated_at'   => now()->format('d/m/Y H:i'),
        ])->setPaper('a4');

        return $pdf->download("bordereau_cnps_{$year}_{$month}.pdf");
    }
}
