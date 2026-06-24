<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opes Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .nav-item { @apply flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition-colors; }
        .nav-active { @apply bg-[#F59E0B] text-[#0A192F]; }
        .nav-inactive { @apply text-slate-300 hover:bg-slate-700 hover:text-white; }
        .card { @apply bg-white rounded-xl border border-slate-200 shadow-sm; }
        .stat-card { @apply card p-5; }
        .btn-primary { @apply bg-[#0A192F] hover:bg-slate-800 text-white font-bold px-4 py-2 rounded-lg text-sm transition-colors; }
        .btn-amber { @apply bg-[#F59E0B] hover:bg-amber-400 text-[#0A192F] font-bold px-4 py-2 rounded-lg text-sm transition-colors; }
        .input { @apply w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B] transition-colors; }
        .label { @apply block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen" x-data="opesApp()" x-cloak @online.window="connStatus='ONLINE'" @offline.window="connStatus='OFFLINE'">

    {{-- ── Sidebar Navigation ───────────────────────────────────────────────── --}}
    <div class="flex min-h-screen">
        <aside class="w-60 bg-[#0A192F] flex flex-col py-5 px-3 shrink-0">
            <div class="px-3 mb-6">
                <h1 class="text-xl font-black text-white">OPES<span class="text-[#F59E0B]">BOOKS</span></h1>
                <p class="text-xs text-slate-400 mt-0.5" x-text="user?.role + ' · ' + (company?.name ?? '...')"></p>
            </div>

            {{-- Connection status --}}
            <div class="mx-3 mb-5 px-3 py-2 rounded-lg text-xs font-bold flex items-center gap-2"
                 :class="connStatus === 'ONLINE' ? 'bg-emerald-900/40 text-emerald-400' : connStatus === 'SYNCING' ? 'bg-indigo-900/40 text-indigo-400' : 'bg-amber-900/40 text-amber-400'">
                <span class="w-2 h-2 rounded-full" :class="connStatus === 'ONLINE' ? 'bg-emerald-400 animate-pulse' : connStatus === 'SYNCING' ? 'bg-indigo-400 animate-spin' : 'bg-amber-400'"></span>
                <span x-text="connStatus === 'ONLINE' ? (lang==='FR' ? 'Connecté' : 'Online') : connStatus === 'SYNCING' ? 'Syncing...' : (lang==='FR' ? 'Hors ligne' : 'Offline')"></span>
            </div>

            <nav class="space-y-1 flex-1">
                <button @click="page='dashboard'" :class="page==='dashboard' ? 'nav-item nav-active' : 'nav-item nav-inactive'" class="w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span x-text="lang==='FR' ? 'Tableau de Bord' : 'Dashboard'"></span>
                </button>
                <button @click="page='transactions'; loadTransactions()" :class="page==='transactions' ? 'nav-item nav-active' : 'nav-item nav-inactive'" class="w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span x-text="lang==='FR' ? 'Journal' : 'Journal'"></span>
                </button>
                <button @click="page='invoice'" :class="page==='invoice' ? 'nav-item nav-active' : 'nav-item nav-inactive'" class="w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-text="lang==='FR' ? 'Facturer' : 'Invoice'"></span>
                </button>
                <button @click="page='calculator'; resetCalc()" :class="page==='calculator' ? 'nav-item nav-active' : 'nav-item nav-inactive'" class="w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span x-text="lang==='FR' ? 'Calculateur TVA' : 'VAT Calculator'"></span>
                </button>
                <button @click="page='tax-dashboard'" :class="page==='tax-dashboard' ? 'nav-item nav-active' : 'nav-item nav-inactive'" class="w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span x-text="lang==='FR' ? 'Bilan Fiscal' : 'Tax Monitor'"></span>
                </button>
            </nav>

            <div class="mt-auto space-y-2 px-0">
                <button @click="toggleLang()" class="nav-item nav-inactive w-full text-left">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                    <span x-text="lang === 'FR' ? 'English' : 'Français'"></span>
                </button>
                <button @click="doLogout()" class="nav-item nav-inactive w-full text-left text-red-400 hover:text-red-300">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span x-text="lang==='FR' ? 'Déconnexion' : 'Logout'"></span>
                </button>
            </div>
        </aside>

        {{-- ── Main Content ────────────────────────────────────────────────────── --}}
        <main class="flex-1 overflow-auto">

            {{-- ── DASHBOARD PAGE ────────────────────────────────────────────── --}}
            <div x-show="page === 'dashboard'" class="p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-black text-[#0A192F]" x-text="lang==='FR' ? 'Tableau de Bord' : 'Dashboard'"></h2>
                    <span class="text-sm text-slate-500" x-text="today"></span>
                </div>

                {{-- Stats grid --}}
                <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
                    <template x-for="stat in stats" :key="stat.key">
                        <div class="stat-card">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2" x-text="lang==='FR' ? stat.labelFR : stat.labelEN"></p>
                            <p class="text-2xl font-black text-[#0A192F]" x-text="fmtXaf(stat.value)"></p>
                            <p class="text-xs text-slate-400 mt-1" x-text="stat.account"></p>
                        </div>
                    </template>
                </div>

                {{-- Fiscal provision --}}
                <div class="bg-[#0A192F] rounded-xl p-5 text-white flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1" x-text="lang==='FR' ? 'Mois en cours · Provision DGI' : 'Current Month · DGI Provision'"></p>
                        <p class="text-3xl font-black text-[#F59E0B]" x-text="fmtXaf(fiscalProvision)"></p>
                        <p class="text-xs text-slate-400 mt-1" x-text="lang==='FR' ? 'À verser avant le 15 du mois prochain' : 'Due before the 15th of next month'"></p>
                    </div>
                    <a href="/tax-dashboard" class="btn-amber shrink-0" x-text="lang==='FR' ? 'Voir le Bilan Fiscal' : 'View Tax Monitor'"></a>
                </div>

                {{-- Quick actions --}}
                <div class="card p-5">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4" x-text="lang==='FR' ? 'Actions Rapides' : 'Quick Actions'"></h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <button @click="page='invoice'" class="flex flex-col items-center gap-2 p-4 border-2 border-dashed border-slate-200 rounded-xl hover:border-[#F59E0B] hover:bg-amber-50 transition-colors group">
                            <svg class="w-6 h-6 text-slate-400 group-hover:text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span class="text-xs font-bold text-slate-600" x-text="lang==='FR' ? 'Nouvelle Facture' : 'New Invoice'"></span>
                        </button>
                        <button @click="page='calculator'" class="flex flex-col items-center gap-2 p-4 border-2 border-dashed border-slate-200 rounded-xl hover:border-[#F59E0B] hover:bg-amber-50 transition-colors group">
                            <svg class="w-6 h-6 text-slate-400 group-hover:text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            <span class="text-xs font-bold text-slate-600" x-text="lang==='FR' ? 'Calc. TVA' : 'VAT Calc'"></span>
                        </button>
                        <a href="/tax-dashboard" class="flex flex-col items-center gap-2 p-4 border-2 border-dashed border-slate-200 rounded-xl hover:border-[#F59E0B] hover:bg-amber-50 transition-colors group">
                            <svg class="w-6 h-6 text-slate-400 group-hover:text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/></svg>
                            <span class="text-xs font-bold text-slate-600" x-text="lang==='FR' ? 'Bilan Fiscal' : 'Tax Monitor'"></span>
                        </a>
                        <button @click="page='transactions'; loadTransactions()" class="flex flex-col items-center gap-2 p-4 border-2 border-dashed border-slate-200 rounded-xl hover:border-[#F59E0B] hover:bg-amber-50 transition-colors group">
                            <svg class="w-6 h-6 text-slate-400 group-hover:text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            <span class="text-xs font-bold text-slate-600" x-text="lang==='FR' ? 'Journal' : 'Ledger'"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── JOURNAL PAGE ──────────────────────────────────────────────── --}}
            <div x-show="page === 'transactions'" class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-black text-[#0A192F]" x-text="lang==='FR' ? 'Journal Comptable' : 'Accounting Journal'"></h2>
                </div>
                <div class="card overflow-hidden">
                    <div class="bg-[#0A192F] px-4 py-3 flex items-center justify-between">
                        <span class="text-xs font-black text-white uppercase tracking-wider" x-text="lang==='FR' ? 'Écritures Comptables' : 'Journal Entries'"></span>
                        <span class="text-xs font-mono bg-[#F59E0B] text-[#0A192F] px-2 py-0.5 rounded font-black" x-text="(transactions.length) + ' ' + (lang==='FR' ? 'entrées' : 'entries')"></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                                <tr>
                                    <th class="px-4 py-3" x-text="lang==='FR' ? 'Référence' : 'Reference'"></th>
                                    <th class="px-4 py-3" x-text="lang==='FR' ? 'Date' : 'Date'"></th>
                                    <th class="px-4 py-3" x-text="lang==='FR' ? 'Mémo' : 'Memo'"></th>
                                    <th class="px-4 py-3" x-text="lang==='FR' ? 'Source' : 'Source'"></th>
                                    <th class="px-4 py-3" x-text="lang==='FR' ? 'Statut' : 'Status'"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm">
                                <template x-if="transactions.length === 0">
                                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400 text-sm" x-text="lang==='FR' ? 'Aucune écriture trouvée.' : 'No entries found.'"></td></tr>
                                </template>
                                <template x-for="txn in transactions" :key="txn.id">
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-4 py-3 font-mono text-xs font-bold text-[#0A192F]" x-text="txn.reference_id"></td>
                                        <td class="px-4 py-3 text-xs text-slate-600" x-text="txn.posting_date"></td>
                                        <td class="px-4 py-3 text-xs text-slate-700 max-w-xs truncate" x-text="txn.memo"></td>
                                        <td class="px-4 py-3"><span class="text-xs font-bold bg-slate-100 text-slate-600 px-2 py-0.5 rounded" x-text="txn.source_pipeline"></span></td>
                                        <td class="px-4 py-3">
                                            <span class="text-xs font-bold px-2 py-0.5 rounded"
                                                  :class="txn.transaction_status === 'SUCCESSFUL' ? 'bg-emerald-100 text-emerald-700' : txn.transaction_status === 'REVERSED' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'"
                                                  x-text="txn.transaction_status"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── INVOICE PAGE ──────────────────────────────────────────────── --}}
            <div x-show="page === 'invoice'" class="p-6 space-y-5">
                <h2 class="text-2xl font-black text-[#0A192F]" x-text="lang==='FR' ? 'Nouvelle Facture' : 'New Invoice'"></h2>

                <div class="card p-5 space-y-4" x-data="invoiceForm()">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="label" x-text="lang==='FR' ? 'N° Facture' : 'Invoice No.'"></label>
                            <input type="text" x-model="form.invoice_number" class="input" placeholder="FAC-2026-001">
                        </div>
                        <div>
                            <label class="label" x-text="lang==='FR' ? 'Date' : 'Date'"></label>
                            <input type="date" x-model="form.invoice_date" class="input">
                        </div>
                        <div>
                            <label class="label" x-text="lang==='FR' ? 'Langue / Language' : 'Language'"></label>
                            <select x-model="form.language" class="input">
                                <option value="FR">Français</option>
                                <option value="EN">English</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="label" x-text="lang==='FR' ? 'Client' : 'Client Name'"></label>
                            <input type="text" x-model="form.client_name" class="input" placeholder="SARL Acheteur Douala">
                        </div>
                        <div>
                            <label class="label">NIU Client</label>
                            <input type="text" x-model="form.client_niu" class="input" placeholder="M082000012 (optionnel)">
                        </div>
                    </div>

                    {{-- Lines --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="label mb-0" x-text="lang==='FR' ? 'Lignes de Facturation' : 'Invoice Lines'"></label>
                            <button @click="addLine()" class="text-xs font-bold text-[#F59E0B] hover:underline" x-text="lang==='FR' ? '+ Ajouter ligne' : '+ Add line'"></button>
                        </div>
                        <div class="space-y-2">
                            <template x-for="(line, idx) in form.lines" :key="idx">
                                <div class="grid grid-cols-12 gap-2 items-center">
                                    <input type="text" x-model="line.description" class="input col-span-5" :placeholder="lang==='FR' ? 'Description' : 'Description'">
                                    <input type="number" x-model="line.quantity" class="input col-span-2" placeholder="1" min="0.01" step="0.01">
                                    <input type="number" x-model="line.unit_price_ht" class="input col-span-3" placeholder="Prix HT" min="0">
                                    <div class="col-span-1 text-xs font-bold text-[#10B981] text-right" x-text="fmtXaf(line.quantity * line.unit_price_ht)"></div>
                                    <button @click="form.lines.splice(idx,1)" class="col-span-1 text-red-400 hover:text-red-600 text-lg font-bold">×</button>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Live total --}}
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 flex justify-between items-center">
                        <div class="text-sm text-slate-500">
                            <span x-text="lang==='FR' ? 'HT :' : 'HT:'"></span>
                            <span class="font-bold text-slate-900 ml-1" x-text="fmtXaf(totalHt)"></span>
                            <span class="mx-2">·</span>
                            <span>TVA:</span>
                            <span class="font-bold text-slate-900 ml-1" x-text="fmtXaf(totalHt * 0.175)"></span>
                            <span class="mx-2">·</span>
                            <span>CAC:</span>
                            <span class="font-bold text-slate-900 ml-1" x-text="fmtXaf(totalHt * 0.175 * 0.10)"></span>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-400 uppercase font-bold">Total TTC</p>
                            <p class="text-xl font-black text-[#0A192F]" x-text="fmtXaf(totalHt * 1.1925)"></p>
                        </div>
                    </div>

                    <div x-show="invoiceError" class="bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700" x-text="invoiceError"></div>

                    <div class="flex gap-3">
                        <button @click="generatePdf()" :disabled="generating"
                                class="btn-amber flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-show="!generating" x-text="lang==='FR' ? 'Générer PDF' : 'Generate PDF'"></span>
                            <span x-show="generating">...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ── VAT CALCULATOR PAGE ────────────────────────────────────────── --}}
            <div x-show="page === 'calculator'" class="p-6 max-w-lg space-y-5">
                <h2 class="text-2xl font-black text-[#0A192F]" x-text="lang==='FR' ? 'Calculateur TVA Camerounais' : 'Cameroonian VAT Calculator'"></h2>
                <div class="card p-5 space-y-4" x-data="vatCalc()">
                    <div class="flex gap-2">
                        <button @click="mode='ht'" :class="mode==='ht' ? 'btn-primary' : 'border-2 border-slate-200 rounded-lg px-4 py-2 text-sm font-bold text-slate-600'" x-text="lang==='FR' ? 'À partir du HT' : 'From HT'"></button>
                        <button @click="mode='ttc'" :class="mode==='ttc' ? 'btn-primary' : 'border-2 border-slate-200 rounded-lg px-4 py-2 text-sm font-bold text-slate-600'" x-text="lang==='FR' ? 'À partir du TTC' : 'From TTC'"></button>
                    </div>
                    <div>
                        <label class="label" x-text="mode==='ht' ? (lang==='FR' ? 'Montant Hors Taxes (XAF)' : 'Amount excl. tax (XAF)') : (lang==='FR' ? 'Montant TTC (XAF)' : 'Amount incl. tax (XAF)')"></label>
                        <input type="number" x-model="amount" @input="calculate()" class="input text-lg font-bold" placeholder="100000" min="0">
                    </div>
                    <div x-show="result" class="bg-slate-50 border-2 border-slate-200 rounded-xl p-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="font-bold text-slate-600" x-text="lang==='FR' ? 'Montant HT' : 'Amount HT'"></span>
                            <span class="font-mono font-black text-slate-900" x-text="fmtXaf(result?.amount_ht)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="font-bold text-slate-600">TVA (17.5%)</span>
                            <span class="font-mono font-black text-blue-600" x-text="fmtXaf(result?.base_vat)"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="font-bold text-slate-600">CAC (10% TVA)</span>
                            <span class="font-mono font-black text-orange-600" x-text="fmtXaf(result?.cac)"></span>
                        </div>
                        <div class="flex justify-between text-sm border-t-2 border-slate-200 pt-3">
                            <span class="font-black text-slate-900 uppercase" x-text="lang==='FR' ? 'Total TTC' : 'Total incl. tax'"></span>
                            <span class="font-mono font-black text-[#0A192F] text-lg" x-text="fmtXaf(result?.amount_ttc)"></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── TAX DASHBOARD (iframe embed of Livewire component) ────────── --}}
            <div x-show="page === 'tax-dashboard'" class="p-0">
                <iframe src="/tax-dashboard" class="w-full border-0" style="height: calc(100vh - 0px);"></iframe>
            </div>

        </main>
    </div>

    <script>
    function opesApp() {
        return {
            page: 'dashboard',
            lang: localStorage.getItem('opes_lang') || 'FR',
            connStatus: navigator.onLine ? 'ONLINE' : 'OFFLINE',
            user: JSON.parse(localStorage.getItem('opes_user') || 'null'),
            company: null,
            transactions: [],
            stats: [],
            fiscalProvision: 0,
            today: new Date().toLocaleDateString('fr-CM', { weekday:'long', year:'numeric', month:'long', day:'numeric' }),

            async init() {
                const token = localStorage.getItem('opes_token');
                if (!token) { window.location.href = '/login'; return; }
                await this.loadMe();
                this.buildStats();
            },

            async api(path, opts = {}) {
                const token = localStorage.getItem('opes_token');
                const res = await fetch('/api/v1/' + path, {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'Content-Type': 'application/json', ...opts.headers },
                    ...opts,
                });
                if (res.status === 401) { localStorage.clear(); window.location.href = '/login'; }
                return res.json();
            },

            async loadMe() {
                const data = await this.api('auth/me');
                this.user = data.user;
                this.company = data.company;
            },

            buildStats() {
                this.stats = [
                    { key: 'revenue', labelFR: 'CA Brut HT', labelEN: 'Gross Revenue HT', value: 0, account: '701100 / 706000' },
                    { key: 'vat', labelFR: 'TVA Collectée', labelEN: 'Output VAT', value: 0, account: '443100' },
                    { key: 'cac', labelFR: 'CAC Dû', labelEN: 'CAC Due', value: 0, account: '448600' },
                    { key: 'expenses', labelFR: 'Charges', labelEN: 'Expenses', value: 0, account: 'Classe 6' },
                ];
            },

            async loadTransactions() {
                if (!this.company) return;
                const data = await this.api(`companies/${this.company.id}/ledger?per_page=50`);
                this.transactions = data.data || [];
            },

            toggleLang() {
                this.lang = this.lang === 'FR' ? 'EN' : 'FR';
                localStorage.setItem('opes_lang', this.lang);
            },

            async doLogout() {
                await this.api('auth/logout', { method: 'POST' });
                localStorage.clear();
                window.location.href = '/login';
            },

            fmtXaf(v) {
                if (v === null || v === undefined) return '— XAF';
                return Number(v).toLocaleString('fr-CM', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' XAF';
            },
        };
    }

    function invoiceForm() {
        return {
            generating: false,
            invoiceError: '',
            form: {
                invoice_number: 'FAC-' + new Date().getFullYear() + '-001',
                invoice_date: new Date().toISOString().split('T')[0],
                language: localStorage.getItem('opes_lang') || 'FR',
                client_name: '',
                client_niu: '',
                lines: [{ description: '', quantity: 1, unit_price_ht: 0 }],
            },
            get totalHt() {
                return this.form.lines.reduce((s, l) => s + (Number(l.quantity) * Number(l.unit_price_ht)), 0);
            },
            addLine() {
                this.form.lines.push({ description: '', quantity: 1, unit_price_ht: 0 });
            },
            fmtXaf(v) {
                if (!v) return '0 XAF';
                return Number(v).toLocaleString('fr-CM', { minimumFractionDigits: 2 }) + ' XAF';
            },
            async generatePdf() {
                this.generating = true; this.invoiceError = '';
                try {
                    const user = JSON.parse(localStorage.getItem('opes_user') || '{}');
                    const token = localStorage.getItem('opes_token');
                    const meRes = await fetch('/api/v1/auth/me', { headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' } });
                    const me = await meRes.json();
                    const companyId = me.company?.id;
                    if (!companyId) throw new Error('Company not found');

                    const payload = { ...this.form, lines: this.form.lines.map(l => ({ ...l, quantity: Number(l.quantity), unit_price_ht: Number(l.unit_price_ht) })) };

                    const res = await fetch(`/api/v1/companies/${companyId}/invoice/generate`, {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/pdf' },
                        body: JSON.stringify(payload),
                    });
                    if (!res.ok) { const err = await res.json(); throw new Error(err.message || 'PDF generation failed'); }
                    const blob = await res.blob();
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url; a.download = 'OPES-' + this.form.invoice_number + '.pdf'; a.click();
                    URL.revokeObjectURL(url);
                } catch(e) {
                    this.invoiceError = e.message;
                } finally {
                    this.generating = false;
                }
            },
        };
    }

    function vatCalc() {
        return {
            mode: 'ht',
            amount: '',
            result: null,
            async calculate() {
                if (!this.amount || this.amount <= 0) { this.result = null; return; }
                const token = localStorage.getItem('opes_token');
                const endpoint = this.mode === 'ht' ? 'tax/from-ht' : 'tax/from-ttc';
                const field = this.mode === 'ht' ? 'amount_ht' : 'amount_ttc';
                const res = await fetch('/api/v1/' + endpoint, {
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + token, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ [field]: this.amount }),
                });
                this.result = await res.json();
            },
            fmtXaf(v) {
                if (v === null || v === undefined) return '—';
                return Number(v).toLocaleString('fr-CM', { minimumFractionDigits: 2 }) + ' XAF';
            },
        };
    }
    </script>
</body>
</html>
