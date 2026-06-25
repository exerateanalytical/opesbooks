<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bon de Livraison {{ $dn->dn_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; }
        .page { padding: 28px 32px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; border-bottom: 3px solid #0ea5e9; padding-bottom: 16px; }
        .brand-name { font-size: 18pt; font-weight: 900; color: #0A192F; }
        .brand-name span { color: #F59E0B; }
        .brand-meta { font-size: 7.5pt; color: #64748b; margin-top: 3px; }
        .doc-label { text-align: right; }
        .doc-label h1 { font-size: 14pt; font-weight: 900; color: #0ea5e9; text-transform: uppercase; letter-spacing: 1px; }
        .doc-label .ref { font-size: 9pt; color: #0ea5e9; font-weight: 700; margin-top: 4px; }
        .doc-label .sub { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 7pt; font-weight: 700; background: #e0f2fe; color: #0369a1; }
        .parties { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
        .party-box { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .party-box h3 { font-size: 7pt; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 6px; }
        .party-name { font-size: 10.5pt; font-weight: 900; }
        .party-detail { font-size: 7.5pt; color: #475569; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        thead tr { background: #0ea5e9; }
        thead th { padding: 7px 10px; font-size: 7.5pt; font-weight: 700; text-transform: uppercase; color: #fff; text-align: left; }
        thead th.num { text-align: right; }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 7px 10px; font-size: 8pt; }
        tbody td.num { text-align: right; }
        .address-box { background: #f0f9ff; border: 1.5px solid #0ea5e9; border-radius: 6px; padding: 10px 14px; margin-bottom: 16px; font-size: 8pt; }
        .notes-box { background: #f8fafc; border-left: 3px solid #0ea5e9; padding: 8px 12px; margin-bottom: 16px; font-size: 8pt; color: #475569; }
        .sig-row { display: flex; justify-content: space-between; margin-top: 30px; font-size: 8pt; }
        .sig-box { text-align: center; width: 45%; border-top: 1px solid #0A192F; padding-top: 6px; }
        .footer { margin-top: 20px; font-size: 7pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div>
            @if($company->logo_path && \Storage::disk('public')->exists($company->logo_path))
                <img src="{{ storage_path('app/public/' . $company->logo_path) }}" style="height:40px;margin-bottom:6px">
            @endif
            <div class="brand-name">Opes<span>Books</span></div>
            <div class="brand-meta">
                {{ $company->name }}<br>
                NIU: {{ $company->niu }} | RCCM: {{ $company->rccm }}<br>
                {{ $company->address }}
            </div>
        </div>
        <div class="doc-label">
            <h1>Bon de Livraison</h1>
            <div class="ref">{{ $dn->dn_number }}</div>
            <div class="sub">Date : {{ \Carbon\Carbon::parse($dn->delivery_date)->format('d/m/Y') }}</div>
            <div class="sub">Type : {{ $dn->dn_type === 'OUT' ? 'Expédition Client' : 'Réception Fournisseur' }}</div>
            <div class="sub"><span class="status-badge">{{ $dn->status }}</span></div>
            <div class="sub">Généré le {{ now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <div class="parties">
        <div class="party-box">
            <h3>{{ $dn->dn_type === 'OUT' ? 'Expéditeur' : 'Destinataire' }}</h3>
            <div class="party-name">{{ $company->name }}</div>
            <div class="party-detail">NIU: {{ $company->niu }}</div>
            <div class="party-detail">{{ $company->address }}</div>
        </div>
        @if($dn->dn_type === 'OUT' && $customer)
        <div class="party-box">
            <h3>Client / Destinataire</h3>
            <div class="party-name">{{ $customer->name }}</div>
            @if($customer->niu)<div class="party-detail">NIU: {{ $customer->niu }}</div>@endif
            @if($customer->email)<div class="party-detail">{{ $customer->email }}</div>@endif
            @if($customer->phone)<div class="party-detail">{{ $customer->phone }}</div>@endif
            @if($customer->address)<div class="party-detail">{{ $customer->address }}</div>@endif
        </div>
        @elseif($dn->dn_type === 'IN' && $supplier)
        <div class="party-box">
            <h3>Fournisseur / Expéditeur</h3>
            <div class="party-name">{{ $supplier->name }}</div>
            @if($supplier->niu)<div class="party-detail">NIU: {{ $supplier->niu }}</div>@endif
            @if($supplier->email)<div class="party-detail">{{ $supplier->email }}</div>@endif
            @if($supplier->phone)<div class="party-detail">{{ $supplier->phone }}</div>@endif
        </div>
        @endif
    </div>

    @if($dn->delivery_address)
    <div class="address-box">
        📦 <strong>Adresse de livraison :</strong> {{ $dn->delivery_address }}
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width:5%">#</th>
                <th style="width:15%">Réf. Article</th>
                <th>Désignation</th>
                <th class="num" style="width:12%">Quantité</th>
                <th style="width:10%">Unité</th>
            </tr>
        </thead>
        <tbody>
            @foreach($lines as $i => $line)
            <tr>
                <td style="opacity:0.5">{{ $i + 1 }}</td>
                <td style="color:#64748b;font-size:7.5pt">{{ $line->product_code ?? '—' }}</td>
                <td>{{ $line->description }}</td>
                <td class="num"><strong>{{ number_format($line->quantity, 2, ',', ' ') }}</strong></td>
                <td>{{ $line->unit ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($dn->notes)
    <div class="notes-box">
        <strong>Observations :</strong> {{ $dn->notes }}
    </div>
    @endif

    <p style="font-size:8pt;color:#475569;margin-bottom:20px">
        Les marchandises décrites ci-dessus ont été livrées en bon état et en quantité conforme.
    </p>

    <div class="sig-row">
        <div class="sig-box">
            @if($dn->dn_type === 'OUT')
            <div>Signature du Livreur</div>
            <div style="color:#64748b;font-size:7pt">{{ $company->name }}</div>
            @else
            <div>Signature du Fournisseur</div>
            <div style="color:#64748b;font-size:7pt">{{ $supplier?->name ?? '—' }}</div>
            @endif
        </div>
        <div class="sig-box">
            @if($dn->dn_type === 'OUT')
            <div>Accusé de réception — Client</div>
            <div style="color:#64748b;font-size:7pt">{{ $customer?->name ?? '—' }}</div>
            @else
            <div>Reçu par {{ $company->name }}</div>
            <div style="color:#64748b;font-size:7pt">Responsable Magasin</div>
            @endif
        </div>
    </div>

    <div class="footer">
        {{ $company->name }} — NIU: {{ $company->niu }} — Bon de Livraison {{ $dn->dn_number }} — Généré par Opes Books © {{ date('Y') }}
    </div>
</div>
</body>
</html>
