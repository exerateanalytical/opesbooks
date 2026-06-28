<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><title>Grand Livre — {{ $company->name }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:DejaVu Sans,sans-serif;font-size:8pt;color:#0f172a}
.page{padding:24px 28px}.header{display:flex;justify-content:space-between;border-bottom:3px solid #010048;padding-bottom:12px;margin-bottom:6px}
.brand{font-size:16pt;font-weight:900;color:#010048}.brand span{color:#C99B0E}.brand-meta{font-size:7.5pt;color:#64748b;margin-top:3px}
.doc-label{text-align:right}.doc-label h1{font-size:13pt;font-weight:900;color:#010048;text-transform:uppercase}.doc-label .sub{font-size:8.5pt;color:#C99B0E;font-weight:700;margin-top:3px}
.legal{font-size:7pt;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:8px 0 12px}
.acct{margin-bottom:12px}.acct-head{background:#010048;color:#fff;padding:5px 9px;font-weight:900;font-size:8.5pt;display:flex;justify-content:space-between;border-radius:4px 4px 0 0}
.acct-head .code{font-family:DejaVu Sans Mono,monospace;color:#C99B0E}
table{width:100%;border-collapse:collapse}th{background:#eef2f7;color:#475569;font-size:7pt;text-transform:uppercase;padding:4px 9px;text-align:left}th.r,td.r{text-align:right}
td{padding:3px 9px;border-bottom:1px solid #f4f6f9;font-size:7.5pt}td.amount{font-family:DejaVu Sans Mono,monospace}td.date{font-family:DejaVu Sans Mono,monospace;color:#64748b;white-space:nowrap}
tr.sub td{background:#f8fafc;font-weight:900;color:#010048;border-top:1px solid #cbd5e1}tr.sub td.amount{color:#010048}
.footer{border-top:1px solid #e2e8f0;padding-top:10px;margin-top:8px;font-size:7pt;color:#94a3b8;text-align:center}
</style></head>
@php $fmt=fn($v)=>number_format((float)$v,0,',',' '); @endphp
<body><div class="page">
@include('documents.letterhead', ['title' => 'Grand Livre', 'subtitle' => $from ? "Du $from au $to" : 'Cumul'])
<div class="legal">Grand Livre des comptes · Référentiel SYSCOHADA révisé · Francs CFA (XAF)</div>

@forelse($accounts as $a)
<div class="acct">
  <div class="acct-head"><span><span class="code">{{ $a['code'] }}</span> &nbsp; {{ $a['label'] }}</span><span>Solde : {{ $fmt($a['debit'] - $a['credit']) }}</span></div>
  <table>
    <thead><tr><th style="width:70px">Date</th><th style="width:110px">Pièce</th><th>Libellé</th><th class="r">Débit</th><th class="r">Crédit</th></tr></thead>
    <tbody>
      @foreach($a['lines'] as $l)
      <tr>
        <td class="date">{{ \Illuminate\Support\Str::of((string)$l->posting_date)->substr(0,10) }}</td>
        <td>{{ $l->reference_id }}</td>
        <td>{{ $l->description }}</td>
        <td class="r amount">{{ (float)$l->debit ? $fmt($l->debit) : '' }}</td>
        <td class="r amount">{{ (float)$l->credit ? $fmt($l->credit) : '' }}</td>
      </tr>
      @endforeach
      <tr class="sub"><td colspan="3">Total compte {{ $a['code'] }}</td><td class="r amount">{{ $fmt($a['debit']) }}</td><td class="r amount">{{ $fmt($a['credit']) }}</td></tr>
    </tbody>
  </table>
</div>
@empty
<p style="text-align:center;color:#94a3b8;font-style:italic;padding:20px">Aucun mouvement sur la période.</p>
@endforelse

@include('documents.footer', ['docType' => 'GLIVRE', 'extraFooter' => 'Grand Livre conforme SYSCOHADA révisé.'])
</div></body></html>
