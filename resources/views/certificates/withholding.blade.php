<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Attestation de Précompte — {{ $supplier->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; background: #fff; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #0A192F; padding-bottom: 16px; }
        .brand-name { font-size: 18pt; font-weight: 900; color: #0A192F; }
        .brand-name span { color: #F59E0B; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .doc-label { text-align: right; }
        .doc-label h1 { font-size: 13pt; font-weight: 900; color: #0A192F; text-transform: uppercase; letter-spacing: 1px; }
        .doc-label .sub { font-size: 8pt; color: #64748b; margin-top: 4px; }
        .legal-box { background: #fff7ed; border: 1.5px solid #F59E0B; border-radius: 6px; padding: 10px 14px; margin-bottom: 18px; font-size: 8pt; color: #92400e; }
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
        tbody td { padding: 6px 10px; font-size: 8pt; }
        tbody td.num { text-align: right; }
        .summary { margin-top: 16px; border: 2px solid #0A192F; border-radius: 6px; padding: 14px; }
        .summary-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 9pt; }
        .summary-row.total { border-top: 2px solid #F59E0B; margin-top: 6px; padding-top: 6px; font-size: 11pt; font-weight: 900; color: #b45309; }
        .attestation { margin-top: 20px; border: 2px solid #0A192F; border-radius: 8px; padding: 16px; background: #f8fafc; }
        .attestation p { font-size: 8.5pt; line-height: 1.6; }
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
            <div class="brand-name">Opes<span>Books</span></div>
            <div class="brand-meta">
                {{ $company->name }}<br>
                NIU: {{ $company->niu }} | RCCM: {{ $company->rccm }}<br>
                {{ $company->address }}
            </div>
        </div>
        <div class="doc-label">
            <h1>Attestation de Précompte</h1>
            <div class="sub">Période : {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</div>
            <div class="sub">Générée le {{ $generated_at }}</div>
        </div>
    </div>

    <div class="legal-box">
        ⚖ Conformément à l'article 18 de la Loi de Finances 2026 et aux dispositions relatives au régime DGE/CIME, le précompte de <strong>5,5%</strong> a été appliqué sur les achats auprès du prestataire ci-dessous. Cette attestation constitue justificatif fiscal valable.
    </div>

    <div class="parties">
        <div class="party-box">
            <h3>Entreprise déductrice (Acheteur)</h3>
            <div class="party-name">{{ $company->name }}</div>
            <div class="party-detail">NIU: {{ $company->niu }}</div>
            <div class="party-detail">Centre fiscal: {{ $company->tax_center }}</div>
            <div class="party-detail">{{ $company->address }}</div>
        </div>
        <div class="party-box">
            <h3>Prestataire (Fournisseur)</h3>
            <div class="party-name">{{ $supplier->name }}</div>
            @if($supplier->tax_id ?? null)<div class="party-detail">NIU: {{ $supplier->tax_id }}</div>@endif
            @if($supplier->email ?? null)<div class="party-detail">{{ $supplier->email }}</div>@endif
            @if($supplier->phone ?? null)<div class="party-detail">{{ $supplier->phone }}</div>@endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Facture</th>
                <th>Date</th>
                <th class="num">Montant HT</th>
                <th class="num">Taux Précompte</th>
                <th class="num">Précompte Retenu</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr>
                <td>{{ $inv->invoice_number }}</td>
                <td>{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d/m/Y') }}</td>
                <td class="num">{{ number_format($inv->amount_ht, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
                <td class="num">5,5%</td>
                <td class="num" style="color:#d97706;font-weight:700">{{ number_format($inv->withholding_amount, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
                <td>{{ $inv->status }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:14px;color:#94a3b8">Aucune facture avec précompte sur cette période.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row"><span>Total Montant HT des achats</span><span>{{ number_format($total_ht, 0, ',', ' ') }} XAF</span></div>
        <div class="summary-row total"><span>Total Précompte Retenu (5,5%)</span><span>{{ number_format($total_withholding, 0, ',', ' ') }} XAF</span></div>
    </div>

    <div class="attestation">
        <p>
            Nous, soussignés, <strong>{{ $company->name }}</strong> (NIU: {{ $company->niu }}), certifions avoir retenu et reversé à la DGI la somme de
            <strong>{{ number_format($total_withholding, 0, ',', ' ') }} XAF</strong> au titre du précompte de 5,5% sur les achats effectués auprès de
            <strong>{{ $supplier->name }}</strong> durant la période du {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}.
        </p>
        <p style="margin-top:8px">
            La présente attestation est délivrée pour valoir ce que de droit.
        </p>
    </div>

    <div class="sig-row">
        <div class="sig-box">
            <div>Cachet et signature du Représentant Légal</div>
            <div style="color:#64748b">{{ $company->name }}</div>
        </div>
        <div class="sig-box">
            <div>Visa DGI (Centre Fiscal)</div>
            <div style="color:#64748b">{{ $company->tax_center }}</div>
        </div>
    </div>

    <div class="footer">
        {{ $company->name }} — {{ $company->address }} — Généré par Opes Books © {{ date('Y') }}
    </div>
</div>
</body>
</html>
