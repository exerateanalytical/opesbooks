<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><title>DSF {{ $dsf['meta']['fiscal_year'] }} — {{ $company->name }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:DejaVu Sans,sans-serif;font-size:8.5pt;color:#0f172a}
.page{padding:26px 30px}.header{display:flex;justify-content:space-between;border-bottom:3px solid #010048;padding-bottom:12px;margin-bottom:8px}
.brand{font-size:16pt;font-weight:900;color:#010048}.brand span{color:#C99B0E}.brand-meta{font-size:7.5pt;color:#64748b;margin-top:3px}
.doc-label{text-align:right}.doc-label h1{font-size:13pt;font-weight:900;color:#010048;text-transform:uppercase}.doc-label .y{font-size:9.5pt;font-weight:700;color:#C99B0E;margin-top:3px}
.legal{font-size:7pt;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin:8px 0 14px}
.tbl-title{font-size:9pt;font-weight:900;color:#010048;text-transform:uppercase;margin:16px 0 5px;border-left:4px solid #C99B0E;padding-left:8px}
table{width:100%;border-collapse:collapse;margin-bottom:6px}th{background:#010048;color:#fff;font-size:7.5pt;text-transform:uppercase;padding:5px 8px;text-align:left}th.r,td.r{text-align:right}
td{padding:3.5px 8px;border-bottom:1px solid #f1f5f9;font-size:8pt}td.amount{font-family:DejaVu Sans Mono,monospace}td.code{font-family:DejaVu Sans Mono,monospace;color:#64748b;width:64px}
tr.tot td{background:#eef2f7;font-weight:900;color:#010048}
</style></head>
@php $fmt=fn($v)=>number_format((float)$v,0,',',' '); $t1=$dsf['table_1_compte_resultat']; @endphp
<body><div class="page">
@include('documents.letterhead', ['title' => 'DSF — Liasse Fiscale', 'subtitle' => 'Exercice ' . $dsf['meta']['fiscal_year'], 'docRef' => 'DSF-' . $dsf['meta']['fiscal_year']])
<div class="legal">Déclaration Statistique et Fiscale · Synthèse SYSCOHADA révisé · Francs CFA (XAF)</div>

<div class="tbl-title">Tableau 1 — Compte de Résultat</div>
<table>
  @foreach([
    ['Chiffre d\'affaires HT','chiffre_affaires_ht'],['Achats consommés','achats_consommes'],
    ['Charges de personnel','charges_personnel'],['Dotations aux amortissements','dotations_amortissements'],
    ['Autres charges','autres_charges'],['Résultat d\'exploitation','resultat_exploitation'],
    ['Résultat net','resultat_net'],
  ] as $r)
  <tr><td>{{ $r[0] }}</td><td class="r amount">{{ $fmt($t1[$r[1]] ?? 0) }}</td></tr>
  @endforeach
</table>

<div class="tbl-title">Tableau 1bis — Fiscalité &amp; Social</div>
<table>
  @foreach([['TVA collectée','tva_collectee'],['TVA déductible','tva_deductible'],['TVA nette due','tva_nette_due'],['IRPP retenu','irpp_retenu'],['CNPS (part salariale)','cnps_salarie']] as $r)
  <tr><td>{{ $r[0] }}</td><td class="r amount">{{ $fmt($t1[$r[1]] ?? 0) }}</td></tr>
  @endforeach
</table>

@php $actif = $dsf['table_2_bilan_actif']['items'] ?? (is_array($dsf['table_2_bilan_actif']) ? $dsf['table_2_bilan_actif'] : []); $passif = $dsf['table_3_bilan_passif']['items'] ?? (is_array($dsf['table_3_bilan_passif']) ? $dsf['table_3_bilan_passif'] : []); @endphp
<div class="tbl-title">Tableau 2 — Bilan : Actif</div>
<table><thead><tr><th>Compte</th><th>Intitulé</th><th class="r">Montant</th></tr></thead><tbody>
  @forelse($actif as $a)<tr><td class="code">{{ $a['code'] ?? '' }}</td><td>{{ $a['label'] ?? '—' }}</td><td class="r amount">{{ $fmt($a['amount'] ?? 0) }}</td></tr>
  @empty<tr><td colspan="3" style="color:#94a3b8;font-style:italic">—</td></tr>@endforelse
  <tr class="tot"><td colspan="2">Total Actif</td><td class="r amount">{{ $fmt($dsf['table_2_bilan_actif']['total'] ?? 0) }}</td></tr>
</tbody></table>

<div class="tbl-title">Tableau 3 — Bilan : Passif</div>
<table><thead><tr><th>Compte</th><th>Intitulé</th><th class="r">Montant</th></tr></thead><tbody>
  @forelse($passif as $p)<tr><td class="code">{{ $p['code'] ?? '' }}</td><td>{{ $p['label'] ?? '—' }}</td><td class="r amount">{{ $fmt($p['amount'] ?? 0) }}</td></tr>
  @empty<tr><td colspan="3" style="color:#94a3b8;font-style:italic">—</td></tr>@endforelse
  <tr class="tot"><td colspan="2">Total Passif</td><td class="r amount">{{ $fmt($dsf['table_3_bilan_passif']['total'] ?? 0) }}</td></tr>
</tbody></table>

@include('documents.footer', ['docType' => 'DSF', 'docRef' => 'DSF-' . $dsf['meta']['fiscal_year'], 'extraFooter' => 'Synthèse SYSCOHADA — à reporter sur le formulaire officiel DGI.'])
</div></body></html>
