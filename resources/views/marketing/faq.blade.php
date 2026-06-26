@extends('layouts.marketing')
@section('title', 'FAQ — OPESBooks | Comptabilité, fiscalité DGI, TVA, CNPS, CEMAC')
@section('description', "Questions fréquentes sur la comptabilité et la fiscalité au Cameroun et en zone CEMAC : TVA 19,25%, DSF, DGI/Fiscalis, MECeF, patente, CNPS, IRPP/DIPE, régimes d'imposition, et OPESBooks.")

@php
$groups = [
    ['Fiscalité camerounaise', [
        ['Quel est le taux de TVA au Cameroun ?', "La TVA est de <strong>17,5 %</strong>. S'y ajoute le Centime Additionnel Communal (CAC) égal à <strong>10 % de la TVA</strong>, soit 1,75 % du HT. Le taux effectif est donc de <strong>19,25 % TTC</strong>. OPESBooks applique automatiquement ce calcul sur vos factures et écritures."],
        ['Qu\'est-ce que le CAC (Centime Additionnel Communal) ?', "C'est une taxe locale égale à 10 % du montant de la TVA, reversée aux communes. Sur 1 000 000 XAF HT : TVA = 175 000, CAC = 17 500, soit 1 192 500 XAF TTC."],
        ['Qu\'est-ce que la DSF et quand la déposer ?', "La <strong>Déclaration Statistique et Fiscale (DSF)</strong> est la déclaration annuelle déposée auprès de la DGI, qui synthétise les comptes de l'exercice. Pour les entreprises clôturant au 31 décembre, elle est généralement attendue au plus tard le <strong>15 mars</strong> de l'année suivante. OPESBooks prépare l'export DSF / D10 en continu."],
        ['Quels sont les régimes d\'imposition au Cameroun ?', "Selon le chiffre d'affaires : <strong>régime de l'impôt libératoire</strong> (très petites activités), <strong>régime simplifié</strong>, et <strong>régime réel</strong>. OPESBooks s'adapte à votre régime et à votre centre fiscal."],
        ['Qu\'est-ce que la patente ?', "La contribution des patentes est une taxe professionnelle annuelle dont le montant dépend du chiffre d'affaires et de l'activité. OPESBooks intègre son calcul et son suivi."],
        ['Que signifient NIU et RCCM ?', "Le <strong>NIU</strong> (Numéro d'Identifiant Unique) identifie votre entreprise auprès de la DGI. Le <strong>RCCM</strong> (Registre du Commerce et du Crédit Mobilier) est votre immatriculation commerciale. Les deux figurent sur vos factures conformes."],
    ]],
    ['DGI, Fiscalis & facturation électronique', [
        ['Qu\'est-ce que Fiscalis / SIGIT ?', "Ce sont les plateformes numériques de la Direction Générale des Impôts (DGI) pour la déclaration et le paiement en ligne. Le moniteur DGI d'OPESBooks suit vos échéances et l'état de vos télétransmissions."],
        ['Qu\'est-ce que la facturation électronique MECeF ?', "Dans le cadre de la Loi de Finances 2026, la facturation électronique certifiée (MECeF) devient un standard. OPESBooks est conçu pour générer des factures certifiables et se synchroniser avec le portail DGI."],
        ['OPESBooks transmet-il directement mes déclarations à la DGI ?', "OPESBooks génère les exports conformes (DSF, D10) et, une fois vos identifiants DGI configurés, prend en charge la synchronisation. Vous gardez toujours la main sur la validation finale."],
    ]],
    ['TVA, calculs & écritures', [
        ['Comment OPESBooks calcule-t-il le TTC ?', "Utilisez le calculateur intégré : saisissez un montant HT ou TTC, OPESBooks affiche instantanément la TVA (17,5 %), le CAC (10 % de la TVA) et le total. Les arrondis suivent la précision exigée (Brick\\Math)."],
        ['Mes factures sont-elles conformes SYSCOHADA et DGI ?', "Oui : plan comptable OHADA révisé, mentions légales (NIU, RCCM, centre fiscal), numérotation séquentielle et calcul TVA/CAC conforme."],
        ['Puis-je importer mon historique comptable ?', "Oui, via l'assistant d'import CSV : clients, fournisseurs et écritures de journal, avec aperçu de validation et contrôle d'équilibre en partie double avant l'import."],
    ]],
    ['Paie & cotisations sociales', [
        ['OPESBooks gère-t-il la paie camerounaise ?', "Oui : bulletins de paie, calcul du net, cotisations <strong>CNPS</strong>, <strong>IRPP</strong> et déclaration <strong>DIPE</strong>, ainsi que les bordereaux."],
        ['Qu\'est-ce que la CNPS ?', "La Caisse Nationale de Prévoyance Sociale collecte les cotisations sociales (vieillesse, prestations familiales, risques professionnels). OPESBooks calcule les parts patronale et salariale et produit les bordereaux."],
        ['Qu\'est-ce que le DIPE ?', "La Déclaration des Informations sur le Personnel Employé regroupe les impôts et cotisations sur salaires à déclarer périodiquement."],
    ]],
    ['Zone CEMAC & OHADA', [
        ['OPESBooks fonctionne-t-il dans toute la zone CEMAC ?', "Oui. La zone CEMAC regroupe le <strong>Cameroun, le Gabon, le Congo, le Tchad, la République Centrafricaine et la Guinée Équatoriale</strong>. OPESBooks propose des configurations fiscales par pays sur la base commune SYSCOHADA/OHADA."],
        ['Quelle devise est utilisée ?', "Le Franc CFA BEAC (<strong>XAF</strong>), monnaie commune de la CEMAC. Tous les montants et tarifs sont en XAF."],
        ['Le plan comptable est-il le même partout ?', "La base SYSCOHADA révisée est commune à l'espace OHADA. Les spécificités fiscales (taux, déclarations) sont paramétrées par pays."],
    ]],
    ['Plateforme, hors ligne & desktop', [
        ['OPESBooks fonctionne-t-il sans Internet ?', "Oui. C'est une plateforme <strong>hors ligne d'abord</strong> : vous saisissez sans connexion, les données sont stockées localement et synchronisées automatiquement dès le retour d'Internet — une réalité du terrain camerounais."],
        ['Existe-t-il une application à installer ?', "Oui : une application de bureau (Windows, macOS, Linux) et une application web installable (PWA) sur mobile. Vous vous connectez avec vos identifiants cloud et travaillez avec ou sans réseau."],
        ['Puis-je gérer plusieurs entreprises ?', "Oui, la gestion multi-société permet de basculer entre vos entreprises depuis un seul compte."],
        ['Y a-t-il une API pour les développeurs ?', "Oui : une API REST documentée (OpenAPI), des clés d'API et des webhooks signés (HMAC) pour vos intégrations."],
    ]],
    ['Tarifs & paiement', [
        ['Combien coûte OPESBooks ?', "À partir de <strong>5 000 XAF/mois</strong>, avec un plan gratuit pour démarrer et des plans supérieurs selon vos besoins. Voir la page <a href='/tarifs' class='text-gold hover:underline'>Tarifs</a>."],
        ['Comment payer mon abonnement ?', "Par <strong>Orange Money</strong>, <strong>MTN Mobile Money</strong> ou virement bancaire, en XAF."],
        ['Y a-t-il un essai gratuit ?', "Oui, <strong>30 jours gratuits</strong>, sans carte bancaire et sans engagement."],
    ]],
    ['Sécurité & données', [
        ['Mes données sont-elles en sécurité ?', "Oui : chiffrement des données sensibles, double authentification (2FA), rôles et permissions (propriétaire, comptable, employé) et journal d'audit complet."],
        ['Qui peut accéder à mes données comptables ?', "Uniquement les membres que vous invitez, selon le rôle que vous leur attribuez. Le propriétaire peut imposer la 2FA à toute l'équipe."],
    ]],
    ['Démarrage', [
        ['Combien de temps pour démarrer ?', "Quelques minutes : créez votre compte, renseignez votre profil fiscal (NIU, RCCM, centre fiscal), et un assistant vous guide pour les premiers réglages."],
        ['Faut-il être comptable pour utiliser OPESBooks ?', "Non. L'interface est pensée pour les dirigeants de PME, avec l'IA et les automatisations qui réduisent les erreurs. Votre comptable peut aussi être invité avec un rôle dédié."],
    ]],
];
@endphp

