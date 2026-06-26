@extends('layouts.marketing')
@section('title', $module['title'] . ' — OPESBooks')
@section('description', $module['meta_desc'])

@section('content')
@php
$colors = [
    'amber'   => ['bg'=>'rgba(245,158,11,0.1)','border'=>'rgba(245,158,11,0.25)','text'=>'#F59E0B','glow'=>'rgba(245,158,11,0.15)'],
    'blue'    => ['bg'=>'rgba(59,130,246,0.1)','border'=>'rgba(59,130,246,0.25)','text'=>'#60A5FA','glow'=>'rgba(59,130,246,0.12)'],
    'emerald' => ['bg'=>'rgba(16,185,129,0.1)','border'=>'rgba(16,185,129,0.25)','text'=>'#34D399','glow'=>'rgba(16,185,129,0.12)'],
    'violet'  => ['bg'=>'rgba(139,92,246,0.1)','border'=>'rgba(139,92,246,0.25)','text'=>'#A78BFA','glow'=>'rgba(139,92,246,0.12)'],
    'cyan'    => ['bg'=>'rgba(6,182,212,0.1)','border'=>'rgba(6,182,212,0.25)','text'=>'#22D3EE','glow'=>'rgba(6,182,212,0.12)'],
    'orange'  => ['bg'=>'rgba(249,115,22,0.1)','border'=>'rgba(249,115,22,0.25)','text'=>'#FB923C','glow'=>'rgba(249,115,22,0.12)'],
    'slate'   => ['bg'=>'rgba(100,116,139,0.1)','border'=>'rgba(100,116,139,0.25)','text'=>'#94A3B8','glow'=>'rgba(100,116,139,0.12)'],
    'teal'    => ['bg'=>'rgba(20,184,166,0.1)','border'=>'rgba(20,184,166,0.25)','text'=>'#2DD4BF','glow'=>'rgba(20,184,166,0.12)'],
    'pink'    => ['bg'=>'rgba(236,72,153,0.1)','border'=>'rgba(236,72,153,0.25)','text'=>'#F472B6','glow'=>'rgba(236,72,153,0.12)'],
    'indigo'  => ['bg'=>'rgba(99,102,241,0.1)','border'=>'rgba(99,102,241,0.25)','text'=>'#818CF8','glow'=>'rgba(99,102,241,0.12)'],
    'yellow'  => ['bg'=>'rgba(234,179,8,0.1)','border'=>'rgba(234,179,8,0.25)','text'=>'#FBBF24','glow'=>'rgba(234,179,8,0.12)'],
    'red'     => ['bg'=>'rgba(239,68,68,0.1)','border'=>'rgba(239,68,68,0.25)','text'=>'#F87171','glow'=>'rgba(239,68,68,0.12)'],
];
$c = $colors[$module['color']];
@endphp

<!-- Hero -->
<section class="relative overflow-hidden pt-16 pb-12 px-5">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 700px 350px at 50% -60px,{{ $c['glow'] }},transparent)"></div>
    <div class="max-w-5xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-8">
            <a href="{{ route('m.features') }}" class="hover:text-white transition">Fonctionnalités</a>
            <span>/</span>
            <span class="text-slate-300">{{ $module['title'] }}</span>
        </nav>
        <div class="flex items-start gap-5">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center shrink-0" style="background:{{ $c['bg'] }};border:1px solid {{ $c['border'] }}">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="{{ $c['text'] }}" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $module['icon'] }}"/></svg>
            </div>
            <div>
                <span class="inline-block text-[11px] font-black uppercase tracking-widest px-3 py-1 rounded-full mb-3" style="background:{{ $c['bg'] }};color:{{ $c['text'] }};border:1px solid {{ $c['border'] }}">{{ $module['tag'] }}</span>
                <h1 class="text-3xl md:text-5xl font-black leading-tight">{{ $module['title'] }}</h1>
                <p class="text-slate-400 mt-4 text-lg leading-relaxed max-w-3xl">{{ $module['headline'] }}</p>
            </div>
        </div>
        <div class="flex flex-wrap gap-3 mt-8">
            <a href="/login" class="px-6 py-3 rounded-xl text-sm font-black text-navy bg-gold hover:bg-gold-light transition">Essayer gratuitement →</a>
            <a href="{{ route('m.features') }}" class="px-6 py-3 rounded-xl text-sm font-bold text-white glass hover:bg-slate-700 transition">← Toutes les fonctionnalités</a>
        </div>
    </div>
</section>

<!-- All features list -->
<section class="max-w-5xl mx-auto px-5 pb-10">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($module['features'] as $feat)
        <div class="glass rounded-xl p-5 flex gap-4">
            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5" style="background:{{ $c['bg'] }};border:1px solid {{ $c['border'] }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $c['text'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $feat['icon'] ?? 'M9 12l2 2 4-4' }}"/></svg>
            </div>
            <div>
                <div class="font-bold text-white text-sm mb-1">{{ $feat['title'] }}</div>
                <p class="text-slate-400 text-xs leading-relaxed">{{ $feat['desc'] }}</p>
            </div>
        </div>
        @endforeach
    </div>
</section>

<!-- Why it matters for Cameroun -->
@if(!empty($module['context']))
<section class="max-w-5xl mx-auto px-5 pb-10">
    <div class="glass rounded-2xl p-8" style="background:linear-gradient(135deg,{{ $c['glow'] }},rgba(255,255,255,0.02))">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:{{ $c['bg'] }};border:1px solid {{ $c['border'] }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $c['text'] }}" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <h2 class="text-lg font-black" style="color:{{ $c['text'] }}">Spécifique au contexte camerounais</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($module['context'] as $ctx)
            <div class="flex gap-3 items-start">
                <span class="text-sm mt-0.5" style="color:{{ $c['text'] }}">●</span>
                <p class="text-slate-300 text-sm leading-relaxed">{{ $ctx }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Related modules -->
<section class="max-w-5xl mx-auto px-5 pb-20">
    <h2 class="text-xl font-black mb-5">Modules complémentaires</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        @foreach($related as $r)
        <a href="{{ route('m.feature', $r['slug']) }}" class="glass rounded-xl p-4 hover:bg-slate-700 transition text-center group">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center mx-auto mb-2" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1)">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="{{ $r['icon'] }}"/></svg>
            </div>
            <div class="text-xs font-bold text-slate-300 group-hover:text-white transition">{{ $r['title'] }}</div>
        </a>
        @endforeach
    </div>
</section>

<!-- CTA -->
<section class="max-w-4xl mx-auto px-5 pb-20 text-center">
    <div class="glass rounded-3xl p-10" style="background:linear-gradient(145deg,rgba(245,158,11,0.08),rgba(255,255,255,0.03))">
        <h2 class="text-2xl md:text-3xl font-black">Commencez avec {{ $module['title'] }}</h2>
        <p class="text-slate-400 mt-3 text-sm">Essai gratuit 30 jours · Sans carte bancaire · Support en français</p>
        <div class="flex flex-wrap justify-center gap-3 mt-6">
            <a href="/login" class="px-7 py-3.5 rounded-xl text-sm font-black text-navy bg-gold hover:bg-gold-light transition">Démarrer l'essai gratuit →</a>
            <a href="{{ route('m.contact') }}" class="px-7 py-3.5 rounded-xl text-sm font-bold text-white glass hover:bg-slate-700 transition">Demander une démo</a>
        </div>
    </div>
</section>
@endsection
