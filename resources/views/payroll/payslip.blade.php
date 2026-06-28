<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bulletin de paie — {{ $employee->name }} — {{ $periodLabel }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color:#0f172a; background:#fff; }
        .page { padding: 28px 32px; }
        .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #010048; padding-bottom:14px; margin-bottom:18px; }
        .brand { font-size:19pt; font-weight:900; color:#010048; letter-spacing:-0.5px; }
        .brand span { color:#C99B0E; }
        .brand-meta { font-size:7.5pt; color:#64748b; margin-top:3px; }
        .doc-label { text-align:right; }
        .doc-label h1 { font-size:15pt; font-weight:900; color:#010048; text-transform:uppercase; letter-spacing:1px; }
        .doc-label .period { font-size:9.5pt; font-weight:700; color:#C99B0E; margin-top:3px; }
        .parties { display:flex; justify-content:space-between; gap:16px; margin-bottom:18px; }
        .box { flex:1; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px; }
        .box h3 { font-size:7pt; font-weight:900; text-transform:uppercase; letter-spacing:1px; color:#94a3b8; margin-bottom:5px; }
        .box .name { font-size:10.5pt; font-weight:900; color:#0f172a; }
        .box .detail { font-size:8pt; color:#475569; margin-top:2px; }
        table { width:100%; border-collapse:collapse; margin-bottom:14px; }
        th { background:#010048; color:#fff; font-size:7.5pt; text-transform:uppercase; letter-spacing:0.5px; padding:7px 10px; text-align:left; }
        th.r, td.r { text-align:right; }
        td { padding:6px 10px; font-size:8.5pt; border-bottom:1px solid #f1f5f9; }
        td.amount { font-family: DejaVu Sans Mono, monospace; }
        .section-row td { background:#eef2f7; font-weight:900; color:#010048; font-size:7.5pt; text-transform:uppercase; letter-spacing:0.5px; }
        .net-box { background:#010048; border-radius:8px; padding:14px 20px; display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; }
        .net-box .lbl { color:#cbd5e1; font-weight:900; text-transform:uppercase; font-size:10pt; letter-spacing:1px; }
        .net-box .val { color:#C99B0E; font-weight:900; font-size:16pt; font-family: DejaVu Sans Mono, monospace; }
        .employer { background:#fffbeb; border:1px solid #fde68a; border-radius:6px; padding:10px 12px; margin-bottom:14px; }
        .employer h4 { font-size:7pt; font-weight:900; text-transform:uppercase; color:#92400e; margin-bottom:6px; }
        .employer .row { display:flex; justify-content:space-between; font-size:8pt; color:#78350f; padding:2px 0; }
        .footer { border-top:1px solid #e2e8f0; padding-top:10px; margin-top:8px; font-size:7pt; color:#94a3b8; text-align:center; }
        .sign { display:flex; justify-content:space-between; margin-top:30px; font-size:8pt; color:#475569; }
        .sign div { width:45%; }
        .sign .line { border-top:1px solid #94a3b8; margin-top:34px; padding-top:4px; }
    </style>
</head>
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
            <h1>Bulletin de Paie</h1>
            <div class="period">{{ $periodLabel }}</div>
        </div>
    </div>

    <div class="parties">
        <div class="box">
            <h3>Employé</h3>
            <div class="name">{{ $employee->name }}</div>
            <div class="detail">{{ $employee->position ?: '—' }}</div>
            <div class="detail">N° CNPS : {{ $employee->cnps_number ?: '—' }}</div>
        </div>
        <div class="box">
            <h3>Employeur</h3>
            <div class="name">{{ $company->name }}</div>
            <div class="detail">NIU : {{ $company->niu ?: '—' }}</div>
            <div class="detail">{{ $company->tax_center ?: '' }}</div>
        </div>
    </div>

    @php $fmt = fn($v) => number_format((float)$v, 0, ',', ' ') . ' XAF'; @endphp

    <table>
        <thead><tr><th>Rubrique</th><th class="r">Montant</th></tr></thead>
        <tbody>
            <tr class="section-row"><td colspan="2">Rémunération brute</td></tr>
            <tr><td>Salaire brut</td><td class="r amount">{{ $fmt($line->gross_salary) }}</td></tr>

            <tr class="section-row"><td colspan="2">Retenues salariales</td></tr>
            <tr><td>CNPS (part salariale)</td><td class="r amount">- {{ $fmt($line->cnps_employee) }}</td></tr>
            <tr><td>IRPP</td><td class="r amount">- {{ $fmt($line->irpp) }}</td></tr>
            <tr><td>CAC sur IRPP</td><td class="r amount">- {{ $fmt($line->cac_irpp) }}</td></tr>
            @if((float)$line->rav > 0)
            <tr><td>RAV (Redevance Audiovisuelle)</td><td class="r amount">- {{ $fmt($line->rav) }}</td></tr>
            @endif
        </tbody>
    </table>

    <div class="net-box">
        <span class="lbl">Net à payer</span>
        <span class="val">{{ $fmt($line->net_salary) }}</span>
    </div>

    <div class="employer">
        <h4>Charges patronales (à la charge de l'employeur)</h4>
        <div class="row"><span>CNPS (part patronale)</span><span>{{ $fmt($line->cnps_employer) }}</span></div>
        @if((float)$line->tsr_employer > 0)
        <div class="row"><span>TSR (Taxe Spéciale Revenus)</span><span>{{ $fmt($line->tsr_employer) }}</span></div>
        @endif
    </div>

    <div class="sign">
        <div><div class="line">Signature de l'employeur</div></div>
        <div><div class="line">Signature de l'employé</div></div>
    </div>

    <div class="footer">
        Bulletin généré par OPESBooks · {{ $periodLabel }} · Conforme au Code du Travail camerounais (CNPS, IRPP, CAC).
    </div>
</div>
</body>
</html>
