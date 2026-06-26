@extends('layouts.marketing')
@section('title', 'Tarifs OPESBooks — À partir de 5 000 XAF/mois | PME Cameroun')
@section('description', 'Tarifs OPESBooks en Francs CFA : plan gratuit, Starter 5 000 XAF/mois, Growth 15 000 XAF/mois, Enterprise sur devis. Paiement Orange Money, MTN MoMo. 30 jours gratuits.')

@section('content')

<!-- Hero -->
<section class="relative overflow-hidden pt-20 pb-12 text-center px-5">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 700px 350px at 50% -80px,rgba(201,155,14,0.1),transparent)"></div>
    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest text-gold mb-6" style="background:rgba(201,155,14,0.1);border:1px solid rgba(201,155,14,0.3)">Tarifs simples en XAF</span>
    <h1 class="text-4xl md:text-6xl font-black leading-tight max-w-4xl mx-auto">Commencez gratuitement.<br><span class="text-gold">Grandissez à votre rythme.</span></h1>
    <p class="text-white/60 mt-6 max-w-xl mx-auto text-base leading-relaxed">Pas de carte bancaire internationale. Payez en Orange Money, MTN MoMo ou virement. Sans engagement.</p>
    <div class="flex flex-wrap justify-center gap-6 mt-8 text-sm text-white/50">
        <span>✓ 30 jours gratuits</span>
        <span>✓ Aucune carte requise</span>
        <span>✓ Annulez à tout moment</span>
        <span>✓ Paiement Mobile Money</span>
    </div>
</section>

