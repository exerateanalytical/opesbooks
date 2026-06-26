@extends('layouts.marketing')
@section('title', ($post->meta_title ?: $post->title) . ' — OPESBooks')
@section('description', $post->meta_description ?: \Illuminate\Support\Str::limit($post->excerpt, 155))

@section('content')
<style>
    .article-body{font-size:1rem;line-height:1.75;color:rgba(226,232,240,0.85)}
    .article-body h2{font-size:1.4rem;font-weight:800;color:#fff;margin:2rem 0 .75rem}
    .article-body h3{font-size:1.12rem;font-weight:700;color:#fff;margin:1.5rem 0 .5rem}
    .article-body p{margin:0 0 1rem}
    .article-body ul,.article-body ol{margin:0 0 1rem;padding-left:1.4rem}
    .article-body ul{list-style:disc}.article-body ol{list-style:decimal}
    .article-body li{margin:.35rem 0}
    .article-body a{color:#C99B0E;text-decoration:underline}
    .article-body strong{color:#fff;font-weight:700}
    .article-body blockquote{border-left:3px solid #C99B0E;padding:.25rem 0 .25rem 1rem;margin:1rem 0;color:rgba(226,232,240,0.7);font-style:italic}
    .article-body code{background:rgba(255,255,255,0.08);padding:.1rem .4rem;border-radius:.3rem;font-size:.9em}
    .article-body table{width:100%;border-collapse:collapse;margin:1rem 0;font-size:.9rem}
    .article-body th,.article-body td{border:1px solid rgba(255,255,255,0.12);padding:.5rem .75rem;text-align:left}
    .article-body th{background:rgba(245,158,11,0.12);color:#fff}
    .article-body hr{border:none;border-top:1px solid rgba(255,255,255,0.1);margin:2rem 0}
</style>
<article class="max-w-3xl mx-auto px-5 py-16">
    <a href="{{ route('blog.index') }}" class="text-gold text-xs font-black uppercase tracking-widest">← Tous les articles</a>
    @if($post->tags)<div class="flex flex-wrap gap-2 mt-5">@foreach($post->tags as $t)<span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-gold" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.22)">{{ $t }}</span>@endforeach</div>@endif
    <h1 class="text-3xl md:text-4xl font-black mt-4 leading-tight">{{ $post->title }}</h1>
    <div class="text-white/40 text-xs mt-3">{{ optional($post->published_at)->translatedFormat('d F Y') }} · {{ $post->reading_time_minutes }} min de lecture</div>
    @if($post->excerpt)<p class="text-white/70 text-lg mt-6 leading-relaxed">{{ $post->excerpt }}</p>@endif
    <div class="article-body max-w-none mt-8">
        {!! \Illuminate\Support\Str::of($post->body)->markdown() !!}
    </div>
    <div class="glass rounded-2xl p-6 mt-12 text-center">
        <div class="font-black">Essayez OPESBooks gratuitement</div>
        <p class="text-white/50 text-sm mt-1">30 jours · Aucune carte requise</p>
        <a href="/login" class="inline-block mt-4 px-6 py-3 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition">Commencer →</a>
    </div>
</article>
@endsection
