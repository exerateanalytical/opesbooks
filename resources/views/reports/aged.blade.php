<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} — {{ $company->name }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8.5pt; color:#0f172a; }
        .page { padding: 26px 30px; }
        .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #010048; padding-bottom:12px; margin-bottom:14px; }
        .brand { font-size:17pt; font-weight:900; color:#010048; } .brand span { color:#C99B0E; }
        .brand-meta { font-size:7.5pt; color:#64748b; margin-top:3px; }
        .doc-label { text-align:right; } .doc-label h1 { font-size:13pt; font-weight:900; color:#010048; text-transform:uppercase; }
        .buckets { display:flex; gap:8px; margin-bottom:16px; }
        .bucket { flex:1; border:1px solid #e2e8f0; border-radius:6px; padding:8px 10px; text-align:center; }
        .bucket .b-lbl { font-size:7pt; color:#94a3b8; text-transform:uppercase; font-weight:900; }
        .bucket .b-val { font-size:9pt; font-weight:900; font-family: DejaVu Sans Mono, monospace; margin-top:3px; }
        table { width:100%; border-collapse:collapse; }
        th { background:#010048; color:#fff; font-size:7.5pt; text-transform:uppercase; padding:6px 8px; text-align:left; }
        th.r, td.r { text-align:right; }
        td { padding:4px 8px; border-bottom:1px solid #f1f5f9; font-size:8pt; }
        td.amount { font-family: DejaVu Sans Mono, monospace; }
        .over td.amount { color:#b91c1c; font-weight:700; }
        tr.total td { background:#010048; color:#fff; font-weight:900; } tr.total td.amount { color:#C99B0E; }
        .footer { margin-top:14px; font-size:7pt; color:#94a3b8; text-align:center; border-top:1px solid #e2e8f0; padding-top:8px; }
    </style>
</head>
@php $fmt = fn($v) => number_format((float)$v, 0, ',', ' '); @endphp
<body>
<div class="page">
    <div class="header">
        <div>
            <div class="brand">{{ strtoupper($company->name ?? 'OPESBOOKS') }}</div>
            <div class="brand-meta">@if($company->niu) NIU : {{ $company->niu }} @endif</div>
        </div>
        <div class="doc-label"><h1>{{ $title }}</h1></div>
    </div>

    <div class="buckets">
        @foreach([['current','Courant'],['1_30','1–30 j'],['31_60','31–60 j'],['61_90','61–90 j'],['over_90','+90 j']] as $b)
        <div class="bucket"><div class="b-lbl">{{ $b[1] }}</div><div class="b-val">{{ $fmt($data[$b[0]] ?? 0) }}</div></div>
        @endforeach
    </div>

    <table>
        <thead><tr>
            <th>Pièce</th><th>Tiers</th><th>Échéance</th><th class="r">Retard (j)</th><th class="r">Montant TTC</th>
        </tr></thead>
        <tbody>
            @forelse($data['invoices'] as $inv)
            <tr class="{{ (int)($inv['days_overdue'] ?? 0) > 90 ? 'over' : '' }}">
                <td>{{ $inv['invoice_number'] ?? '—' }}</td>
                <td>{{ $inv['customer']['name'] ?? ($inv['supplier']['name'] ?? '—') }}</td>
                <td>{{ $inv['due_date'] ?? '' }}</td>
                <td class="r">{{ max(0, (int)($inv['days_overdue'] ?? 0)) }}</td>
                <td class="r amount">{{ $fmt($inv['amount_ttc'] ?? 0) }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#94a3b8;font-style:italic;padding:10px">Aucun encours.</td></tr>
            @endforelse
            <tr class="total">
                <td colspan="4">TOTAL</td>
                <td class="r amount">{{ $fmt($data['grand_total'] ?? 0) }}</td>
            </tr>
        </tbody>
    </table>
    <div class="footer">Généré par OPESBooks · Analyse des encours par ancienneté.</div>
</div>
</body>
</html>
