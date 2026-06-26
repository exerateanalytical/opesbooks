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
    :root{--c-bg:#0F172A;--c-surface:#1E293B;--c-raised:#293548;--c-border:#334155;--c-accent:#F59E0B;--c-accent-dim:#D97706;--c-text:#F1F5F9;--c-muted:#94A3B8;--c-faint:#64748B}
    [x-cloak]{display:none!important}
    *{box-sizing:border-box}
    body{font-family:'Inter',sans-serif;background:var(--c-bg);color:var(--c-text);-webkit-font-smoothing:antialiased}
    .glass{background:var(--c-surface);border:1px solid var(--c-border)}
    .glass-blur{background:rgba(30,41,59,0.8);backdrop-filter:blur(20px);border:1px solid var(--c-border)}
    .text-gold{color:var(--c-accent)}
    .bg-gold{background-color:var(--c-accent)}
    .bg-gold-light{background-color:var(--c-accent-dim)}
    .border-gold{border-color:var(--c-accent)}
    .ring-gold{--tw-ring-color:var(--c-accent)}
    /* Section rhythm — alternate bg to create visual separation */
    .section-alt{background:var(--c-surface)}
    /* Consistent placeholder color for all inputs */
    ::placeholder{color:var(--c-faint)!important}
    /* Tailwind prose overrides for blog */
    .prose{color:var(--c-text)}
    .prose h2,.prose h3,.prose h4{color:var(--c-text)}
    .prose a{color:var(--c-accent)}
    .prose strong{color:var(--c-text)}
    .prose code{background:var(--c-raised);border:1px solid var(--c-border);padding:0.1em 0.4em;border-radius:0.3em;font-size:0.85em}
    .prose blockquote{border-left:3px solid var(--c-accent);color:var(--c-muted);background:var(--c-surface);padding:0.75rem 1rem;border-radius:0 0.5rem 0.5rem 0}
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
            <a href="/login" class="text-sm font-black px-4 py-2.5 rounded-xl transition" style="background:var(--c-accent);color:#0F172A">Essai gratuit →</a>
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
