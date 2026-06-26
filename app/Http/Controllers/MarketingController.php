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
<p>Opesware — Douala, Cameroun — <a href="mailto:contact@opesware.com">contact@opesware.com</a>.</p>
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
