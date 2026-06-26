<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Avoir Fournisseur {{ $cn->credit_note_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #7c3aed; padding-bottom: 16px; }
        .brand-name { font-size: 18pt; font-weight: 900; color: #0A192F; }
        .brand-name span { color: #F59E0B; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .doc-label { text-align: right; }
        .doc-label h1 { font-size: 14pt; font-weight: 900; color: #7c3aed; text-transform: uppercase; letter-spacing: 1px; }
        .doc-label .ref { font-size: 9pt; color: #7c3aed; font-weight: 700; margin-top: 4px; }
        .doc-label .sub { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .info-box { background: #f5f3ff; border: 1.5px solid #7c3aed; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 8pt; color: #5b21b6; }
        .parties { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
        .party-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .party-box h3 { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
        .party-name { font-size: 10.5pt; font-weight: 900; }
        .party-detail { font-size: 7.5pt; color: #475569; margin-top: 2px; }
        .amounts { border: 2px solid #7c3aed; border-radius: 6px; padding: 14px; margin-bottom: 16px; }
        .amount-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 9pt; }
        .amount-row.total { border-top: 2px solid #7c3aed; margin-top: 6px; padding-top: 6px; font-size: 12pt; font-weight: 900; color: #7c3aed; }
        .reason-box { background: #f8fafc; border-left: 3px solid #7c3aed; padding: 8px 12px; margin-bottom: 16px; font-size: 8pt; }
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
            <h1>Avoir Fournisseur</h1>
            <div class="ref">{{ $cn->credit_note_number }}</div>
            <div class="sub">Date : {{ \Carbon\Carbon::parse($cn->credit_note_date)->format('d/m/Y') }}</div>
            <div class="sub">Généré le {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="info-box">
        ↩ Avoir reçu de <strong>{{ $supplier->name }}</strong> en réduction de la facture
        <strong>{{ $originalInvoice?->invoice_number ?? 'N/A' }}</strong>.
        Ce document réduit le montant dû à ce fournisseur.
    </div>

    <div class="parties">
        <div class="party-box">
            <h3>Bénéficiaire (Acheteur)</h3>
            <div class="party-name">{{ $company->name }}</div>
            <div class="party-detail">NIU: {{ $company->niu }}</div>
            <div class="party-detail">{{ $company->address }}</div>
        </div>
        <div class="party-box">
            <h3>Émetteur (Fournisseur)</h3>
            <div class="party-name">{{ $supplier->name }}</div>
            @if($supplier->niu)<div class="party-detail">NIU: {{ $supplier->niu }}</div>@endif
            @if($supplier->email)<div class="party-detail">{{ $supplier->email }}</div>@endif
            @if($supplier->phone)<div class="party-detail">{{ $supplier->phone }}</div>@endif
        </div>
    </div>

    @if($cn->reason)
    <div class="reason-box">
        <strong>Motif :</strong> {{ $cn->reason }}
    </div>
    @endif

    <div class="amounts">
        <div class="amount-row"><span>Montant HT de l'avoir</span><span>{{ number_format($cn->amount_ht, 0, ',', ' ') }} XAF</span></div>
        <div class="amount-row"><span>TVA (17,5%)</span><span>{{ number_format($cn->tva_amount, 0, ',', ' ') }} XAF</span></div>
        <div class="amount-row total"><span>NET À DÉDUIRE</span><span>{{ number_format($cn->net_payable, 0, ',', ' ') }} XAF</span></div>
    </div>

    <div class="sig-row">
        <div class="sig-box">
            <div>Signature du Fournisseur</div>
            <div style="color:#64748b;font-size:7pt">{{ $supplier->name }}</div>
        </div>
        <div class="sig-box">
            <div>Reçu par {{ $company->name }}</div>
            <div style="color:#64748b;font-size:7pt">Responsable Comptable</div>
        </div>
    </div>

    <div class="footer">
        {{ $company->name }} — NIU: {{ $company->niu }} — Avoir {{ $cn->credit_note_number }} — Généré par Opes Books © {{ date('Y') }}
    </div>
</div>
</body>
</html>
