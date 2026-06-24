<?php

namespace Database\Seeders;

use App\Models\SyscohadaAccount;
use Illuminate\Database\Seeder;

class SyscohadaAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // CLASS 1 — CAPITAUX PROPRES
            ['code' => '101000', 'label' => 'Capital Social', 'class_digit' => 1],
            ['code' => '121000', 'label' => 'Report à nouveau créditeur', 'class_digit' => 1],
            ['code' => '131000', 'label' => 'Résultat net de l\'exercice', 'class_digit' => 1],
            ['code' => '161000', 'label' => 'Emprunts d\'une durée supérieure à 1 an', 'class_digit' => 1],

            // CLASS 2 — IMMOBILISATIONS
            ['code' => '211000', 'label' => 'Terrains', 'class_digit' => 2],
            ['code' => '231000', 'label' => 'Bâtiments', 'class_digit' => 2],
            ['code' => '241100', 'label' => 'Matériel industriel', 'class_digit' => 2],
            ['code' => '244100', 'label' => 'Matériel automobile', 'class_digit' => 2],
            ['code' => '245000', 'label' => 'Matériel de bureau', 'class_digit' => 2],
            ['code' => '245100', 'label' => 'Matériel informatique', 'class_digit' => 2],

            // CLASS 3 — STOCKS
            ['code' => '311000', 'label' => 'Marchandises', 'class_digit' => 3],
            ['code' => '321000', 'label' => 'Matières premières', 'class_digit' => 3],
            ['code' => '335000', 'label' => 'Produits finis', 'class_digit' => 3],

            // CLASS 4 — TIERS
            ['code' => '401100', 'label' => 'Fournisseurs d\'exploitation', 'class_digit' => 4],
            ['code' => '401200', 'label' => 'Fournisseurs d\'exploitation - Régime Réel', 'class_digit' => 4],
            ['code' => '401300', 'label' => 'Fournisseurs d\'exploitation - Régime Simplifié', 'class_digit' => 4],
            ['code' => '411100', 'label' => 'Clients', 'class_digit' => 4],
            ['code' => '422000', 'label' => 'Personnel - Rémunérations dues', 'class_digit' => 4],
            ['code' => '441100', 'label' => 'État, Impôt sur le bénéfice', 'class_digit' => 4],
            ['code' => '442100', 'label' => 'État, Acompte d\'Impôt sur le Revenu', 'class_digit' => 4],
            ['code' => '443100', 'label' => 'État, TVA Facturée', 'class_digit' => 4],
            ['code' => '445100', 'label' => 'État, TVA Récupérable sur immobilisations', 'class_digit' => 4],
            ['code' => '445200', 'label' => 'État, TVA Récupérable sur achats', 'class_digit' => 4],
            ['code' => '445400', 'label' => 'État, TVA Récupérable sur services', 'class_digit' => 4],
            ['code' => '447100', 'label' => 'État, Impôts et taxes directs retenus à la source', 'class_digit' => 4],
            ['code' => '448600', 'label' => 'État, Centimes Additionnels Communaux', 'class_digit' => 4],

            // CLASS 5 — FINANCIER
            ['code' => '521100', 'label' => 'Banques locales', 'class_digit' => 5],
            ['code' => '571100', 'label' => 'Caisse principale', 'class_digit' => 5],
            ['code' => '571200', 'label' => 'Caisse Mobile Money - MTN MoMo API Wallet', 'class_digit' => 5],
            ['code' => '571300', 'label' => 'Caisse Mobile Money - Orange Money API Wallet', 'class_digit' => 5],

            // CLASS 6 — CHARGES
            ['code' => '601100', 'label' => 'Achats de marchandises', 'class_digit' => 6],
            ['code' => '602100', 'label' => 'Achats de matières premières', 'class_digit' => 6],
            ['code' => '605100', 'label' => 'Fournitures non stockables - Électricité', 'class_digit' => 6],
            ['code' => '605200', 'label' => 'Fournitures non stockables - Eau', 'class_digit' => 6],
            ['code' => '611000', 'label' => 'Transports de marchandises', 'class_digit' => 6],
            ['code' => '618100', 'label' => 'Voyages et déplacements professionnels', 'class_digit' => 6],
            ['code' => '621100', 'label' => 'Sous-traitance d\'exploitation', 'class_digit' => 6],
            ['code' => '624100', 'label' => 'Publicité et marketing', 'class_digit' => 6],
            ['code' => '632400', 'label' => 'Honoraires comptables', 'class_digit' => 6],
            ['code' => '661100', 'label' => 'Charges de personnel - Salaires bruts', 'class_digit' => 6],

            // CLASS 7 — PRODUITS
            ['code' => '701100', 'label' => 'Ventes de marchandises au Cameroun', 'class_digit' => 7],
            ['code' => '706000', 'label' => 'Prestations de services', 'class_digit' => 7],
        ];

        foreach ($accounts as $account) {
            SyscohadaAccount::updateOrCreate(['code' => $account['code']], $account);
        }
    }
}
