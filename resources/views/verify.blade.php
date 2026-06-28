<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de document — OPESBooks</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px;
            background:radial-gradient(ellipse 120% 80% at 20% -5%,#1a2d4f,#010048 35%,#050d1a 65%,#0f0a1e);color:#e2e8f0}
        .card{width:100%;max-width:440px;background:rgba(255,255,255,0.05);backdrop-filter:blur(20px);
            border:1px solid rgba(255,255,255,0.12);border-radius:18px;padding:32px;text-align:center}
        .brand{font-size:18px;font-weight:900;letter-spacing:2px;margin-bottom:24px}.brand span{color:#C99B0E}
        .icon{width:72px;height:72px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
        .ok{background:rgba(16,185,129,0.15);border:2px solid #10b981}.bad{background:rgba(244,63,94,0.15);border:2px solid #f43f5e}
        h1{font-size:20px;font-weight:900;margin-bottom:6px}
        .ok-t{color:#34d399}.bad-t{color:#fb7185}
        p.sub{color:#94a3b8;font-size:13px;margin-bottom:22px}
        .rows{text-align:left;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.08);border-radius:12px;padding:6px 14px}
        .row{display:flex;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(255,255,255,0.06);font-size:13px}
        .row:last-child{border-bottom:none}.row .k{color:#94a3b8}.row .v{font-weight:700;text-align:right}
        .foot{margin-top:22px;font-size:11px;color:#64748b}
    </style>
</head>
<body>
    <div class="card">
        <div class="brand">OPES<span>BOOKS</span></div>
        @if($valid)
            <div class="icon ok"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></div>
            <h1 class="ok-t">Document authentique</h1>
            <p class="sub">Ce document a bien été émis via OPESBooks et n'a pas été altéré.</p>
            <div class="rows">
                <div class="row"><span class="k">Entreprise émettrice</span><span class="v">{{ $data['n'] ?? '—' }}</span></div>
                <div class="row"><span class="k">NIU</span><span class="v">{{ $data['c'] ?? '—' }}</span></div>
                <div class="row"><span class="k">Type de document</span><span class="v">{{ $data['t'] ?? '—' }}</span></div>
                <div class="row"><span class="k">Référence</span><span class="v" style="font-family:monospace">{{ $data['r'] ?? '—' }}</span></div>
                <div class="row"><span class="k">Émis le</span><span class="v">{{ isset($data['d']) ? \Illuminate\Support\Carbon::parse($data['d'])->format('d/m/Y H:i') : '—' }}</span></div>
            </div>
        @else
            <div class="icon bad"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#f43f5e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div>
            <h1 class="bad-t">Document non vérifiable</h1>
            <p class="sub">La signature est invalide ou le lien est incomplet. Ce document n'a pas pu être authentifié comme provenant d'OPESBooks.</p>
        @endif
        <div class="foot">Vérification cryptographique · OPESBooks · Opesware, Douala</div>
    </div>
</body>
</html>
