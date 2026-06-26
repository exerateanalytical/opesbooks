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
        @php $tag = is_array($post->tags) ? ($post->tags[0] ?? 'GUIDE') : 'GUIDE'; @endphp
        <a href="{{ route('blog.show', $post) }}" class="glass rounded-2xl overflow-hidden hover:border-gold/30 transition flex flex-col">
            <div class="h-36 relative flex items-end p-4" style="background:linear-gradient(135deg,#01006e 0%,#010048 60%,#0a0820 100%)">
                <div class="absolute inset-0 opacity-30" style="background:radial-gradient(ellipse 80% 70% at 80% 10%,rgba(201,155,14,0.5),transparent 60%)"></div>
                <span class="relative px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-[#010048] bg-gold">{{ $tag }}</span>
            </div>
            <div class="p-5 flex-1 flex flex-col">
                <div class="text-[10px] uppercase tracking-widest text-gold mb-2">{{ optional($post->published_at)->translatedFormat('d M Y') }} · {{ $post->reading_time_minutes }} min</div>
                <div class="font-black text-white leading-snug">{{ $post->title }}</div>
                <p class="text-white/50 text-xs mt-2 leading-relaxed flex-1">{{ \Illuminate\Support\Str::limit($post->excerpt, 110) }}</p>
                <span class="text-gold text-xs font-bold mt-3">Lire l'article →</span>
            </div>
        </a>
        @empty
        <div class="col-span-3 text-center py-16 text-white/40">Aucun article pour le moment.</div>
        @endforelse
    </div>
    <div class="mt-10">{{ $posts->links() }}</div>
</section>
@endsection
