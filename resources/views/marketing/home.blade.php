@extends('layouts.marketing')

@section('content')
<!-- Hero -->
<section class="relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 70% 55% at 50% 0%,rgba(245,158,11,0.10) 0%,transparent 65%)"></div>
    <div class="max-w-7xl mx-auto px-5 pt-20 pb-16 text-center relative z-10">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest text-gold mb-6" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.30)">CM · Conçu pour le Cameroun</span>
        <h1 class="text-4xl md:text-6xl font-black leading-tight tracking-tight">
            Gérez votre entreprise.<br><span class="text-gold">Maîtrisez vos impôts.</span>
        </h1>
        <p class="text-lg max-w-2xl mx-auto mt-6 leading-relaxed" style="color:var(--c-muted)">
            La seule plateforme comptable conçue nativement pour les PME camerounaises. SYSCOHADA, DGI, TVA 19,25% et DSF — tout en un.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3 mt-8">
            <a href="/login" class="px-6 py-3.5 rounded-xl text-sm font-black transition" style="background:var(--c-accent);color:#0F172A">Commencer gratuitement — 30 jours</a>
            <a href="{{ route('m.features') }}" class="px-6 py-3.5 rounded-xl text-sm font-semibold text-white transition glass hover:bg-white/5">Voir les fonctionnalités →</a>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 mt-7 text-xs" style="color:var(--c-faint)">
            <span>✓ Aucune carte bancaire requise</span><span>✓ SYSCOHADA certifié</span><span>✓ Fonctionne hors ligne</span>
        </div>
    </div>
</section>

<!-- Trust / stats band -->
<section class="max-w-5xl mx-auto px-5 pb-4">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach([['19,25 %','TVA + CAC calculés'],['6 pays','Zone CEMAC'],['30 jours','Essai gratuit'],['Hors ligne','Avec ou sans Internet']] as $s)
        <div class="glass rounded-xl p-4 text-center">
            <div class="text-xl md:text-2xl font-black text-gold">{{ $s[0] }}</div>
            <div class="text-xs mt-1" style="color:var(--c-muted)">{{ $s[1] }}</div>
        </div>
        @endforeach
    </div>
</section>

