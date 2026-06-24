<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    // GET /companies/{company}/purchase-orders
    public function index(Request $request, Company $company)
    {
        return response()->json(
            PurchaseOrder::where('company_id', $company->id)
                ->with('supplier:id,name', 'lines')
                ->when($request->status,      fn($q, $v) => $q->where('status', $v))
                ->when($request->supplier_id, fn($q, $v) => $q->where('supplier_id', $v))
                ->orderByDesc('order_date')
                ->paginate(25)
        );
    }

    // POST /companies/{company}/purchase-orders
    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'supplier_id'              => 'required|exists:suppliers,id',
            'order_date'               => 'required|date',
            'expected_delivery_date'   => 'nullable|date',
            'notes'                    => 'nullable|string',
            'lines'                    => 'nullable|array',
            'lines.*.description'      => 'required_with:lines|string|max:255',
            'lines.*.account_code'     => 'nullable|string|max:10',
            'lines.*.quantity'         => 'required_with:lines|numeric|min:0.0001',
            'lines.*.unit_price_ht'    => 'required_with:lines|numeric|min:0',
        ]);

        $prefix = 'BC-' . date('Ym') . '-';
        $last   = PurchaseOrder::where('po_number', 'like', $prefix . '%')->count();
        $poNumber = $prefix . str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        $amountHt  = 0;
        $lines     = [];
        foreach ($data['lines'] ?? [] as $l) {
            $total      = round($l['quantity'] * $l['unit_price_ht'], 2);
            $amountHt  += $total;
            $lines[]    = ['description' => $l['description'], 'account_code' => $l['account_code'] ?? null, 'quantity' => $l['quantity'], 'unit_price_ht' => $l['unit_price_ht'], 'line_total_ht' => $total, 'qty_received' => 0];
        }
        $tva       = round($amountHt * 0.175, 2);
        $amountTtc = round($amountHt + $tva, 2);

        $po = DB::transaction(function () use ($company, $data, $poNumber, $amountHt, $tva, $amountTtc, $lines) {
            $po = PurchaseOrder::create([
                'company_id'             => $company->id,
                'supplier_id'            => $data['supplier_id'],
                'po_number'              => $poNumber,
                'order_date'             => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'amount_ht'              => $amountHt,
                'tva_amount'             => $tva,
                'amount_ttc'             => $amountTtc,
                'status'                 => 'DRAFT',
                'notes'                  => $data['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                $po->lines()->create($line);
            }

            return $po;
        });

        return response()->json($po->load('lines', 'supplier:id,name'), 201);
    }

    // GET /companies/{company}/purchase-orders/{po}
    public function show(Company $company, PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->company_id !== $company->id, 404);
        return response()->json($purchaseOrder->load('lines', 'supplier:id,name,email,phone'));
    }

    // PUT /companies/{company}/purchase-orders/{po}/status
    public function updateStatus(Request $request, Company $company, PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->company_id !== $company->id, 404);

        $data = $request->validate(['status' => 'required|in:DRAFT,SENT,PARTIAL,RECEIVED,CANCELLED']);
        $purchaseOrder->update(['status' => $data['status']]);

        return response()->json($purchaseOrder);
    }

    // POST /companies/{company}/purchase-orders/{po}/receive
    public function receive(Request $request, Company $company, PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->company_id !== $company->id, 404);

        $data = $request->validate([
            'lines'             => 'required|array|min:1',
            'lines.*.line_id'   => 'required|exists:purchase_order_lines,id',
            'lines.*.qty_received' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($purchaseOrder, $data) {
            foreach ($data['lines'] as $receipt) {
                PurchaseOrderLine::where('id', $receipt['line_id'])
                    ->where('purchase_order_id', $purchaseOrder->id)
                    ->increment('qty_received', $receipt['qty_received']);
            }

            $po    = $purchaseOrder->fresh(['lines']);
            $total = $po->lines->sum('quantity');
            $rcvd  = $po->lines->sum('qty_received');
            $purchaseOrder->update(['status' => $rcvd >= $total ? 'RECEIVED' : 'PARTIAL']);
        });

        return response()->json($purchaseOrder->fresh(['lines']));
    }

    // DELETE /companies/{company}/purchase-orders/{po}
    public function destroy(Company $company, PurchaseOrder $purchaseOrder)
    {
        abort_if($purchaseOrder->company_id !== $company->id, 404);
        abort_if(!in_array($purchaseOrder->status, ['DRAFT', 'CANCELLED']), 422, 'Only DRAFT or CANCELLED orders can be deleted.');
        $purchaseOrder->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
