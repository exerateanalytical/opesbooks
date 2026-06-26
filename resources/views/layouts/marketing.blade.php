<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OPESBooks — Logiciel Comptable pour PME Camerounaises | SYSCOHADA & DGI')</title>
    <meta name="description" content="@yield('description', 'Logiciel de comptabilité en ligne conçu pour les PME camerounaises. SYSCOHADA, TVA 19,25%, DSF, DGI, Paie CNPS/DIPE. Essai gratuit 30 jours.')">
    <meta property="og:title" content="OPESBooks — Votre Bouclier Fiscal Camerounais">
    <meta property="og:description" content="Logiciel de comptabilité SYSCOHADA pour PME camerounaises.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{gold:'#F59E0B','gold-light':'#FCD34D','gold-dim':'#D97706',surface:'#1E293B','surface-raised':'#293548',border:'#334155',navy:'#0F172A'},fontFamily:{sans:['Inter','sans-serif']}}}};</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
    /* ── Design tokens ─────────────────────────────────────────────── */
    :root{
        --c-bg:#0B1120;
        --c-surface:#151F2E;
        --c-raised:#1C2A3A;
        --c-border:#253347;
        --c-border-strong:#334155;
        --c-accent:#F59E0B;
        --c-accent-dim:#D97706;
        --c-accent-glow:rgba(245,158,11,0.18);
        --c-text:#F0F4FA;
        --c-muted:#8B9EC0;
        --c-faint:#4E647E;
        --radius:0.875rem;
        --radius-sm:0.625rem;
        --shadow-card:0 1px 3px rgba(0,0,0,0.4),0 4px 16px rgba(0,0,0,0.25);
        --shadow-elevated:0 4px 24px rgba(0,0,0,0.5),0 1px 0 rgba(255,255,255,0.04) inset;
    }
    [x-cloak]{display:none!important}
    *{box-sizing:border-box}
    body{font-family:'Inter',sans-serif;background:var(--c-bg);color:var(--c-text);-webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility}

    /* ── Cards / surfaces ──────────────────────────────────────────── */
    .glass{
        background:var(--c-surface);
        border:1px solid var(--c-border);
        box-shadow:var(--shadow-card);
        transition:border-color 0.2s,box-shadow 0.2s;
    }
    .glass:hover{border-color:var(--c-border-strong)}
    .glass-blur{background:rgba(11,17,32,0.85);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid var(--c-border)}
    .glass-raised{background:var(--c-raised);border:1px solid var(--c-border);box-shadow:var(--shadow-card)}

    /* ── Accent utilities ──────────────────────────────────────────── */
    .text-gold{color:var(--c-accent)}
    .bg-gold{background-color:var(--c-accent)}
    .bg-gold-light{background-color:var(--c-accent-dim)}
    .border-gold{border-color:var(--c-accent)}
    .ring-gold{--tw-ring-color:var(--c-accent)}

    /* ── Badge / pill ──────────────────────────────────────────────── */
    .badge-gold{
        display:inline-flex;align-items:center;gap:0.375rem;
        padding:0.25rem 0.75rem;border-radius:999px;
        font-size:0.6875rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;
        color:var(--c-accent);
        background:rgba(245,158,11,0.1);
        border:1px solid rgba(245,158,11,0.25);
    }

    /* ── Buttons ───────────────────────────────────────────────────── */
    .btn-primary{
        display:inline-flex;align-items:center;justify-content:center;gap:0.5rem;
        padding:0.75rem 1.5rem;border-radius:var(--radius-sm);
        font-size:0.875rem;font-weight:700;letter-spacing:0.01em;
        background:var(--c-accent);color:#0B1120;border:none;
        box-shadow:0 2px 8px rgba(245,158,11,0.3);
        transition:background 0.15s,box-shadow 0.15s,transform 0.1s;
        cursor:pointer;text-decoration:none;
    }
    .btn-primary:hover{background:var(--c-accent-dim);box-shadow:0 4px 16px rgba(245,158,11,0.4);transform:translateY(-1px)}
    .btn-primary:active{transform:translateY(0);box-shadow:0 1px 4px rgba(245,158,11,0.2)}

    .btn-secondary{
        display:inline-flex;align-items:center;justify-content:center;gap:0.5rem;
        padding:0.75rem 1.5rem;border-radius:var(--radius-sm);
        font-size:0.875rem;font-weight:600;
        background:var(--c-raised);color:var(--c-text);
        border:1px solid var(--c-border-strong);
        transition:background 0.15s,border-color 0.15s,transform 0.1s;
        cursor:pointer;text-decoration:none;
    }
    .btn-secondary:hover{background:var(--c-border);border-color:#475569;transform:translateY(-1px)}
    .btn-secondary:active{transform:translateY(0)}

    /* ── Section rhythm ────────────────────────────────────────────── */
    .section-alt{background:var(--c-surface)}
    .section-divider{border-top:1px solid var(--c-border)}

    /* ── Form inputs ───────────────────────────────────────────────── */
    .form-input{
        width:100%;background:var(--c-raised);
        border:1.5px solid var(--c-border);
        color:var(--c-text);border-radius:var(--radius-sm);
        padding:0.625rem 0.875rem;font-size:0.875rem;font-family:inherit;
        transition:border-color 0.15s,box-shadow 0.15s;
        outline:none;
    }
    .form-input:focus{border-color:var(--c-accent);box-shadow:0 0 0 3px rgba(245,158,11,0.12)}
    .form-input::placeholder{color:var(--c-faint)}
    ::placeholder{color:var(--c-faint)}

    /* ── Table ─────────────────────────────────────────────────────── */
    .tbl-head{background:var(--c-bg);border-bottom:1px solid var(--c-border-strong)}
    .tbl-row{border-bottom:1px solid var(--c-border);transition:background 0.15s}
    .tbl-row:hover{background:var(--c-raised)}
    .tbl-row:last-child{border-bottom:none}

    /* ── Prose (blog) ──────────────────────────────────────────────── */
    .prose{color:var(--c-text);line-height:1.75}
    .prose h2,.prose h3,.prose h4{color:var(--c-text);font-weight:800;margin-top:2rem;margin-bottom:0.75rem}
    .prose p{color:var(--c-muted);margin-bottom:1rem}
    .prose a{color:var(--c-accent);text-decoration:underline;text-underline-offset:2px}
    .prose strong{color:var(--c-text)}
    .prose ul{list-style:disc;padding-left:1.5rem;color:var(--c-muted)}
    .prose li{margin-bottom:0.35rem}
    .prose code{background:var(--c-raised);border:1px solid var(--c-border);padding:0.1em 0.4em;border-radius:0.3em;font-size:0.82em;color:var(--c-accent)}
    .prose blockquote{border-left:3px solid var(--c-accent);color:var(--c-muted);background:var(--c-surface);padding:0.875rem 1.25rem;border-radius:0 var(--radius-sm) var(--radius-sm) 0;margin:1.5rem 0}
    .prose hr{border-color:var(--c-border);margin:2rem 0}

    /* ── Scrollbar ─────────────────────────────────────────────────── */
    ::-webkit-scrollbar{width:5px;height:5px}
    ::-webkit-scrollbar-track{background:transparent}
    ::-webkit-scrollbar-thumb{background:var(--c-border-strong);border-radius:99px}
    ::-webkit-scrollbar-thumb:hover{background:#475569}

    /* ── Transitions ───────────────────────────────────────────────── */
    a,button{transition:color 0.15s,background 0.15s,border-color 0.15s,opacity 0.15s}
    </style>
    @verbatim
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"SoftwareApplication","name":"OPESBooks","applicationCategory":"BusinessApplication","operatingSystem":"Web","offers":{"@type":"Offer","price":"5000","priceCurrency":"XAF"},"description":"Logiciel de comptabilité SYSCOHADA pour PME camerounaises","url":"https://opesbooks.cm"}
    </script>
    @endverbatim
</head>
<body class="min-h-screen flex flex-col" x-data="{ mobileNav:false }">

<!-- Topnav -->
<header class="sticky top-0 z-50 h-16 glass-blur flex items-center" style="border-bottom:1px solid var(--c-border)">
    <div class="max-w-7xl mx-auto w-full px-5 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-[10px] font-black text-amber-400" style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.35)">OB</div>
            <span class="font-black text-sm tracking-widest text-white">OPES<span class="text-gold">BOOKS</span></span>
        </a>
        <nav class="hidden md:flex items-center gap-7 text-sm font-medium" style="color:var(--c-muted)">
            <a href="{{ route('m.features') }}" class="hover:text-white transition">Fonctionnalités</a>
            <a href="{{ route('m.pricing') }}" class="hover:text-white transition">Tarifs</a>
            <a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a>
            <a href="{{ route('m.faq') }}" class="hover:text-white transition">FAQ</a>
            <a href="{{ route('m.about') }}" class="hover:text-white transition">À propos</a>
            <a href="{{ route('m.contact') }}" class="hover:text-white transition">Contact</a>
        </nav>
        <div class="flex items-center gap-3">
            <a href="/login" class="hidden sm:inline text-sm font-semibold hover:text-white px-3 py-2 transition" style="color:var(--c-muted)">Se connecter</a>
            <a href="/login" class="btn-primary" style="padding:0.5rem 1.125rem;font-size:0.8125rem">Essai gratuit →</a>
            <button @click="mobileNav=!mobileNav" style="color:var(--c-muted)"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
        </div>
    </div>