@section('content')
<section class="max-w-3xl mx-auto px-5 pt-16 pb-8 text-center">
    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest text-gold mb-5" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.30)">Aide & réponses</span>
    <h1 class="text-3xl md:text-5xl font-black leading-tight">Questions fréquentes</h1>
    <p class="text-slate-400 mt-4">Comptabilité et fiscalité au Cameroun et en zone CEMAC — et tout sur OPESBooks.</p>
</section>

<section class="max-w-3xl mx-auto px-5 pb-16 space-y-10">
    @foreach($groups as $g)
    <div>
        <h2 class="text-lg md:text-xl font-black text-gold mb-4">{{ $g[0] }}</h2>
        <div class="space-y-2">
            @foreach($g[1] as $qa)
            <div x-data="{open:false}" class="glass rounded-xl">
                <button @click="open=!open" class="w-full text-left px-5 py-4 flex justify-between items-center gap-4">
                    <span class="text-sm font-bold text-white">{{ $qa[0] }}</span>
                    <span x-text="open?'−':'+'" class="text-gold text-lg shrink-0"></span>
                </button>
                <div x-show="open" x-cloak class="px-5 pb-4 text-sm text-slate-400 leading-relaxed">{!! $qa[1] !!}</div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach

    <div class="glass rounded-2xl p-8 text-center" style="background:linear-gradient(145deg,rgba(245,158,11,0.08),rgba(41,53,72,0.5))">
        <h2 class="text-xl md:text-2xl font-black">Une autre question ?</h2>
        <p class="text-slate-400 mt-2 text-sm">Notre équipe à Douala vous répond.</p>
        <div class="flex flex-wrap justify-center gap-3 mt-5">
            <a href="{{ route('m.contact') }}" class="btn-primary">Nous contacter</a>
            <a href="/login" class="btn-secondary">Essayer gratuitement</a>
        </div>
    </div>
</section>

@php
    $faqLd = ['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>[]];
    foreach ($groups as $g) { foreach ($g[1] as $qa) {
        $faqLd['mainEntity'][] = ['@type'=>'Question','name'=>$qa[0],'acceptedAnswer'=>['@type'=>'Answer','text'=>strip_tags($qa[1])]];
    }}
@endphp
<script type="application/ld+json">{!! json_encode($faqLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endsection
