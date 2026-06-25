<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bon de Commande {{ $po->po_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #0A192F; padding-bottom: 16px; }
        .brand-name { font-size: 18pt; font-weight: 900; color: #0A192F; }
        .brand-name span { color: #C99B0E; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .doc-label { text-align: right; }
        .doc-label h1 { font-size: 14pt; font-weight: 900; color: #0A192F; text-transform: uppercase; letter-spacing: 1px; }
        .doc-label .ref { font-size: 9pt; color: #C99B0E; font-weight: 700; margin-top: 4px; }
        .doc-label .sub { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .parties { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
        .party-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .party-box h3 { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
        .party-name { font-size: 10.5pt; font-weight: 900; }
        .party-detail { font-size: 7.5pt; color: #475569; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background: #0A192F; }
        thead th { padding: 7px 10px; font-size: 7.5pt; font-weight: 700; text-transform: uppercase; color: #fff; text-align: left; }
        thead th.num { text-align: right; }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 7px 10px; font-size: 8pt; }
        tbody td.num { text-align: right; }
        tfoot tr { background: #f1f5f9; font-weight: 700; }
        tfoot td { padding: 7px 10px; font-size: 8.5pt; }
        tfoot td.num { text-align: right; color: #0A192F; }
        .totals { margin-left: auto; width: 260px; }
        .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 8.5pt; }
        .total-row.grand { border-top: 2px solid #0A192F; margin-top: 6px; padding-top: 6px; font-size: 11pt; font-weight: 900; }
        .delivery-box { background: #f0f9ff; border: 1.5px solid #0ea5e9; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 8pt; }
        .notes-box { background: #f8fafc; border-left: 3px solid #C99B0E; padding: 8px 12px; margin-bottom: 16px; font-size: 8pt; color: #475569; }
        .sig-row { display: flex; justify-content: space-between; margin-top: 30px; font-size: 8pt; }
        .sig-box { text-align: center; width: 45%; border-top: 1px solid #0A192F; padding-top: 6px; }
        .footer { margin-top: 20px; font-size: 7pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 7pt; font-weight: 700; background: #e2e8f0; color: #0A192F; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div>
            @if($company->logo_path && \Storage::disk('public')->exists($company->logo_path))
                <img src="{{ storage_path('app/public/' . $company->logo_path) }}" style="height:40px;margin-bottom:6px">
            @endif
            <div class="brand-name">Opes<span>Books</span></div>
            <div class="brand-meta">
                {{ $company->name }}<br>
                NIU: {{ $company->niu }} | RCCM: {{ $company->rccm }}<br>
                {{ $company->address }}
            </div>
        </div>
        <div class="doc-label">
            <h1>Bon de Commande</h1>
            <div class="ref">{{ $po->po_number }}</div>
            <div class="sub">Date commande : {{ \Carbon\Carbon::parse($po->order_date)->format('d/m/Y') }}</div>
            @if($po->expected_delivery_date)
            <div class="sub">Livraison prévue : {{ \Carbon\Carbon::parse($po->expected_delivery_date)->format('d/m/Y') }}</div>
            @endif
            <div class="sub"><span class="status-badge">{{ $po->status }}</span></div>
        </div>
    </div>

    <div class="parties">
        <div class="party-box">
            <h3>Acheteur</h3>
            <div class="party-name">{{ $company->name }}</div>
            <div class="party-detail">NIU: {{ $company->niu }}</div>
            <div class="party-detail">{{ $company->address }}</div>
        </div>
        <div class="party-box">
            <h3>Fournisseur</h3>
            <div class="party-name">{{ $supplier->name }}</div>
            @if($supplier->niu)<div class="party-detail">NIU: {{ $supplier->niu }}</div>@endif
            @if($supplier->email)<div class="party-detail">{{ $supplier->email }}</div>@endif
            @if($supplier->phone)<div class="party-detail">{{ $supplier->phone }}</div>@endif
            @if($supplier->address)<div class="party-detail">{{ $supplier->address }}</div>@endif
        </div>
    </div>

    @if($po->expected_delivery_date)
    <div class="delivery-box">
        📦 <strong>Livraison attendue le {{ \Carbon\Carbon::parse($po->expected_delivery_date)->format('d/m/Y') }}</strong> à l'adresse de {{ $company->name }}.
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th>Désignation</th>
                <th class="num" style="width:12%">Qté commandée</th>
                <th class="num" style="width:12%">Qté reçue</th>
                <th class="num" style="width:18%">PU HT (XAF)</th>
                <th class="num" style="width:18%">Total HT (XAF)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $i => $line)
            <tr>
                <td style="opacity:0.5">{{ $i + 1 }}</td>
                <td>{{ $line->description }}</td>
                <td class="num">{{ number_format($line->quantity, 2, ',', ' ') }}</td>
                <td class="num" style="{{ $line->qty_received >= $line->quantity ? 'color:#16a34a' : 'color:#d97706' }}">
                    {{ number_format($line->qty_received, 2, ',', ' ') }}
                </td>
                <td class="num">{{ number_format($line->unit_price_ht, 0, ',', ' ') }}</td>
                <td class="num">{{ number_format($line->line_total_ht, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row"><span>Total HT</span><span>{{ number_format($po->amount_ht, 0, ',', ' ') }} XAF</span></div>
        <div class="total-row"><span>TVA (17,5%)</span><span>{{ number_format($po->tva_amount, 0, ',', ' ') }} XAF</span></div>
        <div class="total-row grand"><span>TOTAL TTC</span><span>{{ number_format($po->amount_ttc, 0, ',', ' ') }} XAF</span></div>
    </div>

    @if($po->notes)
    <div class="notes-box" style="margin-top:16px">
        <strong>Conditions / Notes :</strong> {{ $po->notes }}
    </div>
    @endif

    <div class="sig-row">
        <div class="sig-box">
            <div>Signature du Fournisseur</div>
            <div style="color:#64748b;font-size:7pt">Bon pour accord — {{ $supplier->name }}</div>
        </div>
        <div class="sig-box">
            <div>Autorisé par {{ $company->name }}</div>
            <div style="color:#64748b;font-size:7pt">Responsable Achats</div>
        </div>
    </div>

    <div class="footer">
        {{ $company->name }} — NIU: {{ $company->niu }} — Bon de Commande {{ $po->po_number }} — Généré par Opes Books © {{ date('Y') }}
    </div>
</div>
</body>
</html>