</header>
<div x-show="mobileNav" x-cloak class="md:hidden px-5 py-4 flex flex-col gap-3 text-sm" style="background:var(--c-surface);border-bottom:1px solid var(--c-border)">
    <a href="{{ route('m.features') }}" class="hover:text-white transition" style="color:var(--c-muted)">Fonctionnalités</a>
    <a href="{{ route('m.pricing') }}" class="hover:text-white transition" style="color:var(--c-muted)">Tarifs</a>
    <a href="{{ route('blog.index') }}" class="hover:text-white transition" style="color:var(--c-muted)">Blog</a>
    <a href="{{ route('m.faq') }}" class="hover:text-white transition" style="color:var(--c-muted)">FAQ</a>
    <a href="{{ route('m.about') }}" class="hover:text-white transition" style="color:var(--c-muted)">À propos</a>
    <a href="{{ route('m.contact') }}" class="hover:text-white transition" style="color:var(--c-muted)">Contact</a>
    <a href="/login" class="font-bold text-gold">Se connecter</a>
</div>

<main class="flex-1">
    @yield('content')
</main>

<!-- Footer -->
<footer class="mt-16" style="background:var(--c-surface);border-top:1px solid var(--c-border)">
    <div class="max-w-7xl mx-auto px-5 py-14 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
        <div class="col-span-2 md:col-span-1">
            <span class="font-black tracking-widest">OPES<span class="text-gold">BOOKS</span></span>
            <p class="text-xs leading-relaxed mt-3" style="color:var(--c-faint)">Votre bouclier fiscal camerounais. SYSCOHADA · DGI · Douala, Cameroun.</p>
            <p class="text-xs leading-relaxed mt-3" style="color:var(--c-faint)">Édité par Opesware · <a href="https://opesware.com" class="hover:text-white transition">opesware.com</a><br>Petite Terrain, Bonamoussadi — Douala, Cameroun<br><a href="mailto:contact@opesware.com" class="hover:text-white transition">contact@opesware.com</a> · +237 670 416 238</p>
        </div>
        <div>
            <div class="text-xs font-black uppercase tracking-widest mb-3" style="color:var(--c-faint)">Produit</div>
            <ul class="space-y-2 text-xs" style="color:var(--c-muted)">
                <li><a href="{{ route('m.features') }}" class="hover:text-white transition">Fonctionnalités</a></li>
                <li><a href="{{ route('m.pricing') }}" class="hover:text-white transition">Tarifs</a></li>
                <li><a href="/developer" class="hover:text-white transition">API Développeurs</a></li>
            </ul>
        </div>
        <div>
            <div class="text-xs font-black uppercase tracking-widest mb-3" style="color:var(--c-faint)">Ressources</div>
            <ul class="space-y-2 text-xs" style="color:var(--c-muted)">
                <li><a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a></li>
                <li><a href="{{ route('m.faq') }}" class="hover:text-white transition">FAQ Cameroun / CEMAC</a></li>
                <li><a href="{{ route('m.about') }}" class="hover:text-white transition">À propos</a></li>
                <li><a href="{{ route('developer.postman') }}" class="hover:text-white transition">Collection Postman</a></li>
                <li><a href="{{ route('m.contact') }}" class="hover:text-white transition">Contact</a></li>
            </ul>
        </div>
        <div>
            <div class="text-xs font-black uppercase tracking-widest mb-3" style="color:var(--c-faint)">Légal</div>
            <ul class="space-y-2 text-xs" style="color:var(--c-muted)">
                <li><a href="{{ route('m.terms') }}" class="hover:text-white transition">CGU</a></li>
                <li><a href="{{ route('m.privacy') }}" class="hover:text-white transition">Confidentialité</a></li>
                <li><a href="{{ route('m.faq') }}" class="hover:text-white transition">FAQ</a></li>
            </ul>
        </div>
    </div>
    <div class="py-5 text-center text-xs" style="border-top:1px solid var(--c-border);color:var(--c-faint)">© {{ date('Y') }} OPESBooks · Fait avec ♥ au Cameroun (CM)</div>
</footer>
</body>
</html>
