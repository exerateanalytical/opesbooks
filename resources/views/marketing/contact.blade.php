@extends('layouts.marketing')
@section('title', 'Contact OPESBooks — Parlez à notre équipe depuis Douala')
@section('description', 'Contactez l\'équipe OPESBooks à Douala. Support en français et anglais. Email, téléphone, WhatsApp. Réponse sous 24h en jours ouvrés.')

@section('content')

<!-- Hero -->
<section class="relative overflow-hidden pt-16 pb-10 text-center px-5">
    <div class="absolute inset-0 pointer-events-none" style="background:radial-gradient(ellipse 600px 300px at 50% -60px,rgba(245,158,11,0.09),transparent)"></div>
    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-widest text-gold mb-5" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.30)">Équipe Douala</span>
    <h1 class="text-3xl md:text-5xl font-black leading-tight">Parlons de votre <span class="text-gold">comptabilité</span></h1>
    <p class="text-slate-400 mt-4 max-w-xl mx-auto text-base leading-relaxed">Une question sur OPESBooks, une démo, un tarif Enterprise ou un problème à résoudre ? Notre équipe à Douala vous répond sous 24h en jours ouvrés.</p>
</section>

<!-- Contact channels -->
<section class="max-w-5xl mx-auto px-5 pb-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['✉️','Email','contact@opesware.com','mailto:contact@opesware.com','Réponse sous 24h'],
            ['📞','Téléphone','+237 670 416 238','tel:+237670416238','Lun–Ven 8h–17h WAT'],
            ['💬','WhatsApp','+237 670 416 238','https://wa.me/237670416238','Discussion rapide'],
            ['📍','Adresse','Petite Terrain, Bonamoussadi — Douala, CMR','https://maps.google.com/?q=Bonamoussadi+Douala+Cameroun','Sur rendez-vous'],
        ] as [$icon, $label, $value, $href, $sub])
        <a href="{{ $href }}" target="{{ str_starts_with($href,'http') ? '_blank' : '_self' }}"
           class="glass rounded-xl p-5 hover:border-slate-600 hover:bg-slate-800 transition group flex flex-col gap-3">
            <span class="text-2xl">{{ $icon }}</span>
            <div>
                <div class="text-[10px] font-black uppercase tracking-widest text-gold">{{ $label }}</div>
                <div class="text-slate-200 text-sm font-semibold mt-1 group-hover:text-white transition">{{ $value }}</div>
                <div class="text-slate-500 text-[11px] mt-1">{{ $sub }}</div>
            </div>
        </a>
        @endforeach
    </div>
</section>

