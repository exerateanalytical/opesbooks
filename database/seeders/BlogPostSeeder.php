<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Guide complet du plan comptable SYSCOHADA révisé pour les PME camerounaises',
                'tags'  => ['SYSCOHADA', 'Comptabilité', 'OHADA'],
                'excerpt' => "Le référentiel SYSCOHADA structure toute la comptabilité dans l'espace OHADA. Voici comment l'aborder concrètement quand on dirige une PME au Cameroun.",
                'body' => <<<MD
Le **Système Comptable OHADA (SYSCOHADA) révisé** est le référentiel comptable obligatoire dans les 17 États membres de l'OHADA, dont le Cameroun. Il définit le plan de comptes, les états financiers et les règles d'évaluation. Le maîtriser, c'est éviter les redressements et préparer sereinement sa DSF.

## Les 9 classes de comptes

Le plan comptable est organisé en neuf classes :

- **Classe 1** — Comptes de ressources durables (capital, emprunts)
- **Classe 2** — Comptes d'actif immobilisé (immobilisations)
- **Classe 3** — Comptes de stocks
- **Classe 4** — Comptes de tiers (clients, fournisseurs, État)
- **Classe 5** — Comptes de trésorerie (banque, caisse, Mobile Money)
- **Classe 6** — Charges des activités ordinaires
- **Classe 7** — Produits des activités ordinaires
- **Classe 8** — Autres charges et produits (HAO)
- **Classe 9** — Comptabilité analytique et engagements

Pour une PME, l'essentiel des opérations se joue dans les classes **6 (charges)** et **7 (produits)**, avec les comptes de tiers (4) et de trésorerie (5) pour le suivi de la caisse et de la banque.

## Les journaux indispensables

Une comptabilité bien tenue repose sur quelques journaux :

1. **Journal des achats** — factures fournisseurs
2. **Journal des ventes** — factures clients
3. **Journal de banque** et **journal de caisse**
4. **Journal des opérations diverses (OD)** — paie, amortissements, régularisations

## La partie double, sans douleur

Chaque écriture doit être **équilibrée** : total des débits = total des crédits. OPESBooks refuse toute écriture déséquilibrée et résout automatiquement les comptes par leur numéro SYSCOHADA, ce qui supprime une grande source d'erreurs.

## Bonnes pratiques

- Saisissez vos opérations **au quotidien**, pas en fin d'exercice.
- **Rapprochez votre banque** chaque mois.
- Préparez votre **DSF en continu** grâce au tableau de bord et aux contrôles automatiques.

