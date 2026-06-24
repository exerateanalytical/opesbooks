<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\FixedAsset;
use App\Services\FixedAssetService;
use Illuminate\Http\Request;

class FixedAssetController extends Controller
{
    public function __construct(private FixedAssetService $svc) {}

    public function index(Company $company)
    {
        $assets = FixedAsset::where('company_id', $company->id)
            ->orderByDesc('acquisition_date')
            ->get()
            ->map(fn($a) => array_merge($a->toArray(), [
                'book_value'             => $a->bookValue(),
                'monthly_depreciation'   => $a->monthlyDepreciation(),
                'is_fully_depreciated'   => $a->isFullyDepreciated(),
            ]));

        return response()->json($assets);
    }

    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'name'                   => 'required|string|max:200',
            'category'               => 'required|in:LAND,BUILDING,MACHINERY,VEHICLE,FURNITURE,IT_EQUIPMENT,OTHER',
            'syscohada_account_code' => 'required|string|size:6',
            'acquisition_date'       => 'required|date',
            'acquisition_cost'       => 'required|numeric|min:1',
            'useful_life_months'     => 'required|integer|min:1',
            'residual_value'         => 'nullable|numeric|min:0',
            'depreciation_method'    => 'nullable|in:LINEAR,DECLINING',
            'credit_account'         => 'required|string|size:6',
        ]);

        $asset = FixedAsset::create([
            'company_id'             => $company->id,
            'name'                   => $data['name'],
            'category'               => $data['category'],
            'syscohada_account_code' => $data['syscohada_account_code'],
            'acquisition_date'       => $data['acquisition_date'],
            'acquisition_cost'       => $data['acquisition_cost'],
            'useful_life_months'     => $data['useful_life_months'],
            'residual_value'         => $data['residual_value'] ?? 0,
            'depreciation_method'    => $data['depreciation_method'] ?? 'LINEAR',
            'accumulated_depreciation' => 0,
            'is_active'              => true,
        ]);

        $asset = $this->svc->postAcquisition($asset, $data['credit_account']);

        return response()->json(array_merge($asset->toArray(), [
            'book_value'           => $asset->bookValue(),
            'monthly_depreciation' => $asset->monthlyDepreciation(),
        ]), 201);
    }

    public function show(Company $company, FixedAsset $asset)
    {
        abort_if($asset->company_id !== $company->id, 404);
        return response()->json(array_merge($asset->toArray(), [
            'book_value'             => $asset->bookValue(),
            'monthly_depreciation'   => $asset->monthlyDepreciation(),
            'is_fully_depreciated'   => $asset->isFullyDepreciated(),
            'depreciation_entries'   => $asset->depreciationEntries()->orderBy('period_year')->orderBy('period_month')->get(),
        ]));
    }

    public function runDepreciation(Request $request, Company $company)
    {
        $data = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020|max:2099',
        ]);

        $processed = $this->svc->runMonthlyDepreciation($data['month'], $data['year']);

        return response()->json(['processed' => $processed, 'period' => sprintf('%02d/%d', $data['month'], $data['year'])]);
    }

    public function dispose(Request $request, Company $company, FixedAsset $asset)
    {
        abort_if($asset->company_id !== $company->id, 404);
        abort_if(!$asset->is_active, 422, 'Asset already disposed.');

        $data = $request->validate([
            'proceeds'         => 'required|numeric|min:0',
            'receipt_account'  => 'required|string|size:6',
        ]);

        $asset = $this->svc->dispose($asset, (float)$data['proceeds'], $data['receipt_account']);

        return response()->json($asset);
    }
}
