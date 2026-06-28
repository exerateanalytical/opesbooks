<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><title>DIPE {{ $year }} — {{ $company->name }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:DejaVu Sans,sans-serif;font-size:8.5pt;color:#0f172a}
.page{padding:24px 28px}.header{display:flex;justify-content:space-between;border-bottom:3px solid #010048;padding-bottom:12px;margin-bottom:6px}
.brand{font-size:16pt;font-weight:900;color:#010048}.brand span{color:#C99B0E}.brand-meta{font-size:7.5pt;color:#64748b;margin-top:3px}
.doc-label{text-align:right}.doc-label h1{font-size:13pt;font-weight:900;color:#010048;text-transform:uppercase}.doc-label .y{font-size:10pt;font-weight:700;color:#C99B0E;margin-top:3px}
.legal{font-size:7pt;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:8px 0 12px}
table{width:100%;border-collapse:collapse}th{background:#010048;color:#fff;font-size:7.5pt;text-transform:uppercase;padding:6px 8px;text-align:left}th.r,td.r{text-align:right}
td{padding:5px 8px;border-bottom:1px solid #f1f5f9;font-size:8pt}td.amount{font-family:DejaVu Sans Mono,monospace}
tr.tot td{background:#010048;color:#fff;font-weight:900}tr.tot td.amount{color:#C99B0E}
.footer{border-top:1px solid #e2e8f0;padding-top:10px;margin-top:14px;font-size:7pt;color:#94a3b8;text-align:center}
</style></head>
@php $fmt=fn($v)=>number_format((float)$v,0,',',' ');
$tg=0;$tc=0;$ti=0;$ta=0; foreach($rows as $r){$tg+=$r['gross'];$tc+=$r['cnps_emp'];$ti+=$r['irpp'];$ta+=$r['cac'];} @endphp
<body><div class="page">
<div class="header">
  <div><div class="brand">{{ strtoupper($company->name ?? 'OPESBOOKS') }}</div>
  <div class="brand-meta">NIU : {{ $company->niu ?: '—' }} @if($company->rccm)· RCCM : {{ $company->rccm }}@endif</div></div>
  <div class="doc-label"><h1>DIPE</h1><div class="y">Exercice {{ $year }}</div></div>
</div>
<div class="legal">Déclaration des Informations sur le Personnel Employé · Cumuls annuels par salarié · Francs CFA (XAF)</div>
<table>
  <thead><tr>
    <th>Nom &amp; Prénom</th><th>N° CNPS</th><th>Poste</th>
    <th class="r">Mois</th><th class="r">Salaire brut</th><th class="r">CNPS salarié</th><th class="r">IRPP</th><th class="r">CAC</th>
  </tr></thead>
  <tbody>
    @forelse($rows as $r)
    <tr>
      <td>{{ $r['name'] ?: '—' }}</td><td>{{ $r['cnps'] ?: '—' }}</td><td>{{ $r['position'] ?: '—' }}</td>
      <td class="r">{{ $r['months'] }}</td>
      <td class="r amount">{{ $fmt($r['gross']) }}</td><td class="r amount">{{ $fmt($r['cnps_emp']) }}</td>
      <td class="r amount">{{ $fmt($r['irpp']) }}</td><td class="r amount">{{ $fmt($r['cac']) }}</td>
    </tr>
    @empty
    <tr><td colspan="8" style="text-align:center;color:#94a3b8;font-style:italic;padding:12px">Aucune paie enregistrée pour {{ $year }}.</td></tr>
    @endforelse
    <tr class="tot"><td colspan="4">TOTAUX ({{ count($rows) }} salarié(s))</td>
      <td class="r amount">{{ $fmt($tg) }}</td><td class="r amount">{{ $fmt($tc) }}</td>
      <td class="r amount">{{ $fmt($ti) }}</td><td class="r amount">{{ $fmt($ta) }}</td></tr>
  </tbody>
</table>
<div class="footer">Généré par OPESBooks · DIPE conforme aux obligations déclaratives camerounaises (CNPS / DGI).</div>
</div></body></html>
