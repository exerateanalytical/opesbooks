@extends('layouts.marketing')
@section('title', 'À propos — OPESBooks')
@section('description', 'OPESBooks est développé par Opesware, société camerounaise d\'ingénierie logicielle basée à Douala.')

@section('content')
<section class="max-w-3xl mx-auto px-5 py-16">
    <div class="text-center">
        <h1 class="text-3xl md:text-5xl font-black">À propos d'OPESBooks</h1>
        <p class="text-white/60 mt-4">Le bouclier fiscal des PME camerounaises.</p>
    </div>

    <div class="glass rounded-2xl p-8 mt-10 space-y-4 text-white/75 leading-relaxed">
        <p><strong class="text-white">OPESBooks</strong> est une plateforme SaaS de comptabilité et de conformité fiscale conçue spécifiquement pour les PME camerounaises. Elle automatise la tenue des livres selon le plan comptable <strong class="text-gold">SYSCOHADA Révisé</strong>, gère la TVA à 19,25% (TVA 17,5% + CAC), et assure la télétransmission à la <strong class="text-white">DGI</strong> via Fiscalis/SIGIT, conformément à la Loi de Finances 2026.</p>
        <p>Pensée <strong class="text-white">hors ligne d'abord</strong>, la plateforme fonctionne même avec une connexion instable et synchronise automatiquement dès le retour d'Internet — une réalité du terrain camerounais.</p>
    </div>

    <div class="glass rounded-2xl p-8 mt-6">
        <div class="text-xs font-black uppercase tracking-widest text-gold mb-4">Développé par</div>
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-black text-indigo-300" style="background:rgba(99,102,241,0.2);border:1px solid rgba(99,102,241,0.35)">OW</div>
            <div>
                <div class="font-black text-white text-lg">OPESWARE</div>
                <div class="text-white/50 text-sm">Software Engineering · Douala, Cameroun</div>
            </div>
        </div>
        <p class="text-white/60 text-sm mt-4 leading-relaxed">Opesware est une société camerounaise d'ingénierie logicielle spécialisée dans les solutions de gestion financière, de conformité fiscale et de transformation numérique pour les entreprises de la zone OHADA.</p>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-6 text-center">
        @foreach([['SYSCOHADA','Plan OHADA révisé'],['TVA 19,25%','TVA + CAC'],['DGI Live-Link','Fiscalis/SIGIT'],['Loi Finances','2026']] as $b)
        <div class="glass rounded-xl p-4"><div class="text-gold font-black text-xs uppercase tracking-wider">{{ $b[0] }}</div><div class="text-white/40 text-[11px] mt-1">{{ $b[1] }}</div></div>
        @endforeach
    </div>

    <div class="text-center mt-10">
        <a href="/login" class="inline-block px-6 py-3.5 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition">Accéder à l'application →</a>
    </div>
</section>
@endsection
