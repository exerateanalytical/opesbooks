<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SyscohadaAccount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubledgerController extends Controller
{
    private const MTN_RANGE    = ['prefix' => '5712', 'min' => 1, 'max' => 99, 'label' => 'MTN MoMo Ligne Marchande'];
    private const ORANGE_RANGE = ['prefix' => '5713', 'min' => 1, 'max' => 99, 'label' => 'Orange Money Ligne Marchande'];
    private const CASH_RANGE   = ['prefix' => '5711', 'min' => 1, 'max' => 99, 'label' => 'Caisse Secondaire - Caissier'];

    /**
     * POST /api/v1/companies/{company}/subledgers
     *
     * Dynamically provisions a new sub-ledger account (cash register or MoMo SIM line)
     * within the allowed dynamic ranges.
     */
    public function provision(Request $request, Company $company): JsonResponse
    {
        $data = $request->validate([
            'type'  => 'required|in:MTN,ORANGE,CASH',
            'label' => 'nullable|string|max:100',
        ]);

        $range = match ($data['type']) {
            'MTN'    => self::MTN_RANGE,
            'ORANGE' => self::ORANGE_RANGE,
            'CASH'   => self::CASH_RANGE,
        };

        // Find the next available slot in the range
        $nextCode = $this->nextAvailableCode($range);

        if (! $nextCode) {
            throw ValidationException::withMessages([
                'type' => ["Dynamic range for {$data['type']} is exhausted (max {$range['max']} sub-ledgers)."],
            ]);
        }

        $account = SyscohadaAccount::create([
            'code'        => $nextCode,
            'label'       => $data['label'] ?? "{$range['label']} #{$this->slotNumber($nextCode, $range['prefix'])}",
            'class_digit' => 5,
        ]);

        return response()->json([
            'message' => 'Sub-ledger account provisioned.',
            'account' => $account,
        ], 201);
    }

    public function list(Company $company): JsonResponse
    {
        $accounts = SyscohadaAccount::where(function ($q) {
            $q->whereBetween('code', ['571101', '571199'])
              ->orWhereBetween('code', ['571201', '571299'])
              ->orWhereBetween('code', ['571301', '571399']);
        })->orderBy('code')->get();

        return response()->json($accounts);
    }

    private function nextAvailableCode(array $range): ?string
    {
        for ($i = $range['min']; $i <= $range['max']; $i++) {
            $code = $range['prefix'] . str_pad($i, 2, '0', STR_PAD_LEFT);
            if (! SyscohadaAccount::where('code', $code)->exists()) {
                return $code;
            }
        }
        return null;
    }

    private function slotNumber(string $code, string $prefix): int
    {
        return (int) ltrim(substr($code, strlen($prefix)), '0') ?: 0;
    }
}
