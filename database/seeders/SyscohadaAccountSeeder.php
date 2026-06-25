<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class SyscohadaAccountSeeder extends Seeder
{
    public function run(): void
    {
        // FK-safe reset: MySQL refuses TRUNCATE on a table referenced by a
        // foreign key (journal_lines), so disable checks around it.
        Schema::disableForeignKeyConstraints();
        DB::table('syscohada_accounts')->truncate();
        Schema::enableForeignKeyConstraints();

        $now      = Carbon::now();
        $accounts = [
            // CLASS 1 — CAPITAUX PROPRES
            ['code' => '101000', 'label' => 'Capital Social (Company share capital)', 'class_digit' => 1],
            ['code' => '121000', 'label' => 'Report à nouveau créditeur (Retained earnings — profit carry-forward)', 'class_digit' => 1],
            ['code' => '129000', 'label' => 'Report à nouveau débiteur (Accumulated losses carry-forward)', 'class_digit' => 1],
            ['code' => '131000', 'label' => "Résultat net de l'exercice (Net profit/loss of the fiscal year)", 'class_digit' => 1],
            ['code' => '161000', 'label' => "Emprunts d'une durée supérieure à 1 an (Long-term bank loans)", 'class_digit' => 1],

            // CLASS 2 — IMMOBILISATIONS
            ['code' => '211000', 'label' => 'Terrains (Land property)', 'class_digit' => 2],
            ['code' => '231000', 'label' => 'Bâtiments (Buildings and physical structures)', 'class_digit' => 2],
            ['code' => '241100', 'label' => 'Matériel industriel (Industrial and processing machinery)', 'class_digit' => 2],
            ['code' => '244100', 'label' => 'Matériel automobile (Company vehicles and transport machinery)', 'class_digit' => 2],
            ['code' => '245000', 'label' => 'Matériel de bureau (Office furniture and hardware equipment)', 'class_digit' => 2],
            ['code' => '245100', 'label' => 'Matériel informatique (Laptops, servers, and routers)', 'class_digit' => 2],

            // CLASS 3 — STOCKS
            ['code' => '311000', 'label' => 'Marchandises (Goods purchased for direct resale)', 'class_digit' => 3],
            ['code' => '321000', 'label' => 'Matières premières (Raw materials for production)', 'class_digit' => 3],
            ['code' => '335000', 'label' => 'Produits finis (Finished goods ready for distribution)', 'class_digit' => 3],

            // CLASS 4 — TIERS
            ['code' => '401100', 'label' => "Fournisseurs d'exploitation (Standard operating suppliers)", 'class_digit' => 4],
            ['code' => '401200', 'label' => "Fournisseurs d'exploitation - Régime Réel", 'class_digit' => 4],
            ['code' => '401300', 'label' => "Fournisseurs d'exploitation - Régime Simplifié", 'class_digit' => 4],
            ['code' => '411100', 'label' => 'Clients (Standard operating customers)', 'class_digit' => 4],
            ['code' => '421100', 'label' => 'Personnel - Rémunérations dues (Net salary payable to employees)', 'class_digit' => 4],
            ['code' => '422000', 'label' => 'Personnel - Rémunérations dues (Employee salaries payable — general)', 'class_digit' => 4],
            ['code' => '431000', 'label' => 'CNPS — Part salariale et patronale (Employee + employer social contributions payable)', 'class_digit' => 4],
            ['code' => '441100', 'label' => 'État, Impôt sur le bénéfice (DGI Corporate Income Tax payable)', 'class_digit' => 4],
            ['code' => '442100', 'label' => "État, Acompte d'Impôt sur le Revenu (Monthly Minimum Tax Installment)", 'class_digit' => 4],
            ['code' => '443100', 'label' => 'État, TVA Facturée (Output VAT - Collected on client sales)', 'class_digit' => 4],
            ['code' => '445100', 'label' => 'État, TVA Récupérable sur immobilisations (Recoverable VAT on fixed assets)', 'class_digit' => 4],
            ['code' => '445200', 'label' => 'État, TVA Récupérable sur achats (Recoverable VAT on supplier inventory)', 'class_digit' => 4],
            ['code' => '445400', 'label' => 'État, TVA Récupérable sur services (Recoverable VAT on utilities and services)', 'class_digit' => 4],
            ['code' => '447000', 'label' => 'État, IRPP et CAC/IRPP à reverser (IRPP + CAC sur salaires)', 'class_digit' => 4],
            ['code' => '447100', 'label' => 'État, Impôts et taxes directs retenus à la source (IRPP and withholding deductions)', 'class_digit' => 4],
            ['code' => '448600', 'label' => 'État, Centimes Additionnels Communaux (CAC Surcharge liabilities)', 'class_digit' => 4],

            // CLASS 5 — FINANCIER (fixed accounts)
            ['code' => '521100', 'label' => 'Banques locales (Standard commercial bank accounts - Afriland, Ecobank)', 'class_digit' => 5],
            ['code' => '571100', 'label' => 'Caisse principale (Global corporate ledger)', 'class_digit' => 5],
        ];

        // Dynamic physical cash register sub-ledgers: 571101–571110 (10 shifts pre-seeded)
        for ($i = 1; $i <= 10; $i++) {
            $code       = '5711' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $accounts[] = ['code' => $code, 'label' => "Caisse Secondaire - Caissier #{$i}", 'class_digit' => 5];
        }

        // Dynamic MTN MoMo merchant SIM sub-ledgers: 571201–571210
        for ($i = 1; $i <= 10; $i++) {
            $code       = '5712' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $accounts[] = ['code' => $code, 'label' => "Caisse Mobile Money - MTN MoMo Ligne Marchande #{$i}", 'class_digit' => 5];
        }

        // Dynamic Orange Money merchant SIM sub-ledgers: 571301–571310
        for ($i = 1; $i <= 10; $i++) {
            $code       = '5713' . str_pad($i, 2, '0', STR_PAD_LEFT);
            $accounts[] = ['code' => $code, 'label' => "Caisse Mobile Money - Orange Money Ligne Marchande #{$i}", 'class_digit' => 5];
        }

        $moreAccounts = [
            // CLASS 2 — AMORTISSEMENTS CUMULÉS
            ['code' => '281310', 'label' => 'Amortissements cumulés — Bâtiments (Accumulated depreciation: buildings)', 'class_digit' => 2],
            ['code' => '284110', 'label' => 'Amortissements cumulés — Matériel industriel (Accumulated depreciation: machinery)', 'class_digit' => 2],
            ['code' => '284410', 'label' => 'Amortissements cumulés — Matériel automobile (Accumulated depreciation: vehicles)', 'class_digit' => 2],
            ['code' => '285010', 'label' => 'Amortissements cumulés — Matériel de bureau (Accumulated depreciation: office furniture)', 'class_digit' => 2],
            ['code' => '285110', 'label' => 'Amortissements cumulés — Matériel informatique (Accumulated depreciation: IT equipment)', 'class_digit' => 2],

            // CLASS 6 — CHARGES
            ['code' => '601100', 'label' => 'Achats de marchandises (Purchase of resale stock)', 'class_digit' => 6],
            ['code' => '602100', 'label' => 'Achats de matières premières (Purchase of raw manufacturing stock)', 'class_digit' => 6],
            ['code' => '605100', 'label' => 'Fournitures non stockables - Électricité (Eneo utility payments)', 'class_digit' => 6],
            ['code' => '605200', 'label' => 'Fournitures non stockables - Eau (Camwater utility payments)', 'class_digit' => 6],
            ['code' => '611000', 'label' => 'Transports de marchandises (Freight and delivery costs)', 'class_digit' => 6],
            ['code' => '618100', 'label' => 'Voyages et déplacements professionnels (Staff transport, taxi, MoMo network fees)', 'class_digit' => 6],
            ['code' => '621100', 'label' => "Sous-traitance d'exploitation (Local contractors and third-party developers)", 'class_digit' => 6],
            ['code' => '624100', 'label' => 'Publicité et marketing (Marketing campaigns, printing flyers, billboard fees)', 'class_digit' => 6],
            ['code' => '632400', 'label' => 'Honoraires comptables (External accountant audit fees)', 'class_digit' => 6],
            ['code' => '661100', 'label' => 'Charges de personnel - Salaires bruts (Base gross employee salaries)', 'class_digit' => 6],
            ['code' => '664000', 'label' => 'Charges sociales - Part patronale CNPS (Employer CNPS contributions)', 'class_digit' => 6],
            ['code' => '681200', 'label' => 'Dotations aux amortissements des immobilisations corporelles (Depreciation charge)', 'class_digit' => 6],
            ['code' => '654100', 'label' => 'Pertes sur cessions d\'immobilisations (Loss on disposal of fixed assets)', 'class_digit' => 6],

            // CLASS 7 — PRODUITS
            ['code' => '701100', 'label' => 'Ventes de marchandises au Cameroun (Local product sales)', 'class_digit' => 7],
            ['code' => '706000', 'label' => 'Prestations de services (Local service delivery billings)', 'class_digit' => 7],
            ['code' => '754100', 'label' => 'Produits sur cessions d\'immobilisations (Gain on disposal of fixed assets)', 'class_digit' => 7],
        ];

        $all = array_merge($accounts, $moreAccounts);

        DB::table('syscohada_accounts')->insert(
            array_map(fn ($a) => array_merge($a, ['created_at' => $now, 'updated_at' => $now]), $all)
        );
    }
}
