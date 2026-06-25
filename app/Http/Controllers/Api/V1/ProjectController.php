<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    private function companyId(Request $request): int
    {
        return (int) $request->user()->company_id;
    }

    private function find(Request $request, int $id): Project
    {
        return Project::where('company_id', $this->companyId($request))->findOrFail($id);
    }

    private function summarize(Project $p): array
    {
        $revenue = $p->totalRevenue();
        $costs   = $p->totalCosts();
        return [
            'id'             => $p->id,
            'name'           => $p->name,
            'code'           => $p->code,
            'client'         => $p->client?->only('id', 'name'),
            'status'         => $p->status,
            'start_date'     => $p->start_date?->toDateString(),
            'end_date'       => $p->end_date?->toDateString(),
            'budget_amount'  => $p->budget_amount,
            'contract_value' => $p->contract_value,
            'revenue'        => round($revenue, 2),
            'costs'          => round($costs, 2),
            'profit'         => round($revenue - $costs, 2),
            'margin'         => $revenue > 0 ? round(($revenue - $costs) / $revenue * 100, 1) : 0,
        ];
    }

    /** GET /api/v1/projects */
    public function index(Request $request): JsonResponse
    {
        $projects = Project::where('company_id', $this->companyId($request))
            ->with('client:id,name')->latest()->get()
            ->map(fn ($p) => $this->summarize($p));

        return response()->json($projects);
    }

    /** POST /api/v1/projects */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'client_id'      => 'nullable|integer|exists:customers,id',
            'description'    => 'nullable|string',
            'status'         => ['nullable', Rule::in(Project::STATUSES)],
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date|after_or_equal:start_date',
            'budget_amount'  => 'nullable|numeric|min:0',
            'contract_value' => 'nullable|numeric|min:0',
        ]);
        $cid = $this->companyId($request);

        $seq  = Project::where('company_id', $cid)->count() + 1;
        $data['company_id'] = $cid;
        $data['status']     = $data['status'] ?? 'active';
        $data['code']       = 'PROJ-' . now()->year . '-' . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);

        $project = Project::create($data);
        return response()->json($this->summarize($project), 201);
    }

    /** GET /api/v1/projects/{id} */
    public function show(Request $request, int $id): JsonResponse
    {
        $project = $this->find($request, $id);
        $entries = $project->entries()->get();

        $byType = [];
        foreach ($entries as $e) {
            $byType[$e->entry_type] = ($byType[$e->entry_type] ?? 0) + $e->amount;
        }

        return response()->json([
            'project'        => $this->summarize($project),
            'description'    => $project->description,
            'entries'        => $entries,
            'cost_breakdown' => $byType,
        ]);
    }

    /** PATCH /api/v1/projects/{id}/status */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $project = $this->find($request, $id);
        $data = $request->validate(['status' => ['required', Rule::in(Project::STATUSES)]]);
        $project->update($data);
        return response()->json($this->summarize($project));
    }

    /** POST /api/v1/projects/{id}/entries — add a manual revenue/cost line */
    public function addEntry(Request $request, int $id): JsonResponse
    {
        $project = $this->find($request, $id);
        $data = $request->validate([
            'entry_type'  => ['required', Rule::in(['invoice', 'supplier_invoice', 'journal_entry', 'payroll_cost', 'expense'])],
            'amount'      => 'required|numeric',
            'direction'   => ['required', Rule::in(['revenue', 'cost'])],
            'description' => 'required|string|max:255',
            'entry_date'  => 'nullable|date',
        ]);

        $signed = $data['direction'] === 'cost' ? -abs($data['amount']) : abs($data['amount']);

        $entry = ProjectEntry::create([
            'project_id'  => $project->id,
            'company_id'  => $project->company_id,
            'entry_type'  => $data['entry_type'],
            'amount'      => $signed,
            'description' => $data['description'],
            'entry_date'  => $data['entry_date'] ?? now()->toDateString(),
        ]);

        return response()->json($entry, 201);
    }
}
