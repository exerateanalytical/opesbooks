<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Demo company
        $company = Company::firstOrCreate(
            ['niu' => 'M082000010'],
            [
                'name'                    => 'Acacia Sarl Demo',
                'rccm'                    => 'RC/DLA/2019/B/1234',
                'tax_regime'              => 'REEL',
                'tax_center'              => 'CIME Douala',
                'phone'                   => '+237 6 99 00 00 00',
                'email'                   => 'contact@acacia-demo.cm',
                'address'                 => 'Akwa, Douala, Cameroun',
                'letterhead_tagline'      => 'Solutions Numériques & Conseil',
                'letterhead_website'      => 'www.acacia-demo.cm',
                'bank_name'              => 'Afriland First Bank',
                'bank_account'           => '10005 00001 00123456789',
                'bank_rib'               => '12',
                'invoice_footer_note'    => 'Paiement à 30 jours. Pénalité de retard : 1,5%/mois.',
                'vat_prorata_coefficient' => 100.00,
                'subscription_status'    => 'ACTIVE',
            ]
        );

        // Owner account
        User::firstOrCreate(
            ['email' => 'owner@demo.cm'],
            [
                'company_id' => $company->id,
                'name'       => 'Marie Fouda',
                'password'   => Hash::make('demo1234'),
                'role'       => 'OWNER',
            ]
        );

        // Accountant account
        User::firstOrCreate(
            ['email' => 'accountant@demo.cm'],
            [
                'company_id' => $company->id,
                'name'       => 'Jean-Paul Mbarga',
                'password'   => Hash::make('demo1234'),
                'role'       => 'ACCOUNTANT',
            ]
        );
    }
}
