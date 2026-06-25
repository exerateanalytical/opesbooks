<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Avoir Client {{ $cn->credit_note_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #dc2626; padding-bottom: 16px; }
        .brand-name { font-size: 18pt; font-weight: 900; color: #0A192F; }
        .brand-name span { color: #C99B0E; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .doc-label { text-align: right; }
        .doc-label h1 { font-size: 14pt; font-weight: 900; color: #dc2626; text-transform: uppercase; letter-spacing: 1px; }
        .doc-label .ref { font-size: 9pt; color: #dc2626; font-weight: 700; margin-top: 4px; }
        .doc-label .sub { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .info-box { background: #fef2f2; border: 1.5px solid #dc2626; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 8pt; color: #991b1b; }
        .parties { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
        .party-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .party-box h3 { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
        .party-name { font-size: 10.5pt; font-weight: 900; }
        .party-detail { font-size: 7.5pt; color: #475569; margin-top: 2px; }
        .amounts { border: 2px solid #dc2626; border-radius: 6px; padding: 14px; margin-bottom: 16px; }
        .amount-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 9pt; }
        .amount-row.total { border-top: 2px solid #dc2626; margin-top: 6px; padding-top: 6px; font-size: 12pt; font-weight: 900; color: #dc2626; }
        .reason-box { background: #f8fafc; border-left: 3px solid #dc2626; padding: 8px 12px; margin-bottom: 16px; font-size: 8pt; }
        .sig-row { display: flex; justify-content: space-between; margin-top: 30px; font-size: 8pt; }
        .sig-box { text-align: center; width: 45%; border-top: 1px solid #0A192F; padding-top: 6px; }
        .footer { margin-top: 20px; font-size: 7pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
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
            <h1>Note d'Avoir</h1>
            <div class="ref">{{ $cn->credit_note_number }}</div>
            <div class="sub">Date : {{ \Carbon\Carbon::parse($cn->credit_note_date)->format('d/m/Y') }}</div>
            <div class="sub">Généré le {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="info-box">
        ↩ Ce document annule partiellement ou totalement la facture
        <strong>{{ $originalInvoice?->invoice_number ?? 'N/A' }}</strong>
        et constitue un avoir au bénéfice du client ci-dessous.
    </div>

    <div class="parties">
        <div class="party-box">
            <h3>Émetteur</h3>
            <div class="party-name">{{ $company->name }}</div>
            <div class="party-detail">NIU: {{ $company->niu }}</div>
            <div class="party-detail">{{ $company->address }}</div>
        </div>
        <div class="party-box">
            <h3>Bénéficiaire</h3>
            <div class="party-name">{{ $customer->name }}</div>
            @if($customer->niu)<div class="party-detail">NIU: {{ $customer->niu }}</div>@endif
            @if($customer->email)<div class="party-detail">{{ $customer->email }}</div>@endif
            @if($customer->phone)<div class="party-detail">{{ $customer->phone }}</div>@endif
        </div>
    </div>

    @if($cn->reason)
    <div class="reason-box">
        <strong>Motif de l'avoir :</strong> {{ $cn->reason }}
    </div>
    @endif

    <div class="amounts">
        <div class="amount-row"><span>Montant HT crédité</span><span>{{ number_format($cn->amount_ht, 0, ',', ' ') }} XAF</span></div>
        <div class="amount-row"><span>TVA créditée (17,5%)</span><span>{{ number_format($cn->tva_amount, 0, ',', ' ') }} XAF</span></div>
        <div class="amount-row"><span>CAC crédité (1,75%)</span><span>{{ number_format($cn->cac_amount, 0, ',', ' ') }} XAF</span></div>
        <div class="amount-row total"><span>TOTAL AVOIR TTC</span><span>{{ number_format($cn->amount_ttc, 0, ',', ' ') }} XAF</span></div>
    </div>

    <p style="font-size:8pt;color:#475569;margin-bottom:16px">
        Cet avoir pourra être déduit du prochain règlement ou remboursé selon accord entre les parties.
    </p>

    <div class="sig-row">
        <div class="sig-box">
            <div>Accusé de réception — Client</div>
            <div style="color:#64748b;font-size:7pt">{{ $customer->name }}</div>
        </div>
        <div class="sig-box">
            <div>Émis par {{ $company->name }}</div>
            <div style="color:#64748b;font-size:7pt">Responsable Comptable</div>
        </div>
    </div>

    <div class="footer">
        {{ $company->name }} — NIU: {{ $company->niu }} — Avoir {{ $cn->credit_note_number }} — Généré par Opes Books © {{ date('Y') }}
    </div>
</div>
</body>
</html>
