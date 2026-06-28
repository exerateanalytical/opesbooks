<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu de paiement — {{ $receiptNumber }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10pt; color:#0f172a; background:#fff; }
        .page { padding: 32px 36px; }
        .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #010048; padding-bottom:16px; margin-bottom:24px; }
        .brand { font-size:19pt; font-weight:900; color:#010048; }
        .brand span { color:#C99B0E; }
        .brand-meta { font-size:8pt; color:#64748b; margin-top:4px; }
        .doc-label { text-align:right; }
        .doc-label h1 { font-size:16pt; font-weight:900; color:#010048; text-transform:uppercase; letter-spacing:1px; }
        .doc-label .num { font-size:10pt; font-weight:700; color:#C99B0E; margin-top:4px; }
        .doc-label .date { font-size:8.5pt; color:#475569; margin-top:2px; }
        .intro { font-size:10pt; color:#334155; line-height:1.7; margin-bottom:18px; }
        .intro strong { color:#010048; }
        .amount-box { background:#010048; border-radius:10px; padding:20px 26px; display:flex; justify-content:space-between; align-items:center; margin:18px 0 24px; }
        .amount-box .lbl { color:#cbd5e1; font-weight:900; text-transform:uppercase; font-size:11pt; letter-spacing:1px; }
        .amount-box .val { color:#C99B0E; font-weight:900; font-size:20pt; font-family: DejaVu Sans Mono, monospace; }
        table.meta { width:100%; border-collapse:collapse; margin-bottom:26px; }
        table.meta td { padding:8px 12px; font-size:9pt; border-bottom:1px solid #f1f5f9; }
        table.meta td.k { color:#64748b; font-weight:700; width:40%; }
        table.meta td.v { font-weight:700; color:#0f172a; }
        .sign { display:flex; justify-content:flex-end; margin-top:40px; }
        .sign .box { width:46%; text-align:center; }
        .sign .line { border-top:1px solid #94a3b8; margin-top:40px; padding-top:6px; font-size:8.5pt; color:#475569; }
        .footer { border-top:1px solid #e2e8f0; padding-top:12px; margin-top:30px; font-size:7.5pt; color:#94a3b8; text-align:center; }
        .stamp { display:inline-block; border:2px solid #10b981; color:#10b981; font-weight:900; text-transform:uppercase; font-size:11pt; padding:4px 14px; border-radius:6px; transform:rotate(-6deg); letter-spacing:1px; }
    </style>
</head>
@php $fmt = fn($v) => number_format((float)$v, 0, ',', ' ') . ' XAF'; @endphp
<body>
<div class="page">
    <div class="header">
        <div>
            <div class="brand">{{ strtoupper($company->name ?? 'OPESBOOKS') }}</div>
            <div class="brand-meta">
                @if($company->niu) NIU : {{ $company->niu }} @endif
                @if($company->rccm) · RCCM : {{ $company->rccm }} @endif<br>
                @if($company->address) {{ $company->address }} @endif
            </div>
        </div>
        <div class="doc-label">
            <h1>Reçu de Paiement</h1>
            <div class="num">{{ $receiptNumber }}</div>
            <div class="date">{{ \Illuminate\Support\Carbon::parse($invoice->paid_at)->format('d/m/Y') }}</div>
        </div>
    </div>

    <p class="intro">
        Reçu de <strong>{{ $customer->name ?? '—' }}</strong>@if($customer && $customer->niu) (NIU {{ $customer->niu }})@endif
        la somme de <strong>{{ $fmt($invoice->amount_ttc) }}</strong>,
        en règlement de la facture <strong>N° {{ $invoice->invoice_number }}</strong>.
    </p>

    <div class="amount-box">
        <span class="lbl">Montant reçu</span>
        <span class="val">{{ $fmt($invoice->amount_ttc) }}</span>
    </div>

    <table class="meta">
        <tr><td class="k">Facture réglée</td><td class="v">{{ $invoice->invoice_number }}</td></tr>
        <tr><td class="k">Date de la facture</td><td class="v">{{ \Illuminate\Support\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</td></tr>
        <tr><td class="k">Date de paiement</td><td class="v">{{ \Illuminate\Support\Carbon::parse($invoice->paid_at)->format('d/m/Y') }}</td></tr>
        <tr><td class="k">Montant HT</td><td class="v">{{ $fmt($invoice->amount_ht) }}</td></tr>
        <tr><td class="k">TVA + CAC</td><td class="v">{{ $fmt(($invoice->tva_amount ?? 0) + ($invoice->cac_amount ?? 0)) }}</td></tr>
        <tr><td class="k">Total TTC réglé</td><td class="v">{{ $fmt($invoice->amount_ttc) }}</td></tr>
    </table>

    <div class="sign">
        <div class="box">
            <span class="stamp">Payé</span>
            <div class="line">Cachet &amp; signature — {{ $company->name }}</div>
        </div>
    </div>

    <div class="footer">
        Reçu généré par OPESBooks · {{ $receiptNumber }} · Ce reçu atteste du règlement intégral de la facture mentionnée.
    </div>
</div>
</body>
</html>
