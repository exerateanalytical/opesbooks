@props(['companyNiu' => '', 'csrfToken' => ''])

<div x-data="intentSelector()"
     class="w-full rounded-2xl overflow-hidden relative glass-shimmer"
     style="background:var(--c-surface);border:1px solid var(--c-border);box-shadow:0 8px 40px rgba(0,0,0,0.5);">

    {{-- Header --}}
    <div class="px-5 py-4 border-b flex items-center gap-3"
         style="border-color:var(--c-border);background:var(--c-bg)">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-xs font-black text-white"
             style="background:linear-gradient(135deg,rgba(99,102,241,0.8),rgba(79,70,229,0.9));box-shadow:0 4px 14px rgba(99,102,241,0.35)">
            +
        </div>
        <div>
            <h2 class="text-sm font-black text-white uppercase tracking-wide leading-none">Nouvelle Transaction</h2>
            <p class="text-[10px] text-slate-400 font-medium mt-0.5">Choisissez une opération ci-dessous</p>
        </div>
    </div>

    {{-- Intent Grid --}}
    <div x-show="!selectedIntent" class="p-4">
        <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3 px-1">Sélectionner l'opération :</p>
        <div class="grid grid-cols-2 gap-2.5">

            <template x-for="intent in intents" :key="intent.id">
                <button
                    @click="selectIntent(intent)"
                    class="relative flex flex-col items-center justify-center p-3.5 rounded-xl text-center transition-all duration-200 active:scale-95 transform group overflow-hidden"
                    :class="intent.highlight
                        ? 'hover:border-emerald-400/40'
                        : 'hover:border-white/20'"
                    style="background:var(--c-raised);border:1px solid var(--c-border);transition:all 0.2s ease"
                    @mouseenter="$el.style.background='rgba(255,255,255,0.10)';$el.style.borderColor='rgba(255,255,255,0.18)'"
                    @mouseleave="$el.style.background='#1C2A3A';$el.style.borderColor='#253347'"
                >
                    <span class="text-2xl mb-2 leading-none" x-text="intent.icon"></span>
                    <span class="text-[10px] font-black text-slate-200 uppercase tracking-wide leading-tight" x-text="intent.labelFr"></span>
                    <span class="text-[9px] font-medium text-slate-500 leading-tight mt-0.5" x-text="intent.labelEn"></span>
                </button>
            </template>

        </div>
    </div>

    {{-- Transaction Form --}}
    <div x-show="selectedIntent" x-cloak class="p-5 float-in">

        {{-- Back + Selected chip --}}
        <div class="flex items-center gap-2 mb-5">
            <button @click="selectedIntent = null"
                    class="text-[10px] font-black text-slate-400 hover:text-white uppercase tracking-wider transition-colors flex items-center gap-1">
                ← Retour
            </button>
            <div class="flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider text-white"
                 style="background:var(--c-raised);border:1px solid var(--c-border-strong)">
                <span x-text="selectedIntent?.icon"></span>
                <span x-text="selectedIntent?.labelFr"></span>
            </div>
        </div>

        {{-- Amount --}}
        <div class="mb-4">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                Montant TTC (XAF) <span class="text-rose-400">*</span>
            </label>
            <div class="relative">
                <input
                    type="number"
                    x-model="form.amount"
                    @input="computeTax()"
                    placeholder="0"
                    min="1"
                    class="glass-input w-full rounded-xl px-4 py-3 font-mono font-black text-white text-xl"
                >
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-500">XAF</span>
            </div>

            {{-- Tax breakdown --}}
            <div x-show="taxBreakdown" x-cloak
                 class="mt-2.5 rounded-xl p-3 float-in"
                 style="background:var(--c-raised);border:1px solid var(--c-border)">
                <div class="grid grid-cols-4 gap-2 text-[10px] font-mono text-center">
                    <div>
                        <div class="text-slate-500 font-bold mb-0.5">Base HT</div>
                        <div class="font-black text-white" x-text="formatXaf(taxBreakdown?.amount_ht)"></div>
                    </div>
                    <div>
                        <div class="text-slate-500 font-bold mb-0.5">TVA 17.5%</div>
                        <div class="font-black text-indigo-400" x-text="formatXaf(taxBreakdown?.base_vat)"></div>
                    </div>
                    <div>
                        <div class="text-slate-500 font-bold mb-0.5">CAC 10%</div>
                        <div class="font-black text-purple-400" x-text="formatXaf(taxBreakdown?.cac)"></div>
                    </div>
                    <div>
                        <div class="text-slate-500 font-bold mb-0.5">TTC</div>
                        <div class="font-black text-emerald-400" x-text="formatXaf(taxBreakdown?.amount_ttc)"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Note --}}
        <div class="mb-4">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                Note (optionnel)
            </label>
            <input
                type="text"
                x-model="form.memo"
                placeholder="Ex: Vente marché central, stand B14..."
                class="glass-input w-full rounded-xl px-4 py-2.5 text-sm text-white font-medium"
            >
        </div>

        {{-- SYSCOHADA mapping preview --}}
        <div x-show="selectedIntent" class="mb-4 rounded-xl p-3"
             style="background:var(--c-bg);border:1px solid var(--c-border)">
            <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-2">
                Mappage SYSCOHADA Automatique
            </div>
            <div class="space-y-1">
                <template x-for="line in selectedIntent?.journalLines ?? []" :key="line.account">
                    <div class="flex items-center gap-2.5">
                        <span class="text-[9px] font-black px-1.5 py-0.5 rounded-md min-w-[18px] text-center"
                              :class="line.type === 'debit'
                                ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30'
                                : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'"
                              x-text="line.type === 'debit' ? 'D' : 'C'"></span>
                        <span class="font-mono text-[10px] font-bold text-amber-400" x-text="line.account"></span>
                        <span class="text-[10px] text-slate-400" x-text="line.label"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- Errors --}}
        <div x-show="errors.length" x-cloak class="mb-4 rounded-xl p-3"
             style="background:rgba(244,63,94,0.08);border:1px solid rgba(244,63,94,0.25)">
            <template x-for="error in errors" :key="error">
                <p class="text-[11px] font-bold text-rose-400" x-text="'⚠ ' + error"></p>
            </template>
        </div>

        {{-- Submit --}}
        <button
            @click="submitTransaction()"
            :disabled="submitting || !form.amount"
            class="w-full glass-btn text-slate-950 font-black py-3 px-6 rounded-xl text-[11px] uppercase tracking-widest disabled:opacity-40 disabled:cursor-not-allowed"
        >
            <span x-show="!submitting">Enregistrer la Transaction</span>
            <span x-show="submitting" x-cloak class="flex items-center justify-center gap-2">
                <svg class="spin-pulse h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Enregistrement…
            </span>
        </button>

    </div>

