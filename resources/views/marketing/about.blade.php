@extends('layouts.marketing')
@section('title', 'À propos d\'OPESBooks — Opesware, Douala, Cameroun')
@section('description', 'OPESBooks est développé par Opesware, société camerounaise d\'ingénierie logicielle basée à Douala. Notre mission : donner aux PME africaines une comptabilité de précision, sans complexité.')

@section('content')

<!-- Hero -->
<section class="relative overflow-hidden pt-20 pb-14 text-center px-5">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 800px 400px at 50% -80px,rgba(245,158,11,0.12),transparent)"></div>
    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest text-gold mb-6" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.30)">Notre histoire</span>
    <h1 class="text-4xl md:text-6xl font-black leading-tight max-w-4xl mx-auto">Le bouclier fiscal des<br><span class="text-gold">PME camerounaises</span></h1>
    <p class="text-slate-400 mt-6 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">Nous avons fondé OPESBooks parce que les PME africaines méritent des outils comptables conçus pour leur réalité — pas des logiciels occidentaux mal adaptés.</p>
</section>

<!-- Mission -->
<section class="max-w-5xl mx-auto px-5 pb-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="glass rounded-2xl p-8 space-y-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-gold" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.22)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h2 class="text-xl font-black">Notre mission</h2>
            <p class="text-slate-400 text-sm leading-relaxed">Donner à chaque PME camerounaise — qu'elle soit à Douala, Yaoundé, Bafoussam ou Garoua — les mêmes outils de gestion financière que les grandes entreprises, à un prix accessible, en Francs CFA, avec un support en français et depuis le Cameroun.</p>
        </div>
        <div class="glass rounded-2xl p-8 space-y-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-gold" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.22)">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            </div>
            <h2 class="text-xl font-black">Notre vision</h2>
            <p class="text-slate-400 text-sm leading-relaxed">Un Cameroun et une zone CEMAC où chaque entrepreneur peut se concentrer sur son métier, en sachant que sa comptabilité est exacte, ses déclarations DGI à jour et ses factures conformes MECeF — automatiquement.</p>
        </div>
    </div>
</section>

<!-- The problem we solve -->
<section class="max-w-5xl mx-auto px-5 pb-16">
    <h2 class="text-2xl md:text-3xl font-black text-center mb-10">Pourquoi OPESBooks existe</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach([
            ['Le problème','Les PME camerounaises jonglent entre des logiciels européens qui ignorent le CAC, des tableurs Excel qui créent des erreurs d\'arrondi et des comptables débordés qui ne peuvent pas suivre les évolutions de la DGI en temps réel.','text-red-400','M6 18L18 6M6 6l12 12'],
            ['Notre approche','OPESBooks est conçu de zéro pour le Cameroun : TVA 19,25 % exacte (Brick\\Math), plan SYSCOHADA révisé pré-chargé, MECeF 2026 natif, Mobile Money ingéré automatiquement et fonctionnement hors ligne pour les zones à connectivité limitée.','text-amber-400','M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9z'],
            ['Le résultat','Des PME qui arrivent sereines à chaque déclaration DGI, qui génèrent des factures certifiables MECeF en un clic, et qui peuvent se concentrer sur leur croissance plutôt que sur leur conformité.','text-emerald-400','M9 12l2 2 4-4 M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z'],
        ] as [$title, $text, $col, $icon])
        <div class="glass rounded-2xl p-6 flex flex-col gap-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" style="background:rgba(30,41,59,0.6);border:1px solid rgba(41,53,72,1)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="{{ $col }}"><path d="{{ $icon }}"/></svg>
            </div>
            <h3 class="font-black text-white">{{ $title }}</h3>
            <p class="text-slate-400 text-sm leading-relaxed">{{ $text }}</p>
        </div>
        @endforeach
    </div>
</section>

