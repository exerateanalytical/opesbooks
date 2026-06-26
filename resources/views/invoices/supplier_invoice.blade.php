<!DOCTYPE html>
<html lang="{{ $lang === 'FR' ? 'fr' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $lang === 'FR' ? 'Facture Fournisseur' : 'Supplier Invoice' }} {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; background: #ffffff; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #4f46e5; padding-bottom: 16px; }
        .brand-name { font-size: 20pt; font-weight: 900; color: #0A192F; letter-spacing: -1px; }
        .brand-name span { color: #F59E0B; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .invoice-label { text-align: right; }
        .invoice-label h1 { font-size: 18pt; font-weight: 900; color: #4f46e5; letter-spacing: 2px; text-transform: uppercase; }
        .invoice-label .inv-num { font-size: 10pt; font-weight: 700; color: #7c3aed; margin-top: 4px; }
        .invoice-label .inv-date { font-size: 8pt; color: #475569; margin-top: 2px; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 7.5pt; font-weight: 900; text-transform: uppercase; margin-top: 6px; }
        .status-DRAFT  { background: #f1f5f9; color: #64748b; }
        .status-POSTED { background: #ede9fe; color: #5b21b6; }
        .status-PAID   { background: #dcfce7; color: #15803d; }
        .parties { display: flex; justify-content: space-between; margin-bottom: 20px; gap: 20px; }
        .party-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .party-box h3 { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
        .party-box .party-name { font-size: 10.5pt; font-weight: 900; color: #0f172a; }
        .party-box .party-detail { font-size: 7.5pt; color: #475569; margin-top: 2px; }
        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; }
        .summary-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 8.5pt; border-bottom: 1px solid #f1f5f9; }
        .summary-row:last-child { border-bottom: none; }
        .summary-row .label { color: #64748b; font-weight: 600; }
        .summary-row .amount { font-family: monospace; font-weight: 700; color: #1e293b; }
        .summary-row.grand { background: #4f46e5; margin: 8px -20px -16px; padding: 10px 20px; border-radius: 0 0 8px 8px; }
        .summary-row.grand .label { color: #e0e7ff; font-weight: 900; text-transform: uppercase; font-size: 9pt; }
        .summary-row.grand .amount { color: #ffffff; font-size: 11pt; }
        .notes-box { background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 6px; padding: 10px 12px; margin-bottom: 16px; }
        .notes-box h4 { font-size: 7pt; font-weight: 900; text-transform: uppercase; color: #5b21b6; margin-bottom: 4px; }
        .notes-box p { font-size: 8pt; color: #4c1d95; }
        .ref-box { background: #fef3c7; border: 1px solid #fde68a; border-radius: 6px; padding: 8px 12px; margin-bottom: 16px; font-size: 7.5pt; color: #92400e; }
        .legal-footer { border-top: 1px solid #e2e8f0; padding-top: 10px; display: flex; justify-content: space-between; }
        .legal-footer p { font-size: 6.5pt; color: #94a3b8; }
        .legal-footer .dgi { font-size: 6.5pt; color: #64748b; font-weight: 700; }
    </style>
</head>
<body>
<div class="page">

    <div class="header">
        <div>
            @if($company->logo_path && \Storage::disk('public')->exists($company->logo_path))
                <img src="{{ storage_path('app/public/' . $company->logo_path) }}"
                     alt="{{ $company->name }}" style="max-height:56px;max-width:160px;object-fit:contain;margin-bottom:6px;display:block;">
            @else
                <div class="brand-name">OPES<span>BOOKS</span></div>
                <div class="brand-meta">opesbooks.cm | Opesware SARL</div>
            @endif
            <div class="brand-meta" style="margin-top:4px;font-weight:900;color:#0f172a;font-size:10pt;">{{ $company->name }}</div>
            @if($company->letterhead_tagline)<div class="brand-meta" style="font-style:italic;">{{ $company->letterhead_tagline }}</div>@endif
            <div class="brand-meta">NIU: {{ $company->niu }} | RCCM: {{ $company->rccm }}</div>
            <div class="brand-meta">{{ $company->tax_center }} · {{ $company->tax_regime }}</div>
            @if($company->address)<div class="brand-meta">{{ $company->address }}</div>@endif
            @if($company->phone)<div class="brand-meta">{{ $company->phone }}@if($company->email) · {{ $company->email }}@endif</div>@endif
        </div>
        <div class="invoice-label">
            <h1>{{ $lang === 'FR' ? 'Facture Fournisseur' : 'Supplier Invoice' }}</h1>
            <div class="inv-num">N° {{ $invoiceNumber }}</div>
            <div class="inv-date">{{ $lang === 'FR' ? 'Date :' : 'Date:' }} {{ \Carbon\Carbon::parse($invoiceDate)->format('d/m/Y') }}</div>
            @if($dueDate)
            <div class="inv-date">{{ $lang === 'FR' ? 'Échéance :' : 'Due:' }} {{ \Carbon\Carbon::parse($dueDate)->format('d/m/Y') }}</div>
            @endif
            <div><span class="status-badge status-{{ $invoice->status }}">{{ $invoice->status }}</span></div>
        </div>
    </div>

    @if($invoice->supplier_ref)
    <div class="ref-box">
        <strong>{{ $lang === 'FR' ? 'Réf. Fournisseur :' : 'Supplier Ref:' }}</strong> {{ $invoice->supplier_ref }}
    </div>
    @endif

    <div class="parties">
        <div class="party-box">
            <h3>{{ $lang === 'FR' ? 'Fournisseur' : 'Supplier' }}</h3>
            <div class="party-name">{{ $supplier->name }}</div>
            @if($supplier->niu)<div class="party-detail">NIU: {{ $supplier->niu }}</div>@endif
            @if($supplier->address)<div class="party-detail">{{ $supplier->address }}</div>@endif
            @if($supplier->phone)<div class="party-detail">{{ $supplier->phone }}</div>@endif
            @if($supplier->email)<div class="party-detail">{{ $supplier->email }}</div>@endif
        </div>
        <div class="party-box">
            <h3>{{ $lang === 'FR' ? 'Destinataire' : 'Bill To' }}</h3>
            <div class="party-name">{{ $company->name }}</div>
            <div class="party-detail">NIU: {{ $company->niu }}</div>
            <div class="party-detail">RCCM: {{ $company->rccm }}</div>
            @if($company->address)<div class="party-detail">{{ $company->address }}</div>@endif
        </div>
    </div>

    <div class="summary-box">
        <div class="summary-row">
            <span class="label">{{ $lang === 'FR' ? 'Montant HT' : 'Amount HT' }}</span>
            <span class="amount">{{ number_format((float)$amountHt, 2) }} XAF</span>
        </div>
        <div class="summary-row">
            <span class="label">TVA (17.5%)</span>
            <span class="amount">{{ number_format((float)$tvaAmount, 2) }} XAF</span>
        </div>
        <div class="summary-row">
            <span class="label">CAC (10% TVA)</span>
            <span class="amount">{{ number_format((float)$cacAmount, 2) }} XAF</span>
        </div>
        <div class="summary-row grand">
            <span class="label">TOTAL TTC</span>
            <span class="amount">{{ number_format((float)$amountTtc, 2) }} XAF</span>
        </div>
    </div>

    @if($notes)
    <div class="notes-box">
        <h4>{{ $lang === 'FR' ? 'Notes' : 'Notes' }}</h4>
        <p>{{ $notes }}</p>
    </div>
    @endif

    <div class="legal-footer">
        <p>{{ $lang === 'FR' ? 'Document comptable interne — Facture fournisseur enregistrée dans Opes Books.' : 'Internal accounting document — Supplier invoice recorded in Opes Books.' }}</p>
        <div class="dgi">DGI Cameroun · OHADA/SYSCOHADA · TVA 17.5% + CAC 10%<br>{{ $lang === 'FR' ? 'Généré par' : 'Generated by' }} Opes Books · opesbooks.cm</div>
    </div>

</div>
</body>
</html>
