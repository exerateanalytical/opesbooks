<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Page introuvable · Opes Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak]{display:none!important}
        *{box-sizing:border-box}
        body{
            font-family:-apple-system,BlinkMacSystemFont,'SF Pro Display','Helvetica Neue',sans-serif;
            background:radial-gradient(ellipse 120% 80% at 20% -5%,#1a2d4f 0%,#0a192f 35%,#050d1a 65%,#0f0a1e 100%);
            min-height:100vh;-webkit-font-smoothing:antialiased;
        }
        body::before{content:'';position:fixed;inset:0;pointer-events:none;
            background:radial-gradient(ellipse 60% 40% at 10% 15%,rgba(245,158,11,.09) 0%,transparent 60%),
                        radial-gradient(ellipse 50% 35% at 90% 80%,rgba(16,185,129,.07) 0%,transparent 55%)}
        .glass-card{
            background:linear-gradient(145deg,rgba(255,255,255,.10) 0%,rgba(255,255,255,.04) 100%);
            backdrop-filter:blur(32px) saturate(200%);-webkit-backdrop-filter:blur(32px) saturate(200%);
            border:1px solid rgba(255,255,255,.14);border-top-color:rgba(255,255,255,.24);
            box-shadow:0 8px 48px rgba(0,0,0,.6),0 1px 0 rgba(255,255,255,.14) inset;
        }
        @keyframes float-in{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
        .float-in{animation:float-in .4s cubic-bezier(.34,1.56,.64,1) both}
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md text-center float-in relative z-10">

        <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl mb-6"
             style="background:linear-gradient(145deg,rgba(244,63,94,.2),rgba(244,63,94,.08));border:1px solid rgba(244,63,94,.35);box-shadow:0 0 60px rgba(244,63,94,.15)">
            <span class="text-rose-400 font-black text-4xl">404</span>
        </div>

        <h1 class="text-3xl font-black text-white tracking-tight mb-2">Page Introuvable</h1>
        <p class="text-slate-400 text-sm mb-1">Page Not Found</p>
        <p class="text-slate-500 text-xs mb-8 leading-relaxed">
            La page que vous cherchez n'existe pas ou a été déplacée.<br>
            <span class="text-slate-600">The page you're looking for doesn't exist or has been moved.</span>
        </p>

        <div class="glass-card rounded-2xl p-6 mb-6 text-left">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-3">Où voulez-vous aller ?</p>
            <div class="space-y-2">
                <a href="/app" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:bg-white/10"
                   style="border:1px solid rgba(255,255,255,.08)">
                    <span class="text-amber-400">⌂</span>
                    <span>Tableau de Bord / Dashboard</span>
                </a>
                <a href="/tax-dashboard" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:bg-white/10"
                   style="border:1px solid rgba(255,255,255,.08)">
                    <span class="text-indigo-400">📈</span>
                    <span>Bilan Fiscal / Tax Monitor</span>
                </a>
                <a href="/dgi-monitor" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:bg-white/10"
                   style="border:1px solid rgba(255,255,255,.08)">
                    <span class="text-emerald-400">📡</span>
                    <span>Suivi DGI / DGI Monitor</span>
                </a>
                <a href="/about" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:bg-white/10"
                   style="border:1px solid rgba(255,255,255,.08)">
                    <span class="text-slate-400">ℹ</span>
                    <span>À Propos / About</span>
                </a>
            </div>
        </div>

        <p class="text-slate-600 text-[10px] font-medium uppercase tracking-widest">
            OPES<span class="text-amber-400/60">BOOKS</span> · Opesware · opesbooks.cm
        </p>
    </div>
</body>
</html>
