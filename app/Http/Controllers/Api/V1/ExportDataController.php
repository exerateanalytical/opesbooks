<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExportDataController extends Controller
{
    public function download(Request $request, Company $company, string $type)
    {
        // Scope: the authenticated user must belong to this company.
        abort_unless($request->user()->company_id === $company->id, 403);

        $request->validate([
            'format' => ['nullable', Rule::in(['xlsx', 'csv', 'pdf'])],
        ]);
        abort_unless(in_array($type, ExportService::TYPES, true), 404);

        return app(ExportService::class)->download($type, $request->query('format', 'xlsx'), $company);
    }
}
