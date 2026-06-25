@extends('layouts.marketing')
@section('title', 'Contact — OPESBooks')

@section('content')
<section class="max-w-2xl mx-auto px-5 py-16">
    <div class="text-center">
        <h1 class="text-3xl md:text-5xl font-black">Contactez-nous</h1>
        <p class="text-white/60 mt-4">Une question ? L'équipe Opesware vous répond depuis Douala.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-10 text-center">
        <div class="glass rounded-xl p-5"><div class="text-gold font-black text-xs uppercase tracking-widest">Email</div><div class="text-sm text-white/80 mt-2">contact@opesware.cm</div></div>
        <div class="glass rounded-xl p-5"><div class="text-gold font-black text-xs uppercase tracking-widest">Téléphone</div><div class="text-sm text-white/80 mt-2">+237 6XX XXX XXX</div></div>
        <div class="glass rounded-xl p-5"><div class="text-gold font-black text-xs uppercase tracking-widest">Adresse</div><div class="text-sm text-white/80 mt-2">Douala, Cameroun</div></div>
    </div>
    <div class="glass rounded-2xl p-6 mt-8"
         x-data="{ sent:false, name:'', email:'', message:'' }">
        <template x-if="!sent">
            <form @submit.prevent="sent=true" class="space-y-3">
                <input x-model="name" required placeholder="Votre nom" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-gold/50">
                <input x-model="email" type="email" required placeholder="Votre email" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-gold/50">
                <textarea x-model="message" required rows="4" placeholder="Votre message" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-gold/50"></textarea>
                <button class="w-full px-4 py-3 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition">Envoyer</button>
            </form>
        </template>
        <template x-if="sent">
            <div class="text-center py-6">
                <div class="text-emerald-400 font-black">✓ Message envoyé</div>
                <p class="text-white/50 text-sm mt-2">Nous vous répondrons sous 24h. (Écrivez-nous directement à contact@opesware.cm.)</p>
            </div>
        </template>
    </div>
</section>
@endsection
