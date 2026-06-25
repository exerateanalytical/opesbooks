@extends('layouts.marketing')

@section('content')
<!-- Hero -->
<section class="relative overflow-hidden">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 70% 60% at 50% 0%,rgba(201,155,14,0.12) 0%,transparent 70%)"></div>
    <div class="max-w-7xl mx-auto px-5 pt-20 pb-16 text-center relative z-10">
        <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest text-gold mb-6" style="background:rgba(201,155,14,0.1);border:1px solid rgba(201,155,14,0.3)">CM · Conçu pour le Cameroun</span>
        <h1 class="text-4xl md:text-6xl font-black leading-tight tracking-tight">
            Gérez votre entreprise.<br><span class="text-gold">Maîtrisez vos impôts.</span>
        </h1>
        <p class="text-white/60 text-lg max-w-2xl mx-auto mt-6">
            La seule plateforme comptable conçue nativement pour les PME camerounaises. SYSCOHADA, DGI, TVA 19,25% et DSF — tout en un.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-3 mt-8">
            <a href="/login" class="px-6 py-3.5 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition">Commencer gratuitement — 30 jours</a>
            <a href="{{ route('m.features') }}" class="px-6 py-3.5 rounded-xl text-sm font-bold text-white glass hover:bg-white/10 transition">Voir les fonctionnalités →</a>
        </div>
        <div class="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 mt-7 text-xs text-white/50">
            <span>✓ Aucune carte bancaire requise</span><span>✓ SYSCOHADA certifié</span><span>✓ Fonctionne hors ligne</span>
        </div>
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
        <div class="glass rounded-xl p-5">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-gold mb-3" style="background:rgba(201,155,14,0.1)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $f[2] }}"/></svg>
            </div>
            <div class="font-black text-white text-sm">{{ $f[0] }}</div>
            <p class="text-white/50 text-xs mt-1 leading-relaxed">{{ $f[1] }}</p>
        </div>
        @endforeach
    </div>
</section>

<!-- Comparison -->
<section class="max-w-4xl mx-auto px-5 py-16">
    <h2 class="text-2xl md:text-3xl font-black text-center">La seule solution 100% camerounaise</h2>
    <p class="text-white/50 text-center mt-2 text-sm">Contrairement aux logiciels importés, OPESBooks épouse votre quotidien.</p>
    <div class="glass rounded-2xl overflow-hidden mt-8">
        <table class="w-full text-sm">
            <thead><tr class="text-xs uppercase tracking-widest text-white/40 border-b border-white/10">
                <th class="text-left p-4">Fonctionnalité</th><th class="p-4 text-gold">OPESBooks</th><th class="p-4 text-white/40">Logiciels importés</th>
            </tr></thead>
            <tbody class="divide-y divide-white/5">
                @foreach(['DGI Cameroun intégré','DSF / D10 export','Patente camerounaise','Fonctionne hors ligne','Prix en XAF','SYSCOHADA natif','Support local'] as $row)
                <tr><td class="p-4 text-white/80">{{ $row }}</td>
                    <td class="p-4 text-center text-emerald-400 font-black">✓</td>
                    <td class="p-4 text-center text-red-400 font-black">✗</td></tr>
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
            <div class="text-xs font-black uppercase tracking-widest text-gold">{{ $p->name }}</div>
            <div class="text-2xl font-black text-white mt-2">{{ $p->price_xaf_monthly ? number_format($p->price_xaf_monthly,0,',',' ').' XAF' : ($p->slug==='enterprise' ? 'Sur devis' : 'Gratuit') }}</div>
            <div class="text-white/40 text-xs">{{ $p->slug==='enterprise' ? '' : '/mois' }}</div>
            <a href="{{ route('m.pricing') }}" class="block mt-4 text-xs font-black text-gold hover:underline">Voir le détail →</a>
        </div>
        @endforeach
    </div>
</section>

<!-- Final CTA -->
<section class="max-w-4xl mx-auto px-5 py-16 text-center">
    <div class="glass rounded-3xl p-10" style="background:linear-gradient(145deg,rgba(201,155,14,0.08),rgba(255,255,255,0.03))">
        <h2 class="text-2xl md:text-3xl font-black">Prêt à simplifier votre comptabilité ?</h2>
        <a href="/login" class="inline-block mt-6 px-8 py-4 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition">Commencer gratuitement →</a>
        <p class="text-white/40 text-xs mt-4">30 jours gratuits · Aucune carte requise · Annulez à tout moment</p>
    </div>
</section>
@endsection
