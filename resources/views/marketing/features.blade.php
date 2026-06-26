@extends('layouts.marketing')
@section('title', 'Fonctionnalités — OPESBooks | Comptabilité SYSCOHADA, DGI, Paie, MECeF, CEMAC')
@section('description', "Découvrez toutes les fonctionnalités d'OPESBooks : comptabilité SYSCOHADA révisé, facturation TVA 19,25%, DSF/DGI, MECeF, paie CNPS/DIPE, trésorerie Mobile Money, immobilisations, CRM, projets, IA, API REST, hors ligne et multi-pays CEMAC.")

@section('content')
<!-- Hero -->
<section class="relative overflow-hidden pt-20 pb-14 text-center px-5">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 900px 400px at 50% -80px,rgba(245,158,11,0.12),transparent)"></div>
    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest text-gold mb-6" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.30)">Une plateforme, tout votre back-office</span>
    <h1 class="text-4xl md:text-6xl font-black leading-tight max-w-4xl mx-auto">Tout ce qu'il faut pour <span class="text-gold">gérer une PME</span><br>au Cameroun et en CEMAC</h1>
    <p class="text-slate-400 mt-6 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">De la première écriture à la télédéclaration DGI — comptabilité SYSCOHADA, facturation électronique MECeF, paie, trésorerie Mobile Money, CRM et IA, conçu nativement pour l'environnement fiscal camerounais.</p>
    <div class="flex flex-wrap justify-center gap-3 mt-8">
        <a href="/login" class="px-6 py-3.5 rounded-xl text-sm font-black text-navy bg-gold hover:bg-gold-light transition">Essai gratuit 30 jours →</a>
        <a href="{{ route('m.pricing') }}" class="px-6 py-3.5 rounded-xl text-sm font-bold text-white glass hover:bg-slate-700 transition">Voir les tarifs</a>
    </div>
    <!-- Stat bar -->
    <div class="flex flex-wrap justify-center gap-8 mt-14 text-center">
        @foreach([['45+','Modules métier'],['176+','Endpoints API'],['SYSCOHADA','Plan OHADA révisé'],['TVA 19,25%','Calcul exact CFA']] as $s)
        <div>
            <div class="text-2xl md:text-3xl font-black text-gold">{{ $s[0] }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</section>

<!-- Feature modules grid -->
@php
$modules = [
    [
        'slug'  => 'comptabilite',
        'icon'  => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z',
        'title' => 'Comptabilité SYSCOHADA',
        'tag'   => 'Cœur du système',
        'desc'  => 'Journal, grand livre, balance de vérification, clôture d\'exercice et plan comptable OHADA révisé pré-chargé en 9 classes.',
        'highlights' => ['Journal multi-lignes équilibré','Grand livre & balance temps réel','Plan SYSCOHADA 9 classes','Clôture & à-nouveaux automatiques','Lettrage & pointage','Pièces jointes par écriture'],
        'color' => 'amber',
    ],
    [
        'slug'  => 'facturation',
        'icon'  => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M9 13h6 M9 17h3',
        'title' => 'Ventes & Facturation',
        'tag'   => 'Facturation conforme',
        'desc'  => 'Factures clients PDF professionnels, calcul exact TVA 17,5% + CAC 10%, devis, avoirs et facturation récurrente.',
        'highlights' => ['TVA 19,25% (HT→TTC)','PDF DomPDF personnalisable','Devis → Facture en 1 clic','Avoirs & notes de crédit','Factures récurrentes','Balance âgée des créances'],
        'color' => 'blue',
    ],
    [
        'slug'  => 'fiscalite',
        'icon'  => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z M9 12l2 2 4-4',
        'title' => 'Fiscalité & DGI',
        'tag'   => 'Conformité 2026',
        'desc'  => 'Export DSF, télétransmission DGI Fiscalis/SIGIT, certification MECeF (Loi de Finances 2026), patente et calculateur fiscal.',
        'highlights' => ['Export DSF/D10 prêt à transmettre','Moniteur DGI temps réel','MECeF — Loi Finances 2026','QR Code DGI sur chaque facture','Patente camerounaise','Calculateur TVA/CAC'],
        'color' => 'emerald',
    ],
    [
        'slug'  => 'paie',
        'icon'  => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 7a4 4 0 1 0 0 .01 M22 21v-2a4 4 0 0 0-3-3.87',
        'title' => 'Paie & Social',
        'tag'   => 'CNPS / DIPE / IRPP',
        'desc'  => 'Bulletins de paie automatiques avec cotisations CNPS employeur/employé, IRPP et génération des bordereaux DIPE.',
        'highlights' => ['Bulletins PDF automatiques','Cotisations CNPS exactes','IRPP & DIPE','Bordereaux CNPS','SMIG XAF respecté','Intégration registre du personnel'],
        'color' => 'violet',
    ],
    [
        'slug'  => 'tresorerie',
        'icon'  => 'M3 10h18 M5 6h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z M7 15h2',
        'title' => 'Trésorerie & Banque',
        'tag'   => 'Mobile Money natif',
        'desc'  => 'Import de relevés bancaires CSV, rapprochement bancaire assisté, prévisionnel de trésorerie 90 jours et sous-comptes MTN/Orange.',
        'highlights' => ['Import CSV relevé bancaire','Rapprochement banque/comptabilité','Prévisionnel 90 jours','MTN MoMo & Orange Money','Comptes auxiliaires 571x','Alertes de solde'],
        'color' => 'cyan',
    ],
    [
        'slug'  => 'achats',
        'icon'  => 'M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z M3 6h18 M16 10a4 4 0 0 1-8 0',
        'title' => 'Achats & Fournisseurs',
        'tag'   => 'Cycle achat complet',
        'desc'  => 'Factures fournisseurs, bons de commande, avoirs, fiches fournisseurs avec NIU et suivi des dettes.',
        'highlights' => ['Factures fournisseurs','Bons de commande','Avoirs fournisseurs','Fiches fournisseurs NIU','Balance âgée des dettes','Rapprochement achat/livraison'],
        'color' => 'orange',
    ],
    [
        'slug'  => 'immobilisations',
        'icon'  => 'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z M3.27 6.96 12 12l8.73-5.04 M12 22V12',
        'title' => 'Immobilisations & Stock',
        'tag'   => 'Actifs & Inventaire',
        'desc'  => 'Registre des immobilisations, dotations aux amortissements automatiques, gestion de stock avec valorisation et inventaire.',
        'highlights' => ['Registre des immobilisations','Amortissements automatiques','Plan d\'amortissement','Gestion des articles','Mouvements de stock','Valorisation FIFO'],
        'color' => 'slate',
    ],
    [
        'slug'  => 'reporting',
        'icon'  => 'M3 3v18h18 M7 16l4-6 3 3 5-7',
        'title' => 'Pilotage & Reporting',
        'tag'   => 'Tableaux de bord',
        'desc'  => 'Compte de résultat, bilan, tableau de trésorerie, budgets et suivi des écarts — tout en temps réel depuis votre dashboard.',
        'highlights' => ['Compte de résultat','Bilan comptable','Tableau de flux de trésorerie','Budgets & écarts','Balance âgée AR/AP','Journal d\'audit complet'],
        'color' => 'teal',
    ],
    [
        'slug'  => 'crm-projets',
        'icon'  => 'M22 7 13.5 15.5 8.5 10.5 2 17 M16 7h6v6',
        'title' => 'CRM & Projets',
        'tag'   => 'Commercial & Chantiers',
        'desc'  => 'Pipeline CRM, activités et relances commerciales, comptabilité analytique par projet ou chantier avec suivi de rentabilité.',
        'highlights' => ['Pipeline prospects/opportunités','Activités & relances','Rentabilité par projet','Saisie de temps & coûts','Tableaux de bord projet','Lien CRM → Facturation'],
        'color' => 'pink',
    ],
    [
        'slug'  => 'api-integration',
        'icon'  => 'M9 12l2 2 4-4 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z',
        'title' => 'API & Intégrations',
        'tag'   => 'API-first',
        'desc'  => 'API REST documentée OpenAPI 3.1, clés API par scope, webhooks signés HMAC-SHA256, PWA hors ligne et application desktop.',
        'highlights' => ['176+ endpoints REST','OpenAPI 3.1 / Swagger UI','Clés API par scope','Webhooks HMAC-SHA256','PWA hors ligne (sync auto)','Application desktop Win/Mac/Linux'],
        'color' => 'indigo',
    ],
    [
        'slug'  => 'ia',
        'icon'  => 'M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9z M5 20h.01 M19 20h.01',
        'title' => 'Intelligence Artificielle',
        'tag'   => 'Copilote comptable',
        'desc'  => 'Catégorisation automatique des écritures, contrôle des DSF avant télétransmission et assistant conversationnel pour vos questions fiscales.',
        'highlights' => ['Catégorisation SYSCOHADA auto','Détection d\'anomalies DSF','Assistant fiscal conversationnel','Suggestions de rapprochement','Alertes de conformité','Analyse de rentabilité'],
        'color' => 'yellow',
    ],
    [
        'slug'  => 'securite',
        'icon'  => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z',
        'title' => 'Sécurité & Conformité',
        'tag'   => 'Données protégées',
        'desc'  => 'Double authentification TOTP, gestion des rôles OWNER/ACCOUNTANT/CLERK, chiffrement au repos et journal d\'audit complet.',
        'highlights' => ['2FA TOTP','Rôles & permissions granulaires','Chiffrement au repos','Journal d\'audit immuable','Sauvegardes automatiques','Multi-société isolée'],
        'color' => 'red',
    ],
];
$colors = [
    'amber'   => ['bg'=>'rgba(245,158,11,0.1)','border'=>'rgba(245,158,11,0.25)','text'=>'#F59E0B'],
    'blue'    => ['bg'=>'rgba(59,130,246,0.1)','border'=>'rgba(59,130,246,0.25)','text'=>'#60A5FA'],
    'emerald' => ['bg'=>'rgba(16,185,129,0.1)','border'=>'rgba(16,185,129,0.25)','text'=>'#34D399'],
    'violet'  => ['bg'=>'rgba(139,92,246,0.1)','border'=>'rgba(139,92,246,0.25)','text'=>'#A78BFA'],
    'cyan'    => ['bg'=>'rgba(6,182,212,0.1)','border'=>'rgba(6,182,212,0.25)','text'=>'#22D3EE'],
    'orange'  => ['bg'=>'rgba(249,115,22,0.1)','border'=>'rgba(249,115,22,0.25)','text'=>'#FB923C'],
    'slate'   => ['bg'=>'rgba(100,116,139,0.1)','border'=>'rgba(100,116,139,0.25)','text'=>'#94A3B8'],
    'teal'    => ['bg'=>'rgba(20,184,166,0.1)','border'=>'rgba(20,184,166,0.25)','text'=>'#2DD4BF'],
    'pink'    => ['bg'=>'rgba(236,72,153,0.1)','border'=>'rgba(236,72,153,0.25)','text'=>'#F472B6'],
    'indigo'  => ['bg'=>'rgba(99,102,241,0.1)','border'=>'rgba(99,102,241,0.25)','text'=>'#818CF8'],
    'yellow'  => ['bg'=>'rgba(234,179,8,0.1)','border'=>'rgba(234,179,8,0.25)','text'=>'#FBBF24'],
    'red'     => ['bg'=>'rgba(239,68,68,0.1)','border'=>'rgba(239,68,68,0.25)','text'=>'#F87171'],
];
@endphp

<section class="max-w-7xl mx-auto px-5 pb-20">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($modules as $m)
        @php $c = $colors[$m['color']]; @endphp
        <a href="{{ route('m.feature', $m['slug']) }}" class="group glass rounded-2xl p-6 flex flex-col gap-4 hover:border-slate-600 transition-all duration-200 hover:scale-[1.01]">
            <div class="flex items-start justify-between">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" style="background:{{ $c['bg'] }};border:1px solid {{ $c['border'] }}">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $c['text'] }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $m['icon'] }}"/></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full" style="background:{{ $c['bg'] }};color:{{ $c['text'] }};border:1px solid {{ $c['border'] }}">{{ $m['tag'] }}</span>
            </div>
            <div>
                <h2 class="text-lg font-black text-white mb-2">{{ $m['title'] }}</h2>
                <p class="text-slate-400 text-sm leading-relaxed">{{ $m['desc'] }}</p>
            </div>
            <ul class="space-y-1.5 flex-1">
                @foreach(array_slice($m['highlights'],0,4) as $h)
                <li class="flex items-center gap-2 text-xs text-slate-400">
                    <span style="color:{{ $c['text'] }}">✓</span> {{ $h }}
                </li>
                @endforeach
                @if(count($m['highlights'])>4)
                <li class="text-xs" style="color:{{ $c['text'] }}">+ {{ count($m['highlights'])-4 }} autres →</li>
                @endif
            </ul>
            <div class="flex items-center gap-1 text-xs font-bold mt-2 group-hover:gap-2 transition-all" style="color:{{ $c['text'] }}">
                En savoir plus <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </div>
        </a>
        @endforeach
    </div>
</section>

<!-- Bottom CTA -->
<section class="max-w-4xl mx-auto px-5 pb-20 text-center">
    <div class="glass rounded-3xl p-10" style="background:linear-gradient(145deg,rgba(245,158,11,0.08),rgba(41,53,72,0.5))">
        <h2 class="text-2xl md:text-3xl font-black">Prêt à simplifier votre comptabilité ?</h2>
        <p class="text-slate-400 mt-3 text-sm">Essai gratuit 30 jours · Sans carte bancaire · Support en français</p>
        <div class="flex flex-wrap justify-center gap-3 mt-6">
            <a href="/login" class="px-7 py-3.5 rounded-xl text-sm font-black text-navy bg-gold hover:bg-gold-light transition">Commencer gratuitement →</a>
            <a href="{{ route('m.contact') }}" class="px-7 py-3.5 rounded-xl text-sm font-bold text-white glass hover:bg-slate-700 transition">Parler à un conseiller</a>
        </div>
    </div>
</section>
@endsection
