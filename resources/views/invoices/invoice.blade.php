<!DOCTYPE html>
<html lang="{{ $lang === 'FR' ? 'fr' : 'en' }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $lang === 'FR' ? 'Facture' : 'Invoice' }} {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #0f172a;
            background: #ffffff;
        }
        .page { padding: 28px 32px; }

        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #0A192F; padding-bottom: 16px; }
        .brand-name { font-size: 20pt; font-weight: 900; color: #0A192F; letter-spacing: -1px; }
        .brand-name span { color: #F59E0B; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .invoice-label { text-align: right; }
        .invoice-label h1 { font-size: 18pt; font-weight: 900; color: #0A192F; letter-spacing: 2px; text-transform: uppercase; }
        .invoice-label .inv-num { font-size: 10pt; font-weight: 700; color: #F59E0B; margin-top: 4px; }
        .invoice-label .inv-date { font-size: 8pt; color: #475569; margin-top: 2px; }

        /* Parties */
        .parties { display: flex; justify-content: space-between; margin-bottom: 20px; gap: 20px; }
        .party-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .party-box h3 { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
        .party-box .party-name { font-size: 10.5pt; font-weight: 900; color: #0f172a; }
        .party-box .party-detail { font-size: 7.5pt; color: #475569; margin-top: 2px; }
        .party-box .badge { display: inline-block; background: #0A192F; color: #F59E0B; font-size: 7pt; font-weight: 700; padding: 1px 6px; border-radius: 3px; margin-top: 4px; }

        /* Line items table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background: #0A192F; }
        thead th { padding: 7px 10px; font-size: 7.5pt; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #ffffff; text-align: left; }
        thead th.num { text-align: right; }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 6px 10px; font-size: 8.5pt; color: #1e293b; vertical-align: top; }
        tbody td.num { text-align: right; font-family: monospace; font-weight: 600; }
        .tax-sub { font-size: 7pt; color: #94a3b8; }

        /* Totals */
        .totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 20px; }
        .totals-box { width: 280px; border: 2px solid #e2e8f0; border-radius: 8px; overflow: hidden; }
        .totals-row { display: flex; justify-content: space-between; padding: 6px 12px; border-bottom: 1px solid #f1f5f9; font-size: 8.5pt; }
        .totals-row .label { color: #64748b; font-weight: 600; }
        .totals-row .amount { font-family: monospace; font-weight: 700; color: #1e293b; }
        .totals-row.grand { background: #0A192F; }
        .totals-row.grand .label { color: #cbd5e1; font-weight: 900; text-transform: uppercase; font-size: 9pt; }
        .totals-row.grand .amount { color: #F59E0B; font-size: 11pt; }

        /* Crypto / QR footer */
        .crypto-band { background: #0f172a; border-radius: 8px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .crypto-text { flex: 1; }
        .crypto-text h4 { font-size: 7pt; font-weight: 900; text-transform: uppercase; color: #F59E0B; letter-spacing: 1px; margin-bottom: 4px; }
        .crypto-text .hash { font-family: monospace; font-size: 6.5pt; color: #94a3b8; word-break: break-all; }
        .crypto-text .ts { font-size: 6.5pt; color: #64748b; margin-top: 3px; }
        .qr-wrap { margin-left: 16px; text-align: center; }
        .qr-wrap img { width: 72px; height: 72px; }
        .qr-wrap p { font-size: 5.5pt; color: #64748b; margin-top: 2px; }

        /* Legal footer */
        .legal-footer { border-top: 1px solid #e2e8f0; padding-top: 10px; display: flex; justify-content: space-between; }
        .legal-footer p { font-size: 6.5pt; color: #94a3b8; }
        .legal-footer .dgi { font-size: 6.5pt; color: #64748b; font-weight: 700; }

        .notes-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 10px 12px; margin-bottom: 16px; }
        .notes-box h4 { font-size: 7pt; font-weight: 900; text-transform: uppercase; color: #92400e; margin-bottom: 4px; }
        .notes-box p { font-size: 8pt; color: #78350f; }
    </style>
</head>
<body>
<div class="page">

    {{-- ── Header ─────────────────────────────────────────────────────────────── --}}
    <div class="header">
        <div>
            <div class="brand-name">OPES<span>BOOKS</span></div>
            <div class="brand-meta">opesbooks.cm | Opesware SARL</div>
            <div class="brand-meta" style="margin-top:6px; font-weight:700; color:#0f172a;">
                {{ $company->name }}
            </div>
            <div class="brand-meta">NIU: {{ $company->niu }} | RCCM: {{ $company->rccm }}</div>
            <div class="brand-meta">{{ $company->tax_center }} · {{ $company->tax_regime }}</div>
            @if($company->address)
                <div class="brand-meta">{{ $company->address }}</div>
            @endif
        </div>
        <div class="invoice-label">
            <h1>{{ $lang === 'FR' ? 'Facture' : 'Invoice' }}</h1>
            <div class="inv-num">N° {{ $invoiceNumber }}</div>
            <div class="inv-date">
                {{ $lang === 'FR' ? 'Date :' : 'Date:' }} {{ \Carbon\Carbon::parse($invoiceDate)->format('d/m/Y') }}
            </div>
            @if($dueDate)
                <div class="inv-date">
                    {{ $lang === 'FR' ? 'Échéance :' : 'Due:' }} {{ \Carbon\Carbon::parse($dueDate)->format('d/m/Y') }}
                </div>
            @endif
        </div>
    </div>

    {{-- ── Parties ─────────────────────────────────────────────────────────────── --}}
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
            <h3>{{ $lang === 'FR' ? 'Destinataire' : 'Bill To' }}</h3>
            <div class="party-name">{{ $client['name'] }}</div>
            @if($client['niu'])
                <div class="party-detail">NIU: {{ $client['niu'] }}</div>
                <div class="badge">ASSUJETTI TVA</div>
            @endif
            @if($client['address'])
                <div class="party-detail">{{ $client['address'] }}</div>
            @endif
        </div>
    </div>

    {{-- ── Line Items ──────────────────────────────────────────────────────────── --}}
    <table>
        <thead>
            <tr>
                <th style="width:38%">{{ $lang === 'FR' ? 'Désignation' : 'Description' }}</th>
                <th class="num" style="width:8%">{{ $lang === 'FR' ? 'Qté' : 'Qty' }}</th>
                <th class="num" style="width:13%">{{ $lang === 'FR' ? 'P.U. HT (XAF)' : 'Unit Price HT' }}</th>
                <th class="num" style="width:13%">{{ $lang === 'FR' ? 'Total HT' : 'Total HT' }}</th>
                <th class="num" style="width:10%">TVA 17.5%</th>
                <th class="num" style="width:8%">CAC 10%</th>
                <th class="num" style="width:10%">Total TTC</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $line)
            <tr>
                <td>{{ $line['description'] }}</td>
                <td class="num">{{ number_format((float)$line['quantity'], 2) }}</td>
                <td class="num">{{ number_format((float)$line['unit_price_ht'], 2) }}</td>
                <td class="num">{{ number_format((float)$line['total_ht'], 2) }}</td>
                <td class="num">{{ number_format((float)$line['tva'], 2) }}</td>
                <td class="num">{{ number_format((float)$line['cac'], 2) }}</td>
                <td class="num">{{ number_format((float)$line['total_ttc'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Totals ──────────────────────────────────────────────────────────────── --}}
    <div class="totals-wrap">
        <div class="totals-box">
            <div class="totals-row">
                <span class="label">{{ $lang === 'FR' ? 'Sous-total HT' : 'Subtotal HT' }}</span>
                <span class="amount">{{ number_format((float)$totals['amount_ht'], 2) }} XAF</span>
            </div>
            <div class="totals-row">
                <span class="label">TVA (17.5%)</span>
                <span class="amount">{{ number_format((float)$totals['base_vat'], 2) }} XAF</span>
            </div>
            <div class="totals-row">
                <span class="label">CAC (10% TVA)</span>
                <span class="amount">{{ number_format((float)$totals['cac'], 2) }} XAF</span>
            </div>
            <div class="totals-row">
                <span class="label">{{ $lang === 'FR' ? 'Total Taxes' : 'Total Tax' }} (19.25%)</span>
                <span class="amount">{{ number_format((float)$totals['total_tax'], 2) }} XAF</span>
            </div>
            <div class="totals-row grand">
                <span class="label">TOTAL TTC</span>
                <span class="amount">{{ number_format((float)$totals['amount_ttc'], 2) }} XAF</span>
            </div>
        </div>
    </div>

    {{-- ── Notes ───────────────────────────────────────────────────────────────── --}}
    @if($notes)
    <div class="notes-box">
        <h4>{{ $lang === 'FR' ? 'Notes' : 'Notes' }}</h4>
        <p>{{ $notes }}</p>
    </div>
    @endif

    {{-- ── Cryptographic QR band ───────────────────────────────────────────────── --}}
    <div class="crypto-band">
        <div class="crypto-text">
            <h4>{{ $lang === 'FR' ? 'Signature Numérique DGI — Empreinte SHA-256' : 'DGI Digital Signature — SHA-256 Fingerprint' }}</h4>
            <div class="hash">{{ $hash }}</div>
            <div class="ts">{{ $lang === 'FR' ? 'Horodatage ISO :' : 'ISO Timestamp:' }} {{ $isoTimestamp }}</div>
            <div class="ts" style="margin-top:4px; color:#F59E0B;">
                {{ $lang === 'FR'
                    ? 'Scannez le QR code pour vérifier l\'authenticité sur opesbooks.cm'
                    : 'Scan QR code to verify authenticity at opesbooks.cm' }}
            </div>
        </div>
        <div class="qr-wrap">
            <img src="data:image/png;base64,{{ $qrBase64 }}" alt="QR Verification">
            <p>{{ $lang === 'FR' ? 'Vérification DGI' : 'DGI Verify' }}</p>
        </div>
    </div>

    {{-- ── Legal footer ───────────────────────────────────────────────────────── --}}
    <div class="legal-footer">
        <p>
            {{ $lang === 'FR'
                ? 'Ce document est une facture électronique conforme au droit fiscal camerounais (Loi de Finances).'
                : 'This document is an electronic invoice compliant with Cameroonian tax law (Finance Law).' }}
        </p>
        <div class="dgi">
            DGI Cameroun · OHADA/SYSCOHADA · TVA 17.5% + CAC 10%<br>
            {{ $lang === 'FR' ? 'Généré par' : 'Generated by' }} Opes Books · opesbooks.cm
        </div>
    </div>

</div>
</body>
</html>
