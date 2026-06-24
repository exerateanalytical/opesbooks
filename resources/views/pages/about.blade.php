<x-app-layout title="À Propos — Opes Books">
<div class="max-w-4xl mx-auto space-y-8" x-data>

    {{-- Hero --}}
    <div class="glass-card rounded-3xl p-10 text-center relative overflow-hidden float-in">
        <div class="absolute inset-0 pointer-events-none"
             style="background:radial-gradient(ellipse 70% 60% at 50% 0%,rgba(245,158,11,0.10) 0%,transparent 70%)"></div>
        <div class="relative z-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl mb-6"
                 style="background:linear-gradient(145deg,rgba(245,158,11,0.22),rgba(245,158,11,0.08));border:1px solid rgba(245,158,11,0.35);box-shadow:0 0 60px rgba(245,158,11,0.18)">
                <span class="text-amber-400 font-black text-2xl tracking-widest">OB</span>
            </div>
            <h1 class="text-4xl font-black text-white tracking-tight">
                OPES<span class="text-amber-400">BOOKS</span>
            </h1>
            <p class="text-slate-400 text-sm mt-2 font-semibold uppercase tracking-widest">
                <span x-show="$root.closest('[x-data]').__x?.$data?.lang === 'EN'">Cameroonian Tax Shield — SYSCOHADA Accounting SaaS</span>
                <span x-show="!($root.closest('[x-data]').__x?.$data?.lang === 'EN')">Bouclier Fiscal Camerounais — SaaS Comptable SYSCOHADA</span>
            </p>
            <div class="flex items-center justify-center gap-3 mt-5 flex-wrap">
                <span class="glass-pill px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-400"
                      style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.28)">Version 1.0.0</span>
                <span class="glass-pill px-3 py-1 text-[10px] font-black uppercase tracking-widest text-amber-400"
                      style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.28)">SYSCOHADA Révisé</span>
                <span class="glass-pill px-3 py-1 text-[10px] font-black uppercase tracking-widest text-indigo-400"
                      style="background:rgba(99,102,241,0.12);border:1px solid rgba(99,102,241,0.28)">DGI Live-Link 2026</span>
                <span class="glass-pill px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-300"
                      style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.14)">Loi Finance 2026</span>
            </div>
        </div>
    </div>

    {{-- What is Opes Books --}}
    <div class="glass-card rounded-3xl p-8 float-in" style="animation-delay:0.05s">
        <h2 class="text-xs font-black uppercase tracking-widest text-amber-400 mb-4">À Propos de la Plateforme</h2>
        <p class="text-slate-300 text-sm leading-relaxed mb-4">
            <strong class="text-white">Opes Books</strong> est une plateforme SaaS de comptabilité et de conformité fiscale conçue spécifiquement pour les PME camerounaises. Elle automatise la tenue des livres selon le plan comptable <strong class="text-amber-400">SYSCOHADA Révisé</strong>, gère la TVA à <strong class="text-white">17,5 % + CAC 10 %</strong> (soit 19,25 % TTC), et assure la <strong class="text-white">télétransmission en temps réel à la DGI</strong> via le portail Fiscalis/SIGIT, conformément à la <strong class="text-white">Loi de Finances 2026</strong>.
        </p>
        <p class="text-slate-400 text-sm leading-relaxed">
            Opes Books fonctionne en mode <strong class="text-white">hors ligne d'abord</strong> : toutes les écritures sont enregistrées localement et synchronisées automatiquement dès que la connexion est rétablie. L'interface Liquid Glass offre une expérience premium sur tous les appareils.
        </p>
    </div>

    {{-- Feature grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 float-in" style="animation-delay:0.08s">
        @foreach ([
            ['icon' => '📊', 'title' => 'Comptabilité SYSCOHADA', 'desc' => 'Plan comptable OHADA révisé complet — classes 1 à 7, codes à 6 chiffres, journaux d\'écritures, grand livre, balance.'],
            ['icon' => '🧾', 'title' => 'Facturation & TVA', 'desc' => 'Génération de factures PDF avec TVA 17,5 % + CAC 10 %, QR code de validation DGI et numérotation automatique.'],
            ['icon' => '📡', 'title' => 'DGI Live-Link', 'desc' => 'Télétransmission automatique à Fiscalis/SIGIT. Token de validation horodaté, retry automatique en cas d\'échec.'],
            ['icon' => '📱', 'title' => 'Intégration Mobile Money', 'desc' => 'Ingestion des webhooks MTN MoMo et Orange Money. Chaque encaissement génère automatiquement une écriture comptable.'],
            ['icon' => '🔒', 'title' => 'Multi-Utilisateurs & Rôles', 'desc' => 'OWNER, ACCOUNTANT, CLERK — chaque rôle dispose d\'accès strictement calibrés.'],
            ['icon' => '🌐', 'title' => 'Mode Hors Ligne', 'desc' => 'Toutes les saisies fonctionnent sans internet. Synchronisation automatique dès le retour de la connexion.'],
            ['icon' => '📈', 'title' => 'Tableau de Bord Fiscal', 'desc' => 'TVA nette, IS, provisions automatiques, simulation de charges fiscales par période.'],
            ['icon' => '🏦', 'title' => 'Import Relevé Bancaire', 'desc' => 'Import CSV de relevés bancaires avec rapprochement automatique sur les écritures existantes.'],
        ] as $f)
        <div class="glass-card rounded-2xl p-5 hover:border-white/20 transition-all">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-xl"
                     style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.10)">{{ $f['icon'] }}</div>
                <div>
                    <div class="text-xs font-black uppercase tracking-wider text-white mb-1">{{ $f['title'] }}</div>
                    <p class="text-slate-400 text-[11px] leading-relaxed">{{ $f['desc'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Technical Stack --}}
    <div class="glass-card rounded-3xl p-8 float-in" style="animation-delay:0.10s">
        <h2 class="text-xs font-black uppercase tracking-widest text-amber-400 mb-5">Stack Technique</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @foreach ([
                ['Laravel 13', 'PHP 8.3+', 'text-rose-400', 'rgba(244,63,94,0.12)', 'rgba(244,63,94,0.28)'],
                ['MySQL / SQLite', 'Base de données', 'text-blue-400', 'rgba(59,130,246,0.12)', 'rgba(59,130,246,0.28)'],
                ['Livewire v4', 'Composants temps réel', 'text-pink-400', 'rgba(236,72,153,0.12)', 'rgba(236,72,153,0.28)'],
                ['Alpine.js v3', 'Réactivité client', 'text-sky-400', 'rgba(14,165,233,0.12)', 'rgba(14,165,233,0.28)'],
                ['Tailwind CSS', 'Design System', 'text-cyan-400', 'rgba(6,182,212,0.12)', 'rgba(6,182,212,0.28)'],
                ['Laravel Sanctum', 'Auth Bearer Token', 'text-amber-400', 'rgba(245,158,11,0.12)', 'rgba(245,158,11,0.28)'],
                ['SYSCOHADA Révisé', 'Plan Comptable OHADA', 'text-emerald-400', 'rgba(16,185,129,0.12)', 'rgba(16,185,129,0.28)'],
                ['DGI Fiscalis', 'API Cameroun 2026', 'text-indigo-400', 'rgba(99,102,241,0.12)', 'rgba(99,102,241,0.28)'],
            ] as [$name, $role, $color, $bg, $border])
            <div class="rounded-xl p-3 text-center" style="background:{{ $bg }};border:1px solid {{ $border }}">
                <div class="text-[10px] font-black uppercase tracking-wider {{ $color }}">{{ $name }}</div>
                <div class="text-slate-500 text-[9px] mt-0.5 font-medium">{{ $role }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Developed by Opesware --}}
    <div class="glass-card rounded-3xl p-8 relative overflow-hidden float-in" style="animation-delay:0.12s">
        <div class="absolute inset-0 pointer-events-none"
             style="background:radial-gradient(ellipse 60% 80% at 100% 50%,rgba(99,102,241,0.08) 0%,transparent 70%)"></div>
        <div class="relative z-10">
            <h2 class="text-xs font-black uppercase tracking-widest text-amber-400 mb-6">Développé par</h2>
            <div class="flex items-center gap-5 mb-6">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center flex-shrink-0"
                     style="background:linear-gradient(145deg,rgba(99,102,241,0.25),rgba(99,102,241,0.08));border:1px solid rgba(99,102,241,0.35);box-shadow:0 0 40px rgba(99,102,241,0.12)">
                    <span class="text-indigo-400 font-black text-lg tracking-widest">OW</span>
                </div>
                <div>
                    <div class="text-white font-black text-xl tracking-tight">OPESWARE</div>
                    <div class="text-slate-400 text-xs font-medium mt-0.5">Software Engineering · Douala, Cameroun</div>
                    <div class="text-indigo-400 text-[11px] font-bold mt-1">opesware.cm</div>
                </div>
            </div>
            <p class="text-slate-400 text-sm leading-relaxed mb-5">
                Opesware est une société camerounaise d'ingénierie logicielle spécialisée dans les solutions de gestion financière, de conformité fiscale et de transformation numérique pour les entreprises africaines. Fondée à Douala, Opesware développe des produits SaaS adaptés aux réalités réglementaires et opérationnelles du marché camerounais et de la zone OHADA.
            </p>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                <div class="rounded-xl p-3" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08)">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Contact</div>
                    <div class="text-slate-300 text-xs font-medium">contact@opesware.cm</div>
                </div>
                <div class="rounded-xl p-3" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08)">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Siège</div>
                    <div class="text-slate-300 text-xs font-medium">Douala, Cameroun</div>
                </div>
                <div class="rounded-xl p-3" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08)">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">Année</div>
                    <div class="text-slate-300 text-xs font-medium">2025 – {{ date('Y') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Legal & Compliance --}}
    <div class="glass-card rounded-3xl p-8 float-in" style="animation-delay:0.14s">
        <h2 class="text-xs font-black uppercase tracking-widest text-amber-400 mb-4">Conformité Légale & Réglementaire</h2>
        <div class="space-y-2.5">
            @foreach ([
                ['SYSCOHADA Révisé 2017', 'Plan comptable de référence de l\'OHADA, en vigueur au Cameroun.'],
                ['Loi de Finances 2026 (Cameroun)', 'Obligation de facturation électronique et télétransmission DGI en temps réel.'],
                ['TVA 17,5 % + CAC 10 %', 'Taux en vigueur selon le Code Général des Impôts camerounais (art. 125 et suivants).'],
                ['DGI Fiscalis / SIGIT', 'Portail officiel de la Direction Générale des Impôts du Cameroun pour la télédéclaration.'],
                ['OHADA (Organisation pour l\'Harmonisation en Afrique du Droit des Affaires)', 'Cadre juridique supranational applicable en zone franc.'],
            ] as [$title, $desc])
            <div class="flex items-start gap-3 py-2.5 border-b last:border-b-0" style="border-color:rgba(255,255,255,0.06)">
                <div class="w-1.5 h-1.5 rounded-full bg-amber-400 mt-1.5 flex-shrink-0"></div>
                <div>
                    <div class="text-white text-xs font-bold">{{ $title }}</div>
                    <div class="text-slate-500 text-[11px] mt-0.5">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center py-4 float-in" style="animation-delay:0.16s">
        <p class="text-slate-600 text-[10px] font-medium uppercase tracking-widest">
            © {{ date('Y') }} Opesware · Tous droits réservés · Opes Books v1.0.0
        </p>
        <p class="text-slate-700 text-[9px] mt-1">
            Plateforme conçue et développée au Cameroun · Made in 🇨🇲
        </p>
    </div>

</div>
</x-app-layout>
