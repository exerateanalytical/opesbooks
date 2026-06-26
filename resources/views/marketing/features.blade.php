@extends('layouts.marketing')
@section('title', 'Fonctionnalités — OPESBooks | Comptabilité, DGI, Paie, CEMAC')
@section('description', "Toutes les fonctionnalités d'OPESBooks : comptabilité SYSCOHADA, facturation TVA 19,25%, DSF/DGI, MECeF, paie CNPS/DIPE, trésorerie, stock, immobilisations, CRM, projets, IA, Mobile Money, API, hors ligne et multi-pays CEMAC.")

@section('content')
<section class="max-w-6xl mx-auto px-5 pt-16 pb-8">
    <div class="text-center">
        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest text-gold mb-5" style="background:rgba(201,155,14,0.1);border:1px solid rgba(201,155,14,0.3)">Une plateforme, tout votre back-office</span>
        <h1 class="text-3xl md:text-5xl font-black leading-tight">Tout ce qu'il faut pour gérer<br class="hidden sm:block"> une PME au Cameroun et en zone CEMAC</h1>
        <p class="text-white/60 mt-5 max-w-2xl mx-auto text-base md:text-lg">De la première écriture à la télédéclaration DGI — comptabilité, facturation, fiscalité, paie, trésorerie, stock, CRM et IA, le tout conçu nativement pour l'environnement camerounais et OHADA.</p>
    </div>
</section>

@php
$categories = [
    ['Comptabilité SYSCOHADA', 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z', [
        ['Journal & écritures', 'Saisie au quotidien, écritures multi-lignes équilibrées, opérations diverses.'],
        ['Grand Livre & Balance', 'Grand livre détaillé, balance des comptes, lettrage et soldes en temps réel.'],
        ['Plan comptable OHADA révisé', 'Plan SYSCOHADA pré-chargé, 9 classes de comptes, comptes auxiliaires.'],
        ['Clôture d\'exercice', 'À-nouveaux, écritures de clôture, report automatique des soldes.'],
    ]],
    ['Ventes & Facturation', 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M9 13h6 M9 17h3', [
        ['Devis & bons de commande', 'Devis professionnels, conversion en facture en un clic.'],
        ['Factures clients & PDF', 'Numérotation automatique, en-tête personnalisé, export PDF DomPDF.'],
        ['TVA 19,25% (17,5% + CAC)', 'Calcul exact HT ↔ TTC avec Centime Additionnel Communal.'],
        ['Avoirs & factures récurrentes', 'Notes de crédit, abonnements et factures automatiques.'],
        ['Bons de livraison', 'Suivi des livraisons et rapprochement avec les factures.'],
        ['Clients & encours', 'Fiches clients, limites de crédit, balance âgée des créances.'],
    ]],
    ['Achats & Fournisseurs', 'M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z M3 6h18 M16 10a4 4 0 0 1-8 0', [
        ['Factures fournisseurs', 'Saisie des achats, échéances et suivi des dettes.'],
        ['Bons de commande', 'Commandes fournisseurs et réception.'],
        ['Avoirs fournisseurs', 'Notes de crédit reçues et régularisations.'],
        ['Fiches fournisseurs', 'Coordonnées, NIU, délais de paiement.'],
    ]],
    ['Fiscalité & DGI', 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z M9 12l2 2 4-4', [
        ['Export DSF / D10', 'Déclaration Statistique et Fiscale prête à transmettre à la DGI.'],
        ['Moniteur DGI — Fiscalis / SIGIT', 'Télétransmission en temps réel et suivi des échéances.'],
        ['Facturation électronique MECeF', 'Certification des factures conforme Loi de Finances 2026.'],
        ['Patente camerounaise', 'Calcul et suivi de la patente.'],
        ['Calculateur TVA / CAC', 'Outil de calcul instantané HT, TVA, CAC et TTC.'],
    ]],
    ['Paie & Social', 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 7a4 4 0 1 0 0 .01 M22 21v-2a4 4 0 0 0-3-3.87', [
        ['Bulletins de paie', 'Génération automatique, net à payer, congés.'],
        ['Cotisations CNPS', 'Calcul employeur/employé et bordereaux CNPS.'],
        ['IRPP / DIPE', 'Impôt sur le revenu et déclaration DIPE.'],
    ]],
    ['Trésorerie & Banque', 'M3 10h18 M5 6h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z M7 15h2', [
        ['Import de relevés bancaires', 'Import CSV, mapping de colonnes, dédoublonnage.'],
        ['Rapprochement bancaire', 'Lettrage banque/comptabilité assisté.'],
        ['Prévisionnel de trésorerie', 'Projection des encaissements et décaissements.'],
        ['Sous-comptes Mobile Money', 'Comptes auxiliaires 571x pour MTN MoMo & Orange Money.'],
    ]],
    ['Immobilisations & Stock', 'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z M3.27 6.96 12 12l8.73-5.04 M12 22V12', [
        ['Immobilisations & amortissements', 'Registre des immobilisations, dotations automatiques.'],
        ['Gestion de stock', 'Articles, mouvements, valorisation et inventaire.'],
    ]],
    ['Pilotage & Reporting', 'M3 3v18h18 M7 16l4-6 3 3 5-7', [
        ['Tableau de bord KPI', 'CA, TVA, CAC, charges et marges en un coup d\'œil.'],
        ['Rapports financiers', 'Compte de résultat, bilan, états personnalisés.'],
        ['Budgets', 'Définition de budgets et suivi des écarts.'],
        ['Journal d\'audit', 'Traçabilité complète des actions par utilisateur.'],
    ]],
    ['CRM & Projets', 'M22 7 13.5 15.5 8.5 10.5 2 17 M16 7h6v6', [
        ['Pipeline commercial', 'Prospects, opportunités et relances.'],
        ['Comptabilité par projet', 'Rentabilité, coûts et revenus par projet/chantier.'],
    ]],
    ['Intelligence Artificielle', 'M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9z M5 20h.01 M19 20h.01', [
        ['Catégorisation automatique', 'Affectation des écritures au bon compte SYSCOHADA.'],
        ['Contrôle DSF par IA', 'Détection d\'anomalies avant transmission.'],
        ['Assistant comptable', 'Réponses à vos questions fiscales camerounaises.'],
    ]],
    ['Plateforme & Intégrations', 'M9 12l2 2 4-4 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z', [
        ['Mobile Money', 'Ingestion MTN MoMo & Orange Money, paiement des abonnements.'],
        ['API REST & Webhooks', 'API documentée (OpenAPI), clés, webhooks signés HMAC.'],
        ['Application desktop (Windows/Mac/Linux)', 'Application installable, connexion avec vos identifiants cloud.'],
        ['Hors ligne (PWA)', 'Saisie sans connexion, file d\'attente et synchronisation automatique.'],
        ['Multi-société', 'Gérez plusieurs entreprises depuis un seul compte.'],
        ['Multi-pays CEMAC', 'Configurations fiscales par pays de la zone CEMAC.'],
    ]],
    ['Sécurité & Conformité', 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z', [
        ['Double authentification (2FA)', 'TOTP, codes de secours, application possible à toute l\'équipe.'],
        ['Rôles & permissions', 'Propriétaire, comptable, employé — accès maîtrisé.'],
        ['Chiffrement & sauvegardes', 'Données chiffrées au repos, sauvegardes régulières.'],
    ]],
];
@endphp

