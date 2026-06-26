@extends('layouts.marketing')
@section('title', 'Contact — OPESBooks')

@section('content')
<section class="max-w-2xl mx-auto px-5 py-16">
    <div class="text-center">
        <h1 class="text-3xl md:text-5xl font-black">Contactez-nous</h1>
        <p class="text-white/60 mt-4">Une question ? L'équipe Opesware vous répond depuis Douala.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-10 text-center">
        <div class="glass rounded-xl p-5"><div class="text-gold font-black text-xs uppercase tracking-widest">Email</div><div class="text-sm text-white/80 mt-2">contact@opesware.com</div></div>
        <div class="glass rounded-xl p-5"><div class="text-gold font-black text-xs uppercase tracking-widest">Téléphone</div><div class="text-sm text-white/80 mt-2">+237 670 416 238</div></div>
        <div class="glass rounded-xl p-5"><div class="text-gold font-black text-xs uppercase tracking-widest">Adresse</div><div class="text-sm text-white/80 mt-2">Petite Terrain, Bonamoussadi<br>Douala, Cameroun</div></div>
    </div>
    <div class="glass rounded-2xl p-6 mt-8"
         x-data="{ sent:false, sending:false, error:'', name:'', email:'', message:'',
            async submit(){
                this.sending=true; this.error='';
                try{
                    const res=await fetch('{{ route('m.contact.submit') }}',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},body:JSON.stringify({name:this.name,email:this.email,message:this.message})});
                    const d=await res.json();
                    if(!res.ok) throw new Error(d.message||Object.values(d.errors||{}).flat().join(' '));
                    this.sent=true;
                }catch(e){ this.error=e.message; }finally{ this.sending=false; }
            } }">
        <template x-if="!sent">
            <form @submit.prevent="submit()" class="space-y-3">
                <input x-model="name" required placeholder="Votre nom" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-gold/50">
                <input x-model="email" type="email" required placeholder="Votre email" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-gold/50">
                <textarea x-model="message" required rows="4" placeholder="Votre message" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-gold/50"></textarea>
                <p x-show="error" x-cloak class="text-red-400 text-xs font-bold" x-text="error"></p>
                <button :disabled="sending" class="w-full px-4 py-3 rounded-xl text-sm font-black text-[#010048] bg-gold hover:bg-gold-light transition disabled:opacity-50" x-text="sending?'Envoi…':'Envoyer'"></button>
            </form>
        </template>
        <template x-if="sent">
            <div class="text-center py-6">
                <div class="text-emerald-400 font-black">✓ Message envoyé</div>
                <p class="text-white/50 text-sm mt-2">Nous vous répondrons sous 24h. Vous pouvez aussi écrire à contact@opesware.com.</p>
            </div>
        </template>
    </div>
</section>
@endsection
