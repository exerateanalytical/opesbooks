<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\StockMovement;
use App\Services\JournalPostingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Stock / inventory movements — SYSCOHADA Class 3 (Stocks).
 * Valuation method: weighted-average cost (CMUP).
 */
class StockMovementController extends Controller
{
    public function __construct(private JournalPostingService $posting) {}

    // GET /companies/{company}/stock?product_code=&from=&to=
    public function index(Request $request, Company $company)
    {
        $q = StockMovement::where('company_id', $company->id)
            ->when($request->product_code, fn($q, $v) => $q->where('product_code', $v))
            ->when($request->from,         fn($q, $v) => $q->whereDate('movement_date', '>=', $v))
            ->when($request->to,           fn($q, $v) => $q->whereDate('movement_date', '<=', $v))
            ->orderBy('movement_date')
            ->orderBy('id')
            ->paginate(50);

        return response()->json($q);
    }

    // GET /companies/{company}/stock/ledger — running stock card per product
    public function ledger(Request $request, Company $company)
    {
        $productCode = $request->input('product_code');
        if (!$productCode) {
            return response()->json(['error' => 'product_code required'], 422);
        }

        $movements = StockMovement::where('company_id', $company->id)
            ->where('product_code', $productCode)
            ->orderBy('movement_date')
            ->orderBy('id')
            ->get();

        $balance  = 0;
        $avgCost  = 0;
        $card     = [];

        foreach ($movements as $m) {
            if ($m->movement_type === 'IN') {
                $newQty    = $balance + $m->quantity;
                $avgCost   = $newQty > 0
                    ? (($balance * $avgCost) + $m->total_cost_xaf) / $newQty
                    : $m->unit_cost_xaf;
                $balance   = $newQty;
            } elseif ($m->movement_type === 'OUT') {
                $balance  -= $m->quantity;
            } else {
                // ADJUSTMENT — set absolute quantity; recalc total at current avg
                $balance   = $m->quantity;
            }

            $card[] = [
                'id'            => $m->id,
                'movement_date' => $m->movement_date->format('Y-m-d'),
                'movement_type' => $m->movement_type,
                'reference'     => $m->reference,
                'quantity'      => $m->quantity,
                'unit_cost_xaf' => round($m->unit_cost_xaf, 2),
                'total_cost_xaf'=> round($m->total_cost_xaf, 2),
                'balance_qty'   => round($balance, 4),
                'avg_cost_xaf'  => round($avgCost, 2),
                'stock_value'   => round($balance * $avgCost, 2),
            ];
        }

        return response()->json([
            'product_code'  => $productCode,
            'product_name'  => $movements->first()?->product_name,
            'current_qty'   => round($balance, 4),
            'avg_cost_xaf'  => round($avgCost, 2),
            'stock_value'   => round($balance * $avgCost, 2),
            'movements'     => $card,
        ]);
    }

    // GET /companies/{company}/stock/valuation — summary per product
    public function valuation(Request $request, Company $company)
    {
        $rows = StockMovement::where('company_id', $company->id)
            ->select('product_code', 'product_name', 'account_code')
            ->selectRaw("
                SUM(CASE WHEN movement_type='IN'  THEN quantity  ELSE 0 END)
              - SUM(CASE WHEN movement_type='OUT' THEN quantity  ELSE 0 END) AS qty_in_stock
            ")
            ->selectRaw("
                SUM(CASE WHEN movement_type='IN'  THEN total_cost_xaf ELSE 0 END)
              - SUM(CASE WHEN movement_type='OUT' THEN total_cost_xaf ELSE 0 END) AS stock_value
            ")
            ->groupBy('product_code', 'product_name', 'account_code')
            ->orderBy('product_code')
            ->get();

        $total = $rows->sum('stock_value');

        return response()->json(['items' => $rows, 'total_value_xaf' => round($total, 2)]);
    }

    // POST /companies/{company}/stock
    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'product_code'   => 'required|string|max:50',
            'product_name'   => 'required|string|max:255',
            'account_code'   => 'required|string|max:10',
            'movement_type'  => 'required|in:IN,OUT,ADJUSTMENT',
            'quantity'       => 'required|numeric|min:0.0001',
            'unit_cost_xaf'  => 'required|numeric|min:0',
            'movement_date'  => 'required|date',
            'reference'      => 'nullable|string|max:100',
            'description'    => 'nullable|string',
            'post_to_gl'     => 'boolean',
        ]);

        $totalCost = round($data['quantity'] * $data['unit_cost_xaf'], 2);

        $journalEntryId = null;

        if ($request->boolean('post_to_gl', true)) {
            // Stock IN  → Dr Stock account / Cr 401000 Fournisseurs (or 471000 for adjustments)
            // Stock OUT → Dr 601000 Achats consommés / Cr Stock account
            [$dr, $cr] = match ($data['movement_type']) {
                'IN'         => [$data['account_code'], '401000'],
                'OUT'        => ['601000', $data['account_code']],
                'ADJUSTMENT' => [$data['account_code'], '471000'],
            };

            $entry = $this->posting->post([
                'company_id'   => $company->id,
                'entry_date'   => $data['movement_date'],
                'reference'    => $data['reference'] ?? ('STOCK-' . date('Ymd')),
                'description'  => $data['description'] ?? ("Mouvement stock: {$data['product_name']}"),
                'posting_type' => 'STANDARD',
                'source'       => 'STOCK',
            ], [
                ['account_code' => $dr, 'debit'  => $totalCost, 'credit' => 0],
                ['account_code' => $cr, 'debit'  => 0,          'credit' => $totalCost],
            ]);

            $journalEntryId = $entry->id;
        }

        $movement = StockMovement::create([
            'company_id'      => $company->id,
            'product_code'    => $data['product_code'],
            'product_name'    => $data['product_name'],
            'account_code'    => $data['account_code'],
            'movement_type'   => $data['movement_type'],
            'quantity'        => $data['quantity'],
            'unit_cost_xaf'   => $data['unit_cost_xaf'],
            'total_cost_xaf'  => $totalCost,
            'movement_date'   => $data['movement_date'],
            'reference'       => $data['reference'] ?? null,
            'description'     => $data['description'] ?? null,
            'journal_entry_id'=> $journalEntryId,
        ]);

        return response()->json($movement, 201);
    }
}
