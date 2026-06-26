<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opes Books — Hors ligne</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', -apple-system, sans-serif; background: #0B1120; min-height: 100vh; }
        .glass-card { background: #151F2E; border: 1px solid #253347; border-radius: 0.75rem; }
    </style>
</head>
<body class="text-slate-200 flex items-center justify-center p-6">
    <div class="text-center max-w-md">
        <svg width="72" height="72" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mx-auto opacity-60 mb-5"><line x1="1" y1="1" x2="23" y2="23"/><path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/><path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/><path d="M10.71 5.05A16 16 0 0 1 22.58 9"/><path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>
        <h1 class="text-2xl font-black text-white mb-2">Vous êtes hors ligne</h1>
        <p class="text-sm text-slate-400 mb-7">OPESBooks fonctionne sans connexion. Vos données seront synchronisées automatiquement dès que vous retrouvez Internet.</p>
        <div class="grid grid-cols-2 gap-3 mb-7">
            <div class="glass-card rounded-xl p-4 text-center"><div class="text-sm text-slate-300 font-bold">Journal</div><div class="text-[11px] text-emerald-400 mt-1">✓ Disponible</div></div>
            <div class="glass-card rounded-xl p-4 text-center"><div class="text-sm text-slate-300 font-bold">Factures</div><div class="text-[11px] text-emerald-400 mt-1">✓ Disponible</div></div>
            <div class="glass-card rounded-xl p-4 text-center"><div class="text-sm text-slate-300 font-bold">Rapports</div><div class="text-[11px] text-amber-400 mt-1">⚡ Dernière synchro</div></div>
            <div class="glass-card rounded-xl p-4 text-center"><div class="text-sm text-slate-300 font-bold">IA</div><div class="text-[11px] text-amber-400 mt-1">⚡ Mode local</div></div>
        </div>
        <button onclick="window.location.href='/app'" class="px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest text-slate-900" style="background:#F59E0B">Réessayer la connexion</button>
    </div>
</body>
</html>
