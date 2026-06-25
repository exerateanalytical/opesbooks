@extends('layouts.marketing')
@section('title', 'Blog — OPESBooks | Guides SYSCOHADA, TVA, DSF')
@section('description', 'Guides pratiques de comptabilité et fiscalité pour les PME camerounaises : SYSCOHADA, TVA 19,25%, DSF, DGI.')

@section('content')
<section class="max-w-5xl mx-auto px-5 py-16">
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-5xl font-black">Le Blog OPESBooks</h1>
        <p class="text-white/60 mt-4">Guides comptables et fiscaux pour les PME camerounaises.</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @forelse($posts as $post)
        <a href="{{ route('blog.show', $post) }}" class="glass rounded-2xl overflow-hidden hover:border-gold/30 transition block">
            <div class="h-36 bg-gradient-to-br from-[#01006e] to-[#010048] flex items-center justify-center">
                <span class="font-black tracking-widest text-gold/40">OPESBOOKS</span>
            </div>
            <div class="p-5">
                <div class="text-[10px] uppercase tracking-widest text-gold mb-2">{{ optional($post->published_at)->translatedFormat('d M Y') }} · {{ $post->reading_time_minutes }} min</div>
                <div class="font-black text-white leading-snug">{{ $post->title }}</div>
                <p class="text-white/50 text-xs mt-2 leading-relaxed">{{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}</p>
            </div>
        </a>
        @empty
        <div class="col-span-3 text-center py-16 text-white/40">Aucun article pour le moment.</div>
        @endforelse
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
</section>
@endsection