<!-- Stats -->
<section class="max-w-5xl mx-auto px-5 pb-16">
    <div class="glass rounded-3xl p-8 md:p-10" style="background:linear-gradient(145deg,rgba(245,158,11,0.07),rgba(41,53,72,0.5))">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            @foreach([['2023','Année de création'],['Douala','Siège social'],['CEMAC','Zone couverte'],['XAF','Devise native']] as $s)
            <div>
                <div class="text-2xl md:text-3xl font-black text-gold">{{ $s[0] }}</div>
                <div class="text-slate-500 text-xs mt-1">{{ $s[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Opesware -->
<section class="max-w-5xl mx-auto px-5 pb-16">
    <h2 class="text-2xl md:text-3xl font-black text-center mb-10">Développé par Opesware</h2>
    <div class="glass rounded-2xl p-8 md:p-10">
        <div class="flex items-start gap-6 flex-wrap">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center font-black text-2xl text-indigo-300 shrink-0" style="background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.3)">OW</div>
            <div class="flex-1 min-w-0">
                <div class="font-black text-white text-xl">OPESWARE</div>
                <div class="text-slate-400 text-sm mt-0.5">Software Engineering · Douala, Cameroun</div>
                <p class="text-slate-400 text-sm mt-4 leading-relaxed max-w-2xl">Opesware est une société camerounaise d'ingénierie logicielle spécialisée dans les solutions de gestion financière, de conformité fiscale et de transformation numérique pour les entreprises de la zone OHADA. Nous construisons des outils qui comprennent le terrain : connexion instable, paiements Mobile Money, fiscalité évolutive et besoins bilingues français/anglais.</p>
            </div>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-8">
            @foreach([['SYSCOHADA','Plan OHADA révisé 2017'],['TVA 19,25 %','TVA + CAC exact'],['DGI Live-Link','Fiscalis/SIGIT'],['MECeF 2026','Loi de Finances']] as $b)
            <div class="text-center p-4 rounded-xl" style="background:rgba(30,41,59,0.4);border:1px solid rgba(41,53,72,0.8)">
                <div class="text-gold font-black text-xs uppercase tracking-wider">{{ $b[0] }}</div>
                <div class="text-slate-500 text-[11px] mt-1">{{ $b[1] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Values -->
<section class="max-w-5xl mx-auto px-5 pb-16">
    <h2 class="text-2xl md:text-3xl font-black text-center mb-10">Nos engagements</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach([
            ['Précision fiscale','Chaque centime compte. Nos calculs TVA/CAC utilisent l\'arithmétique décimale exacte — pas de virgule flottante, pas d\'erreur d\'arrondi sur vos factures.','M9 12l2 2 4-4'],
            ['Hors ligne d\'abord','Au Cameroun, la connexion est parfois le luxe. OPESBooks fonctionne sans internet et synchronise quand le réseau revient.','M1 1l22 22M16.72 11.06A10.94 10.94 0 0 1 19 12.55'],
            ['Prix accessible','À partir de 5 000 XAF/mois, payable par MTN MoMo ou Orange Money. Pas de carte bancaire internationale requise.','M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6'],
            ['Support local','Notre équipe est à Douala, parle français (et anglais) et connaît les réalités du terrain camerounais. Pas un centre d\'appels en Inde.','M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2'],
            ['Sécurité totale','2FA, rôles, journal d\'audit, chiffrement — vos données comptables sont aussi protégées que dans une banque.','M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
            ['Conformité continue','Les règles fiscales évoluent. Nous mettons à jour OPESBooks dès chaque nouvelle loi de finances — vous restez conforme sans rien faire.','M4 4v5h.582m15.356 2A8.001 8.001 0 0 0 4.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 0 1-15.357-2m15.357 2H15'],
        ] as [$title, $desc, $icon])
        <div class="glass rounded-xl p-5 flex gap-4">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.22)">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2"><path d="{{ $icon }}"/></svg>
            </div>
            <div>
                <div class="font-bold text-white text-sm mb-1">{{ $title }}</div>
                <p class="text-slate-400 text-xs leading-relaxed">{{ $desc }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Contact block -->
<section class="max-w-5xl mx-auto px-5 pb-20">
    <div class="glass rounded-2xl p-8 md:p-10 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="col-span-1 md:col-span-2">
            <h2 class="text-xl md:text-2xl font-black">Vous voulez en savoir plus ?</h2>
            <p class="text-slate-400 text-sm mt-2 leading-relaxed">Parlez à notre équipe depuis Douala. Nous répondons en français et en anglais, sous 24h en jours ouvrés.</p>
            <div class="flex flex-wrap gap-3 mt-6">
                <a href="{{ route('m.contact') }}" class="btn-primary">Nous contacter →</a>
                <a href="/login" class="btn-secondary">Essai gratuit 30 jours</a>
            </div>
        </div>
        <div class="space-y-3 text-sm">
            @foreach([['📍','Petite Terrain, Bonamoussadi, Douala'],['✉️','contact@opesware.com'],['📞','+237 670 416 238'],['🌐','opesware.com']] as $c)
            <div class="flex items-start gap-3 text-slate-400">
                <span>{{ $c[0] }}</span><span>{{ $c[1] }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
