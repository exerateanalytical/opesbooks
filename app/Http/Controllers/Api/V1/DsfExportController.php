<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\DsfExportService;
use Illuminate\Http\Request;

class DsfExportController extends Controller
{
    public function __construct(private DsfExportService $svc) {}

    public function generate(Request $request, Company $company)
    {
        $data = $request->validate([
            'fiscal_year' => 'required|integer|min:2000|max:2099',
        ]);

        return response()->json($this->svc->generate($company, $data['fiscal_year']));
    }

    public function monthlyTva(Request $request, Company $company)
    {
        $data = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000|max:2099',
        ]);

        return response()->json($this->svc->monthlyTvaReturn($company, $data['month'], $data['year']));
    }

    /** GET /exports/dsf/pdf?fiscal_year= — printable DSF (liasse fiscale, synthèse). */
    public function generatePdf(Request $request, Company $company)
    {
        $data = $request->validate(['fiscal_year' => 'required|integer|min:2000|max:2099']);
        $dsf  = $this->svc->generate($company, $data['fiscal_year']);
        $pdf  = \Barryvdh\DomPDF\Facade\Pdf::loadView('tax.dsf', compact('company', 'dsf'))->setPaper('a4');
        return $pdf->stream("dsf-{$data['fiscal_year']}.pdf");
    }

    /** GET /exports/tva-monthly/pdf?month=&year= — printable TVA declaration (D10). */
    public function monthlyTvaPdf(Request $request, Company $company)
    {
        $data = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2000|max:2099',
        ]);
        $tva = $this->svc->monthlyTvaReturn($company, $data['month'], $data['year']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tax.tva_declaration', compact('company', 'tva'))->setPaper('a4');
        return $pdf->stream("declaration-tva-{$data['month']}-{$data['year']}.pdf");
    }
}
