@extends('layouts.marketing')
@section('title', $title . ' — OPESBooks')
@section('description', $title . " d'OPESBooks, plateforme de comptabilité et conformité fiscale éditée par Opesware (Douala, Cameroun).")

@section('content')
<style>
    .legal{font-size:.95rem;line-height:1.75;color:rgba(226,232,240,0.82)}
    .legal h2{font-size:1.25rem;font-weight:800;color:#fff;margin:2rem 0 .6rem}
    .legal p{margin:0 0 .9rem}.legal ul{margin:0 0 .9rem;padding-left:1.3rem;list-style:disc}
    .legal li{margin:.3rem 0}.legal strong{color:#fff}.legal a{color:#C99B0E;text-decoration:underline}
</style>
<article class="max-w-3xl mx-auto px-5 py-16">
    <h1 class="text-3xl md:text-4xl font-black">{{ $title }}</h1>
    <p class="text-white/40 text-xs mt-3">Dernière mise à jour : {{ now()->translatedFormat('F Y') }}</p>
    <div class="legal mt-8">{!! $content !!}</div>
    <div class="glass rounded-xl p-5 mt-10 text-sm text-white/60">
        Pour toute question juridique, écrivez à <a href="mailto:contact@opesware.cm" class="text-gold">contact@opesware.cm</a>.
    </div>
</article>
@endsection