> Avec OPESBooks, le plan SYSCOHADA est pré-chargé et les écritures sont guidées : vous vous concentrez sur votre activité, pas sur la mécanique comptable.
MD,
            ],
            [
                'title' => 'TVA au Cameroun : comprendre le taux effectif de 19,25 %',
                'tags'  => ['TVA', 'Fiscalité', 'DGI'],
                'excerpt' => "TVA à 17,5 %, plus le Centime Additionnel Communal à 10 % de la TVA : le taux effectif est de 19,25 %. Décryptage et exemples de calcul.",
                'body' => <<<MD
La TVA est l'un des impôts les plus fréquents — et l'une des premières sources d'erreur dans les factures des PME camerounaises. Voici comment elle se calcule réellement.

## 17,5 % de TVA + 10 % de CAC

Au Cameroun, le taux de **TVA est de 17,5 %**. À cela s'ajoute le **Centime Additionnel Communal (CAC)**, égal à **10 % du montant de la TVA**, soit 1,75 % du montant hors taxes. Le taux effectif appliqué au HT est donc :

> **17,5 % + 1,75 % = 19,25 %**

## Exemple chiffré

Pour une prestation de **1 000 000 XAF HT** :

| Élément | Calcul | Montant (XAF) |
|---|---|---|
| Base HT | — | 1 000 000 |
| TVA (17,5 %) | 1 000 000 × 0,175 | 175 000 |
| CAC (10 % de la TVA) | 175 000 × 0,10 | 17 500 |
| **Total TTC** | — | **1 192 500** |

Pour retrouver le HT à partir d'un TTC, on divise par **1,1925**.

## Pourquoi l'automatiser

Calculer la TVA et le CAC à la main, facture après facture, finit toujours par produire des écarts. OPESBooks applique le **multiplicateur 1,1925** automatiquement, gère les arrondis avec une précision décimale (bibliothèque `Brick\Math`) et ventile correctement la TVA collectée et le CAC dans les bons comptes SYSCOHADA.

## TVA collectée, TVA déductible

N'oubliez pas que la TVA que vous **collectez** sur vos ventes peut être diminuée de la TVA **déductible** sur vos achats professionnels. Le solde est ce que vous reversez à l'État. Une comptabilité à jour est la clé pour ne pas payer plus que nécessaire.
MD,
            ],
            [
                'title' => 'Comment préparer et déposer votre DSF sans stress',
                'tags'  => ['DSF', 'DGI', 'Fiscalité'],
                'excerpt' => "La Déclaration Statistique et Fiscale est l'obligation annuelle clé au Cameroun. Voici la méthode pour l'aborder en continu plutôt qu'en urgence.",
                'body' => <<<MD
La **Déclaration Statistique et Fiscale (DSF)** est la déclaration annuelle déposée auprès de la Direction Générale des Impôts (DGI). Elle synthétise vos comptes de l'exercice : bilan, compte de résultat et tableaux annexes conformes au SYSCOHADA.

## Quand la déposer ?

Pour les entreprises dont l'exercice coïncide avec l'année civile (clôture au 31 décembre), la DSF est généralement attendue **au plus tard le 15 mars** de l'année suivante. Le retard expose à des pénalités — d'où l'intérêt d'anticiper.

## La méthode « en continu »

Plutôt que de tout reconstituer en février, adoptez une routine :

1. **Tenez votre comptabilité à jour** chaque semaine.
2. **Rapprochez la banque** chaque mois.
3. Lancez le **contrôle DSF assisté par IA** pour repérer les anomalies (comptes déséquilibrés, écritures suspectes).
4. Générez l'**export DSF / D10** prêt à transmettre.

## Le rôle du moniteur DGI

Le moniteur DGI d'OPESBooks suit vos **échéances** et l'**état de vos télétransmissions** vers les plateformes Fiscalis / SIGIT. Vous savez en un coup d'œil ce qui est déclaré, en attente, ou à corriger.

## Erreurs fréquentes à éviter

- Oublier d'enregistrer des charges payées en espèces ou en Mobile Money.
- Ne pas amortir les immobilisations.
- Confondre dépenses personnelles et professionnelles.

> Une DSF se prépare toute l'année. Avec un logiciel qui tient les comptes au fil de l'eau, le 15 mars devient une formalité.
MD,
            ],
            [
                'title' => 'La patente au Cameroun : ce que toute entreprise doit savoir',
                'tags'  => ['Patente', 'Fiscalité', 'PME'],
                'excerpt' => "La contribution des patentes est une taxe professionnelle annuelle. Qui la paie, comment elle se calcule, et comment la suivre.",
                'body' => <<<MD
La **contribution des patentes** est une taxe professionnelle due par les personnes physiques et morales exerçant une activité commerciale, industrielle ou de prestation de services au Cameroun.

## Qui est concerné ?

La plupart des entreprises sont assujetties à la patente, sauf exonérations spécifiques prévues par le Code Général des Impôts. Elle conditionne souvent l'obtention de marchés et la régularité administrative.

## Comment est-elle calculée ?

Le montant dépend principalement du **chiffre d'affaires** et de la **nature de l'activité**, selon un barème. La patente comporte généralement une part proportionnelle au chiffre d'affaires. Le montant exact se détermine en fonction de votre situation et de votre commune.

## Pourquoi la suivre dans votre comptabilité

La patente est une charge déductible qui doit figurer correctement dans vos comptes (classe 6). L'oublier fausse votre résultat et votre DSF. OPESBooks intègre le **calcul et le suivi de la patente** pour que cette obligation ne passe pas à la trappe.

## Conseil pratique

Conservez votre **attestation de patente** : elle est souvent exigée lors d'appels d'offres et de démarches bancaires. Anticipez son paiement en début d'exercice pour éviter les pénalités de retard.
MD,
            ],
            [
                'title' => 'Paie au Cameroun : CNPS, IRPP et DIPE expliqués simplement',
                'tags'  => ['Paie', 'CNPS', 'IRPP'],
                'excerpt' => "Salaires, cotisations sociales et impôts sur traitements : le guide de la paie conforme pour les employeurs camerounais.",
                'body' => <<<MD
Gérer la paie au Cameroun, c'est articuler trois dimensions : le **salaire**, les **cotisations sociales (CNPS)** et la **fiscalité salariale (IRPP, DIPE)**.

## La CNPS

La **Caisse Nationale de Prévoyance Sociale** collecte les cotisations couvrant notamment :

- les **pensions** (vieillesse, invalidité, décès) ;
- les **prestations familiales** ;
- les **risques professionnels** (accidents du travail).

Ces cotisations comportent une **part patronale** et une **part salariale**, calculées sur le salaire cotisable dans la limite d'un plafond. L'employeur établit chaque mois un **bordereau** à reverser à la CNPS.

## L'IRPP

L'**Impôt sur le Revenu des Personnes Physiques** s'applique aux traitements et salaires, selon un barème progressif après abattements. Il est retenu à la source par l'employeur.

## Le DIPE

La **Déclaration des Informations sur le Personnel Employé** regroupe les éléments d'impôts et cotisations sur salaires à déclarer périodiquement à l'administration.

## Automatiser pour fiabiliser

La paie cumule les sources d'erreur : plafonds, taux, arrondis, bulletins. OPESBooks génère les **bulletins de paie**, calcule les **cotisations CNPS** et l'**IRPP**, et produit les **bordereaux** et éléments DIPE — puis passe automatiquement les écritures correspondantes en comptabilité.

> Une paie juste, c'est la sérénité sociale et fiscale. Laissez le calcul au logiciel et concentrez-vous sur vos équipes.
MD,
            ],
            [
                'title' => 'Facturation électronique MECeF : préparez la Loi de Finances 2026',
                'tags'  => ['MECeF', 'DGI', 'Facturation'],
                'excerpt' => "La facture électronique certifiée devient incontournable. Voici ce que cela change pour les PME et comment s'y préparer dès maintenant.",
                'body' => <<<MD
La digitalisation de l'administration fiscale camerounaise se poursuit. Dans le sillage de la **Loi de Finances 2026**, la **facturation électronique certifiée (MECeF)** s'impose progressivement comme un standard.

## Qu'est-ce que la facturation électronique certifiée ?

Il s'agit d'émettre des factures dont l'authenticité et l'intégrité sont garanties et **transmises / certifiées** auprès de la DGI, via les plateformes dédiées. L'objectif : lutter contre la fraude et fiabiliser la TVA.

## Ce que cela change pour votre PME

- Vos factures doivent comporter les **mentions obligatoires** (NIU, RCCM, centre fiscal, numérotation séquentielle).
- L'émission doit pouvoir être **certifiée** et tracée.
- La cohérence entre facturation et déclarations devient plus étroitement contrôlée.

## Comment OPESBooks vous prépare

OPESBooks est conçu pour :

1. produire des factures **conformes** (mentions légales, TVA 19,25 %, numérotation) ;
2. générer un **hash d'intégrité** par facture ;
3. se **synchroniser** avec le portail DGI une fois vos identifiants configurés.

## Anticiper, c'est gagner

Mieux vaut adopter dès aujourd'hui des factures conformes que de devoir tout reprendre dans l'urgence. En structurant votre facturation maintenant, la bascule vers la certification devient transparente.
MD,
            ],
            [
                'title' => 'Réel, simplifié ou libératoire : quel régime fiscal pour votre activité ?',
                'tags'  => ['Fiscalité', 'Régimes', 'PME'],
                'excerpt' => "Le régime d'imposition dépend de votre chiffre d'affaires et conditionne vos obligations. Tour d'horizon pour bien se situer.",
                'body' => <<<MD
Au Cameroun, vos obligations déclaratives dépendent de votre **régime d'imposition**, lui-même lié à votre **chiffre d'affaires** et à la nature de votre activité.

## L'impôt libératoire

Destiné aux **très petites activités**, l'impôt libératoire est un montant forfaitaire qui « libère » des principaux impôts pour les plus petits chiffres d'affaires. Les obligations comptables y sont allégées.

## Le régime simplifié

Pour les entreprises de taille intermédiaire, le **régime simplifié** impose une comptabilité plus complète et des déclarations périodiques, tout en restant moins lourd que le régime réel.

## Le régime réel

Au-delà d'un certain seuil de chiffre d'affaires, le **régime réel** s'applique : comptabilité SYSCOHADA complète, TVA, DSF détaillée, et obligations déclaratives régulières.

## Comment bien se situer

- Estimez votre **chiffre d'affaires annuel**.
- Vérifiez le **seuil** correspondant à votre activité auprès de votre centre fiscal.
- Adaptez votre **organisation comptable** en conséquence.

OPESBooks s'adapte à votre régime : vous renseignez votre profil fiscal (régime, centre fiscal, NIU, RCCM) et la plateforme ajuste les déclarations et les calculs.

> En cas de doute, parlez-en à votre centre des impôts ou à votre comptable : le bon régime, c'est moins d'impôt et moins de risques.
MD,
            ],
            [
                'title' => "Comptabilité hors ligne : pourquoi c'est vital au Cameroun",
                'tags'  => ['Hors ligne', 'Plateforme', 'PME'],
                'excerpt' => "Coupures de réseau, connexions instables : une comptabilité qui s'arrête dès que l'Internet flanche n'est pas une option. Voici l'approche hors ligne d'abord.",
                'body' => <<<MD
Sur le terrain camerounais, la connexion Internet est précieuse — mais pas toujours fiable. Une solution comptable qui exige une connexion permanente devient vite un frein. C'est pourquoi OPESBooks est pensé **hors ligne d'abord**.

## Le problème des logiciels « tout en ligne »

Avec une application 100 % en ligne, la moindre coupure bloque la saisie : impossible d'émettre une facture, d'enregistrer une vente ou de consulter un solde. Pour un commerce ou une PME, c'est du chiffre d'affaires perdu.

## L'approche hors ligne d'abord

OPESBooks vous laisse **travailler sans réseau** :

- vos saisies sont **enregistrées localement** instantanément ;
- une **file d'attente de synchronisation** conserve les opérations effectuées hors ligne ;
- dès que la connexion revient, tout se **synchronise automatiquement** vers le cloud.

Un indicateur vous montre en permanence l'état : connecté, hors ligne, ou nombre d'opérations en attente.

## Application de bureau et mobile

Au-delà du web, une **application de bureau** (Windows, macOS, Linux) et une **application installable** sur mobile (PWA) vous permettent d'ouvrir OPESBooks comme une vraie application — et de travailler avec ou sans Internet, avec vos identifiants cloud.

## Le meilleur des deux mondes

Vous gardez la **disponibilité du local** et la **sécurité du cloud** (sauvegardes, accès multi-appareils, collaboration). La connexion n'est plus une condition pour travailler : elle sert à synchroniser.
MD,
            ],
            [
                'title' => 'Faire des affaires en zone CEMAC : fiscalité et SYSCOHADA dans 6 pays',
                'tags'  => ['CEMAC', 'OHADA', 'International'],
                'excerpt' => "Cameroun, Gabon, Congo, Tchad, RCA, Guinée Équatoriale : une base comptable commune, des spécificités fiscales locales. Ce qu'il faut savoir pour se développer.",
                'body' => <<<MD
La **CEMAC** (Communauté Économique et Monétaire de l'Afrique Centrale) regroupe six pays partageant une monnaie commune et un cadre comptable harmonisé. Pour une entreprise qui se développe, c'est un marché régional cohérent.

## Les six pays

- Cameroun
- Gabon
- Congo
- Tchad
- République Centrafricaine
- Guinée Équatoriale

## Une monnaie commune : le XAF

Tous ces pays utilisent le **Franc CFA BEAC (XAF)**, ce qui simplifie les échanges et la facturation régionale. Les tarifs et montants d'OPESBooks sont d'ailleurs en XAF.

## Un socle comptable commun : SYSCOHADA

Les pays de la CEMAC appartiennent à l'espace **OHADA** : ils partagent le **plan comptable SYSCOHADA révisé** et des états financiers harmonisés. Une entreprise qui maîtrise le SYSCOHADA au Cameroun retrouve la même logique chez ses voisins.

## Des spécificités fiscales locales

Si la base comptable est commune, **la fiscalité reste nationale** : taux de TVA, déclarations, cotisations sociales et obligations varient d'un pays à l'autre. C'est pourquoi OPESBooks propose des **configurations fiscales par pays**.

## Se développer sereinement

Avec une plateforme qui parle déjà SYSCOHADA et gère le multi-pays, étendre son activité dans la CEMAC devient un paramétrage plutôt qu'une refonte. Vous pilotez plusieurs entités, dans plusieurs pays, depuis un seul outil.
MD,
            ],
        ];

        // Retire earlier short stub posts superseded by the in-depth versions.
        BlogPost::whereIn('slug', [
            'comment-preparer-votre-dsf-avec-opesbooks',
            'tva-au-cameroun-tout-ce-que-vous-devez-savoir',
            'guide-complet-syscohada-pour-les-pme-camerounaises',
        ])->delete();

        foreach ($posts as $i => $p) {
            BlogPost::updateOrCreate(['slug' => Str::slug($p['title'])], [
                'title'                => $p['title'],
                'excerpt'              => $p['excerpt'],
                'body'                 => $p['body'],
                'is_published'         => true,
                'published_at'         => now()->subDays(($i + 1) * 4),
                'reading_time_minutes' => max(3, (int) ceil(str_word_count(strip_tags($p['body'])) / 200)),
                'tags'                 => $p['tags'],
                'meta_description'     => $p['excerpt'],
            ]);
        }
    }
}
