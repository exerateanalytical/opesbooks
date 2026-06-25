<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relevé de Compte Client — {{ $customer->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; background: #fff; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #0A192F; padding-bottom: 16px; }
        .brand-name { font-size: 18pt; font-weight: 900; color: #0A192F; }
        .brand-name span { color: #C99B0E; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .doc-label { text-align: right; }
        .doc-label h1 { font-size: 14pt; font-weight: 900; color: #0A192F; text-transform: uppercase; letter-spacing: 1px; }
        .doc-label .period { font-size: 8pt; color: #64748b; margin-top: 4px; }
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
        tbody td { padding: 7px 10px; font-size: 8pt; }
        tbody td.num { text-align: right; }
        .status-paid { color: #16a34a; font-weight: 700; }
        .status-overdue { color: #dc2626; font-weight: 700; }
        .status-sent { color: #d97706; font-weight: 700; }
        .summary { margin-top: 16px; border: 2px solid #0A192F; border-radius: 6px; padding: 14px; }
        .summary-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 9pt; }
        .summary-row.total { border-top: 2px solid #0A192F; margin-top: 6px; padding-top: 6px; font-size: 11pt; font-weight: 900; }
        .summary-row.total span:last-child { color: #dc2626; }
        .footer { margin-top: 24px; font-size: 7pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
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
            <h1>Relevé de Compte</h1>
            <div class="period">Période : {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</div>
            <div class="period">Généré le {{ $generated_at }}</div>
        </div>
    </div>

    <div class="parties">
        <div class="party-box">
            <h3>Fournisseur</h3>
            <div class="party-name">{{ $company->name }}</div>
            <div class="party-detail">NIU: {{ $company->niu }}</div>
            <div class="party-detail">{{ $company->tax_center }}</div>
        </div>
        <div class="party-box">
            <h3>Client</h3>
            <div class="party-name">{{ $customer->name }}</div>
            @if($customer->tax_id)<div class="party-detail">NIU: {{ $customer->tax_id }}</div>@endif
            @if($customer->email)<div class="party-detail">{{ $customer->email }}</div>@endif
            @if($customer->phone)<div class="party-detail">{{ $customer->phone }}</div>@endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>N° Facture</th>
                <th>Date</th>
                <th>Échéance</th>
                <th class="num">Montant HT</th>
                <th class="num">TVA</th>
                <th class="num">TTC</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $inv)
            <tr>
                <td>{{ $inv->invoice_number }}</td>
                <td>{{ \Carbon\Carbon::parse($inv->invoice_date)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($inv->due_date)->format('d/m/Y') }}</td>
                <td class="num">{{ number_format($inv->amount_ht, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
                <td class="num">{{ number_format($inv->tva_amount, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
                <td class="num">{{ number_format($inv->amount_ttc, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
                <td class="status-{{ strtolower($inv->status) }}">{{ $inv->status }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:16px;color:#94a3b8">Aucune facture sur cette période.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row"><span>Total Facturé (TTC)</span><span>{{ number_format($total_invoiced, 0, ',', ' ') }} XAF</span></div>
        <div class="summary-row"><span>Total Réglé</span><span style="color:#16a34a">{{ number_format($total_paid, 0, ',', ' ') }} XAF</span></div>
        <div class="summary-row total"><span>Solde Dû</span><span>{{ number_format($total_outstanding, 0, ',', ' ') }} XAF</span></div>
    </div>

    <div class="footer">
        {{ $company->name }} — {{ $company->address }} — Généré par Opes Books © {{ date('Y') }}
    </div>
</div>
</body>
</html>