</div>

<script>
function intentSelector() {
    return {
        selectedIntent: null,
        taxBreakdown: null,
        errors: [],
        submitting: false,
        form: { amount: '', memo: '' },

        intents: [
            {
                id: 'sell_goods', icon: '🛒',
                labelFr: 'Vente de marchandises', labelEn: 'Sold goods', highlight: true,
                journalLines: [
                    { type: 'debit',  account: '571100', label: 'Caisse principale' },
                    { type: 'credit', account: '701100', label: 'Ventes Cameroun' },
                    { type: 'credit', account: '443100', label: 'TVA Facturée' },
                    { type: 'credit', account: '448600', label: 'CAC Communal' },
                ],
            },
            {
                id: 'momo_receipt', icon: '📱',
                labelFr: 'Mobile Money reçu', labelEn: 'MoMo received', highlight: true,
                journalLines: [
                    { type: 'debit',  account: '571200', label: 'MTN MoMo Wallet' },
                    { type: 'credit', account: '701100', label: 'Ventes Cameroun' },
                    { type: 'credit', account: '443100', label: 'TVA Facturée' },
                    { type: 'credit', account: '448600', label: 'CAC Communal' },
                ],
            },
            {
                id: 'pay_eneo', icon: '💡',
                labelFr: 'Facture Eneo payée', labelEn: 'Paid Eneo',
                journalLines: [
                    { type: 'debit',  account: '605100', label: 'Charges Électricité' },
                    { type: 'credit', account: '571100', label: 'Caisse principale' },
                ],
            },
            {
                id: 'pay_camwater', icon: '💧',
                labelFr: 'Facture Camwater', labelEn: 'Paid Camwater',
                journalLines: [
                    { type: 'debit',  account: '605200', label: 'Charges Eau' },
                    { type: 'credit', account: '571100', label: 'Caisse principale' },
                ],
            },
            {
                id: 'pay_salary', icon: '👥',
                labelFr: 'Salaires payés', labelEn: 'Paid salaries',
                journalLines: [
                    { type: 'debit',  account: '661100', label: 'Salaires bruts' },
                    { type: 'credit', account: '422000', label: 'Personnel - Rém. dues' },
                    { type: 'credit', account: '521100', label: 'Banque / Caisse' },
                ],
            },
            {
                id: 'buy_stock', icon: '📦',
                labelFr: 'Achat marchandises', labelEn: 'Purchased goods',
                journalLines: [
                    { type: 'debit',  account: '601100', label: 'Achats marchandises' },
                    { type: 'debit',  account: '445200', label: 'TVA Récupérable' },
                    { type: 'credit', account: '401100', label: 'Fournisseurs' },
                ],
            },
            {
                id: 'pay_transport', icon: '🚕',
                labelFr: 'Transport payé', labelEn: 'Paid transport',
                journalLines: [
                    { type: 'debit',  account: '618100', label: 'Déplacements pro.' },
                    { type: 'credit', account: '571100', label: 'Caisse principale' },
                ],
            },
            {
                id: 'pay_marketing', icon: '📢',
                labelFr: 'Publicité payée', labelEn: 'Paid marketing',
                journalLines: [
                    { type: 'debit',  account: '624100', label: 'Publicité & marketing' },
                    { type: 'credit', account: '571100', label: 'Caisse principale' },
                ],
            },
        ],

        selectIntent(intent) {
            this.selectedIntent = intent;
            this.errors = [];
            this.form = { amount: '', memo: '' };
            this.taxBreakdown = null;
        },

        formatXaf(value) {
            if (!value) return '0';
            return new Intl.NumberFormat('fr-CM').format(parseFloat(value));
        },

        async computeTax() {
            if (!this.form.amount || parseFloat(this.form.amount) <= 0) {
                this.taxBreakdown = null;
                return;
            }
            try {
                const res = await fetch('/api/v1/tax/from-ttc', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ amount_ttc: this.form.amount }),
                });
                if (res.ok) this.taxBreakdown = await res.json();
            } catch {}
        },

        async submitTransaction() {
            this.errors = [];
            if (!this.form.amount || parseFloat(this.form.amount) <= 0) {
                this.errors.push('Montant requis et doit être supérieur à 0');
                return;
            }
            this.submitting = true;
            try {
                const payload = this.buildPayload('{{ $companyNiu }}');
                const res = await fetch('/api/v1/journal/manual', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ $csrfToken }}' },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (res.ok) {
                    this.selectedIntent = null;
                    this.form = { amount: '', memo: '' };
                    this.$dispatch('transaction-saved', data);
                } else {
                    const errs = data.errors ?? {};
                    this.errors = Object.values(errs).flat();
                }
            } catch {
                this.errors = ['Erreur réseau. Veuillez réessayer.'];
            } finally {
                this.submitting = false;
            }
        },

        buildPayload(companyNiu) {
            const tax    = this.taxBreakdown ?? {};
            const amount = parseFloat(this.form.amount);
            const lines  = this.selectedIntent.journalLines.map(l => ({
                account_code: l.account,
                debit:  l.type === 'debit'  ? amount : 0,
                credit: l.type === 'credit' ? amount : 0,
                description: l.label,
            }));
            return {
                company_niu:     companyNiu,
                posting_date:    new Date().toISOString().split('T')[0],
                reference_id:    'MANUAL-' + Date.now(),
                source_pipeline: 'MANUAL_CASH',
                memo:            this.form.memo || this.selectedIntent.labelFr,
                lines,
            };
        },
    };
}
</script>
