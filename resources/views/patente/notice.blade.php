<!DOCTYPE html>
<html lang="fr"><head><meta charset="UTF-8"><title>Patente {{ $record->tax_year }} — {{ $company->name }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}body{font-family:DejaVu Sans,sans-serif;font-size:10pt;color:#0f172a}
.page{padding:32px 36px}.header{display:flex;justify-content:space-between;border-bottom:3px solid #010048;padding-bottom:16px;margin-bottom:24px}
.brand{font-size:19pt;font-weight:900;color:#010048}.brand span{color:#C99B0E}.brand-meta{font-size:8pt;color:#64748b;margin-top:4px}
.doc-label{text-align:right}.doc-label h1{font-size:16pt;font-weight:900;color:#010048;text-transform:uppercase}.doc-label .y{font-size:11pt;font-weight:700;color:#C99B0E;margin-top:4px}
.amount-box{background:#010048;border-radius:10px;padding:18px 24px;display:flex;justify-content:space-between;align-items:center;margin:18px 0}
.amount-box .lbl{color:#cbd5e1;font-weight:900;text-transform:uppercase;font-size:10pt;letter-spacing:1px}.amount-box .val{color:#C99B0E;font-weight:900;font-size:18pt;font-family:DejaVu Sans Mono,monospace}
table{width:100%;border-collapse:collapse;margin-bottom:20px}td{padding:8px 12px;font-size:9.5pt;border-bottom:1px solid #f1f5f9}td.k{color:#64748b;font-weight:700;width:45%}td.v{font-weight:700}
.badge{display:inline-block;padding:3px 12px;border-radius:5px;font-size:9pt;font-weight:900;text-transform:uppercase}.PAID{background:#dcfce7;color:#15803d}.PENDING{background:#fef9c3;color:#a16207}.OVERDUE{background:#fee2e2;color:#b91c1c}
.footer{border-top:1px solid #e2e8f0;padding-top:12px;margin-top:24px;font-size:7.5pt;color:#94a3b8;text-align:center}
</style></head>
@php $fmt=fn($v)=>number_format((float)$v,0,',',' ').' XAF'; @endphp
<body><div class="page">
@include('documents.letterhead', ['title' => 'Patente', 'subtitle' => 'Exercice ' . $record->tax_year, 'docRef' => $record->patente_number ?: ('PAT-' . $record->tax_year)])
<p style="font-size:10pt;color:#334155;line-height:1.7;margin-bottom:6px">Contribution des patentes due au titre de l'exercice <strong>{{ $record->tax_year }}</strong> par <strong>{{ $company->name }}</strong>.</p>
<div class="amount-box"><span class="lbl">Montant de la patente</span><span class="val">{{ $fmt($record->amount_due_xaf) }}</span></div>
<table>
  <tr><td class="k">N° de patente</td><td class="v">{{ $record->patente_number ?: '—' }}</td></tr>
  <tr><td class="k">Exercice fiscal</td><td class="v">{{ $record->tax_year }}</td></tr>
  <tr><td class="k">Montant dû</td><td class="v">{{ $fmt($record->amount_due_xaf) }}</td></tr>
  <tr><td class="k">Montant payé</td><td class="v">{{ $fmt($record->amount_paid_xaf) }}</td></tr>
  <tr><td class="k">Reste à payer</td><td class="v">{{ $fmt(($record->amount_due_xaf ?? 0)-($record->amount_paid_xaf ?? 0)) }}</td></tr>
  <tr><td class="k">Échéance</td><td class="v">{{ $record->due_date ?: '—' }}</td></tr>
  <tr><td class="k">Statut</td><td class="v"><span class="badge {{ $record->status }}">{{ $record->status }}</span></td></tr>
  @if($record->notes)<tr><td class="k">Notes</td><td class="v">{{ $record->notes }}</td></tr>@endif
</table>
@include('documents.footer', ['docType' => 'PATENTE', 'docRef' => $record->patente_number ?: ('PAT-' . $record->tax_year), 'extraFooter' => 'Avis de patente — contribution calculée sur le chiffre d\'affaires.'])
</div></body></html>
