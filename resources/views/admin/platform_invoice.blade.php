<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture proforma — {{ $invoiceNumber }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9.5pt; color:#0f172a; }
        .page { padding: 32px 36px; }
        .header { display:flex; justify-content:space-between; border-bottom:3px solid #010048; padding-bottom:16px; margin-bottom:24px; }
        .brand { font-size:19pt; font-weight:900; color:#010048; }
        .brand span { color:#C99B0E; }
        .brand-meta { font-size:8pt; color:#64748b; margin-top:4px; line-height:1.5; }
        .doc-label { text-align:right; }
        .doc-label h1 { font-size:15pt; font-weight:900; color:#010048; text-transform:uppercase; letter-spacing:1px; }
        .doc-label .num { font-size:10pt; font-weight:700; color:#C99B0E; margin-top:4px; }
        .doc-label .date { font-size:8pt; color:#64748b; margin-top:3px; }
        .bill-to { background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:14px; margin-bottom:20px; }
        .bill-to h3 { font-size:7pt; font-weight:900; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin-bottom:6px; }
        .bill-to .name { font-size:11pt; font-weight:900; }
        .bill-to .detail { font-size:8pt; color:#475569; margin-top:2px; }
        table { width:100%; border-collapse:collapse; margin-bottom:18px; }
        thead tr { background:#010048; }
        thead th { padding:8px 12px; font-size:7.5pt; font-weight:700; text-transform:uppercase; color:#fff; text-align:left; }
        thead th.num { text-align:right; }
        tbody td { padding:9px 12px; font-size:9pt; border-bottom:1px solid #f1f5f9; }
        tbody td.num { text-align:right; }
        .total-box { background:#010048; border-radius:8px; padding:14px 20px; display:flex; justify-content:space-between; align-items:center; }
        .total-box .lbl { color:#cbd5e1; font-weight:900; text-transform:uppercase; font-size:10pt; letter-spacing:1px; }
        .total-box .val { color:#C99B0E; font-weight:900; font-size:16pt; font-family: DejaVu Sans Mono, monospace; }
        .note { margin-top:18px; font-size:8pt; color:#64748b; line-height:1.6; }
        .proforma-badge { display:inline-block; background:#fef3c7; color:#92400e; border:1px solid #fcd34d; border-radius:5px; padding:3px 10px; font-size:8pt; font-weight:900; text-transform:uppercase; }
        .footer { border-top:1px solid #e2e8f0; padding-top:12px; margin-top:28px; font-size:7.5pt; color:#94a3b8; text-align:center; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div>
            <div class="brand">OPES<span>WARE</span></div>
            <div class="brand-meta">
                Opesware SARL · Douala, Cameroun<br>
                Éditeur de Opes Books — logiciel de comptabilité &amp; conformité fiscale<br>
                contact@opesware.cm
            </div>
        </div>
        <div class="doc-label">
            <h1>Facture Proforma</h1>
            <div class="num">{{ $invoiceNumber }}</div>
            <div class="date">{{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    <div style="margin-bottom:16px"><span class="proforma-badge">Proforma — non comptabilisée</span></div>

    <div class="bill-to">
        <h3>Facturé à</h3>
        <div class="name">{{ $company->name }}</div>
        @if($company->niu)<div class="detail">NIU : {{ $company->niu }}</div>@endif
        @if($company->rccm)<div class="detail">RCCM : {{ $company->rccm }}</div>@endif
        @if($company->address)<div class="detail">{{ $company->address }}</div>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Désignation</th>
                <th>Plan</th>
                <th class="num">Période</th>
                <th class="num">Montant (XAF)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Abonnement Opes Books</td>
                <td>{{ $plan?->name ?? strtoupper($company->plan_slug ?? '—') }}</td>
                <td class="num">Mensuel</td>
                <td class="num">{{ number_format($amount, 0, ',', ' ') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        <span class="lbl">Total à payer</span>
        <span class="val">{{ number_format($amount, 0, ',', ' ') }} XAF</span>
    </div>

    <div class="note">
        Règlement par Mobile Money (Orange Money / MTN MoMo) ou virement bancaire. Cette facture proforma précède
        l'émission du reçu officiel après encaissement. Les montants sont exprimés en francs CFA (XAF).
    </div>

    <div class="footer">
        Opesware SARL — Douala, Cameroun — Facture proforma générée par Opes Books © {{ date('Y') }}
    </div>
</div>
</body>
</html>
