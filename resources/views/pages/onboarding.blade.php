<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opes Books — Bienvenue</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{amber:{300:'#E3B420',400:'#C99B0E',500:'#B5890C',600:'#A07C08'}}}}};</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}*{box-sizing:border-box}
        body{font-family:'Inter',-apple-system,sans-serif;background:radial-gradient(ellipse 120% 80% at 20% -5%,#1a2d4f 0%,#010048 35%,#050d1a 65%,#0f0a1e 100%);min-height:100vh;color:#e2e8f0}
        .glass-card{background:linear-gradient(145deg,rgba(255,255,255,0.10),rgba(255,255,255,0.04));backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,0.13);box-shadow:0 8px 40px rgba(0,0,0,0.5)}
        .glass-input{background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.14);color:#f1f5f9;width:100%;border-radius:0.75rem;padding:0.6rem 1rem;font-size:0.875rem}
        .glass-input:focus{outline:none;border-color:rgba(201,155,14,0.6);box-shadow:0 0 0 3px rgba(201,155,14,0.12)}
        select.glass-input option{background:#010048}
        .glass-btn-amber{background:linear-gradient(135deg,rgba(201,155,14,0.95),rgba(160,124,8,0.95));border:1px solid rgba(201,155,14,0.5);color:#010048;font-weight:900}
        @keyframes pop{0%{transform:scale(0)}70%{transform:scale(1.15)}100%{transform:scale(1)}}
        .pop{animation:pop .5s cubic-bezier(.34,1.56,.64,1) both}
    </style>
</head>
<body class="flex items-center justify-center p-4" x-data="onboardingApp()" x-init="init()" x-cloak>
<div class="w-full max-w-xl">

    <!-- Brand + progress -->
    <div class="text-center mb-6">
        <h1 class="text-2xl font-black text-white tracking-tight">OPES<span class="text-amber-400">BOOKS</span></h1>
    </div>
    <div class="flex items-center justify-center gap-2 mb-6">
        <template x-for="i in 5" :key="i">
            <div class="flex items-center">
                <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-black transition-all"
                     :style="i < step ? 'background:#C99B0E;color:#010048' : i===step ? 'background:rgba(201,155,14,0.25);color:#E3B420;border:1px solid #C99B0E' : 'background:rgba(255,255,255,0.06);color:rgba(148,163,184,0.6)'">
                    <span x-show="i < step">✓</span><span x-show="i >= step" x-text="i"></span>
                </div>
                <div x-show="i < 5" class="w-8 h-0.5 mx-1" :style="i < step ? 'background:#C99B0E' : 'background:rgba(255,255,255,0.1)'"></div>
            </div>
        </template>
    </div>

    <div class="glass-card rounded-3xl p-7">
        <div x-show="error" x-cloak class="mb-4 px-3 py-2 rounded-lg text-xs font-bold" style="background:rgba(244,63,94,0.12);color:rgb(252,165,165)" x-text="error"></div>

        <!-- STEP 1 — Company profile -->
        <div x-show="step===1">
            <div class="flex items-center gap-2 mb-1">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="rgb(201,155,14)" stroke-width="2"><path d="M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9z"/></svg>
                <h2 class="text-lg font-black text-white">Bienvenue sur OPESBooks</h2>
            </div>
            <p class="text-xs text-slate-400 mb-4">Configurons votre entreprise.</p>
            <div class="space-y-3">
                <input x-model="profile.name" class="glass-input" placeholder="Nom de l'entreprise *">
                <div class="grid grid-cols-2 gap-2">
                    <input x-model="profile.niu" class="glass-input" placeholder="NIU">
                    <input x-model="profile.rccm" class="glass-input" placeholder="RCCM">
                </div>
                <input x-model="profile.address" class="glass-input" placeholder="Adresse">
                <div class="grid grid-cols-2 gap-2">
                    <input x-model="profile.phone" class="glass-input" placeholder="Téléphone">
                    <select x-model="profile.tax_regime" class="glass-input">
                        <option value="REEL">Régime Réel</option>
                        <option value="SIMPLIFIE">Simplifié</option>
                        <option value="LIBERATOIRE">Libératoire</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-between mt-5">
                <button @click="skip()" class="text-xs text-slate-500 hover:text-slate-300 uppercase tracking-wider">Passer</button>
                <button @click="saveProfile()" :disabled="busy" class="glass-btn-amber px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest disabled:opacity-50" x-text="busy?'…':'Suivant →'"></button>
            </div>
        </div>

        <!-- STEP 2 — First client -->
        <div x-show="step===2" x-cloak>
            <h2 class="text-lg font-black text-white mb-1">Ajoutez votre premier client</h2>
            <p class="text-xs text-slate-400 mb-4">Vous pourrez en ajouter d'autres plus tard.</p>
            <div class="space-y-3">
                <input x-model="client.name" class="glass-input" placeholder="Nom du client / Raison sociale *">
                <div class="grid grid-cols-2 gap-2">
                    <input x-model="client.phone" class="glass-input" placeholder="Téléphone">
                    <input x-model="client.email" class="glass-input" placeholder="Email">
                </div>
                <input x-model="client.niu" class="glass-input" placeholder="NIU client (optionnel)">
            </div>
            <div class="flex justify-between mt-5">
                <button @click="skip()" class="text-xs text-slate-500 hover:text-slate-300 uppercase tracking-wider">Passer</button>
                <button @click="saveClient()" :disabled="busy" class="glass-btn-amber px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest disabled:opacity-50" x-text="busy?'…':'Ajouter et continuer →'"></button>
            </div>
        </div>

        <!-- STEP 3 — First invoice -->
        <div x-show="step===3" x-cloak>
            <h2 class="text-lg font-black text-white mb-1">Créez votre première facture</h2>
            <p class="text-xs text-slate-400 mb-4">Voyez OPESBooks en action.</p>
            <div class="space-y-3">
                <select x-model="invoice.customer_id" class="glass-input">
                    <option value="">— Choisir un client —</option>
                    <template x-for="c in clients" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                </select>
                <input x-model="invoice.description" class="glass-input" placeholder="Prestation / Article">
                <input x-model.number="invoice.amount_ht" type="number" class="glass-input" placeholder="Montant HT (XAF)">
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="px-3 py-2 rounded-lg" style="background:rgba(255,255,255,0.04)">TVA 19,25% : <strong x-text="fmt((invoice.amount_ht||0)*0.1925)"></strong></div>
                    <div class="px-3 py-2 rounded-lg" style="background:rgba(255,255,255,0.04)">TTC : <strong class="text-amber-400" x-text="fmt((invoice.amount_ht||0)*1.1925)"></strong></div>
                </div>
            </div>
            <div class="flex justify-between mt-5">
                <button @click="skip()" class="text-xs text-slate-500 hover:text-slate-300 uppercase tracking-wider">Passer</button>
                <button @click="saveInvoice()" :disabled="busy" class="glass-btn-amber px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest disabled:opacity-50" x-text="busy?'…':'Créer la facture →'"></button>
            </div>
        </div>

        <!-- STEP 4 — Invite team -->
        <div x-show="step===4" x-cloak>
            <h2 class="text-lg font-black text-white mb-1">Invitez votre équipe</h2>
            <p class="text-xs text-slate-400 mb-4">Comptable ou employés — optionnel.</p>
            <div class="space-y-2">
                <template x-for="(m,i) in members" :key="i">
                    <div class="grid grid-cols-2 gap-2">
                        <input x-model="m.email" type="email" class="glass-input" placeholder="email@entreprise.cm">
                        <select x-model="m.role" class="glass-input">
                            <option value="ACCOUNTANT">Comptable</option>
                            <option value="CLERK">Employé</option>
                        </select>
                    </div>
                </template>
                <button @click="members.push({email:'',role:'ACCOUNTANT'})" class="text-[11px] text-amber-400 font-black uppercase tracking-wider">+ Ajouter une personne</button>
            </div>
            <div class="flex justify-between mt-5">
                <button @click="skip()" class="text-xs text-slate-500 hover:text-slate-300 uppercase tracking-wider">Passer</button>
                <button @click="saveInvites()" :disabled="busy" class="glass-btn-amber px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest disabled:opacity-50" x-text="busy?'…':'Envoyer les invitations →'"></button>
            </div>
        </div>

        <!-- STEP 5 — Done -->
        <div x-show="step===5" x-cloak class="text-center py-4">
            <div class="pop w-16 h-16 rounded-full mx-auto flex items-center justify-center mb-4" style="background:#C99B0E">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#010048" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h2 class="text-xl font-black text-white mb-1">Vous êtes prêt !</h2>
            <p class="text-xs text-slate-400 mb-6">Votre espace OPESBooks est configuré.</p>
            <button @click="finish()" class="glass-btn-amber px-8 py-3 rounded-xl text-xs uppercase tracking-widest w-full">Accéder au tableau de bord →</button>
        </div>
    </div>
</div>

<script>
function onboardingApp() {
    return {
        step: 1, busy: false, error: '',
        profile: { name:'', niu:'', rccm:'', address:'', phone:'', tax_regime:'REEL' },
        client: { name:'', phone:'', email:'', niu:'' },
        clients: [],
        invoice: { customer_id:'', description:'', amount_ht:null },
        members: [{ email:'', role:'ACCOUNTANT' }],
        _tok() { return localStorage.getItem('opes_token'); },
        async _post(path, body) {
            const r = await fetch('/api/v1/onboarding/'+path, { method:'POST', headers:{'Authorization':'Bearer '+this._tok(),'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify(body) });
            const d = await r.json();
            if (!r.ok) throw new Error(d.message || Object.values(d.errors??{}).flat().join(' | '));
            return d;
        },
        async init() {
            if (!this._tok()) { window.location.href='/login'; return; }
            // prefill company from cache
            try { const c = JSON.parse(localStorage.getItem('opes_company')||'{}'); if (c.name) this.profile.name = c.name; if (c.niu) this.profile.niu = c.niu; } catch(e){}
        },
        fmt(v){ return Math.round(v||0).toLocaleString('fr-CM')+' XAF'; },
        skip(){ this.error=''; this.step++; if (this.step===3) this.loadClients(); },
        async loadClients(){
            try { const c = JSON.parse(localStorage.getItem('opes_company')||'{}'); const r = await fetch('/api/v1/companies/'+c.id+'/customers',{headers:{'Authorization':'Bearer '+this._tok(),'Accept':'application/json'}}); const d = await r.json(); this.clients = Array.isArray(d)?d:(d.data??[]); } catch(e){}
        },
        async saveProfile(){ this.busy=true; this.error=''; try{ await this._post('profile', this.profile); this.step=2; }catch(e){this.error=e.message}finally{this.busy=false} },
        async saveClient(){ if(!this.client.name){this.error='Nom du client requis.';return} this.busy=true; this.error=''; try{ await this._post('client', this.client); await this.loadClients(); this.step=3; }catch(e){this.error=e.message}finally{this.busy=false} },
        async saveInvoice(){ if(!this.invoice.customer_id||!this.invoice.amount_ht){this.error='Client et montant requis.';return} this.busy=true; this.error=''; try{ await this._post('invoice', this.invoice); this.step=4; }catch(e){this.error=e.message}finally{this.busy=false} },
        async saveInvites(){ const valid=this.members.filter(m=>m.email); if(!valid.length){this.skip();return} this.busy=true; this.error=''; try{ await this._post('invite', {members:valid}); this.step=5; }catch(e){this.error=e.message}finally{this.busy=false} },
        async finish(){ try{ await this._post('complete', {}); }catch(e){} window.location.href='/app'; },
    };
}
</script>
</body>
</html>