<!-- Plans -->
<section class="max-w-7xl mx-auto px-5 pb-16">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @foreach($plans as $p)
        @php $popular = $p->slug === 'growth' || $p->slug === 'business'; @endphp
        <div class="glass rounded-2xl p-6 flex flex-col relative {{ $popular ? 'ring-1 ring-gold' : '' }}">
            @if($popular)
            <span class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest text-[#010048] bg-gold whitespace-nowrap">⭐ Populaire</span>
            @endif
            <div class="text-[11px] font-black uppercase tracking-widest text-gold mb-3">{{ $p->name }}</div>
            <div class="flex items-end gap-1 mb-1">
                <span class="text-4xl font-black text-white leading-none">
                    {{ $p->price_xaf_monthly ? number_format($p->price_xaf_monthly,0,',',' ') : ($p->slug==='enterprise' ? 'Sur devis' : 'Gratuit') }}
                </span>
                @if($p->price_xaf_monthly)<span class="text-white/40 text-sm pb-1">XAF</span>@endif
            </div>
            @if($p->price_xaf_monthly)<div class="text-white/30 text-xs mb-4">par mois, HT</div>@else<div class="mb-4"></div>@endif

            <p class="text-white/55 text-xs leading-relaxed mb-5">
                @if($p->slug==='free') Idéal pour démarrer et tester la plateforme, sans engagement.
                @elseif($p->slug==='starter') Pour les indépendants et très petites entreprises qui facturent régulièrement.
                @elseif($p->slug==='growth' || $p->slug==='business') La solution complète pour une PME en croissance — comptabilité, paie, DGI et IA.
                @else Pour les groupes, cabinets comptables et entreprises à besoins spécifiques.
                @endif
            </p>

            <ul class="space-y-2.5 flex-1 mb-6">
                @php
                $features = [
                    'free'       => ['1 utilisateur','50 factures/mois','Journal & Grand Livre','Plan SYSCOHADA','Calculateur TVA/CAC','Export DSF basique'],
                    'starter'    => ['3 utilisateurs','200 factures/mois','Tout le plan gratuit','Devis & avoirs','Factures PDF personnalisées','CRM basique','Support email'],
                    'growth'     => [($p->max_users===-1?'Utilisateurs illimités':$p->max_users.' utilisateurs'),'Factures illimitées','Tout le plan Starter','Paie CNPS/DIPE/IRPP','Immobilisations','Rapports financiers complets','Budgets & prévisionnel','IA comptable','API 1 000 appels/h','Webhooks','Support prioritaire'],
                    'business'   => [($p->max_users===-1?'Utilisateurs illimités':$p->max_users.' utilisateurs'),'Factures illimitées','Tout le plan Starter','Paie CNPS/DIPE/IRPP','Immobilisations','Rapports financiers complets','Budgets & prévisionnel','IA comptable','API 1 000 appels/h','Webhooks','Support prioritaire'],
                    'enterprise' => ['Tout illimité','Multi-société','API illimitée','SLA garanti','Déploiement on-premise','Intégration ERP sur mesure','Compte manager dédié','Formation & onboarding'],
                ];
                $list = $features[$p->slug] ?? $features['growth'];
                @endphp
                @foreach($list as $feat)
                <li class="flex items-start gap-2 text-xs text-white/70">
                    <span class="text-gold mt-0.5 shrink-0">✓</span>{{ $feat }}
                </li>
                @endforeach
            </ul>

            <a href="{{ $p->slug==='enterprise' ? route('m.contact') : '/login' }}"
               class="block text-center px-4 py-3 rounded-xl text-sm font-black transition
               {{ $popular ? 'bg-gold hover:bg-gold-light text-[#010048]' : 'glass hover:bg-white/10 text-white' }}">
                @if($p->slug==='free') Commencer gratuitement
                @elseif($p->slug==='enterprise') Nous contacter
                @else Choisir {{ $p->name }} →
                @endif
            </a>
        </div>
        @endforeach
    </div>
    <p class="text-center text-white/30 text-xs mt-6">Tous les prix sont HT. TVA 19,25 % applicable sur facture si assujetti.</p>
</section>

<!-- Feature comparison table -->
<section class="max-w-5xl mx-auto px-5 pb-16">
    <h2 class="text-2xl md:text-3xl font-black text-center mb-10">Comparaison détaillée</h2>
    <div class="glass rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/10">
                        <th class="text-left px-5 py-4 text-white/40 text-xs font-black uppercase tracking-widest w-1/2">Fonctionnalité</th>
                        @foreach(['Gratuit','Starter','Growth','Enterprise'] as $h)
                        <th class="px-4 py-4 text-center text-xs font-black uppercase tracking-widest {{ $h==='Growth' ? 'text-gold' : 'text-white/50' }}">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-xs">
                    @php
                    $rows = [
                        ['🧾 Comptabilité & Journal','','','',''],
                        ['Journal SYSCOHADA','✓','✓','✓','✓'],
                        ['Grand Livre & Balance','✓','✓','✓','✓'],
                        ['Clôture d\'exercice','—','✓','✓','✓'],
                        ['Export DSF / D10','Basique','✓','✓','✓'],
                        ['Moniteur DGI / MECeF','—','—','✓','✓'],
                        ['📄 Ventes & Achats','','','',''],
                        ['Factures clients PDF','50/mois','200/mois','Illimité','Illimité'],
                        ['Devis & avoirs','—','✓','✓','✓'],
                        ['Factures fournisseurs','✓','✓','✓','✓'],
                        ['Factures récurrentes','—','✓','✓','✓'],
                        ['👥 Équipe & Rôles','','','',''],
                        ['Utilisateurs','1','3','Illimité','Illimité'],
                        ['Rôles RBAC','—','✓','✓','✓'],
                        ['Double authentification (2FA)','—','✓','✓','✓'],
                        ['📊 Pilotage','','','',''],
                        ['Tableau de bord KPI','✓','✓','✓','✓'],
                        ['Rapports financiers (P&L, Bilan)','—','—','✓','✓'],
                        ['Budgets & prévisionnel','—','—','✓','✓'],
                        ['CRM & Projets','—','Basique','✓','✓'],
                        ['💰 Paie & Social','','','',''],
                        ['Bulletins de paie CNPS/IRPP','—','—','✓','✓'],
                        ['Export DIPE','—','—','✓','✓'],
                        ['🤖 IA & Intégrations','','','',''],
                        ['Assistant IA comptable','—','—','✓','✓'],
                        ['API REST','—','—','1 000/h','Illimitée'],
                        ['Webhooks HMAC','—','—','✓','✓'],
                        ['Hors ligne (PWA)','✓','✓','✓','✓'],
                        ['Multi-société','—','—','—','✓'],
                        ['Déploiement on-premise','—','—','—','✓'],
                        ['🎧 Support','','','',''],
                        ['Support email','—','✓','✓','✓'],
                        ['Support prioritaire','—','—','✓','✓'],
                        ['Manager dédié','—','—','—','✓'],
                    ];
                    @endphp
                    @foreach($rows as $row)
                    @if($row[1]==='' && $row[2]==='' && $row[3]==='' && $row[4]==='')
                    <tr class="bg-white/[0.02]">
                        <td colspan="5" class="px-5 py-2.5 text-[11px] font-black uppercase tracking-widest text-white/30">{{ $row[0] }}</td>
                    </tr>
                    @else
                    <tr class="hover:bg-white/[0.02]">
                        <td class="px-5 py-3 text-white/70">{{ $row[0] }}</td>
                        @foreach(array_slice($row,1) as $i => $v)
                        <td class="px-4 py-3 text-center {{ $v==='✓'?'text-emerald-400 font-bold':($v==='—'?'text-white/20':($i===2?'text-gold font-bold':'text-white/70')) }}">{{ $v }}</td>
                        @endforeach
                    </tr>
                    @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Payment methods -->
<section class="max-w-4xl mx-auto px-5 pb-16">
    <h2 class="text-xl md:text-2xl font-black text-center mb-8">Moyens de paiement acceptés</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach([
            ['MTN Mobile Money','Paiement direct par STK push — saisissez votre numéro MoMo et confirmez sur votre téléphone.','#FFCC00'],
            ['Orange Money (Flooz)','Paiement par Orange Money depuis votre tableau de bord, en quelques secondes.','#FF6600'],
            ['Virement bancaire','Virement en XAF pour les plans Enterprise ou les clients avec compte bancaire local (SGC, Afriland, UBA…).','#64748B'],
        ] as [$name, $desc, $color])
        <div class="glass rounded-xl p-5 flex gap-4 items-start">
            <div class="w-10 h-10 rounded-lg shrink-0 flex items-center justify-center font-black text-xs" style="background:{{ $color }}22;border:1px solid {{ $color }}44;color:{{ $color }}">{{ substr($name,0,2) }}</div>
            <div>
                <div class="font-bold text-white text-sm">{{ $name }}</div>
                <p class="text-white/50 text-xs mt-1 leading-relaxed">{{ $desc }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- FAQ -->
<section class="max-w-3xl mx-auto px-5 pb-20">
    <h2 class="text-xl md:text-2xl font-black text-center mb-8">Questions sur les tarifs</h2>
    <div class="space-y-2">
        @foreach([
            ['Puis-je changer de plan à tout moment ?','Oui, à tout moment depuis votre espace Abonnement. La mise à niveau est immédiate. La rétrogradation prend effet au prochain cycle de facturation.'],
            ['Y a-t-il un engagement de durée ?','Non. Tous nos plans sont sans engagement. Vous pouvez annuler à tout moment et exporter vos données.'],
            ['Que se passe-t-il après les 30 jours gratuits ?','Votre compte passe automatiquement en plan Gratuit. Aucune facturation automatique — vous choisissez vous-même si et quand vous souhaitez passer à un plan payant.'],
            ['Les prix incluent-ils la TVA ?','Les prix affichés sont HT. La TVA camerounaise de 19,25 % s\'applique si vous êtes assujetti et que votre entreprise est domiciliée au Cameroun.'],
            ['Est-ce que je peux obtenir une facture ?','Oui. Chaque paiement génère une facture PDF téléchargeable depuis votre tableau de bord, avec vos coordonnées NIU/RCCM.'],
            ['Proposez-vous des tarifs ONG / associations ?','Oui. Contactez-nous à contact@opesware.com pour discuter d\'un tarif adapté.'],
        ] as $faq)
        <div x-data="{open:false}" class="glass rounded-xl">
            <button @click="open=!open" class="w-full text-left px-5 py-4 flex justify-between items-center gap-4">
                <span class="text-sm font-bold text-white">{{ $faq[0] }}</span>
                <span x-text="open?'−':'+'" class="text-gold text-lg shrink-0"></span>
            </button>
            <div x-show="open" x-cloak class="px-5 pb-4 text-sm text-white/60 leading-relaxed">{{ $faq[1] }}</div>
        </div>
        @endforeach
    </div>

    <div class="glass rounded-2xl p-8 mt-10 text-center" style="background:linear-gradient(145deg,rgba(201,155,14,0.08),rgba(255,255,255,0.02))">
        <h2 class="text-xl font-black">Encore des questions ?</h2>
        <p class="text-white/55 text-sm mt-2">Notre équipe à Douala vous répond sous 24h.</p>
        <div class="flex flex-wrap justify-center gap-3 mt-5">
            <a href="/login" class="px-6 py-3 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition">Commencer gratuitement →</a>
            <a href="{{ route('m.contact') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-white glass hover:bg-white/10 transition">Parler à un conseiller</a>
        </div>
    </div>
</section>
@endsection
