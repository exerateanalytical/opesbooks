<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Balance des comptes — {{ $company->name }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8.5pt; color:#0f172a; }
        .page { padding: 26px 30px; }
        .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #010048; padding-bottom:12px; margin-bottom:6px; }
        .brand { font-size:17pt; font-weight:900; color:#010048; } .brand span { color:#C99B0E; }
        .brand-meta { font-size:7.5pt; color:#64748b; margin-top:3px; }
        .doc-label { text-align:right; } .doc-label h1 { font-size:14pt; font-weight:900; color:#010048; text-transform:uppercase; }
        .doc-label .sub { font-size:8.5pt; color:#C99B0E; font-weight:700; margin-top:3px; }
        .syscohada { font-size:7pt; color:#94a3b8; margin:8px 0 12px; text-transform:uppercase; letter-spacing:1px; }
        table { width:100%; border-collapse:collapse; }
        th { background:#010048; color:#fff; font-size:7.5pt; text-transform:uppercase; padding:6px 8px; text-align:left; }
        th.r, td.r { text-align:right; }
        td { padding:3.5px 8px; border-bottom:1px solid #f1f5f9; font-size:8pt; }
        td.code { font-family: DejaVu Sans Mono, monospace; color:#475569; }
        td.amount { font-family: DejaVu Sans Mono, monospace; }
        tr.total td { background:#010048; color:#fff; font-weight:900; border-top:2px solid #010048; }
        tr.total td.amount { color:#C99B0E; }
        .badge { display:inline-block; padding:2px 8px; border-radius:4px; font-size:7.5pt; font-weight:900; }
        .ok { background:#dcfce7; color:#15803d; } .bad { background:#fee2e2; color:#b91c1c; }
        .footer { margin-top:14px; font-size:7pt; color:#94a3b8; text-align:center; border-top:1px solid #e2e8f0; padding-top:8px; }
    </style>
</head>
@php $fmt = fn($v) => number_format((float)$v, 0, ',', ' '); @endphp
<body>
<div class="page">
    <div class="header">
        <div>
            <div class="brand">{{ strtoupper($company->name ?? 'OPESBOOKS') }}</div>
            <div class="brand-meta">@if($company->niu) NIU : {{ $company->niu }} @endif @if($company->rccm) · RCCM : {{ $company->rccm }} @endif</div>
        </div>
        <div class="doc-label">
            <h1>Balance des Comptes</h1>
            <div class="sub">{{ $from ? "Du $from au $to" : 'Cumul' }}</div>
        </div>
    </div>
    <div class="syscohada">Référentiel SYSCOHADA révisé · Francs CFA (XAF) ·
        <span class="badge {{ $data['balanced'] ? 'ok' : 'bad' }}">{{ $data['balanced'] ? 'Équilibrée' : 'Déséquilibrée' }}</span>
    </div>

    <table>
        <thead><tr>
            <th style="width:70px">Compte</th><th>Libellé</th>
            <th class="r">Débit</th><th class="r">Crédit</th><th class="r">Solde</th>
        </tr></thead>
        <tbody>
            @foreach($data['accounts'] as $a)
                @php $d=(float)$a['total_debit']; $c=(float)$a['total_credit']; @endphp
                @if($d != 0 || $c != 0)
                <tr>
                    <td class="code">{{ $a['code'] }}</td>
                    <td>{{ $a['label'] }}</td>
                    <td class="r amount">{{ $d ? $fmt($d) : '' }}</td>
                    <td class="r amount">{{ $c ? $fmt($c) : '' }}</td>
                    <td class="r amount">{{ $fmt($d - $c) }}</td>
                </tr>
                @endif
            @endforeach
            <tr class="total">
                <td colspan="2">TOTAUX</td>
                <td class="r amount">{{ $fmt($data['grand_debit']) }}</td>
                <td class="r amount">{{ $fmt($data['grand_credit']) }}</td>
                <td class="r amount">{{ $fmt((float)$data['grand_debit'] - (float)$data['grand_credit']) }}</td>
            </tr>
        </tbody>
    </table>
    <div class="footer">Généré par OPESBooks · Balance des comptes conforme SYSCOHADA révisé.</div>
</div>
</body>
</html>
