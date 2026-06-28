<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }} — {{ $company->name }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color:#0f172a; background:#fff; }
        .page { padding: 28px 32px; }
        .header { display:flex; justify-content:space-between; align-items:flex-start; border-bottom:3px solid #010048; padding-bottom:14px; margin-bottom:6px; }
        .brand { font-size:18pt; font-weight:900; color:#010048; }
        .brand span { color:#C99B0E; }
        .brand-meta { font-size:7.5pt; color:#64748b; margin-top:3px; }
        .doc-label { text-align:right; }
        .doc-label h1 { font-size:15pt; font-weight:900; color:#010048; text-transform:uppercase; letter-spacing:0.5px; }
        .doc-label .sub { font-size:8.5pt; font-weight:700; color:#C99B0E; margin-top:3px; }
        .syscohada { font-size:7pt; color:#94a3b8; margin: 8px 0 16px; text-transform:uppercase; letter-spacing:1px; }
        table { width:100%; border-collapse:collapse; margin-bottom:14px; }
        th { background:#010048; color:#fff; font-size:8pt; text-transform:uppercase; letter-spacing:0.5px; padding:7px 10px; text-align:left; }
        th.r, td.r { text-align:right; }
        td { padding:5px 10px; font-size:8.5pt; border-bottom:1px solid #f1f5f9; }
        td.code { font-family: DejaVu Sans Mono, monospace; color:#94a3b8; width:60px; }
        td.amount { font-family: DejaVu Sans Mono, monospace; }
        tr.subtotal td { background:#eef2f7; font-weight:900; color:#010048; border-top:2px solid #010048; }
        .group-title { font-size:9pt; font-weight:900; color:#010048; text-transform:uppercase; letter-spacing:0.5px; margin:14px 0 4px; }
        .highlight { background:#010048; border-radius:8px; padding:14px 20px; display:flex; justify-content:space-between; align-items:center; margin-top:10px; }
        .highlight .lbl { color:#cbd5e1; font-weight:900; text-transform:uppercase; font-size:10pt; letter-spacing:1px; }
        .highlight .val { color:#C99B0E; font-weight:900; font-size:15pt; font-family: DejaVu Sans Mono, monospace; }
        .highlight.neg .val { color:#fca5a5; }
        .footer { border-top:1px solid #e2e8f0; padding-top:10px; margin-top:18px; font-size:7pt; color:#94a3b8; text-align:center; }
        .empty { color:#94a3b8; font-size:8pt; font-style:italic; padding:6px 10px; }
    </style>
</head>
<body>
@php $fmt = fn($v) => number_format((float)$v, 0, ',', ' ') . ' XAF'; @endphp
<div class="page">
    @include('documents.letterhead', ['title' => $title, 'subtitle' => $subtitle])
    <div class="syscohada">Référentiel SYSCOHADA révisé · Montants en Francs CFA (XAF)</div>

    @foreach($groups as $g)
        <div class="group-title">{{ $g['heading'] }}</div>
        <table>
            <thead><tr><th>Compte</th><th>Libellé</th><th class="r">Montant</th></tr></thead>
            <tbody>
                @forelse($g['rows'] as $row)
                    <tr>
                        <td class="code">{{ $row['code'] ?? '' }}</td>
                        <td>{{ $row['label'] ?? '—' }}</td>
                        <td class="r amount">{{ $fmt($row['amount'] ?? 0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty">Aucun mouvement sur la période.</td></tr>
                @endforelse
                <tr class="subtotal">
                    <td colspan="2">Total {{ $g['heading'] }}</td>
                    <td class="r amount">{{ $fmt($g['total']) }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <div class="highlight {{ $highlight['positive'] ? '' : 'neg' }}">
        <span class="lbl">{{ $highlight['label'] }}</span>
        <span class="val">{{ $fmt($highlight['amount']) }}</span>
    </div>

    @include('documents.footer', ['docType' => 'ETAT', 'extraFooter' => 'Document conforme au plan comptable SYSCOHADA révisé.'])
</div>
</body>
</html>
