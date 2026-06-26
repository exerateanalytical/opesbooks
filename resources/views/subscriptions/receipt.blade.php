<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Reçu Abonnement — {{ $company->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #0f172a; }
        .page { padding: 28px 32px; max-width: 420px; margin: auto; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 3px solid #0A192F; padding-bottom: 16px; }
        .brand-name { font-size: 20pt; font-weight: 900; color: #0A192F; }
        .brand-name span { color: #F59E0B; }
        .tagline { font-size: 8pt; color: #64748b; margin-top: 2px; }
        .check { font-size: 32pt; text-align: center; margin: 16px 0; color: #16a34a; }
        .receipt-box { border: 2px solid #0A192F; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
        .receipt-title { font-size: 11pt; font-weight: 900; text-align: center; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 8.5pt; border-bottom: 1px solid #f1f5f9; }
        .row:last-child { border-bottom: none; }
        .row .label { color: #64748b; }
        .row .value { font-weight: 600; }
        .amount-big { text-align: center; background: #f0fdf4; border: 2px solid #16a34a; border-radius: 8px; padding: 14px; margin: 16px 0; }
        .amount-big .label { font-size: 8pt; color: #64748b; }
        .amount-big .value { font-size: 18pt; font-weight: 900; color: #16a34a; margin-top: 4px; }
        .plan-badge { display: inline-block; background: #F59E0B; color: #0A192F; font-weight: 900; padding: 4px 14px; border-radius: 20px; font-size: 9pt; margin: 8px auto; text-align: center; }
        .footer { margin-top: 20px; font-size: 7pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 8px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="brand-name">Opes<span>Books</span></div>
        <div class="tagline">Plateforme Comptable & Fiscale — Cameroun</div>
    </div>

    <div class="check">✓</div>

    <div style="text-align:center;margin-bottom:16px">
        <div class="plan-badge">Plan {{ strtoupper($subscription->plan_name ?? 'PRO') }}</div>
    </div>

    <div class="amount-big">
        <div class="label">Montant réglé</div>
        <div class="value">{{ number_format($subscription->amount_xaf ?? 0, 0, ',', ' ') }} XAF</div>
    </div>

    <div class="receipt-box">
        <div class="receipt-title">Reçu de Paiement</div>
        <div class="row"><span class="label">Entreprise</span><span class="value">{{ $company->name }}</span></div>
        <div class="row"><span class="label">NIU</span><span class="value">{{ $company->niu }}</span></div>
        <div class="row"><span class="label">N° Reçu</span><span class="value">{{ $receiptNumber }}</span></div>
        <div class="row"><span class="label">Date paiement</span><span class="value">{{ \Carbon\Carbon::parse($subscription->updated_at ?? now())->format('d/m/Y H:i') }}</span></div>
        <div class="row"><span class="label">Mode de paiement</span><span class="value">Mobile Money</span></div>
        <div class="row"><span class="label">Période couverte</span><span class="value">{{ \Carbon\Carbon::parse($subscription->starts_at)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($subscription->ends_at)->format('d/m/Y') }}</span></div>
        <div class="row"><span class="label">Statut</span><span class="value" style="color:#16a34a">PAYÉ ✓</span></div>
    </div>

    <p style="font-size:7.5pt;color:#475569;text-align:center">
        Merci de votre confiance. Conservez ce reçu comme justificatif de votre abonnement Opes Books.
    </p>

    <div class="footer">
        Opes Books par Opesware — Douala, Cameroun — Généré le {{ now()->format('d/m/Y H:i') }}<br>
        Ce document tient lieu de facture d'abonnement conformément à la législation camerounaise.
    </div>
</div>
</body>
</html>
