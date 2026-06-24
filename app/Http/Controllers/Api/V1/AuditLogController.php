<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Company;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request, Company $company)
    {
        $query = AuditLog::where('company_id', $company->id)
            ->with('user')
            ->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        return response()->json($query->paginate(100));
    }
}
