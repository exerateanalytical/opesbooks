<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CameroonTaxEngine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaxCalculatorController extends Controller
{
    public function fromHt(Request $request): JsonResponse
    {
        $request->validate([
            'amount_ht' => 'required|numeric|min:0',
        ]);

        return response()->json(CameroonTaxEngine::compute((string) $request->input('amount_ht')));
    }

    public function fromTtc(Request $request): JsonResponse
    {
        $request->validate([
            'amount_ttc' => 'required|numeric|min:0',
        ]);

        return response()->json(CameroonTaxEngine::reverseFromTtc((string) $request->input('amount_ttc')));
    }
}
