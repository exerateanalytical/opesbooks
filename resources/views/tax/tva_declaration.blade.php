<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><title>Déclaration TVA {{ $tva['period'] }} — {{ $company->name }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:DejaVu Sans,sans-serif;font-size:10pt;color:#0f172a}
.page{padding:30px 34px}.header{display:flex;justify-content:space-between;border-bottom:3px solid #010048;padding-bottom:14px;margin-bottom:8px}
.brand{font-size:18pt;font-weight:900;color:#010048}.brand span{color:#C99B0E}.brand-meta{font-size:7.5pt;color:#64748b;margin-top:3px}
.doc-label{text-align:right}.doc-label h1{font-size:14pt;font-weight:900;color:#010048;text-transform:uppercase}.doc-label .p{font-size:10pt;font-weight:700;color:#C99B0E;margin-top:3px}
.legal{font-size:7.5pt;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:8px 0 16px}
table{width:100%;border-collapse:collapse;margin-bottom:8px}th{background:#010048;color:#fff;font-size:8pt;text-transform:uppercase;padding:7px 12px;text-align:left}th.r,td.r{text-align:right}
td{padding:8px 12px;font-size:9.5pt;border-bottom:1px solid #f1f5f9}td.amount{font-family:DejaVu Sans Mono,monospace;font-weight:700}
tr.sec td{background:#eef2f7;font-weight:900;color:#010048;font-size:8pt;text-transform:uppercase}
.total-box{background:#010048;border-radius:8px;padding:16px 22px;display:flex;justify-content:space-between;align-items:center;margin-top:14px}
.total-box .lbl{color:#cbd5e1;font-weight:900;text-transform:uppercase;font-size:10pt;letter-spacing:1px}.total-box .val{color:#C99B0E;font-weight:900;font-size:17pt;font-family:DejaVu Sans Mono,monospace}
.footer{border-top:1px solid #e2e8f0;padding-top:12px;margin-top:26px;font-size:7.5pt;color:#94a3b8;text-align:center}
.sign{display:flex;justify-content:flex-end;margin-top:30px}.sign .line{border-top:1px solid #94a3b8;margin-top:38px;padding-top:5px;font-size:8pt;color:#475569;width:46%;text-align:center}
</style></head>
@php $fmt=fn($v)=>number_format((float)$v,0,',',' '); @endphp
<body><div class="page">
<div class="header">
  <div><div class="brand">{{ strtoupper($company->name ?? 'OPESBOOKS') }}</div>
  <div class="brand-meta">NIU : {{ $tva['company_niu'] ?: '—' }} @if($company->rccm)· RCCM : {{ $company->rccm }}@endif<br>Centre des Impôts : {{ $tva['tax_center'] ?: '—' }}</div></div>
  <div class="doc-label"><h1>Déclaration TVA</h1><div class="p">Période {{ $tva['period'] }}</div></div>
</div>
<div class="legal">Déclaration mensuelle de TVA &amp; CAC · à déposer au plus tard le 15 du mois suivant · Francs CFA (XAF)</div>
<table>
  <thead><tr><th>Rubrique</th><th class="r">Montant</th></tr></thead>
  <tbody>
    <tr class="sec"><td colspan="2">Taxe sur la Valeur Ajoutée (17,5 %)</td></tr>
    <tr><td>TVA collectée (sur ventes)</td><td class="r amount">{{ $fmt($tva['tva_collectee']) }}</td></tr>
    <tr><td>TVA déductible (sur achats)</td><td class="r amount">- {{ $fmt($tva['tva_deductible']) }}</td></tr>
    <tr><td><strong>TVA nette due</strong></td><td class="r amount">{{ $fmt($tva['tva_nette_due']) }}</td></tr>
    <tr class="sec"><td colspan="2">Centime Additionnel Communal (10 % de la TVA)</td></tr>
    <tr><td>CAC collecté</td><td class="r amount">{{ $fmt($tva['cac_collecte']) }}</td></tr>
    <tr><td><strong>CAC net dû</strong></td><td class="r amount">{{ $fmt($tva['cac_net_du']) }}</td></tr>
  </tbody>
</table>
<div class="total-box"><span class="lbl">Total à payer (TVA + CAC)</span><span class="val">{{ $fmt($tva['total_a_payer']) }} XAF</span></div>
<div class="sign"><div class="line">Signature du contribuable / Cachet</div></div>
<div class="footer">Généré par OPESBooks · Déclaration conforme au Code Général des Impôts du Cameroun (TVA 17,5 % + CAC 10 %).</div>
</div></body></html>
