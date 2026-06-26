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
    <script>tailwind.config={theme:{extend:{colors:{gold:'#C99B0E','gold-light':'#E3B420',navy:'#010048','navy-mid':'#010057'},fontFamily:{sans:['Inter','sans-serif']}}}};</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}body{font-family:'Inter',sans-serif;background:#010048;color:#e2e8f0}
    .glass{background:rgba(255,255,255,0.05);backdrop-filter:blur(20px);border:1px solid rgba(255,255,255,0.1)}</style>
    @verbatim
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"SoftwareApplication","name":"OPESBooks","applicationCategory":"BusinessApplication","operatingSystem":"Web","offers":{"@type":"Offer","price":"5000","priceCurrency":"XAF"},"description":"Logiciel de comptabilité SYSCOHADA pour PME camerounaises","url":"https://opesbooks.cm"}
    </script>
    @endverbatim
</head>
<body class="min-h-screen flex flex-col" x-data="{ mobileNav:false }">

<!-- Topnav -->
<header class="sticky top-0 z-50 h-16 bg-[#010048]/95 backdrop-blur border-b border-white/10 flex items-center">
    <div class="max-w-7xl mx-auto w-full px-5 flex items-center justify-between">
        <a href="/" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-gold/20 border border-gold/40 flex items-center justify-center text-[10px] font-black text-gold">OB</div>
            <span class="font-black text-sm tracking-widest">OPES<span class="text-gold">BOOKS</span></span>
        </a>
        <nav class="hidden md:flex items-center gap-7 text-sm font-medium text-white/70">
            <a href="{{ route('m.features') }}" class="hover:text-white transition">Fonctionnalités</a>
            <a href="{{ route('m.pricing') }}" class="hover:text-white transition">Tarifs</a>
            <a href="{{ route('blog.index') }}" class="hover:text-white transition">Blog</a>
            <a href="{{ route('m.faq') }}" class="hover:text-white transition">FAQ</a>
            <a href="{{ route('m.about') }}" class="hover:text-white transition">À propos</a>
            <a href="{{ route('m.contact') }}" class="hover:text-white transition">Contact</a>
        </nav>
        <div class="flex items-center gap-3">
            <a href="/login" class="hidden sm:inline text-sm font-semibold text-white/80 hover:text-white px-3 py-2">Se connecter</a>
            <a href="/login" class="text-sm font-black text-[#010048] bg-gold hover:bg-gold-light px-4 py-2.5 rounded-xl transition">Essai gratuit →</a>
            <button @click="mobileNav=!mobileNav" class="md:hidden text-white/70"><svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg></button>
        </div>
    </div>
</header>
<div x-show="mobileNav" x-cloak class="md:hidden bg-[#010057] border-b border-white/10 px-5 py-4 flex flex-col gap-3 text-sm">
    <a href="{{ route('m.features') }}" class="text-white/80">Fonctionnalités</a>
    <a href="{{ route('m.pricing') }}" class="text-white/80">Tarifs</a>
    <a href="{{ route('blog.index') }}" class="text-white/80">Blog</a>
    <a href="{{ route('m.faq') }}" class="text-white/80">FAQ</a>
    <a href="{{ route('m.about') }}" class="text-white/80">À propos</a>
    <a href="{{ route('m.contact') }}" class="text-white/80">Contact</a>
    <a href="/login" class="text-gold font-bold">Se connecter</a>
</div>

<main class="flex-1">
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-[#010048] border-t border-white/10 mt-16">
    <div class="max-w-7xl mx-auto px-5 py-14 grid grid-cols-2 md:grid-cols-4 gap-8 text-sm">
        <div class="col-span-2 md:col-span-1">
            <span class="font-black tracking-widest">OPES<span class="text-gold">BOOKS</span></span>
            <p class="text-white/40 mt-3 text-xs leading-relaxed">Votre bouclier fiscal camerounais. SYSCOHADA · DGI · Douala, Cameroun.</p>
            <p class="text-white/40 mt-3 text-xs leading-relaxed">Édité par Opesware · <a href="https://opesware.com" class="hover:text-white">opesware.com</a><br><a href="mailto:contact@opesware.com" class="hover:text-white">contact@opesware.com</a> · +237 670 416 238</p>
        </div>
        <div>
            <div class="text-white/40 text-xs font-black uppercase tracking-widest mb-3">Produit</div>
            <ul class="space-y-2 text-white/60 text-xs">
                <li><a href="{{ route('m.features') }}" class="hover:text-white">Fonctionnalités</a></li>
                <li><a href="{{ route('m.pricing') }}" class="hover:text-white">Tarifs</a></li>
                <li><a href="/developer" class="hover:text-white">API Développeurs</a></li>
            </ul>
        </div>
        <div>
            <div class="text-white/40 text-xs font-black uppercase tracking-widest mb-3">Ressources</div>
            <ul class="space-y-2 text-white/60 text-xs">
                <li><a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a></li>
                <li><a href="{{ route('m.faq') }}" class="hover:text-white">FAQ Cameroun / CEMAC</a></li>
                <li><a href="{{ route('m.about') }}" class="hover:text-white">À propos</a></li>
                <li><a href="{{ route('developer.postman') }}" class="hover:text-white">Collection Postman</a></li>
                <li><a href="{{ route('m.contact') }}" class="hover:text-white">Contact</a></li>
            </ul>
        </div>
        <div>
            <div class="text-white/40 text-xs font-black uppercase tracking-widest mb-3">Légal</div>
            <ul class="space-y-2 text-white/60 text-xs">
                <li><a href="{{ route('m.terms') }}" class="hover:text-white">CGU</a></li>
                <li><a href="{{ route('m.privacy') }}" class="hover:text-white">Confidentialité</a></li>
                <li><a href="{{ route('m.faq') }}" class="hover:text-white">FAQ</a></li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 py-5 text-center text-white/40 text-xs">© {{ date('Y') }} OPESBooks · Fait avec ♥ au Cameroun (CM)</div>
</footer>
</body>
</html>
