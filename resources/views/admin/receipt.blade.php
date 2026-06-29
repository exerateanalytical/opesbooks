<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; }
        .brand { font-size: 22px; font-weight: 900; letter-spacing: 2px; }
        .brand span { color: #F59E0B; }
        .muted { color: #6b7280; }
        .box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        td { padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .label { color: #6b7280; }
        .right { text-align: right; }
        .total { font-size: 16px; font-weight: 900; color: #F59E0B; }
        .pill { display: inline-block; border-radius: 9999px; padding: 2px 10px; font-size: 10px; font-weight: 700; }
        .pill-ok { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
        .pill-bad { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    </style>
</head>
<body>
    <div class="brand">OPES<span>BOOKS</span></div>
    <div class="muted">Reçu de paiement · Opesware, Douala, Cameroun</div>

    <div class="box">
        <table>
            <tr><td class="label">N° de reçu</td><td class="right"><strong>{{ $payment->receipt_number }}</strong></td></tr>
            <tr><td class="label">Date</td><td class="right">{{ $payment->created_at?->format('d/m/Y H:i') }}</td></tr>
            <tr><td class="label">Entreprise</td><td class="right">{{ $payment->company?->name }}</td></tr>
            <tr><td class="label">Plan</td><td class="right">{{ $planName ?? strtoupper($payment->plan_slug ?? '—') }}</td></tr>
            <tr><td class="label">Méthode de paiement</td><td class="right">{{ str_replace('_', ' ', ucfirst($payment->payment_method)) }}</td></tr>
            @if($payment->reference)<tr><td class="label">Référence</td><td class="right">{{ $payment->reference }}</td></tr>@endif
            @if($payment->period_start)<tr><td class="label">Période</td><td class="right">{{ \Carbon\Carbon::parse($payment->period_start)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($payment->period_end)->format('d/m/Y') }}</td></tr>@endif
            <tr><td class="label">Statut</td><td class="right"><span class="pill {{ $payment->status === 'completed' ? 'pill-ok' : 'pill-bad' }}">{{ strtoupper($payment->status) }}</span></td></tr>
            <tr><td class="total">MONTANT</td><td class="right total">{{ number_format($payment->amount_xaf, 0, ',', ' ') }} XAF</td></tr>
        </table>
    </div>

    <p class="muted" style="margin-top:24px;font-size:10px">Ce reçu est généré automatiquement par la plateforme OPESBooks. Merci de votre confiance.</p>
</body>
</html>
