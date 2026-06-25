@extends('layouts.marketing')
@section('title', ($post->meta_title ?: $post->title) . ' — OPESBooks')
@section('description', $post->meta_description ?: \Illuminate\Support\Str::limit($post->excerpt, 155))

@section('content')
<article class="max-w-3xl mx-auto px-5 py-16">
    <a href="{{ route('blog.index') }}" class="text-gold text-xs font-black uppercase tracking-widest">← Tous les articles</a>
    <h1 class="text-3xl md:text-4xl font-black mt-5 leading-tight">{{ $post->title }}</h1>
    <div class="text-white/40 text-xs mt-3">{{ optional($post->published_at)->translatedFormat('d F Y') }} · {{ $post->reading_time_minutes }} min de lecture</div>
    @if($post->excerpt)<p class="text-white/70 text-lg mt-6 leading-relaxed">{{ $post->excerpt }}</p>@endif
    <div class="prose prose-invert max-w-none mt-8 text-white/80 leading-relaxed space-y-4">
        {!! \Illuminate\Support\Str::of($post->body)->markdown() !!}
    </div>
    <div class="glass rounded-2xl p-6 mt-12 text-center">
        <div class="font-black">Essayez OPESBooks gratuitement</div>
        <p class="text-white/50 text-sm mt-1">30 jours · Aucune carte requise</p>
        <a href="/login" class="inline-block mt-4 px-6 py-3 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition">Commencer →</a>
    </div>
</article>
@endsection
