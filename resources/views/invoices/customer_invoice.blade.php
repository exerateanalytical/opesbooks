<!DOCTYPE html>
<html lang="{{ $lang === 'FR' ? 'fr' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $lang === 'FR' ? 'Facture Client' : 'Customer Invoice' }} {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; background: #ffffff; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #0A192F; padding-bottom: 16px; }
        .brand-name { font-size: 20pt; font-weight: 900; color: #0A192F; letter-spacing: -1px; }
        .brand-name span { color: #F59E0B; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .invoice-label { text-align: right; }
        .invoice-label h1 { font-size: 18pt; font-weight: 900; color: #0A192F; letter-spacing: 2px; text-transform: uppercase; }
        .invoice-label .inv-num { font-size: 10pt; font-weight: 700; color: #F59E0B; margin-top: 4px; }
        .invoice-label .inv-date { font-size: 8pt; color: #475569; margin-top: 2px; }
        .status-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 7.5pt; font-weight: 900; text-transform: uppercase; margin-top: 6px; }
        .status-DRAFT    { background: #f1f5f9; color: #64748b; }
        .status-SENT     { background: #dbeafe; color: #1d4ed8; }
        .status-PAID     { background: #dcfce7; color: #15803d; }
        .status-OVERDUE  { background: #fee2e2; color: #b91c1c; }
        .parties { display: flex; justify-content: space-between; margin-bottom: 20px; gap: 20px; }
        .party-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .party-box h3 { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
        .party-box .party-name { font-size: 10.5pt; font-weight: 900; color: #0f172a; }
        .party-box .party-detail { font-size: 7.5pt; color: #475569; margin-top: 2px; }
        .party-box .badge { display: inline-block; background: #0A192F; color: #F59E0B; font-size: 7pt; font-weight: 700; padding: 1px 6px; border-radius: 3px; margin-top: 4px; }
        .summary-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; margin-bottom: 20px; }
        .summary-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 8.5pt; border-bottom: 1px solid #f1f5f9; }
        .summary-row:last-child { border-bottom: none; }
        .summary-row .label { color: #64748b; font-weight: 600; }
        .summary-row .amount { font-family: monospace; font-weight: 700; color: #1e293b; }
        .summary-row.grand { background: #0A192F; margin: 8px -20px -16px; padding: 10px 20px; border-radius: 0 0 8px 8px; }
        .summary-row.grand .label { color: #cbd5e1; font-weight: 900; text-transform: uppercase; font-size: 9pt; }
        .summary-row.grand .amount { color: #F59E0B; font-size: 11pt; }
        .notes-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 12px; margin-bottom: 16px; }
        .notes-box h4 { font-size: 7pt; font-weight: 900; text-transform: uppercase; color: #92400e; margin-bottom: 4px; }
        .notes-box p { font-size: 8pt; color: #78350f; }
        .crypto-band { background: #0f172a; border-radius: 8px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .crypto-text { flex: 1; }
        .crypto-text h4 { font-size: 7pt; font-weight: 900; text-transform: uppercase; color: #F59E0B; letter-spacing: 1px; margin-bottom: 4px; }
        .crypto-text .hash { font-family: monospace; font-size: 6.5pt; color: #94a3b8; word-break: break-all; }
        .crypto-text .ts { font-size: 6.5pt; color: #64748b; margin-top: 3px; }
        .qr-wrap { margin-left: 16px; text-align: center; }
        .qr-wrap img { width: 72px; height: 72px; }
        .qr-wrap p { font-size: 5.5pt; color: #64748b; margin-top: 2px; }
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
            @if($company->letterhead_tagline)<div class="brand-meta" style="font-style:italic;color:#64748b;">{{ $company->letterhead_tagline }}</div>@endif
            <div class="brand-meta">NIU: {{ $company->niu }} | RCCM: {{ $company->rccm }}</div>
            <div class="brand-meta">{{ $company->tax_center }} · {{ $company->tax_regime }}</div>
            @if($company->address)<div class="brand-meta">{{ $company->address }}</div>@endif
            @if($company->phone)<div class="brand-meta">{{ $company->phone }}@if($company->email) · {{ $company->email }}@endif</div>@endif
        </div>
        <div class="invoice-label">
            <h1>{{ $lang === 'FR' ? 'Facture' : 'Invoice' }}</h1>
            <div class="inv-num">N° {{ $invoiceNumber }}</div>
            <div class="inv-date">{{ $lang === 'FR' ? 'Date :' : 'Date:' }} {{ \Carbon\Carbon::parse($invoiceDate)->format('d/m/Y') }}</div>
            @if($dueDate)
            <div class="inv-date">{{ $lang === 'FR' ? 'Échéance :' : 'Due:' }} {{ \Carbon\Carbon::parse($dueDate)->format('d/m/Y') }}</div>
            @endif
            <div><span class="status-badge status-{{ $invoice->status }}">{{ $invoice->status }}</span></div>
        </div>
    </div>

    <div class="parties">
        <div class="party-box">
            <h3>{{ $lang === 'FR' ? 'Émetteur' : 'From' }}</h3>
            <div class="party-name">{{ $company->name }}</div>
            <div class="party-detail">NIU: {{ $company->niu }}</div>
            <div class="party-detail">RCCM: {{ $company->rccm }}</div>
            <div class="party-detail">{{ $company->tax_center }}</div>
            @if($company->phone)<div class="party-detail">{{ $company->phone }}</div>@endif
            @if($company->email)<div class="party-detail">{{ $company->email }}</div>@endif
        </div>
        <div class="party-box">
            <h3>{{ $lang === 'FR' ? 'Facturé à' : 'Bill To' }}</h3>
            <div class="party-name">{{ $client['name'] }}</div>
            @if($client['niu'])<div class="party-detail">NIU: {{ $client['niu'] }}</div><div class="badge">ASSUJETTI TVA</div>@endif
            @if($client['address'])<div class="party-detail">{{ $client['address'] }}</div>@endif
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
        @if($invoice->withholding_received > 0)
        <div class="summary-row">
            <span class="label">{{ $lang === 'FR' ? 'Précompte reçu (5.5%)' : 'Withholding received (5.5%)' }}</span>
            <span class="amount" style="color:#b91c1c;">- {{ number_format((float)$invoice->withholding_received, 2) }} XAF</span>
        </div>
        @endif
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

    @if($company->bank_name || $company->bank_account)
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:8px 12px;margin-bottom:12px;font-size:7.5pt;color:#475569;">
        <span style="font-weight:900;text-transform:uppercase;letter-spacing:0.5px;color:#64748b;font-size:6.5pt;">{{ $lang==='FR' ? 'Domiciliation Bancaire' : 'Bank Details' }} :</span>
        @if($company->bank_name) {{ $company->bank_name }} @endif
        @if($company->bank_account) · N° {{ $company->bank_account }} @endif
        @if($company->bank_rib) · RIB: {{ $company->bank_rib }} @endif
    </div>
    @endif

    <div class="crypto-band">
        <div class="crypto-text">
            <h4>{{ $lang === 'FR' ? 'Signature Numérique DGI — SHA-256' : 'DGI Digital Signature — SHA-256' }}</h4>
            <div class="hash">{{ $hash }}</div>
            <div class="ts">{{ $lang === 'FR' ? 'Horodatage :' : 'Timestamp:' }} {{ $isoTimestamp }}</div>
        </div>
        <div class="qr-wrap">
            <img src="{{ $qrBase64 }}" alt="QR">
            <p>{{ $lang === 'FR' ? 'Vérification DGI' : 'DGI Verify' }}</p>
        </div>
    </div>

    @if($company->invoice_footer_note)
    <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:4px;padding:6px 10px;margin-bottom:10px;font-size:7pt;color:#78350f;">{{ $company->invoice_footer_note }}</div>
    @endif

    <div class="legal-footer">
        <p>{{ $lang === 'FR' ? 'Facture électronique conforme au droit fiscal camerounais (Loi de Finances).' : 'Electronic invoice compliant with Cameroonian tax law (Finance Law).' }}</p>
        <div class="dgi">DGI Cameroun · OHADA/SYSCOHADA · TVA 17.5% + CAC 10%<br>{{ $lang === 'FR' ? 'Généré par' : 'Generated by' }} Opes Books · opesbooks.cm</div>
    </div>

</div>
</body>
</html>
