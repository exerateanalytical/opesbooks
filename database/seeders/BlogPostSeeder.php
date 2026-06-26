<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title'                => 'TVA au Cameroun : tout comprendre sur le taux de 19,25 %',
                'slug'                 => 'tva-cameroun-taux-19-25-percent',
                'excerpt'              => 'En pratique, la TVA camerounaise n\'est pas 17,5 % mais 19,25 %. Explications sur le Centime Additionnel Communal (CAC) et comment calculer vos factures correctement.',
                'tags'                 => ['TVA', 'FISCALITÉ', 'CAMEROUN'],
                'reading_time_minutes' => 5,
                'published_at'         => Carbon::now()->subDays(3),
                'is_published'         => true,
                'meta_title'           => 'TVA Cameroun 19,25 % : TVA 17,5 % + CAC expliqués',
                'meta_description'     => 'Comprendre la TVA camerounaise : taux de 17,5 % + CAC 10 % = 19,25 % TTC. Exemples de calcul HT/TTC pour PME.',
                'body'                 => "## Pourquoi 19,25 % et non 17,5 % ?\n\nEn République du Cameroun, le taux légal de la Taxe sur la Valeur Ajoutée (TVA) est de **17,5 %**. Mais la quasi-totalité des factures affichent **19,25 %** : c'est parce qu'il faut y ajouter le **Centime Additionnel Communal (CAC)**.\n\nLe CAC est une taxe locale reversée aux communes, égale à **10 % du montant de la TVA** (et non 10 % du HT). Ainsi :\n\n| Composante | Taux sur le HT |\n|---|---|\n| TVA | 17,5 % |\n| CAC (10 % de la TVA) | 1,75 % |\n| **Total TTC** | **19,25 %** |\n\n## Exemple concret\n\nPour une facture de **500 000 XAF HT** :\n\n- TVA : 500 000 × 17,5 % = **87 500 XAF**\n- CAC : 87 500 × 10 % = **8 750 XAF**\n- **Total TTC : 596 250 XAF**\n\n## Opérations exonérées\n\nCertains biens et services sont exonérés de TVA : produits de première nécessité (certains aliments), médicaments, activités agricoles, exportations, et services financiers. Vérifiez toujours le statut fiscal de votre activité auprès de votre Centre des Impôts.\n\n## Comment OPESBooks gère ce calcul\n\nOPESBooks intègre la formule exacte grâce à la bibliothèque `Brick\\Math\\BigDecimal`, qui évite les erreurs d'arrondi en virgule flottante. Chaque facture affiche :\n- Le montant HT\n- La ligne TVA (17,5 %)\n- La ligne CAC (10 % de la TVA)\n- Le total TTC\n\n> Le taux effectif global à appliquer est **1,1925 × HT**. OPESBooks l'applique automatiquement — aucun calcul manuel n'est nécessaire.",
            ],
            [
                'title'                => 'SYSCOHADA révisé : le plan comptable des PME camerounaises expliqué',
                'slug'                 => 'syscohada-revise-plan-comptable-pme-cameroun',
                'excerpt'              => 'Le plan comptable SYSCOHADA révisé structure la comptabilité de toute entreprise en zone OHADA. Découvrez les 9 classes de comptes, leur logique et leur application pratique pour une PME camerounaise.',
                'tags'                 => ['SYSCOHADA', 'COMPTABILITÉ', 'OHADA'],
                'reading_time_minutes' => 7,
                'published_at'         => Carbon::now()->subDays(10),
                'is_published'         => true,
                'meta_title'           => 'SYSCOHADA révisé : plan comptable OHADA pour PME camerounaises',
                'meta_description'     => 'Guide pratique du plan comptable SYSCOHADA révisé 2017 pour les PME camerounaises : 9 classes de comptes, logique OHADA, exemples d\'écritures.',
                'body'                 => "## Qu'est-ce que SYSCOHADA ?\n\nLe **Système Comptable OHADA (SYSCOHADA)** est le référentiel comptable commun aux 17 États membres de l'OHADA. Sa version révisée, en vigueur depuis le **1er janvier 2018**, modernise le plan comptable et aligne les états financiers sur les standards internationaux.\n\n## Les 9 classes de comptes\n\n| Classe | Intitulé | Exemples |\n|---|---|---|\n| 1 | Ressources durables | Capital, dettes LT |\n| 2 | Actif immobilisé | Matériel, bâtiments |\n| 3 | Stocks | Marchandises, MP |\n| 4 | Comptes de tiers | Clients (411), Fourn. (401) |\n| 5 | Trésorerie | Banque (521), Caisse (571) |\n| 6 | Charges | Achats (601), Salaires (661) |\n| 7 | Produits | Ventes (701) |\n| 8 | HAO | Hors activités ordinaires |\n| 9 | Analytique | Par projet, département |\n\n## L'écriture d'une vente (500 000 XAF HT)\n\n```\nDébit  411 – Client XYZ            596 250\n  Crédit  701 – Ventes              500 000\n  Crédit  443 – TVA collectée        87 500\n  Crédit  4452 – CAC collecté         8 750\n```\n\nOPESBooks génère cette écriture automatiquement lors de la création d'une facture.\n\n## Mobile Money et comptes 571x\n\nOPESBooks crée des sous-comptes auxiliaires dédiés :\n- **5711** : MTN Mobile Money\n- **5712** : Orange Money (Flooz)\n\n> OPESBooks intègre le plan comptable SYSCOHADA révisé 2017 complet : plus de 600 comptes pré-chargés, modifiables selon votre secteur d'activité.",
            ],
            [
                'title'                => 'DSF au Cameroun : comment préparer votre déclaration statistique et fiscale',
                'slug'                 => 'dsf-cameroun-declaration-statistique-fiscale-guide',
                'excerpt'              => 'La DSF est la déclaration annuelle obligatoire pour toute entreprise au Cameroun. Ce guide explique ce qu\'elle contient, les délais à respecter et comment OPESBooks la génère automatiquement.',
                'tags'                 => ['DSF', 'DGI', 'DÉCLARATION'],
                'reading_time_minutes' => 6,
                'published_at'         => Carbon::now()->subDays(18),
                'is_published'         => true,
                'meta_title'           => 'DSF Cameroun : guide complet de la Déclaration Statistique et Fiscale',
                'meta_description'     => 'Tout savoir sur la DSF au Cameroun : contenu, délais, format DGI, export depuis OPESBooks. Guide PME 2026.',
                'body'                 => "## Qu'est-ce que la DSF ?\n\nLa **Déclaration Statistique et Fiscale (DSF)** est la déclaration annuelle obligatoire que toute entreprise soumise au régime réel au Cameroun doit déposer auprès de la **DGI**. Elle synthétise l'ensemble des données comptables et fiscales de l'exercice.\n\n## Délais de dépôt\n\n| Clôture | Délai DSF |\n|---|---|\n| 31 décembre | **15 mars** N+1 |\n| Autre date | 75 jours après clôture |\n\n## Contenu de la DSF\n\n1. Bilan comptable (actif et passif)\n2. Compte de résultat\n3. Tableau des flux de trésorerie\n4. Notes annexes (immobilisations, dettes)\n5. État des salaires (IRPP, CNPS)\n6. Déclaration TVA annuelle (D10)\n7. Calcul de l'IS ou de l'IRPP\n\n## Comment OPESBooks prépare votre DSF\n\n- **Balance comptable** SYSCOHADA à jour en temps réel\n- **Export DSF/D10** formaté selon le modèle DGI Cameroun\n- **Bilan et compte de résultat** OHADA en un clic\n- **Intégration Fiscalis** pour la télétransmission directe\n\n## Erreurs fréquentes à éviter\n\n- Déséquilibre entre actif et passif du bilan\n- Discordance entre TVA déclarée mensuellement et DSF annuelle\n- Oubli des écritures de clôture (amortissements, provisions)\n- Montants en XAF entiers (pas en milliers)\n\n> Conseil : ne préparez pas votre DSF la dernière semaine de février. Avec OPESBooks, elle est constituée progressivement tout au long de l'exercice.",
            ],
            [
                'title'                => 'Mobile Money en comptabilité SYSCOHADA : MTN MoMo et Orange Money',
                'slug'                 => 'mobile-money-comptabilite-syscohada-mtn-orange-money',
                'excerpt'              => 'Au Cameroun, la majorité des paiements PME transitent par MTN Mobile Money et Orange Money. Voici comment les intégrer correctement dans votre comptabilité SYSCOHADA avec les comptes auxiliaires 571x.',
                'tags'                 => ['MOBILE MONEY', 'TRÉSORERIE', 'MTN'],
                'reading_time_minutes' => 5,
                'published_at'         => Carbon::now()->subDays(25),
                'is_published'         => true,
                'meta_title'           => 'Comptabilité Mobile Money au Cameroun : MTN MoMo et Orange Money en SYSCOHADA',
                'meta_description'     => 'Intégrer MTN MoMo et Orange Money dans votre comptabilité SYSCOHADA : comptes 571x, écritures, rapprochement. Guide PME camerounaise.',
                'body'                 => "## La réalité du terrain camerounais\n\nAu Cameroun, **plus de 60 % des transactions B2B des PME** transitent par Mobile Money. Pourtant, la plupart des logiciels n'ont pas de traitement natif — les montants sont souvent passés en vrac dans un seul compte de caisse.\n\n## Les comptes 571x — la solution SYSCOHADA\n\n| Compte | Portefeuille |\n|---|---|\n| 5711 | MTN Mobile Money |\n| 5712 | Orange Money (Flooz) |\n| 5713 | Wave |\n| 5714 | Autres wallets |\n\n## Exemple : encaissement par MTN MoMo (119 250 XAF TTC)\n\n```\nDébit  5711 – MTN Mobile Money     119 250\n  Crédit  411 – Client XYZ          119 250\n```\n\n## Frais de transaction MTN (1 %, soit 1 193 XAF)\n\n```\nDébit  627 – Frais bancaires         1 193\n  Crédit  5711 – MTN Mobile Money     1 193\n```\n\n## Ce qu'OPESBooks apporte\n\n- Comptes auxiliaires 571x pré-créés pour MTN et Orange\n- Ingestion des callbacks API MTN MoMo : l'écriture est créée automatiquement\n- Module de rapprochement : importez le CSV et lettrez en un clic\n- Vue consolidée de tous vos wallets\n\n> Ne mélangez jamais votre portefeuille Mobile Money personnel et votre compte professionnel — c'est une obligation de séparation des patrimoines OHADA.",
            ],
            [
                'title'                => 'Facturation électronique MECeF : ce que les PME camerounaises doivent savoir en 2026',
                'slug'                 => 'facturation-electronique-mecef-cameroun-2026',
                'excerpt'              => 'La Loi de Finances 2026 impose la facturation électronique certifiée MECeF. Ce guide explique les obligations, les délais et comment s\'y conformer.',
                'tags'                 => ['MECEF', 'FACTURATION ÉLECTRONIQUE', 'LOI FINANCES 2026'],
                'reading_time_minutes' => 6,
                'published_at'         => Carbon::now()->subDays(35),
                'is_published'         => true,
                'meta_title'           => 'MECeF Cameroun 2026 : facturation électronique certifiée pour PME',
                'meta_description'     => 'Obligations MECeF Cameroun 2026 : QR code DGI, SHA-256, certification des factures. Guide conformité Loi de Finances.',
                'body'                 => "## Qu'est-ce que la MECeF ?\n\nLa **Machine Électronique de Contrôle et de Facturation (MECeF)** est le dispositif de facturation électronique imposé par la **Loi de Finances 2026**. Son objectif : certifier chaque facture émise en temps réel.\n\n## Comment fonctionne la MECeF ?\n\n1. Les données sont **envoyées en temps réel** au serveur DGI\n2. La DGI génère un **numéro de certification** unique\n3. Un **QR code** est ajouté à la facture\n4. Une **empreinte SHA-256** garantit l'intégrité du document\n\n## Ce que doit afficher une facture MECeF conforme\n\n- Le NIU de l'émetteur\n- Le numéro MECeF attribué par la DGI\n- Le QR code de vérification\n- La date et l'heure de certification\n- L'empreinte numérique de la facture\n\n## Sanctions en cas de non-conformité\n\nL'émission d'une facture non certifiée est passible d'amendes. La DGI peut refuser la déductibilité de la TVA lors d'un contrôle.\n\n## OPESBooks et la conformité MECeF\n\n- Calcul SHA-256 de chaque facture émise\n- QR code DGI intégré dans le PDF\n- Synchronisation avec le portail DGI via API\n- Moniteur MECeF : suivi des certifications et des erreurs\n\n> Une facture non certifiée MECeF est considérée non conforme par la DGI Cameroun. Assurez-vous que votre logiciel supporte ce dispositif avant 2026.",
            ],
            [
                'title'                => 'Paie au Cameroun : guide CNPS, IRPP et DIPE pour les PME',
                'slug'                 => 'paie-cameroun-cnps-irpp-dipe-guide-pme',
                'excerpt'              => 'Calculer les salaires au Cameroun implique CNPS, IRPP et déclaration DIPE. Ce guide pratique explique les taux, les délais et les obligations des employeurs.',
                'tags'                 => ['PAIE', 'CNPS', 'IRPP', 'DIPE'],
                'reading_time_minutes' => 7,
                'published_at'         => Carbon::now()->subDays(45),
                'is_published'         => true,
                'meta_title'           => 'Paie Cameroun : CNPS, IRPP, DIPE — Guide complet pour PME',
                'meta_description'     => 'Guide pratique de la paie au Cameroun : taux CNPS, calcul IRPP, déclaration DIPE, SMIG 36 270 XAF, bulletins conformes.',
                'body'                 => "## Le SMIG au Cameroun\n\nLe **Salaire Minimum Interprofessionnel Garanti (SMIG)** au Cameroun est fixé à **36 270 XAF brut par mois**.\n\n## Cotisations CNPS\n\n| Branche | Part employeur | Part salarié |\n|---|---|---|\n| Vieillesse / Invalidité | 4,2 % | 4,2 % |\n| Allocations familiales | 7,0 % | — |\n| Accidents du travail | 1,75 % | — |\n| FNE | 1,0 % | — |\n| **Total indicatif** | **≈ 13,95 %** | **4,2 %** |\n\n## L'IRPP\n\nL'IRPP est calculé sur le revenu imposable net selon un barème progressif de **0 % à 35 %**. Des parts fiscales pour les enfants à charge réduisent la base imposable.\n\n## La déclaration DIPE\n\nLa DIPE doit être déposée avant le **15 du mois suivant** le versement des salaires.\n\n## Exemple de bulletin (salaire brut 150 000 XAF)\n\n| Rubrique | Montant |\n|---|---|\n| Salaire brut | 150 000 |\n| Cotisation salariale CNPS (4,2 %) | − 6 300 |\n| IRPP retenu | ≈ − 4 500 |\n| **Net à payer** | **≈ 139 200** |\n\n## Ce qu'OPESBooks automatise\n\n- Calcul du net à payer et de toutes les cotisations\n- Bulletins PDF avec toutes les rubriques\n- Bordereaux CNPS mensuels et trimestriels\n- Export DIPE pour télétransmission à la DGI\n- Passation automatique des écritures de paie dans le journal SYSCOHADA\n- Vérification du SMIG à chaque fiche de paie",
            ],
            [
                'title'                => 'Comment choisir son logiciel de comptabilité pour une PME camerounaise',
                'slug'                 => 'choisir-logiciel-comptabilite-pme-camerounaise',
                'excerpt'              => 'Sage, QuickBooks, Odoo ou OPESBooks ? Ce guide compare les critères décisifs : conformité DGI, prix en XAF, support local, SYSCOHADA et Mobile Money.',
                'tags'                 => ['GUIDE', 'CHOIX LOGICIEL', 'PME'],
                'reading_time_minutes' => 6,
                'published_at'         => Carbon::now()->subDays(55),
                'is_published'         => true,
                'meta_title'           => 'Quel logiciel de comptabilité pour PME camerounaise ? Comparatif 2026',
                'meta_description'     => 'Comparatif logiciels comptabilité PME Cameroun 2026 : Sage, QuickBooks, Odoo, OPESBooks. Critères : DGI, TVA 19,25%, SYSCOHADA, Mobile Money, XAF.',
                'body'                 => "## Les 7 critères décisifs\n\n### 1. Conformité DGI Cameroun\nLe logiciel génère-t-il nativement l'export **DSF/D10** ? Supporte-t-il la **MECeF** (Loi de Finances 2026) ?\n\n### 2. Calcul TVA camerounaise\nLe taux est-il **19,25 %** (17,5 % TVA + 1,75 % CAC) ? Le CAC est-il une ligne séparée ?\n\n### 3. Plan comptable SYSCOHADA\nLe plan OHADA révisé 2017 est-il **pré-chargé** ?\n\n### 4. Prix en XAF\nY a-t-il un abonnement en **Francs CFA** payable par **Mobile Money** ?\n\n### 5. Fonctionnement hors ligne\nLe logiciel fonctionne-t-il **offline** avec synchronisation auto ?\n\n### 6. Intégration Mobile Money\nLes flux **MTN MoMo** et **Orange Money** sont-ils gérés nativement ?\n\n### 7. Conformité paie locale\nCNPS, **IRPP** et bordereau **DIPE** sont-ils calculés correctement ?\n\n## Comparatif rapide\n\n| Critère | Sage | QuickBooks | Odoo | **OPESBooks** |\n|---|---|---|---|---|\n| DSF / DGI natif | ✗ | ✗ | ✗ | **✓** |\n| TVA 19,25 % (CAC) | Partiel | ✗ | Paramétrable | **✓** |\n| SYSCOHADA pré-chargé | Partiel | ✗ | Paramétrable | **✓** |\n| Prix en XAF | ✗ | ✗ | ✗ | **✓** |\n| Paiement Mobile Money | ✗ | ✗ | ✗ | **✓** |\n| Hors ligne | ✗ | ✗ | ✗ | **✓** |\n| MECeF 2026 | ✗ | ✗ | ✗ | **✓** |\n\n> OPESBooks est conçu de zéro pour les PME camerounaises — pas adapté, **conçu nativement**. Essayez gratuitement pendant 30 jours.",
            ],
            [
                'title'                => 'Clôture d\'exercice comptable SYSCOHADA : guide pas-à-pas pour les PME',
                'slug'                 => 'cloture-exercice-comptable-syscohada-guide',
                'excerpt'              => 'La clôture de l\'exercice comptable est une étape critique. Ce guide couvre les à-nouveaux, les amortissements, les provisions et la préparation de la DSF.',
                'tags'                 => ['CLÔTURE', 'SYSCOHADA', 'EXERCICE'],
                'reading_time_minutes' => 8,
                'published_at'         => Carbon::now()->subDays(78),
                'is_published'         => true,
                'meta_title'           => 'Clôture d\'exercice SYSCOHADA : guide complet pour PME camerounaises',
                'meta_description'     => 'Comment clôturer son exercice SYSCOHADA : à-nouveaux, amortissements, provisions, inventaire, DSF. Guide PME Cameroun.',
                'body'                 => "## Pourquoi la clôture est-elle importante ?\n\nLa clôture annuelle est le moment où vous photographiez la situation financière de votre entreprise. Elle sert de base à la **DSF**, au calcul de l'**IS** et à l'évaluation de votre entreprise.\n\n## Les étapes de la clôture\n\n### 1. Inventaire des stocks\nComptez physiquement et comparez avec les quantités comptables. Les écarts sont passés en charges (6031) ou produits (7031).\n\n### 2. Amortissements\n```\nDébit  6811 – Dotations aux amortissements   XXX\n  Crédit  281x – Amortissements               XXX\n```\n\n### 3. Provisions\n- Créances douteuses\n- Risques et charges connus\n- Congés payés acquis\n\n### 4. Régularisations\nPassez les charges à payer et produits à recevoir pour rattacher à la bonne période.\n\n### 5. Vérification de l'équilibre\nTotal débit = total crédit avant les écritures de clôture.\n\n### 6. Écritures de clôture\nSoldez tous les comptes 6xx et 7xx vers le compte de résultat (130).\n\n### 7. À-nouveaux\nReportez les soldes de bilan en début de nouvel exercice.\n\n## Ce qu'OPESBooks automatise\n\n- Calcul des amortissements et passation automatique\n- Balance avant et après clôture\n- Export DSF basé sur les comptes clôturés\n- Verrouillage de la période après clôture\n- À-nouveaux automatiques en début de nouvel exercice\n\n> Commencez votre clôture en novembre — pas en mars. Avec OPESBooks, elle est constituée progressivement tout au long de l'exercice.",
            ],
        ];

        foreach ($posts as $data) {
            BlogPost::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
