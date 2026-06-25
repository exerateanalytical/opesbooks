<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 — Erreur Serveur · Opes Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        *{box-sizing:border-box}
        body{
            font-family:-apple-system,BlinkMacSystemFont,'SF Pro Display','Helvetica Neue',sans-serif;
            background:radial-gradient(ellipse 120% 80% at 20% -5%,#1a2d4f 0%,#0a192f 35%,#050d1a 65%,#0f0a1e 100%);
            min-height:100vh;-webkit-font-smoothing:antialiased;
        }
        body::before{content:'';position:fixed;inset:0;pointer-events:none;
            background:radial-gradient(ellipse 60% 40% at 10% 15%,rgba(245,158,11,.07) 0%,transparent 60%),
                        radial-gradient(ellipse 50% 35% at 90% 80%,rgba(244,63,94,.08) 0%,transparent 55%)}
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
             style="background:linear-gradient(145deg,rgba(245,158,11,.2),rgba(245,158,11,.08));border:1px solid rgba(245,158,11,.35);box-shadow:0 0 60px rgba(245,158,11,.15)">
            <span class="text-amber-400 font-black text-4xl">500</span>
        </div>

        <h1 class="text-3xl font-black text-white tracking-tight mb-2">Erreur Serveur</h1>
        <p class="text-slate-400 text-sm mb-1">Internal Server Error</p>
        <p class="text-slate-500 text-xs mb-8 leading-relaxed">
            Une erreur inattendue s'est produite. Notre équipe a été notifiée.<br>
            <span class="text-slate-600">An unexpected error occurred. Our team has been notified.</span>
        </p>

        <div class="glass-card rounded-2xl p-5 mb-6">
            @if(isset($exception) && config('app.debug'))
                <p class="text-[10px] font-black text-rose-400 uppercase tracking-widest mb-2">Debug Info</p>
                <p class="text-xs text-slate-400 font-mono text-left break-all">{{ $exception->getMessage() }}</p>
            @else
                <p class="text-sm text-slate-400">
                    Veuillez réessayer dans quelques instants ou contacter
                    <span class="text-amber-400 font-bold">support@opesware.cm</span>
                </p>
            @endif
        </div>

        <a href="/app"
           class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest transition-all active:scale-98"
           style="background:linear-gradient(135deg,rgba(245,158,11,.95),rgba(160,124,8,.95));border:1px solid rgba(245,158,11,.5);color:#0a192f;box-shadow:0 4px 20px rgba(245,158,11,.3)">
            ← Retour au Tableau de Bord
        </a>

        <p class="text-slate-600 text-[10px] font-medium uppercase tracking-widest mt-6">
            OPES<span class="text-amber-400/60">BOOKS</span> · Opesware · opesbooks.cm
        </p>
    </div>
</body>
</html>