<section class="max-w-6xl mx-auto px-5 pb-16 space-y-12">
    @foreach($categories as $cat)
    <div>
        <div class="flex items-center gap-3 mb-5">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center text-gold shrink-0" style="background:rgba(201,155,14,0.1);border:1px solid rgba(201,155,14,0.25)">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $cat[1] }}"/></svg>
            </div>
            <h2 class="text-xl md:text-2xl font-black">{{ $cat[0] }}</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($cat[2] as $m)
            <div class="glass rounded-xl p-5">
                <div class="flex items-start gap-2">
                    <span class="text-gold mt-0.5 shrink-0">✓</span>
                    <div>
                        <div class="font-bold text-white text-sm">{{ $m[0] }}</div>
                        <p class="text-white/50 text-xs mt-1 leading-relaxed">{{ $m[1] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</section>

<!-- CTA -->
<section class="max-w-4xl mx-auto px-5 pb-16 text-center">
    <div class="glass rounded-3xl p-8 md:p-10" style="background:linear-gradient(145deg,rgba(201,155,14,0.08),rgba(255,255,255,0.03))">
        <h2 class="text-2xl md:text-3xl font-black">Une seule plateforme. Tout votre back-office.</h2>
        <p class="text-white/60 mt-3 text-sm">Essayez gratuitement pendant 30 jours, sans carte bancaire.</p>
        <div class="flex flex-wrap justify-center gap-3 mt-6">
            <a href="/login" class="px-6 py-3.5 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition">Commencer gratuitement →</a>
            <a href="{{ route('m.pricing') }}" class="px-6 py-3.5 rounded-xl text-sm font-bold text-white glass hover:bg-white/10 transition">Voir les tarifs</a>
        </div>
    </div>
</section>
@endsection
