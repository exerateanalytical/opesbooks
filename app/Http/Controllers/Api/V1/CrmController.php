<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CrmActivity;
use App\Models\CrmLead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CrmController extends Controller
{
    private function companyId(Request $request): int
    {
        return (int) $request->user()->company_id;
    }

    private function findLead(Request $request, int $id): CrmLead
    {
        return CrmLead::where('company_id', $this->companyId($request))->findOrFail($id);
    }

    /** GET /api/v1/crm/leads */
    public function leads(Request $request): JsonResponse
    {
        $leads = CrmLead::where('company_id', $this->companyId($request))
            ->with(['assignee:id,name', 'client:id,name'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->source, fn ($q, $s) => $q->where('source', $s))
            ->when($request->search, fn ($q, $s) => $q->where(fn ($w) =>
                $w->where('contact_name', 'like', "%{$s}%")
                  ->orWhere('company_name', 'like', "%{$s}%")))
            ->latest()
            ->get();

        return response()->json($leads);
    }

    /** GET /api/v1/crm/stats */
    public function stats(Request $request): JsonResponse
    {
        $cid = $this->companyId($request);
        $base = CrmLead::where('company_id', $cid);

        $active   = (clone $base)->whereNotIn('status', ['won', 'lost'])->count();
        $pipeline = (clone $base)->whereNotIn('status', ['won', 'lost'])->sum('estimated_value');
        $won      = (clone $base)->where('status', 'won')->count();
        $lost     = (clone $base)->where('status', 'lost')->count();
        $closed   = $won + $lost;
        $wonMonth = (clone $base)->where('status', 'won')
            ->whereMonth('updated_at', now()->month)->whereYear('updated_at', now()->year);

        return response()->json([
            'active_leads'      => $active,
            'pipeline_value'    => round((float) $pipeline, 2),
            'conversion_rate'   => $closed > 0 ? round($won / $closed * 100, 1) : 0,
            'won_this_month'    => $wonMonth->count(),
            'won_value_month'   => round((float) (clone $wonMonth)->sum('estimated_value'), 2),
        ]);
    }

    /** POST /api/v1/crm/leads */
    public function storeLead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contact_name'    => 'required|string|max:255',
            'contact_phone'   => 'nullable|string|max:30',
            'contact_email'   => 'nullable|email|max:255',
            'company_name'    => 'nullable|string|max:255',
            'source'          => ['nullable', Rule::in(['referral', 'cold_call', 'walk_in', 'social_media', 'other'])],
            'estimated_value' => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
            'assigned_to'     => 'nullable|integer|exists:users,id',
        ]);
        $data['company_id']       = $this->companyId($request);
        $data['status']           = 'new';
        $data['source']           = $data['source'] ?? 'other';
        $data['estimated_value']  = $data['estimated_value'] ?? 0;
        $data['stage_changed_at'] = now();

        $lead = CrmLead::create($data);
        return response()->json($lead->load('assignee:id,name'), 201);
    }

    /** PUT /api/v1/crm/leads/{id} */
    public function updateLead(Request $request, int $id): JsonResponse
    {
        $lead = $this->findLead($request, $id);
        $data = $request->validate([
            'contact_name'    => 'sometimes|string|max:255',
            'contact_phone'   => 'nullable|string|max:30',
            'contact_email'   => 'nullable|email|max:255',
            'company_name'    => 'nullable|string|max:255',
            'source'          => ['nullable', Rule::in(['referral', 'cold_call', 'walk_in', 'social_media', 'other'])],
            'estimated_value' => 'nullable|numeric|min:0',
            'notes'           => 'nullable|string',
            'assigned_to'     => 'nullable|integer|exists:users,id',
        ]);
        $lead->update($data);
        return response()->json($lead->fresh('assignee:id,name'));
    }

    /** PATCH /api/v1/crm/leads/{id}/stage */
    public function updateStage(Request $request, int $id): JsonResponse
    {
        $lead = $this->findLead($request, $id);
        $data = $request->validate([
            'status'      => ['required', Rule::in(CrmLead::STATUSES)],
            'lost_reason' => 'nullable|string|max:255',
        ]);

        $lead->update([
            'status'           => $data['status'],
            'lost_reason'      => $data['status'] === 'lost' ? ($data['lost_reason'] ?? null) : null,
            'stage_changed_at' => now(),
        ]);

        // Auto-log the stage change as an activity.
        CrmActivity::create([
            'lead_id'     => $lead->id,
            'user_id'     => $request->user()->id,
            'type'        => 'note',
            'description' => 'Statut changé → ' . strtoupper($data['status'])
                . ($data['status'] === 'lost' && ! empty($data['lost_reason']) ? " ({$data['lost_reason']})" : ''),
            'completed_at' => now(),
        ]);

        return response()->json($lead->fresh());
    }

    /** POST /api/v1/crm/leads/{id}/activities */
    public function storeActivity(Request $request, int $id): JsonResponse
    {
        $lead = $this->findLead($request, $id);
        $data = $request->validate([
            'type'         => ['required', Rule::in(['call', 'meeting', 'email', 'whatsapp', 'note'])],
            'description'  => 'required|string|max:2000',
            'scheduled_at' => 'nullable|date',
        ]);
        $data['lead_id'] = $lead->id;
        $data['user_id'] = $request->user()->id;
        if (empty($data['scheduled_at'])) {
            $data['completed_at'] = now();
        }

        $activity = CrmActivity::create($data);
        return response()->json($activity->load('user:id,name'), 201);
    }

    /** GET /api/v1/crm/activities — cross-lead timeline */
    public function activities(Request $request): JsonResponse
    {
        $cid = $this->companyId($request);
        $activities = CrmActivity::whereHas('lead', fn ($q) => $q->where('company_id', $cid))
            ->with(['user:id,name', 'lead:id,contact_name'])
            ->when($request->type, fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->limit(100)
            ->get();

        return response()->json($activities);
    }
}
