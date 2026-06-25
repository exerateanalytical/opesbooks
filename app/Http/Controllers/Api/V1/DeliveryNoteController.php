<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\DeliveryNote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    // GET /companies/{company}/delivery-notes
    public function index(Request $request, Company $company)
    {
        return response()->json(
            DeliveryNote::where('company_id', $company->id)
                ->with('customer:id,name', 'supplier:id,name')
                ->when($request->status,      fn($q, $v) => $q->where('status', $v))
                ->when($request->dn_type,     fn($q, $v) => $q->where('dn_type', $v))
                ->orderByDesc('delivery_date')
                ->paginate(25)
        );
    }

    // POST /companies/{company}/delivery-notes
    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'dn_type'               => 'required|in:OUT,IN',
            'customer_id'           => 'nullable|exists:customers,id',
            'supplier_id'           => 'nullable|exists:suppliers,id',
            'customer_invoice_id'   => 'nullable|exists:customer_invoices,id',
            'purchase_order_id'     => 'nullable|exists:purchase_orders,id',
            'delivery_date'         => 'required|date',
            'delivery_address'      => 'nullable|string',
            'notes'                 => 'nullable|string',
            'lines'                 => 'nullable|array',
            'lines.*.description'   => 'required_with:lines|string|max:255',
            'lines.*.product_code'  => 'nullable|string|max:50',
            'lines.*.quantity'      => 'required_with:lines|numeric|min:0.0001',
            'lines.*.unit'          => 'nullable|string|max:20',
        ]);

        $prefix = 'BL-' . date('Ym') . '-';
        $last   = DeliveryNote::where('dn_number', 'like', $prefix . '%')->count();
        $number = $prefix . str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        $dn = DB::transaction(function () use ($company, $data, $number) {
            $dn = DeliveryNote::create([
                'company_id'           => $company->id,
                'dn_type'              => $data['dn_type'],
                'customer_id'          => $data['customer_id'] ?? null,
                'supplier_id'          => $data['supplier_id'] ?? null,
                'customer_invoice_id'  => $data['customer_invoice_id'] ?? null,
                'purchase_order_id'    => $data['purchase_order_id'] ?? null,
                'dn_number'            => $number,
                'delivery_date'        => $data['delivery_date'],
                'delivery_address'     => $data['delivery_address'] ?? null,
                'status'               => 'DRAFT',
                'notes'                => $data['notes'] ?? null,
            ]);

            foreach ($data['lines'] ?? [] as $line) {
                $dn->lines()->create([
                    'description'  => $line['description'],
                    'product_code' => $line['product_code'] ?? null,
                    'quantity'     => $line['quantity'],
                    'unit'         => $line['unit'] ?? null,
                ]);
            }

            return $dn;
        });

        return response()->json($dn->load('lines', 'customer:id,name', 'supplier:id,name'), 201);
    }

    // GET /companies/{company}/delivery-notes/{dn}
    public function show(Company $company, DeliveryNote $deliveryNote)
    {
        abort_if($deliveryNote->company_id !== $company->id, 404);
        return response()->json($deliveryNote->load('lines', 'customer:id,name,address', 'supplier:id,name,address'));
    }

    // PUT /companies/{company}/delivery-notes/{dn}/status
    public function updateStatus(Request $request, Company $company, DeliveryNote $deliveryNote)
    {
        abort_if($deliveryNote->company_id !== $company->id, 404);
        $data = $request->validate(['status' => 'required|in:DRAFT,DELIVERED,SIGNED']);
        $deliveryNote->update(['status' => $data['status']]);
        return response()->json($deliveryNote);
    }

    // DELETE /companies/{company}/delivery-notes/{dn}
    public function destroy(Company $company, DeliveryNote $deliveryNote)
    {
        abort_if($deliveryNote->company_id !== $company->id, 404);
        abort_if($deliveryNote->status === 'SIGNED', 422, 'Les bons de livraison signés ne peuvent pas être supprimés.');
        $deliveryNote->delete();
        return response()->json(['message' => 'Supprimé']);
    }

    // GET /companies/{company}/delivery-notes/{dn}/pdf
    public function pdf(Company $company, DeliveryNote $deliveryNote)
    {
        abort_if($deliveryNote->company_id !== $company->id, 404);
        $deliveryNote->load('lines', 'customer', 'supplier');

        $pdf = Pdf::loadView('delivery_notes.delivery_note', [
            'company'      => $company,
            'dn'           => $deliveryNote,
            'lines'        => $deliveryNote->lines,
            'customer'     => $deliveryNote->customer,
            'supplier'     => $deliveryNote->supplier,
        ])->setPaper('a4');

        return $pdf->download("BL-{$deliveryNote->dn_number}.pdf");
    }
}
