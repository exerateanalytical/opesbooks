<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $payrolls = DB::select("
            SELECT e.name AS employee_name, e.cnps_number AS employee_id_number,
                   pl.gross_salary, pl.cnps_employee, pl.cnps_employer, pl.net_salary,
                   COALESCE(pl.tsr_employer, 0) AS tsr_employer
            FROM payroll_lines pl
            JOIN employees e ON pl.employee_id = e.id
            JOIN payroll_periods pp ON pl.payroll_period_id = pp.id
            WHERE pp.company_id = ?
              AND pp.period_month = ?
              AND pp.period_year = ?
              AND pp.status = 'POSTED'
            ORDER BY e.name
        ", [$company->id, $month, $year]);

        $totalGross    = array_sum(array_column((array)$payrolls, 'gross_salary'));
        $totalEmployee = array_sum(array_column((array)$payrolls, 'cnps_employee'));
        $totalEmployer = array_sum(array_column((array)$payrolls, 'cnps_employer'));
        $totalTsr      = array_sum(array_column((array)$payrolls, 'tsr_employer'));
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
            'total_tsr'      => $totalTsr,
            'total_cnps'     => $totalCnps,
            'generated_at'   => now()->format('d/m/Y H:i'),
        ])->setPaper('a4');

        return $pdf->download("bordereau_cnps_{$year}_{$month}.pdf");
    }
}