<!-- Features grid -->
<section class="max-w-7xl mx-auto px-5 py-16">
    <h2 class="text-2xl md:text-3xl font-black text-center">Tout ce dont votre PME a besoin</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-10">
        @php $features = [
            ['Journal & Grand Livre','Écritures SYSCOHADA, balance, grand livre.','M4 19.5A2.5 2.5 0 0 1 6.5 17H20'],
            ['Facturation & TVA','Factures PDF, TVA 19,25%, numérotation auto.','M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z'],
            ['Déclaration DSF / D10','Export fiscal prêt pour la DGI.','M9 17v-6h6v6M4 7h16'],
            ['Moniteur DGI','Télétransmission Fiscalis/SIGIT en temps réel.','M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
            ['Paie & CNPS/DIPE','Bulletins, cotisations, bordereaux.','M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
            ['CRM & Projets','Pipeline commercial, rentabilité par projet.','M22 7 13.5 15.5 8.5 10.5 2 17'],
            ['IA comptable','Catégorisation et contrôles assistés par IA.','M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9z'],
            ['Mobile Money','Ingestion MTN MoMo & Orange Money.','M5 2h14a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z'],
            ['Sync hors ligne','Saisie sans internet, synchro automatique.','M1 1l22 22M16.7 11A6 6 0 0 0 8.5 16.1'],
        ]; @endphp
        @foreach($features as $f)
        <div class="glass rounded-xl p-5 hover:border-border transition">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-gold mb-3" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.20)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $f[2] }}"/></svg>
            </div>
            <div class="font-bold text-white text-sm">{{ $f[0] }}</div>
            <p class="text-xs mt-1 leading-relaxed" style="color:var(--c-muted)">{{ $f[1] }}</p>
        </div>
        @endforeach
    </div>
    <div class="text-center mt-8"><a href="{{ route('m.features') }}" class="text-gold text-sm font-bold hover:underline">Voir les 45+ fonctionnalités →</a></div>
</section>

<!-- Comparison -->
<section class="max-w-4xl mx-auto px-5 py-16">
    <h2 class="text-2xl md:text-3xl font-black text-center">La seule solution 100% camerounaise</h2>
    <p class="text-center mt-2 text-sm" style="color:var(--c-muted)">Contrairement aux logiciels importés, OPESBooks épouse votre quotidien.</p>
    <div class="glass rounded-2xl overflow-hidden mt-8">
        <table class="w-full text-sm">
            <thead><tr class="text-xs uppercase tracking-widest border-b" style="color:var(--c-faint);border-color:var(--c-border)">
                <th class="text-left p-4">Fonctionnalité</th><th class="p-4 text-gold">OPESBooks</th><th class="p-4" style="color:var(--c-faint)">Logiciels importés</th>
            </tr></thead>
            <tbody style="border-color:var(--c-border)">
                @foreach(['DGI Cameroun intégré','DSF / D10 export','Patente camerounaise','Fonctionne hors ligne','Prix en XAF','SYSCOHADA natif','Support local'] as $row)
                <tr style="border-bottom:1px solid rgba(51,65,85,0.5)">
                    <td class="p-4 text-white/80">{{ $row }}</td>
                    <td class="p-4 text-center text-emerald-400 font-black">✓</td>
                    <td class="p-4 text-center text-red-400 font-black">✗</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<!-- Pricing teaser -->
<section class="max-w-7xl mx-auto px-5 py-16">
    <h2 class="text-2xl md:text-3xl font-black text-center">À partir de 5 000 XAF/mois</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-10">
        @foreach($plans as $p)
        <div class="glass rounded-2xl p-5 text-center {{ $p->slug==='business' ? 'ring-1 ring-gold' : '' }}">
            <div class="text-xs font-bold uppercase tracking-widest text-gold">{{ $p->name }}</div>
            <div class="text-2xl font-black text-white mt-2">{{ $p->price_xaf_monthly ? number_format($p->price_xaf_monthly,0,',',' ').' XAF' : ($p->slug==='enterprise' ? 'Sur devis' : 'Gratuit') }}</div>
            <div class="text-xs" style="color:var(--c-faint)">{{ $p->slug==='enterprise' ? '' : '/mois' }}</div>
            <a href="{{ route('m.pricing') }}" class="block mt-4 text-xs font-bold text-gold hover:underline">Voir le détail →</a>
        </div>
        @endforeach
    </div>
</section>

<!-- How it works -->
<section class="max-w-5xl mx-auto px-5 py-16">
    <h2 class="text-2xl md:text-3xl font-black text-center">Démarrez en 3 étapes</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-10">
        @foreach([
            ['1','Créez votre compte','Renseignez votre profil fiscal : NIU, RCCM, centre fiscal et régime d\'imposition.'],
            ['2','Saisissez ou importez','Importez vos clients, fournisseurs et écritures par CSV, ou saisissez au quotidien — même hors ligne.'],
            ['3','Déclarez sereinement','Générez factures, DSF, bulletins de paie et suivez vos échéances DGI en temps réel.'],
        ] as $st)
        <div class="glass rounded-2xl p-6">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-black text-sm" style="background:var(--c-accent);color:#0F172A">{{ $st[0] }}</div>
            <div class="font-bold text-white mt-4">{{ $st[1] }}</div>
            <p class="text-sm mt-1.5 leading-relaxed" style="color:var(--c-muted)">{{ $st[2] }}</p>
        </div>
        @endforeach
    </div>
</section>

<!-- CEMAC coverage -->
<section class="max-w-5xl mx-auto px-5 py-16">
    <div class="glass rounded-3xl p-8 md:p-10 text-center" style="background:linear-gradient(145deg,rgba(245,158,11,0.06),rgba(30,41,59,0.4))">
        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-widest text-gold mb-4" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.28)">Zone CEMAC · OHADA</span>
        <h2 class="text-2xl md:text-3xl font-black">Conçu pour le Cameroun, prêt pour l'Afrique centrale</h2>
        <p class="mt-3 max-w-2xl mx-auto text-sm leading-relaxed" style="color:var(--c-muted)">Base comptable SYSCOHADA commune, monnaie XAF, et configurations fiscales par pays — développez votre activité au-delà des frontières sans changer d'outil.</p>
        <div class="flex flex-wrap justify-center gap-2 mt-6">
            @foreach(['Cameroun','Gabon','Congo','Tchad','RCA','Guinée Équatoriale'] as $pays)
            <span class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white/80 glass">{{ $pays }}</span>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ teaser -->
<section class="max-w-3xl mx-auto px-5 py-16">
    <h2 class="text-2xl md:text-3xl font-black text-center">Vos questions, nos réponses</h2>
    <p class="text-center mt-2 text-sm" style="color:var(--c-muted)">Tout sur la fiscalité camerounaise et OPESBooks.</p>
    <div class="mt-8 space-y-2">
        @foreach([
            ['Quel est le taux de TVA au Cameroun ?','17,5 % de TVA + 10 % de CAC sur la TVA, soit 19,25 % TTC. OPESBooks calcule tout automatiquement.'],
            ['OPESBooks fonctionne-t-il sans Internet ?','Oui. Saisie hors ligne, stockage local et synchronisation automatique dès le retour du réseau.'],
            ['Comment payer mon abonnement ?','Orange Money, MTN Mobile Money ou virement bancaire, en XAF.'],
        ] as $faq)
        <div x-data="{open:false}" class="glass rounded-xl">
            <button @click="open=!open" class="w-full text-left px-5 py-4 flex justify-between items-center gap-4 text-sm font-semibold"><span>{{ $faq[0] }}</span><span x-text="open?'−':'+'" class="text-gold text-lg shrink-0"></span></button>
            <div x-show="open" x-cloak class="px-5 pb-4 text-sm leading-relaxed" style="color:var(--c-muted)">{{ $faq[1] }}</div>
        </div>
        @endforeach
    </div>
    <div class="text-center mt-6"><a href="{{ route('m.faq') }}" class="text-gold text-sm font-bold hover:underline">Voir toutes les questions →</a></div>
</section>

<!-- Final CTA -->
<section class="max-w-4xl mx-auto px-5 py-16 text-center">
    <div class="glass rounded-3xl p-10" style="background:linear-gradient(145deg,rgba(245,158,11,0.07),rgba(30,41,59,0.5))">
        <h2 class="text-2xl md:text-3xl font-black">Prêt à simplifier votre comptabilité ?</h2>
        <a href="/login" class="inline-block mt-6 px-8 py-4 rounded-xl text-sm font-black transition" style="background:var(--c-accent);color:#0F172A">Commencer gratuitement →</a>
        <p class="text-xs mt-4" style="color:var(--c-faint)">30 jours gratuits · Aucune carte requise · Annulez à tout moment</p>
    </div>
</section>
@endsection
