<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bordereau CNPS — {{ $period_label }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 3px solid #0A192F; padding-bottom: 14px; }
        .brand-name { font-size: 18pt; font-weight: 900; color: #0A192F; }
        .brand-name span { color: #F59E0B; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .doc-label { text-align: right; }
        .doc-label h1 { font-size: 13pt; font-weight: 900; color: #0A192F; text-transform: uppercase; letter-spacing: 1px; }
        .doc-label .sub { font-size: 8pt; color: #64748b; margin-top: 4px; }
        .cnps-info { background: #f0f9ff; border: 1.5px solid #0ea5e9; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 8pt; }
        .employer-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; margin-bottom: 18px; }
        .employer-box h3 { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        thead tr { background: #0A192F; }
        thead th { padding: 7px 8px; font-size: 7pt; font-weight: 700; text-transform: uppercase; color: #fff; text-align: left; }
        thead th.num { text-align: right; }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody td { padding: 6px 8px; font-size: 8pt; }
        tbody td.num { text-align: right; }
        tfoot tr { background: #f1f5f9; font-weight: 700; }
        tfoot td { padding: 7px 8px; font-size: 8.5pt; }
        tfoot td.num { text-align: right; }
        .summary { border: 2px solid #0A192F; border-radius: 6px; padding: 14px; margin-bottom: 14px; }
        .summary-grid { display: flex; gap: 16px; }
        .summary-item { flex: 1; background: #f8fafc; border-radius: 4px; padding: 10px; text-align: center; }
        .summary-item .label { font-size: 7pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-item .value { font-size: 11pt; font-weight: 900; margin-top: 3px; }
        .total-box { background: #0A192F; color: #fff; border-radius: 6px; padding: 12px 16px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
        .total-box .label { font-size: 9pt; opacity: 0.7; }
        .total-box .value { font-size: 14pt; font-weight: 900; color: #F59E0B; }
        .rates { font-size: 7pt; color: #64748b; margin-bottom: 14px; }
        .sig-row { display: flex; justify-content: space-between; margin-top: 24px; font-size: 8pt; }
        .sig-box { text-align: center; width: 45%; border-top: 1px solid #0A192F; padding-top: 6px; }
        .footer { margin-top: 20px; font-size: 7pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
        .xaf { font-size: 6.5pt; opacity: 0.6; }
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
            <h1>Bordereau CNPS</h1>
            <div class="sub">Déclaration mensuelle des cotisations sociales</div>
            <div class="sub">Période : {{ $period_label }}</div>
            <div class="sub">Généré le {{ $generated_at }}</div>
        </div>
    </div>

    <div class="cnps-info">
        📋 <strong>Caisse Nationale de Prévoyance Sociale (CNPS)</strong> — Cotisations patronales et salariales dues pour le mois de {{ $period_label }}. Taux salarial : <strong>2,8%</strong> (plafond 750 000 XAF). Taux patronal : <strong>11,2%</strong> (plafond 750 000 XAF).
    </div>

    <div class="employer-box">
        <h3>Employeur</h3>
        <strong>{{ $company->name }}</strong> &nbsp;|&nbsp; NIU: {{ $company->niu }} &nbsp;|&nbsp; {{ $company->address }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Nom de l'employé</th>
                <th>N° Matricule</th>
                <th class="num">Salaire Brut</th>
                <th class="num">Base CNPS</th>
                <th class="num">Part Salariale (2,8%)</th>
                <th class="num">Part Patronale (11,2%)</th>
                <th class="num">Total CNPS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payrolls as $p)
            @php
                $baseCnps = min($p->gross_salary, 750000);
                $total    = $p->cnps_employee + $p->cnps_employer;
            @endphp
            <tr>
                <td>{{ $p->employee_name }}</td>
                <td style="font-family:monospace;font-size:7.5pt">{{ $p->employee_id_number ?? '—' }}</td>
                <td class="num">{{ number_format($p->gross_salary, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
                <td class="num">{{ number_format($baseCnps, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
                <td class="num" style="color:#0369a1">{{ number_format($p->cnps_employee, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
                <td class="num" style="color:#7c3aed">{{ number_format($p->cnps_employer, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
                <td class="num" style="font-weight:700">{{ number_format($total, 0, ',', ' ') }} <span class="xaf">XAF</span></td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:14px;color:#94a3b8">Aucune paie traitée pour cette période.</td></tr>
            @endforelse
        </tbody>
        @if(count($payrolls) > 0)
        <tfoot>
            <tr>
                <td colspan="2"><strong>TOTAUX</strong></td>
                <td class="num">{{ number_format($total_gross, 0, ',', ' ') }} XAF</td>
                <td class="num">—</td>
                <td class="num" style="color:#0369a1">{{ number_format($total_employee, 0, ',', ' ') }} XAF</td>
                <td class="num" style="color:#7c3aed">{{ number_format($total_employer, 0, ',', ' ') }} XAF</td>
                <td class="num" style="color:#0A192F">{{ number_format($total_cnps, 0, ',', ' ') }} XAF</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="total-box">
        <div>
            <div class="label">Total cotisations CNPS dues pour {{ $period_label }}</div>
            <div style="font-size:7pt;opacity:0.6;margin-top:2px">Part salariale {{ number_format($total_employee, 0, ',', ' ') }} XAF + Part patronale {{ number_format($total_employer, 0, ',', ' ') }} XAF</div>
        </div>
        <div class="value">{{ number_format($total_cnps, 0, ',', ' ') }} XAF</div>
    </div>

    <div class="sig-row">
        <div class="sig-box">
            <div>Cachet et signature de l'employeur</div>
            <div style="color:#64748b">{{ $company->name }}</div>
        </div>
        <div class="sig-box">
            <div>Visa CNPS</div>
            <div style="color:#64748b">Date de réception: ___/___/______</div>
        </div>
    </div>

    <div class="footer">
        {{ $company->name }} — NIU: {{ $company->niu }} — Bordereau CNPS {{ $period_label }} — Généré par Opes Books © {{ date('Y') }}
    </div>
</div>
</body>
</html>