<!-- Form + sidebar -->
<section class="max-w-5xl mx-auto px-5 pb-20 grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Form -->
    <div class="lg:col-span-2">
        <div class="glass rounded-2xl p-8"
             x-data="{
                sent: false, sending: false, error: '',
                subject: 'general',
                form: { name:'', email:'', company:'', phone:'', subject:'general', message:'' },
                async submit() {
                    this.sending = true; this.error = '';
                    try {
                        const res = await fetch('{{ route('m.contact.submit') }}', {
                            method: 'POST',
                            headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content},
                            body: JSON.stringify(this.form)
                        });
                        const d = await res.json();
                        if (!res.ok) throw new Error(d.message || Object.values(d.errors||{}).flat().join(' '));
                        this.sent = true;
                    } catch(e) { this.error = e.message; }
                    finally { this.sending = false; }
                }
             }">

            <template x-if="!sent">
                <div>
                    <h2 class="text-xl font-black mb-6">Envoyez-nous un message</h2>

                    <!-- Subject tabs -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach(['general'=>'Question générale','demo'=>'Demander une démo','support'=>'Support technique','enterprise'=>'Plan Enterprise','partnership'=>'Partenariat'] as $val => $label)
                        <button type="button" @click="form.subject='{{ $val }}'"
                                :class="form.subject==='{{ $val }}' ? 'bg-gold text-navy' : 'glass text-slate-400 hover:text-white'"
                                class="px-3 py-1.5 rounded-lg text-xs font-bold transition">{{ $label }}</button>
                        @endforeach
                    </div>

                    <form @submit.prevent="submit()" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs mb-1.5 font-medium" style="color:var(--c-muted)">Votre nom *</label>
                                <input x-model="form.name" required placeholder="Jean Dupont"
                                       class="w-full rounded-xl px-4 py-3 text-white text-sm focus:outline-none transition"
                                       style="background:#293548;border:1px solid #334155" onfocus="this.style.borderColor='rgba(245,158,11,0.7)'" onblur="this.style.borderColor='#334155'">
                            </div>
                            <div>
                                <label class="block text-xs mb-1.5 font-medium" style="color:var(--c-muted)">Email *</label>
                                <input x-model="form.email" type="email" required placeholder="jean@example.cm"
                                       class="w-full rounded-xl px-4 py-3 text-white text-sm focus:outline-none transition"
                                       style="background:#293548;border:1px solid #334155" onfocus="this.style.borderColor='rgba(245,158,11,0.7)'" onblur="this.style.borderColor='#334155'">
                            </div>
                            <div>
                                <label class="block text-xs mb-1.5 font-medium" style="color:var(--c-muted)">Entreprise</label>
                                <input x-model="form.company" placeholder="Nom de votre entreprise"
                                       class="w-full rounded-xl px-4 py-3 text-white text-sm focus:outline-none transition"
                                       style="background:#293548;border:1px solid #334155" onfocus="this.style.borderColor='rgba(245,158,11,0.7)'" onblur="this.style.borderColor='#334155'">
                            </div>
                            <div>
                                <label class="block text-xs mb-1.5 font-medium" style="color:var(--c-muted)">Téléphone / WhatsApp</label>
                                <input x-model="form.phone" placeholder="+237 6XX XXX XXX"
                                       class="w-full rounded-xl px-4 py-3 text-white text-sm focus:outline-none transition"
                                       style="background:#293548;border:1px solid #334155" onfocus="this.style.borderColor='rgba(245,158,11,0.7)'" onblur="this.style.borderColor='#334155'">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs mb-1.5 font-medium" style="color:var(--c-muted)">Votre message *</label>
                            <textarea x-model="form.message" required rows="5"
                                      placeholder="Décrivez votre besoin, votre activité, le nombre d'utilisateurs…"
                                      class="w-full rounded-xl px-4 py-3 text-white text-sm focus:outline-none transition resize-none"
                                      style="background:#293548;border:1px solid #334155" onfocus="this.style.borderColor='rgba(245,158,11,0.7)'" onblur="this.style.borderColor='#334155'"></textarea>
                        </div>
                        <p x-show="error" x-cloak class="text-red-400 text-xs font-bold p-3 rounded-lg bg-red-900/20" x-text="error"></p>
                        <button :disabled="sending" type="submit"
                                class="btn-primary w-full disabled:opacity-50"
                                x-text="sending ? 'Envoi en cours…' : 'Envoyer le message →'"></button>
                        <p class="text-xs text-center" style="color:var(--c-faint)">En soumettant ce formulaire, vous acceptez d'être contacté par l'équipe Opesware.</p>
                    </form>
                </div>
            </template>

            <template x-if="sent">
                <div class="text-center py-10">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-5 bg-emerald-900/30 border border-emerald-500/30">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#34D399" stroke-width="2.5"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                    </div>
                    <h3 class="text-xl font-black text-emerald-400">Message envoyé !</h3>
                    <p class="text-slate-400 text-sm mt-3 leading-relaxed">Merci pour votre message. Notre équipe à Douala vous répondra sous 24h en jours ouvrés.<br><br>En attendant, vous pouvez nous écrire directement à <a href="mailto:contact@opesware.com" class="text-gold hover:underline">contact@opesware.com</a> ou nous appeler au <a href="tel:+237670416238" class="text-gold hover:underline">+237 670 416 238</a>.</p>
                    <a href="/login" class="btn-primary mt-6 inline-flex">Essayer OPESBooks gratuitement →</a>
                </div>
            </template>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-5">
        <!-- Hours -->
        <div class="glass rounded-xl p-5">
            <h3 class="text-sm font-black mb-4 uppercase tracking-widest text-gold">Horaires</h3>
            @foreach([['Lundi – Vendredi','8h00 – 17h30 WAT'],['Samedi','9h00 – 13h00 WAT'],['Dimanche','Fermé']] as $h)
            <div class="flex justify-between text-xs text-slate-400 py-1.5 border-b border-slate-800 last:border-0">
                <span>{{ $h[0] }}</span><span class="font-semibold">{{ $h[1] }}</span>
            </div>
            @endforeach
        </div>

        <!-- Common topics -->
        <div class="glass rounded-xl p-5">
            <h3 class="text-sm font-black mb-4 uppercase tracking-widest text-gold">Sujets fréquents</h3>
            <ul class="space-y-2">
                @foreach(['Démo personnalisée','Configuration MECeF/DGI','Migration depuis Sage','Plan Enterprise multi-société','Intégration API/ERP','Formation comptable SYSCOHADA'] as $t)
                <li class="flex items-center gap-2 text-xs text-slate-400">
                    <span class="text-gold">→</span> {{ $t }}
                </li>
                @endforeach
            </ul>
        </div>

        <!-- Quick links -->
        <div class="glass rounded-xl p-5">
            <h3 class="text-sm font-black mb-4 uppercase tracking-widest text-gold">Ressources</h3>
            <ul class="space-y-2">
                @foreach([[route('m.faq'),'FAQ — Questions fréquentes'],[route('m.pricing'),'Tarifs & Plans'],[route('m.features'),'Toutes les fonctionnalités'],['/developer','Documentation API']] as $l)
                <li><a href="{{ $l[0] }}" class="flex items-center gap-2 text-xs text-slate-400 hover:text-gold transition"><span>→</span>{{ $l[1] }}</a></li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
@endsection
