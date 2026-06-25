@extends('layouts.marketing')
@section('title', 'Tarifs — OPESBooks')
@section('description', 'Tarifs OPESBooks : Free, Starter (5 000 XAF/mois), Business (15 000 XAF/mois), Enterprise. 30 jours gratuits.')

@section('content')
<section class="max-w-7xl mx-auto px-5 py-16">
    <div class="text-center">
        <h1 class="text-3xl md:text-5xl font-black">Des tarifs simples, en XAF</h1>
        <p class="text-white/60 mt-4">Commencez gratuitement. Passez à un plan supérieur quand vous grandissez.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-12">
        @foreach($plans as $p)
        <div class="glass rounded-2xl p-6 flex flex-col {{ $p->slug==='business' ? 'ring-1 ring-gold relative' : '' }}">
            @if($p->slug==='business')<span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest text-[#010048] bg-gold">Populaire</span>@endif
            <div class="text-sm font-black uppercase tracking-widest text-gold">{{ $p->name }}</div>
            <div class="text-3xl font-black text-white mt-3">
                {{ $p->price_xaf_monthly ? number_format($p->price_xaf_monthly,0,',',' ') : ($p->slug==='enterprise' ? 'Sur devis' : '0') }}
                @if($p->slug!=='enterprise')<span class="text-sm text-white/40 font-normal">XAF/mois</span>@endif
            </div>
            <ul class="mt-5 space-y-2 text-sm text-white/70 flex-1">
                <li>✓ {{ $p->max_users === -1 ? 'Utilisateurs illimités' : $p->max_users.' utilisateur'.($p->max_users>1?'s':'') }}</li>
                <li>✓ {{ $p->max_invoices_per_month === -1 ? 'Factures illimitées' : $p->max_invoices_per_month.' factures/mois' }}</li>
                <li>{{ $p->api_calls_per_hour === 0 ? '✗ Pas d\'API' : '✓ API '.($p->api_calls_per_hour===-1?'illimitée':number_format($p->api_calls_per_hour,0,',',' ').'/h') }}</li>
                <li>{{ in_array($p->slug,['starter','business','enterprise']) ? '✓ CRM, Projets, IA' : '✗ Modules avancés' }}</li>
                <li>{{ in_array($p->slug,['business','enterprise']) ? '✓ Paie & rapports avancés' : '' }}</li>
            </ul>
            <a href="/login" class="block mt-6 text-center px-4 py-3 rounded-xl text-sm font-black transition {{ $p->slug==='free' ? 'glass hover:bg-white/10 text-white' : 'bg-gold hover:bg-gold-light text-[#010048]' }}">
                {{ $p->slug==='free' ? 'Commencer' : ($p->slug==='enterprise' ? 'Nous contacter' : 'Choisir ce plan') }}
            </a>
        </div>
        @endforeach
    </div>

    <div class="max-w-2xl mx-auto mt-16">
        <h2 class="text-xl font-black text-center mb-6">Questions fréquentes</h2>
        @foreach([
            ['Puis-je changer de plan ?','Oui, à tout moment depuis votre espace. Le changement est immédiat.'],
            ['Y a-t-il un engagement ?','Non. Sans engagement, annulez quand vous voulez.'],
            ['Comment payer ?','Orange Money, MTN MoMo ou virement bancaire, en XAF.'],
            ['Mes données sont-elles en sécurité ?','Oui — chiffrement, 2FA, et hébergement africain.'],
        ] as $faq)
        <div x-data="{open:false}" class="glass rounded-xl mb-2">
            <button @click="open=!open" class="w-full text-left px-5 py-4 flex justify-between items-center text-sm font-bold">
                <span>{{ $faq[0] }}</span><span x-text="open?'−':'+'" class="text-gold"></span>
            </button>
            <div x-show="open" x-cloak class="px-5 pb-4 text-sm text-white/60">{{ $faq[1] }}</div>
        </div>
        @endforeach
    </div>
</section>
@endsection
