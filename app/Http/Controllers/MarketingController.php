<?php

namespace App\Http\Controllers;

use App\Models\PlanConfig;

class MarketingController extends Controller
{
    public function home()
    {
        $plans = PlanConfig::where('is_active', true)->orderBy('sort_order')->get();
        return view('marketing.home', compact('plans'));
    }

    public function features()
    {
        return view('marketing.features');
    }

    public function feature(string $slug)
    {
        $all = $this->allModules();
        if (!isset($all[$slug])) abort(404);

        $module  = $all[$slug];
        $related = collect($all)->filter(fn($m,$k) => $k !== $slug)->take(4)->map(fn($m) => [
            'slug' => array_search($m, $all), 'title' => $m['title'], 'icon' => $m['icon'],
        ])->values()->toArray();

        return view('marketing.feature-detail', compact('module', 'related'));
    }

    private function allModules(): array
    {
        return [
            'comptabilite' => [
                'slug'      => 'comptabilite',
                'color'     => 'amber',
                'icon'      => 'M4 19.5A2.5 2.5 0 0 1 6.5 17H20M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z',
                'title'     => 'Comptabilité SYSCOHADA',
                'tag'       => 'Cœur du système',
                'headline'  => 'Une comptabilité conforme au référentiel SYSCOHADA révisé, avec le plan comptable OHADA pré-chargé, la saisie au journal, le grand livre et la balance des comptes en temps réel.',
                'meta_desc' => 'Comptabilité SYSCOHADA révisé pour PME camerounaises : journal, grand livre, balance, clôture d\'exercice, lettrage et plan comptable OHADA pré-chargé.',
                'features'  => [
                    ['icon'=>'M4 19.5A2.5 2.5 0 0 1 6.5 17H20', 'title'=>'Journal des écritures','desc'=>'Saisie au quotidien avec écritures multi-lignes équilibrées. Opérations diverses, régularisations et contrepassations.'],
                    ['icon'=>'M3 3v18h18','title'=>'Grand Livre','desc'=>'Historique complet par compte, navigation entre les comptes, export PDF et Excel.'],
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Balance de vérification','desc'=>'Soldes débiteurs et créditeurs en temps réel, balance avant et après clôture, comparaison N-1.'],
                    ['icon'=>'M14 2H6a2 2 0 0 0-2 2v16','title'=>'Plan SYSCOHADA pré-chargé','desc'=>'Plan comptable OHADA révisé à 9 classes, numéros de comptes standardisés, comptes auxiliaires clients et fournisseurs.'],
                    ['icon'=>'M8 6h13 M8 12h13 M8 18h13','title'=>'Clôture d\'exercice','desc'=>'Génération automatique des écritures de clôture, report des à-nouveaux, verrouillage de la période.'],
                    ['icon'=>'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z','title'=>'Lettrage & pointage','desc'=>'Lettrage des comptes clients/fournisseurs, pointage des règlements et suivi des encours.'],
                    ['icon'=>'M15 2H6a2 2 0 0 0-2 2v16','title'=>'Pièces jointes','desc'=>'Ajout de factures scan, reçus et justificatifs directement sur chaque écriture.'],
                    ['icon'=>'M12 20h9 M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z','title'=>'Mémo et annotations','desc'=>'Commentaires internes sur les écritures pour la traçabilité et la revue comptable.'],
                ],
                'context' => [
                    'Plan comptable SYSCOHADA révisé 2017 (OHADA) pré-chargé : 9 classes de comptes, numérotation standardisée pour la zone CEMAC.',
                    'Calcul exact des taxes en Francs CFA (XAF) avec arrondis conformes aux pratiques DGI Cameroun.',
                    'Export DSF conforme au format attendu par la Direction Générale des Impôts du Cameroun.',
                    'Journaux séparés : journal des ventes, des achats, de banque, de caisse et des opérations diverses.',
                ],
            ],

            'facturation' => [
                'slug'      => 'facturation',
                'color'     => 'blue',
                'icon'      => 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z M14 2v6h6 M9 13h6 M9 17h3',
                'title'     => 'Ventes & Facturation',
                'tag'       => 'Facturation conforme',
                'headline'  => 'Générez des factures clients PDF professionnels avec calcul exact TVA 17,5% + CAC 10% = 19,25% TTC. Devis, avoirs, récurrents et suivi des encours clients.',
                'meta_desc' => 'Facturation TVA 19,25% pour PME camerounaises : factures PDF, devis, avoirs, récurrents, balance âgée. Numérotation automatique, en-tête personnalisé.',
                'features'  => [
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Calcul TVA 19,25% exact','desc'=>'TVA 17,5% + Centime Additionnel Communal (CAC) 10% = 19,25% TTC. Calcul HT → TTC ou TTC → HT avec Brick\Math pour une précision absolue.'],
                    ['icon'=>'M14 2H6a2 2 0 0 0-2 2v16','title'=>'Factures PDF professionnels','desc'=>'Génération DomPDF avec en-tête personnalisé (logo, NIU, RCCM), numérotation automatique, statut coloré.'],
                    ['icon'=>'M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2','title'=>'Devis → Facture','desc'=>'Créez un devis, envoyez-le au client, convertissez en facture en un clic sans ressaisie.'],
                    ['icon'=>'M9 14l6-6 M15 14H9v-6','title'=>'Avoirs & notes de crédit','desc'=>'Notes de crédit partielles ou totales liées à la facture d\'origine, avec génération automatique de l\'écriture comptable de régularisation.'],
                    ['icon'=>'M12 2v4 M12 18v4 M4.93 4.93l2.83 2.83','title'=>'Factures récurrentes','desc'=>'Abonnements, loyers, contrats de maintenance — générez automatiquement les factures selon la périodicité choisie.'],
                    ['icon'=>'M3 10h18 M3 14h18','title'=>'Balance âgée des créances','desc'=>'Suivi des encours clients par tranche d\'échéance (0–30, 31–60, 61–90, +90 jours).'],
                    ['icon'=>'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z','title'=>'Certification MECeF','desc'=>'QR code DGI et empreinte SHA-256 sur chaque facture, conformément à la Loi de Finances 2026.'],
                    ['icon'=>'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2','title'=>'Gestion des clients','desc'=>'Fiches clients avec NIU, RCCM, coordonnées bancaires, historique des factures et limites de crédit.'],
                ],
                'context' => [
                    'Numérotation conforme DGI : préfixe configurable par exercice fiscal camerounais (ex. FAC-2026-XXXXX).',
                    'Calcul exact du précompte libératoire applicable selon le régime d\'imposition du client.',
                    'Mention légale obligatoire : NIU, RCCM, Centre des Impôts, numéro MECeF sur chaque facture.',
                    'Export DSF des ventes : intégration directe avec la Déclaration Statistique et Fiscale.',
                ],
            ],

            'fiscalite' => [
                'slug'      => 'fiscalite',
                'color'     => 'emerald',
                'icon'      => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z M9 12l2 2 4-4',
                'title'     => 'Fiscalité & DGI',
                'tag'       => 'Conformité 2026',
                'headline'  => 'Télétransmission DGI, certification MECeF conforme à la Loi de Finances 2026, export DSF/D10, suivi des échéances fiscales et calculateur TVA/CAC intégré.',
                'meta_desc' => 'Fiscalité camerounaise : MECeF, DSF, DGI Fiscalis/SIGIT, TVA 17,5%, CAC, patente. Conformité Loi de Finances 2026 pour PME CEMAC.',
                'features'  => [
                    ['icon'=>'M12 22s8-4 8-10V5l-8-3','title'=>'Certification MECeF','desc'=>'Intégration avec le dispositif MECeF (Machine Électronique de Contrôle et de Facturation) imposé par la Loi de Finances 2026.'],
                    ['icon'=>'M9 12l2 2 4-4 M3 12h.01','title'=>'QR Code DGI','desc'=>'Chaque facture certifiée porte un QR code et une empreinte SHA-256 permettant la vérification en ligne par les agents DGI.'],
                    ['icon'=>'M14 2H6a2 2 0 0 0-2 2v16','title'=>'Export DSF / D10','desc'=>'Génération du fichier DSF (Déclaration Statistique et Fiscale) prêt à importer dans Fiscalis/SIGIT.'],
                    ['icon'=>'M3 10h18 M5 6h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5','title'=>'Moniteur DGI','desc'=>'Tableau de bord temps réel de l\'état de synchronisation avec le portail DGI, statuts et erreurs de transmission.'],
                    ['icon'=>'M12 20V10 M18 20V4 M6 20v-6','title'=>'Résumé TVA mensuel','desc'=>'État mensuel TVA collectée, TVA déductible et TVA à payer — prêt pour la déclaration M.'],
                    ['icon'=>'M9 14l6-6 M15 14H9v-6','title'=>'Calculateur HT / TTC','desc'=>'Outil de calcul instantané : HT → TVA → CAC → TTC ou TTC → HT avec affichage des composantes.'],
                    ['icon'=>'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z','title'=>'Patente camerounaise','desc'=>'Calcul de la patente selon le chiffre d\'affaires et la catégorie d\'activité. Suivi des paiements et rappels d\'échéance.'],
                    ['icon'=>'M4 6h16 M4 10h16 M4 14h16','title'=>'Suivi des échéances fiscales','desc'=>'Calendrier fiscal camerounais intégré : TVA mensuelle, IS, patente, IRPP, CNPS — alertes proactives.'],
                ],
                'context' => [
                    'MECeF obligatoire à partir de 2026 pour les entreprises du régime réel au Cameroun — OPESBooks intègre nativement ce dispositif.',
                    'TVA au taux de 17,5% + CAC 10% de la TVA = 19,25% du HT. Calcul exact conforme au Code Général des Impôts camerounais.',
                    'Déclarations DGI : export DSF compatible Fiscalis, avec structure Liasse SYSCOHADA attendue par l\'administration.',
                    'Délai de déclaration TVA : 15 du mois suivant — le calendrier fiscal OPESBooks intègre toutes les échéances légales.',
                ],
            ],

            'paie' => [
                'slug'      => 'paie',
                'color'     => 'violet',
                'icon'      => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M9 7a4 4 0 1 0 0 .01 M22 21v-2a4 4 0 0 0-3-3.87',
                'title'     => 'Paie & Social',
                'tag'       => 'CNPS / DIPE / IRPP',
                'headline'  => 'Bulletins de paie automatiques avec cotisations CNPS exactes, calcul IRPP, bordereaux DIPE et intégration comptable directe dans le journal.',
                'meta_desc' => 'Logiciel de paie camerounais : CNPS, IRPP, DIPE, SMIG, bulletins PDF. Conformité sociale pour PME au Cameroun.',
                'features'  => [
                    ['icon'=>'M14 2H6a2 2 0 0 0-2 2v16','title'=>'Bulletins de paie PDF','desc'=>'Génération automatique des bulletins de paie mensuels avec toutes les rubriques : salaire brut, cotisations, net à payer.'],
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Cotisations CNPS','desc'=>'Calcul automatique des cotisations employeur (17,5%) et employé (4,2%) selon les taux CNPS en vigueur.'],
                    ['icon'=>'M12 20V10 M18 20V4 M6 20v-6','title'=>'IRPP','desc'=>'Calcul de l\'Impôt sur le Revenu des Personnes Physiques (IRPP) selon le barème progressif camerounais et les charges de famille.'],
                    ['icon'=>'M3 10h18 M5 6h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5','title'=>'Déclaration DIPE','desc'=>'Génération du bordereau DIPE (Déclaration des Impôts sur les Salaires) pour télétransmission à la DGI.'],
                    ['icon'=>'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2','title'=>'Registre du personnel','desc'=>'Fiches employés avec contrat, catégorie professionnelle, date d\'embauche, ancienneté et congés.'],
                    ['icon'=>'M12 2l3.09 6.26L22 9.27l-5 4.87','title'=>'SMIG intégré','desc'=>'Vérification automatique que le salaire brut respecte le SMIG en vigueur au Cameroun (36 270 XAF/mois).'],
                    ['icon'=>'M4 6h16 M4 10h16 M4 14h16','title'=>'Intégration comptable','desc'=>'Passation automatique des écritures de paie dans le journal SYSCOHADA (comptes 66x pour charges, 43x pour cotisations).'],
                    ['icon'=>'M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6','title'=>'Bordereaux CNPS','desc'=>'Édition des bordereaux mensuels et trimestriels CNPS prêts à déposer à la caisse régionale.'],
                ],
                'context' => [
                    'SMIG camerounais : 36 270 XAF/mois — OPESBooks bloque toute fiche de paie en dessous de ce seuil légal.',
                    'Cotisations CNPS : part employeur 17,5% (vieillesse 4,2%, AT/MP variable, AF 7%, FNE 1%) + part salarié 4,2%.',
                    'IRPP progressif : tranches de 0% à 35% selon les revenus et les parts fiscales (enfants à charge).',
                    'DIPE obligatoire avant le 15 du mois suivant le versement des salaires — OPESBooks génère le fichier prêt à l\'envoi.',
                ],
            ],

            'tresorerie' => [
                'slug'      => 'tresorerie',
                'color'     => 'cyan',
                'icon'      => 'M3 10h18 M5 6h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z M7 15h2',
                'title'     => 'Trésorerie & Banque',
                'tag'       => 'Mobile Money natif',
                'headline'  => 'Import de relevés bancaires CSV, rapprochement banque/comptabilité assisté, prévisionnel de trésorerie 90 jours et sous-comptes auxiliaires pour MTN MoMo et Orange Money.',
                'meta_desc' => 'Trésorerie et banque pour PME camerounaises : import CSV, rapprochement bancaire, MTN Mobile Money, Orange Money, prévisionnel 90 jours.',
                'features'  => [
                    ['icon'=>'M4 16v1a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1','title'=>'Import relevé bancaire CSV','desc'=>'Import des relevés bancaires au format CSV (toutes banques camerounaises). Mapping des colonnes, détection des doublons.'],
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Rapprochement bancaire','desc'=>'Lettrage assisté entre les écritures comptables et les mouvements bancaires. Statut de rapprochement en temps réel.'],
                    ['icon'=>'M3 3v18h18 M7 16l4-6 3 3 5-7','title'=>'Prévisionnel de trésorerie','desc'=>'Projection sur 90 jours basée sur les créances clients, dettes fournisseurs et récurrents — vue par décade.'],
                    ['icon'=>'M12 2l3.09 6.26L22 9.27l-5 4.87','title'=>'Sous-comptes Mobile Money','desc'=>'Comptes auxiliaires 571x dédiés : MTN Mobile Money (5711), Orange Money (5712), Flooz, Wave. Séparation claire des flux.'],
                    ['icon'=>'M22 12h-4l-3 9L9 3l-3 9H2','title'=>'Alertes de solde','desc'=>'Alertes quand le solde d\'un compte passe sous un seuil configuré — évitez les découverts.'],
                    ['icon'=>'M17 9V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2m2 4h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2zm7-5a2 2 0 1 1-4 0 2 2 0 0 1 4 0z','title'=>'Paiement d\'abonnement Mobile Money','desc'=>'Payez votre abonnement OPESBooks directement par MTN MoMo ou Orange Money — STK push depuis le dashboard.'],
                    ['icon'=>'M3 10h18 M8 6h.01 M16 6h.01','title'=>'Multi-comptes bancaires','desc'=>'Gérez plusieurs comptes bancaires (SGC, Afriland, UBA, BGFI…) et caisses dans la même interface.'],
                    ['icon'=>'M12 20h9 M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z','title'=>'Saisie manuelle des opérations','desc'=>'Enregistrement des encaissements et décaissements sans import — idéal pour les opérations en espèces.'],
                ],
                'context' => [
                    'MTN MoMo et Orange Money sont les principaux canaux de paiement au Cameroun — OPESBooks les traite nativement.',
                    'Sous-comptes SYSCOHADA 571x pour les portefeuilles Mobile Money, conformément aux recommandations OHADA.',
                    'Ingestion automatique des callbacks MTN MoMo via l\'API Collection — réconciliation comptable en temps réel.',
                    'Rapprochement bancaire obligatoire pour la revue DGI — le module génère le procès-verbal de rapprochement.',
                ],
            ],

            'achats' => [
                'slug'      => 'achats',
                'color'     => 'orange',
                'icon'      => 'M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z M3 6h18 M16 10a4 4 0 0 1-8 0',
                'title'     => 'Achats & Fournisseurs',
                'tag'       => 'Cycle achat complet',
                'headline'  => 'Gérez l\'intégralité du cycle achat : bons de commande, réception, factures fournisseurs, avoirs et suivi des dettes avec balance âgée.',
                'meta_desc' => 'Gestion des achats et fournisseurs pour PME camerounaises : factures fournisseurs, bons de commande, avoirs, balance âgée des dettes SYSCOHADA.',
                'features'  => [
                    ['icon'=>'M14 2H6a2 2 0 0 0-2 2v16','title'=>'Factures fournisseurs','desc'=>'Saisie des factures d\'achat avec ventilation comptable automatique, suivi des échéances et TVA déductible.'],
                    ['icon'=>'M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6','title'=>'Bons de commande','desc'=>'Émission de bons de commande aux fournisseurs, réception partielle ou totale, rapprochement BC/facture.'],
                    ['icon'=>'M9 14l6-6 M15 14H9v-6','title'=>'Avoirs fournisseurs','desc'=>'Enregistrement des notes de crédit reçues, régularisation comptable et suivi du solde.'],
                    ['icon'=>'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2','title'=>'Fiches fournisseurs','desc'=>'NIU, RCCM, coordonnées bancaires, délais de paiement convenus et historique complet.'],
                    ['icon'=>'M3 10h18 M3 14h18','title'=>'Balance âgée des dettes','desc'=>'Analyse des dettes fournisseurs par tranche d\'âge — anticipez vos décaissements.'],
                    ['icon'=>'M22 12h-4l-3 9L9 3l-3 9H2','title'=>'Bons de livraison','desc'=>'Gestion des livraisons entrantes, rapprochement avec les bons de commande et les factures.'],
                ],
                'context' => [
                    'Identification fournisseur : NIU (Numéro d\'Identifiant Unique) et RCCM requis pour la déductibilité de la TVA.',
                    'TVA sur achats déductible uniquement si le fournisseur est assujetti — OPESBooks gère ce paramètre par fournisseur.',
                    'Retenue à la source : calcul automatique du précompte sur les achats selon le régime DGI du fournisseur.',
                    'Délai de paiement moyen légal : 60 jours en zone OHADA — le tableau de bord signale les dépassements.',
                ],
            ],

            'immobilisations' => [
                'slug'      => 'immobilisations',
                'color'     => 'slate',
                'icon'      => 'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z M3.27 6.96 12 12l8.73-5.04 M12 22V12',
                'title'     => 'Immobilisations & Stock',
                'tag'       => 'Actifs & Inventaire',
                'headline'  => 'Registre des immobilisations avec plans d\'amortissement automatiques, gestion de stock multi-entrepôt et valorisation FIFO conforme SYSCOHADA.',
                'meta_desc' => 'Immobilisations et stock SYSCOHADA pour PME camerounaises : amortissements linéaires/dégressifs, inventaire, mouvements, valorisation FIFO.',
                'features'  => [
                    ['icon'=>'M21 16V8a2 2 0 0 0-1-1.73l-7-4','title'=>'Registre des immobilisations','desc'=>'Fiche complète par immobilisation : désignation, date d\'acquisition, valeur d\'origine, VNC, durée de vie.'],
                    ['icon'=>'M3 3v18h18 M7 16l4-6 3 3 5-7','title'=>'Amortissements automatiques','desc'=>'Calcul mensuel ou annuel des dotations aux amortissements (linéaire ou dégressif) avec passation comptable automatique.'],
                    ['icon'=>'M14 2H6a2 2 0 0 0-2 2v16','title'=>'Plan d\'amortissement PDF','desc'=>'Export du tableau d\'amortissement complet par immobilisation ou par catégorie.'],
                    ['icon'=>'M4 16v1a3 3 0 0 0 3 3h10','title'=>'Gestion des articles & entrepôts','desc'=>'Catalogue des articles avec référence, unité, stock minimum, prix de vente et d\'achat.'],
                    ['icon'=>'M12 20V10 M18 20V4 M6 20v-6','title'=>'Mouvements de stock','desc'=>'Entrées (achats, production), sorties (ventes, consommation), transferts entre entrepôts.'],
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Valorisation FIFO','desc'=>'Valorisation du stock au coût moyen pondéré ou FIFO — solde de stock en valeur en temps réel.'],
                ],
                'context' => [
                    'SYSCOHADA impose la méthode des amortissements dégressifs pour certaines catégories — OPESBooks supporte les deux méthodes.',
                    'Comptes d\'immobilisations OHADA : 21x (incorp.), 22x (terrain), 24x (matériel), 28x (amortissements) pré-mappés.',
                    'Inventaire physique : fichier de comptage CSV importable, écarts d\'inventaire passés automatiquement en charge.',
                    'Sorties d\'immobilisations : cession, mise au rebut — OPESBooks calcule la plus ou moins-value et passe l\'écriture.',
                ],
            ],

            'reporting' => [
                'slug'      => 'reporting',
                'color'     => 'teal',
                'icon'      => 'M3 3v18h18 M7 16l4-6 3 3 5-7',
                'title'     => 'Pilotage & Reporting',
                'tag'       => 'Tableaux de bord',
                'headline'  => 'Compte de résultat, bilan, flux de trésorerie, budgets et balance âgée — tout en temps réel depuis votre tableau de bord, exportable en PDF.',
                'meta_desc' => 'Reporting financier SYSCOHADA pour PME camerounaises : compte de résultat, bilan, trésorerie, budget, balance âgée, journal d\'audit.',
                'features'  => [
                    ['icon'=>'M3 3v18h18 M7 16l4-6 3 3 5-7','title'=>'Tableau de bord KPI','desc'=>'Chiffre d\'affaires, TVA collectée, charges, marge — les indicateurs clés en un coup d\'œil.'],
                    ['icon'=>'M14 2H6a2 2 0 0 0-2 2v16','title'=>'Compte de résultat','desc'=>'Compte de résultat SYSCOHADA avec comparaison N vs N-1, filtrable par période.'],
                    ['icon'=>'M4 19.5A2.5 2.5 0 0 1 6.5 17H20','title'=>'Bilan comptable','desc'=>'Bilan actif/passif OHADA révisé, généré à partir de la balance des comptes.'],
                    ['icon'=>'M3 10h18 M5 6h14a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5','title'=>'Tableau de flux de trésorerie','desc'=>'Flux opérationnels, d\'investissement et de financement selon la méthode SYSCOHADA.'],
                    ['icon'=>'M12 20V10 M18 20V4 M6 20v-6','title'=>'Budgets & écarts','desc'=>'Définissez vos budgets par compte ou catégorie. Suivez les réalisations vs prévisions en temps réel.'],
                    ['icon'=>'M3 3h18v18H3z M3 9h18 M9 21V9','title'=>'Balance âgée AR/AP','desc'=>'Créances clients et dettes fournisseurs analysées par tranche d\'âge — vue trésorerie à 90 jours.'],
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Journal d\'audit','desc'=>'Traçabilité complète : qui a fait quoi, quand — chaque action est horodatée et signée par utilisateur.'],
                    ['icon'=>'M4 16v1a3 3 0 0 0 3 3h10','title'=>'Exports PDF & Excel','desc'=>'Tous les états financiers sont exportables en PDF et Excel pour transmission à l\'expert-comptable ou à la DGI.'],
                ],
                'context' => [
                    'États financiers conformes au référentiel SYSCOHADA révisé 2017 — liasse fiscale attendue par la DGI Cameroun.',
                    'Compte de résultat par nature (charges d\'exploitation, financières, HAO) selon le plan OHADA.',
                    'Provision pour impôt sur les sociétés (IS) calculée automatiquement selon le taux applicable (33% Cameroun).',
                    'Export DSF intégré avec les états comptables — aucune ressaisie nécessaire pour la déclaration annuelle.',
                ],
            ],

            'crm-projets' => [
                'slug'      => 'crm-projets',
                'color'     => 'pink',
                'icon'      => 'M22 7 13.5 15.5 8.5 10.5 2 17 M16 7h6v6',
                'title'     => 'CRM & Projets',
                'tag'       => 'Commercial & Chantiers',
                'headline'  => 'Pipeline CRM pour le suivi des opportunités commerciales et comptabilité analytique par projet ou chantier avec tableau de bord de rentabilité.',
                'meta_desc' => 'CRM et gestion de projets pour PME camerounaises : pipeline commercial, activités, rentabilité par chantier, comptabilité analytique SYSCOHADA.',
                'features'  => [
                    ['icon'=>'M22 7 13.5 15.5 8.5 10.5 2 17','title'=>'Pipeline commercial','desc'=>'Prospects, qualifiés, propositions, négociation, clôture — visualisez votre pipeline en kanban ou liste.'],
                    ['icon'=>'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2','title'=>'Activités & relances','desc'=>'Appels, emails, rendez-vous — journalisez chaque interaction client et configurez des relances automatiques.'],
                    ['icon'=>'M3 3v18h18 M7 16l4-6 3 3 5-7','title'=>'Rentabilité par projet','desc'=>'Tableau de bord de rentabilité par projet : revenus, coûts directs, marge brute et heures passées.'],
                    ['icon'=>'M12 20V10 M18 20V4 M6 20v-6','title'=>'Saisie de temps & coûts','desc'=>'Imputez les heures collaborateurs et les achats directement à un projet ou chantier.'],
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Comptabilité analytique','desc'=>'Axes analytiques liés aux projets, intégration directe dans le journal SYSCOHADA (comptes auxiliaires).'],
                    ['icon'=>'M14 2H6a2 2 0 0 0-2 2v16','title'=>'Facturation liée au projet','desc'=>'Générez des factures d\'avancement directement depuis la fiche projet — le lien avec la comptabilité est automatique.'],
                ],
                'context' => [
                    'Comptabilité analytique par projet conforme SYSCOHADA : axes de coûts et revenus en complément du plan de comptes.',
                    'Industries visées : BTP, cabinets d\'études, agences de communication, entreprises de services au Cameroun.',
                    'Facturation à l\'avancement ou au forfait — les deux modes sont supportés avec gestion des retenues de garantie.',
                    'Intégration CRM → Devis → Facture → Comptabilité en un flux continu sans ressaisie.',
                ],
            ],

            'api-integration' => [
                'slug'      => 'api-integration',
                'color'     => 'indigo',
                'icon'      => 'M9 12l2 2 4-4 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z',
                'title'     => 'API & Intégrations',
                'tag'       => 'API-first',
                'headline'  => 'API REST complète avec 176+ endpoints, documentation OpenAPI 3.1, clés API par scope, webhooks HMAC-SHA256, PWA hors ligne et application desktop multi-OS.',
                'meta_desc' => 'API REST OPESBooks : OpenAPI 3.1, 176 endpoints, clés API, webhooks HMAC, PWA hors ligne, application desktop. Intégration ERP, e-commerce, Mobile Money.',
                'features'  => [
                    ['icon'=>'M9 12l2 2 4-4 M21 12a9 9 0 1 1-18 0','title'=>'176+ endpoints REST','desc'=>'Factures, journal, TVA, rapports, webhooks, paie, projets — accès programmatique à toutes les données.'],
                    ['icon'=>'M14 2H6a2 2 0 0 0-2 2v16','title'=>'Documentation OpenAPI 3.1','desc'=>'Swagger UI interactif disponible sur /developer — testez les endpoints directement depuis la documentation.'],
                    ['icon'=>'M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778','title'=>'Clés API par scope','desc'=>'Émettez des clés avec des permissions granulaires : invoices:read, invoices:write, journal:read, tax:read, reports:read, webhooks:manage.'],
                    ['icon'=>'M22 12h-4l-3 9L9 3l-3 9H2','title'=>'Webhooks HMAC-SHA256','desc'=>'Recevez des notifications en temps réel sur vos endpoints. Chaque livraison est signée avec votre secret (HMAC-SHA256).'],
                    ['icon'=>'M3 10h18 M3 14h18','title'=>'PWA hors ligne','desc'=>'Saisie sans connexion internet — les données sont stockées localement et synchronisées automatiquement au retour du réseau.'],
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Application desktop','desc'=>'Application installable (Windows, macOS, Linux) — même interface que le web, avec accès hors ligne natif.'],
                    ['icon'=>'M17 9V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v6','title'=>'MTN MoMo & Orange Money','desc'=>'Ingestion des paiements Mobile Money via API officielle. Rapprochement automatique avec les factures.'],
                    ['icon'=>'M4 6h16 M4 10h16 M4 14h16','title'=>'Multi-société','desc'=>'Un seul compte pour plusieurs entreprises — changez de contexte en un clic, données strictement isolées.'],
                ],
                'context' => [
                    '176 endpoints couvrent l\'intégralité du cycle comptable SYSCOHADA — prêt pour l\'intégration ERP ou e-commerce.',
                    'Webhook events : invoice.created, invoice.paid, invoice.certified_mecef, payment.received, journal.entry.posted.',
                    'Rétry automatique des webhooks échoués : 5 tentatives avec backoff exponentiel (60s → 8h).',
                    'PWA hors ligne indispensable au Cameroun où la connectivité peut être intermittente — les données ne sont jamais perdues.',
                ],
            ],

            'ia' => [
                'slug'      => 'ia',
                'color'     => 'yellow',
                'icon'      => 'M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9z M5 20h.01 M19 20h.01',
                'title'     => 'Intelligence Artificielle',
                'tag'       => 'Copilote comptable',
                'headline'  => 'Un assistant IA spécialisé dans la fiscalité et la comptabilité camerounaises : catégorisation automatique, détection d\'anomalies et réponses aux questions fiscales en temps réel.',
                'meta_desc' => 'IA comptable pour PME camerounaises : catégorisation SYSCOHADA automatique, contrôle DSF, assistant fiscal conversationnel, détection d\'anomalies.',
                'features'  => [
                    ['icon'=>'M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6','title'=>'Catégorisation automatique','desc'=>'L\'IA suggère le compte SYSCOHADA correct pour chaque ligne d\'écriture — apprend de vos corrections.'],
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Contrôle DSF avant transmission','desc'=>'Analyse votre DSF avant envoi à la DGI — détecte les incohérences, montants anormaux et cases manquantes.'],
                    ['icon'=>'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z','title'=>'Assistant fiscal conversationnel','desc'=>'Posez vos questions en français : "Quel est le taux de TVA sur les médicaments ?" — réponses basées sur le CGI camerounais.'],
                    ['icon'=>'M22 12h-4l-3 9L9 3l-3 9H2','title'=>'Suggestions de rapprochement','desc'=>'L\'IA identifie les paiements Mobile Money qui correspondent à des factures en attente — rapprochement en 1 clic.'],
                    ['icon'=>'M3 3v18h18 M7 16l4-6 3 3 5-7','title'=>'Analyse de rentabilité','desc'=>'Posez une question en langage naturel sur votre P&L : "Quel est mon meilleur mois ?" ou "Quels clients sont les plus rentables ?"'],
                    ['icon'=>'M12 22s8-4 8-10V5l-8-3','title'=>'Alertes de conformité','desc'=>'Alertes proactives : déclaration TVA à venir, seuil de patente atteint, CNPS en retard.'],
                ],
                'context' => [
                    'Entraîné sur le Code Général des Impôts camerounais, SYSCOHADA révisé et les circulaires DGI récentes.',
                    'Réponses en français (et anglais) — adapté au contexte bilingue du Cameroun.',
                    'Mode hors ligne dégradé : les suggestions de catégorisation fonctionnent sans connexion (modèle local embarqué).',
                    'Aucune donnée comptable transmise à un tiers : l\'assistant IA traite vos questions sans exposer vos écritures.',
                ],
            ],

            'securite' => [
                'slug'      => 'securite',
                'color'     => 'red',
                'icon'      => 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z',
                'title'     => 'Sécurité & Conformité',
                'tag'       => 'Données protégées',
                'headline'  => 'Double authentification TOTP, gestion des rôles OWNER/ACCOUNTANT/CLERK, chiffrement au repos, journal d\'audit immuable et isolation multi-société.',
                'meta_desc' => 'Sécurité SaaS pour PME camerounaises : 2FA TOTP, rôles RBAC, chiffrement, audit log, isolation multi-tenant, sauvegardes. Conformité OHADA.',
                'features'  => [
                    ['icon'=>'M12 22s8-4 8-10V5l-8-3','title'=>'Double authentification 2FA','desc'=>'TOTP compatible Google Authenticator, Authy — codes de secours téléchargeables. Applicable à tous les membres de l\'équipe.'],
                    ['icon'=>'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2','title'=>'Rôles & permissions RBAC','desc'=>'OWNER : accès total. ACCOUNTANT : comptabilité + facturation. CLERK : saisie facturation uniquement. Accès maîtrisé sans sur-permission.'],
                    ['icon'=>'M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778','title'=>'Chiffrement des données','desc'=>'Données sensibles (clés API, secrets webhook, tokens) chiffrées au repos. Connexions HTTPS forcées.'],
                    ['icon'=>'M9 12l2 2 4-4','title'=>'Journal d\'audit immuable','desc'=>'Chaque action (écriture, modification, suppression, connexion) est tracée avec l\'utilisateur, l\'horodatage et l\'IP source.'],
                    ['icon'=>'M4 16v1a3 3 0 0 0 3 3h10','title'=>'Sauvegardes automatiques','desc'=>'Snapshots quotidiens de votre base de données — rétention configurable. Restauration en libre-service.'],
                    ['icon'=>'M4 6h16 M4 10h16 M4 14h16','title'=>'Isolation multi-tenant','desc'=>'Chaque entreprise est strictement isolée — aucune donnée ne peut fuiter d\'un tenant à un autre.'],
                    ['icon'=>'M3 10h18 M3 14h18','title'=>'Expiration de session','desc'=>'Sessions avec timeout configurable, révocation à distance des tokens actifs depuis le tableau de bord.'],
                    ['icon'=>'M22 12h-4l-3 9L9 3l-3 9H2','title'=>'Headers de sécurité','desc'=>'CSP, X-Frame-Options, HSTS, X-Content-Type-Options — en-têtes de sécurité HTTP activés sur toutes les réponses.'],
                ],
                'context' => [
                    'Conformité OHADA : la piste d\'audit est obligatoire pour la tenue comptable légale — OPESBooks en fait un pilier central.',
                    'Gestion des accès pour les cabinets d\'expertise comptable : un comptable peut accéder à plusieurs dossiers clients.',
                    'DGI peut requérir la production du journal d\'audit lors d\'un contrôle fiscal — OPESBooks génère l\'export en PDF.',
                    'Aucune donnée stockée hors Cameroun sur demande — option de déploiement on-premise disponible (Enterprise).',
                ],
            ],
        ];
    }

    public function pricing()
    {
        $plans = PlanConfig::where('is_active', true)->orderBy('sort_order')->get();
        return view('marketing.pricing', compact('plans'));
    }

    public function contact()
    {
        return view('marketing.contact');
    }

    public function about()
    {
        return view('marketing.about');
    }

    public function faq()
    {
        return view('marketing.faq');
    }

    public function terms()
    {
        $content = <<<'HTML'
<p>Les présentes Conditions Générales d'Utilisation (« CGU ») régissent l'accès et l'utilisation de la plateforme <strong>OPESBooks</strong>, éditée par <strong>Opesware</strong>, société de droit camerounais basée à Douala (Cameroun).</p>

<h2>1. Objet</h2>
<p>OPESBooks est une plateforme logicielle (SaaS) de comptabilité et de conformité fiscale destinée aux PME du Cameroun et de la zone CEMAC, conforme au référentiel SYSCOHADA révisé.</p>

<h2>2. Acceptation</h2>
<p>L'utilisation du service implique l'acceptation pleine et entière des présentes CGU. En créant un compte, vous reconnaissez avoir pris connaissance de ces conditions et les accepter.</p>

<h2>3. Compte et accès</h2>
<ul>
<li>Vous êtes responsable de l'exactitude des informations fournies (NIU, RCCM, centre fiscal, etc.).</li>
<li>Vous êtes responsable de la confidentialité de vos identifiants et de l'activité de votre compte.</li>
<li>L'activation de la double authentification (2FA) est recommandée.</li>
</ul>

<h2>4. Abonnements et paiement</h2>
<p>Les abonnements sont facturés en Francs CFA (XAF) selon la formule choisie. Le paiement s'effectue par Orange Money, MTN Mobile Money ou virement bancaire. L'essai gratuit est de 30 jours, sans engagement.</p>

<h2>5. Obligations de l'utilisateur</h2>
<p>Vous vous engagez à utiliser le service conformément à la loi et à ne pas porter atteinte à son intégrité. Vous demeurez seul responsable de l'exactitude de vos données comptables et de vos obligations déclaratives auprès de l'administration fiscale (DGI).</p>

<h2>6. Disponibilité du service</h2>
<p>OPESBooks fonctionne en mode hors ligne d'abord ; les données saisies sont synchronisées avec le cloud dès le retour de la connexion. Opesware met en œuvre des moyens raisonnables pour assurer la disponibilité du service, sans garantie d'absence totale d'interruption.</p>

<h2>7. Limitation de responsabilité</h2>
<p>OPESBooks est un outil d'aide à la tenue comptable et à la conformité. Il ne se substitue pas au conseil d'un expert-comptable ou d'un conseil fiscal. Opesware ne saurait être tenue responsable des décisions prises sur la base des informations produites par la plateforme.</p>

<h2>8. Propriété intellectuelle</h2>
<p>La plateforme, sa marque et ses contenus sont la propriété d'Opesware. Vos données comptables restent votre propriété exclusive.</p>

<h2>9. Résiliation</h2>
<p>Vous pouvez résilier votre abonnement à tout moment depuis votre espace. Vous pouvez exporter vos données avant la fermeture de votre compte.</p>

<h2>10. Droit applicable</h2>
<p>Les présentes CGU sont régies par le droit camerounais. Tout litige relève des juridictions compétentes de Douala, sous réserve des dispositions d'ordre public.</p>
HTML;

        return view('marketing.legal', ['title' => "Conditions Générales d'Utilisation", 'content' => $content]);
    }

    public function privacy()
    {
        $content = <<<'HTML'
<p>La présente Politique de Confidentialité décrit comment <strong>Opesware</strong> (Douala, Cameroun) collecte, utilise et protège vos données dans le cadre de la plateforme <strong>OPESBooks</strong>.</p>

<h2>1. Données collectées</h2>
<ul>
<li><strong>Données de compte</strong> : nom, email, téléphone, rôle.</li>
<li><strong>Données d'entreprise</strong> : raison sociale, NIU, RCCM, centre fiscal, régime d'imposition.</li>
<li><strong>Données comptables</strong> : écritures, factures, clients, fournisseurs, paie — saisies par vos soins.</li>
<li><strong>Données techniques</strong> : journaux de connexion, adresse IP, horodatages.</li>
</ul>

<h2>2. Finalités</h2>
<p>Vos données sont utilisées pour fournir le service (tenue comptable, facturation, déclarations), assurer la sécurité, facturer votre abonnement et améliorer la plateforme.</p>

<h2>3. Base légale et consentement</h2>
<p>Le traitement repose sur l'exécution du contrat de service et, le cas échéant, sur votre consentement. Vous gardez la maîtrise de vos données.</p>

<h2>4. Sécurité</h2>
<ul>
<li>Chiffrement des données sensibles au repos.</li>
<li>Double authentification (2FA) et gestion des rôles.</li>
<li>Journal d'audit et sauvegardes régulières.</li>
</ul>

<h2>5. Conservation</h2>
<p>Vos données sont conservées tant que votre compte est actif, puis selon les obligations légales de conservation comptable et fiscale en vigueur.</p>

<h2>6. Partage</h2>
<p>Vos données comptables ne sont jamais vendues. Elles peuvent être transmises à l'administration fiscale (DGI) uniquement sur votre instruction (télédéclaration), ou à des prestataires techniques strictement nécessaires au fonctionnement du service.</p>

<h2>7. Vos droits</h2>
<p>Vous disposez d'un droit d'accès, de rectification, d'export et de suppression de vos données. L'export est disponible depuis votre espace ; pour toute demande, écrivez à <a href="mailto:contact@opesware.com">contact@opesware.com</a>.</p>

<h2>8. Contact</h2>
<p>Opesware — Petite Terrain, Bonamoussadi, Douala, Cameroun — <a href="mailto:contact@opesware.com">contact@opesware.com</a> — +237 670 416 238.</p>
HTML;

        return view('marketing.legal', ['title' => 'Politique de Confidentialité', 'content' => $content]);
    }

    public function contactSubmit(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:120',
            'email'   => 'required|email|max:180',
            'message' => 'required|string|max:3000',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::to('contact@opesware.com')->send(new \App\Mail\TransactionalMail(
                subjectLine: "Contact site — {$data['name']}",
                heading: 'Nouveau message depuis le site',
                lines: [
                    "<strong>Nom :</strong> {$data['name']}",
                    "<strong>Email :</strong> {$data['email']}",
                    "<strong>Message :</strong><br>" . nl2br(e($data['message'])),
                ],
            ));
        } catch (\Throwable $e) { /* never block the user on mail errors */ }

        return response()->json(['ok' => true, 'message' => 'Message envoyé. Nous vous répondrons sous 24h.']);
    }
}
