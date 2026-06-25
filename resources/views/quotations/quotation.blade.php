<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Devis {{ $quotation->quotation_number }} — {{ $customer->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #0A192F; padding-bottom: 16px; }
        .brand-name { font-size: 18pt; font-weight: 900; color: #0A192F; }
        .brand-name span { color: #F59E0B; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .doc-label { text-align: right; }
        .doc-label h1 { font-size: 14pt; font-weight: 900; color: #0A192F; text-transform: uppercase; letter-spacing: 1px; }
        .doc-label .ref { font-size: 9pt; color: #F59E0B; font-weight: 700; margin-top: 4px; }
        .doc-label .sub { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .validity { background: #fff7ed; border: 1.5px solid #F59E0B; border-radius: 6px; padding: 8px 14px; margin-bottom: 16px; font-size: 8pt; color: #92400e; }
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
        .totals { margin-left: auto; width: 260px; }
        .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 8.5pt; }
        .total-row.grand { border-top: 2px solid #0A192F; margin-top: 6px; padding-top: 6px; font-size: 11pt; font-weight: 900; }
        .notes-box { background: #f8fafc; border-left: 3px solid #F59E0B; padding: 8px 12px; margin-bottom: 16px; font-size: 8pt; color: #475569; }
        .cta { background: #0A192F; color: #fff; border-radius: 6px; padding: 12px 16px; margin-top: 16px; font-size: 8pt; }
        .sig-row { display: flex; justify-content: space-between; margin-top: 30px; font-size: 8pt; }
        .sig-box { text-align: center; width: 45%; border-top: 1px solid #0A192F; padding-top: 6px; }
        .footer { margin-top: 20px; font-size: 7pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .xaf { font-size: 7pt; opacity: 0.6; }
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
            <h1>Devis / Pro-Forma</h1>
            <div class="ref">{{ $quotation->quotation_number }}</div>
            <div class="sub">Date : {{ \Carbon\Carbon::parse($quotation->quotation_date)->format('d/m/Y') }}</div>
            @if($quotation->valid_until)
            <div class="sub">Valable jusqu'au : {{ \Carbon\Carbon::parse($quotation->valid_until)->format('d/m/Y') }}</div>
            @endif
            <div class="sub">Généré le {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    @if($quotation->valid_until)
    <div class="validity">
        ⏱ Ce devis est valable jusqu'au <strong>{{ \Carbon\Carbon::parse($quotation->valid_until)->format('d/m/Y') }}</strong>. Au-delà de cette date, les prix peuvent être révisés.
    </div>
    @endif

    <div class="parties">
        <div class="party-box">
            <h3>Vendeur</h3>
            <div class="party-name">{{ $company->name }}</div>
            <div class="party-detail">NIU: {{ $company->niu }}</div>
            <div class="party-detail">{{ $company->address }}</div>
            @if($company->phone ?? null)<div class="party-detail">{{ $company->phone }}</div>@endif
        </div>
        <div class="party-box">
            <h3>Client</h3>
            <div class="party-name">{{ $customer->name }}</div>
            @if($customer->niu)<div class="party-detail">NIU: {{ $customer->niu }}</div>@endif
            @if($customer->email)<div class="party-detail">{{ $customer->email }}</div>@endif
            @if($customer->phone)<div class="party-detail">{{ $customer->phone }}</div>@endif
            @if($customer->address)<div class="party-detail">{{ $customer->address }}</div>@endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th>Désignation</th>
                <th class="num" style="width:12%">Qté</th>
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
                <td class="num">{{ number_format($line->unit_price_ht, 0, ',', ' ') }}</td>
                <td class="num">{{ number_format($line->line_total_ht, 0, ',', ' ') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row"><span>Total HT</span><span>{{ number_format($quotation->amount_ht, 0, ',', ' ') }} XAF</span></div>
        <div class="total-row"><span>TVA (17,5%)</span><span>{{ number_format($quotation->tva_amount, 0, ',', ' ') }} XAF</span></div>
        <div class="total-row"><span>CAC (1,75%)</span><span>{{ number_format($quotation->cac_amount, 0, ',', ' ') }} XAF</span></div>
        <div class="total-row grand"><span>TOTAL TTC</span><span>{{ number_format($quotation->amount_ttc, 0, ',', ' ') }} XAF</span></div>
    </div>

    @if($quotation->notes)
    <div class="notes-box" style="margin-top:16px">
        <strong>Notes :</strong> {{ $quotation->notes }}
    </div>
    @endif

    <div class="cta">
        <strong>Pour accepter ce devis</strong>, veuillez nous retourner ce document signé et cacheté, ou confirmer par email à {{ $company->email ?? 'notre service commercial' }}.
    </div>

    <div class="sig-row">
        <div class="sig-box">
            <div>Signature et cachet du Client</div>
            <div style="color:#64748b;font-size:7pt">{{ $customer->name }}</div>
        </div>
        <div class="sig-box">
            <div>Pour {{ $company->name }}</div>
            <div style="color:#64748b;font-size:7pt">Représentant autorisé</div>
        </div>
    </div>

    <div class="footer">
        {{ $company->name }} — NIU: {{ $company->niu }} — {{ $company->address }} — Généré par Opes Books © {{ date('Y') }}
    </div>
</div>
</body>
</html>
