<div class="min-h-screen">

    {{-- Page Header --}}
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white uppercase tracking-wide leading-none">
                {{ $language === 'FR' ? 'Tableau de Bord Fiscal' : 'Tax Dashboard' }}
            </h1>
            @if($taxMetrics['company_name'] ?? false)
            <p class="text-xs text-slate-400 font-medium mt-1.5">
                <span class="text-slate-300 font-semibold">{{ $taxMetrics['company_name'] }}</span>
                • {{ $taxMetrics['tax_center'] }}
                • <span class="text-amber-400 font-bold">{{ $taxMetrics['tax_regime'] }}</span>
            </p>
            @endif
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <select wire:model.live="selectedMonth"
                    class="glass-input rounded-xl px-3 py-2 text-xs font-bold">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" style="background:#0a192f">
                        {{ $language === 'FR'
                            ? ['Janv','Févr','Mars','Avr','Mai','Juin','Juil','Août','Sept','Oct','Nov','Déc'][$m - 1]
                            : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][$m - 1]
                        }}
                    </option>
                @endforeach
            </select>
            <select wire:model.live="selectedYear"
                    class="glass-input rounded-xl px-3 py-2 text-xs font-bold">
                @foreach(range(now()->year - 3, now()->year) as $y)
                    <option value="{{ $y }}" style="background:#0a192f">{{ $y }}</option>
                @endforeach
            </select>
            <button wire:click="toggleLanguage"
                    class="glass-btn text-slate-950 font-black px-4 py-2 rounded-xl text-[11px] uppercase tracking-widest">
                {{ $language === 'FR' ? 'EN' : 'FR' }}
            </button>
        </div>
    </div>

    {{-- DGI Deadline badge --}}
    <div class="mb-5">
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[11px] font-black uppercase tracking-wider"
              style="background:rgba(201,155,14,0.12);border:1px solid rgba(201,155,14,0.3);color:rgb(252,211,77)">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            {{ $language === 'FR' ? 'Échéance DGI :' : 'DGI Deadline:' }}
            {{ \Carbon\Carbon::parse($taxMetrics['filing_deadline'])->format('d/m/Y') }}
            — {{ $language === 'FR' ? '15 du Mois Suivant' : '15th of Following Month' }}
        </span>
    </div>

    {{-- Main grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- LEFT 2/3 --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Turnover panel --}}
            <div class="glass-shimmer rounded-2xl p-5"
                 style="background:rgba(255,255,255,0.055);backdrop-filter:blur(28px) saturate(180%);-webkit-backdrop-filter:blur(28px) saturate(180%);border:1px solid rgba(255,255,255,0.13);box-shadow:0 4px 24px rgba(0,0,0,0.4)">
                <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'Chiffre d\'Affaires' : 'Turnover' }}
                </h2>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-3xl font-black text-white font-mono">
                            {{ number_format((float) $taxMetrics['base_turnover_ht'], 2) }}
                            <span class="text-base font-bold text-slate-500 ml-1">XAF</span>
                        </p>
                        <p class="text-xs text-slate-400 mt-1.5 font-medium">
                            {{ $language === 'FR' ? 'Hors Taxes (HT)' : 'Excl. Tax (HT)' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] text-slate-500 uppercase tracking-widest font-black">Prorata</p>
                        <p class="text-2xl font-black text-emerald-400">{{ $taxMetrics['prorata_coefficient'] }}%</p>
                    </div>
                </div>
            </div>

            {{-- Output taxes --}}
            <div class="glass-shimmer rounded-2xl p-5"
                 style="background:rgba(255,255,255,0.055);backdrop-filter:blur(28px) saturate(180%);-webkit-backdrop-filter:blur(28px) saturate(180%);border:1px solid rgba(255,255,255,0.13);box-shadow:0 4px 24px rgba(0,0,0,0.4)">
                <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'Taxes Collectées (Sortie)' : 'Output Taxes Collected' }}
                </h2>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-xl p-4" style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2)">
                        <p class="text-[10px] text-indigo-400 font-black uppercase tracking-wider mb-2">
                            {{ $language === 'FR' ? 'TVA Collectée 17.5%' : 'Output VAT 17.5%' }}
                        </p>
                        <p class="text-xl font-black text-white font-mono">{{ number_format((float) $taxMetrics['output_vat_collected'], 2) }}</p>
                        <p class="text-[9px] text-slate-500 mt-1 font-mono">XAF · Cpte 443100</p>
                    </div>
                    <div class="rounded-xl p-4" style="background:rgba(201,155,14,0.1);border:1px solid rgba(201,155,14,0.2)">
                        <p class="text-[10px] text-amber-400 font-black uppercase tracking-wider mb-2">
                            {{ $language === 'FR' ? 'CAC (10% TVA)' : 'CAC (10% of VAT)' }}
                        </p>
                        <p class="text-xl font-black text-white font-mono">{{ number_format((float) $taxMetrics['output_cac_collected'], 2) }}</p>
                        <p class="text-[9px] text-slate-500 mt-1 font-mono">XAF · Cpte 448600</p>
                    </div>
                    <div class="rounded-xl p-4" style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.14)">
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-wider mb-2">
                            {{ $language === 'FR' ? 'Total Taxes Sortie' : 'Total Output Tax' }}
                        </p>
                        <p class="text-xl font-black text-amber-400 font-mono">{{ number_format((float) $taxMetrics['total_output_tax'], 2) }}</p>
                        <p class="text-[9px] text-slate-500 mt-1 font-mono">XAF</p>
                    </div>
                </div>
            </div>

            {{-- Input VAT --}}
            <div class="glass-shimmer rounded-2xl p-5"
                 style="background:rgba(255,255,255,0.055);backdrop-filter:blur(28px) saturate(180%);-webkit-backdrop-filter:blur(28px) saturate(180%);border:1px solid rgba(255,255,255,0.13);box-shadow:0 4px 24px rgba(0,0,0,0.4)">
                <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'TVA Déductible (Entrée)' : 'Input VAT Recovery' }}
                </h2>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-xl p-4" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1)">
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-wider mb-2">
                            {{ $language === 'FR' ? 'TVA Brute Payée' : 'Gross Input VAT' }}
                        </p>
                        <p class="text-xl font-black text-white font-mono">{{ number_format((float) $taxMetrics['input_vat_gross'], 2) }}</p>
                        <p class="text-[9px] text-slate-500 mt-1 font-mono">XAF · Cpte 445xxx</p>
                    </div>
                    <div class="rounded-xl p-4" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2)">
                        <p class="text-[10px] text-emerald-400 font-black uppercase tracking-wider mb-2">
                            {{ $language === 'FR' ? 'TVA Récupérable' : 'Recoverable VAT' }}
                        </p>
                        <p class="text-xl font-black text-emerald-400 font-mono">{{ number_format((float) $taxMetrics['input_vat_recoverable'], 2) }}</p>
                        <p class="text-[9px] text-slate-500 mt-1 font-mono">XAF ({{ $taxMetrics['prorata_coefficient'] }}%)</p>
                    </div>
                    <div class="rounded-xl p-4" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1)">
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-wider mb-2">
                            {{ $language === 'FR' ? 'Charges Déduct. HT' : 'Deductible Expenses' }}
                        </p>
                        <p class="text-xl font-black text-white font-mono">{{ number_format((float) $taxMetrics['deductible_expenses_ht'], 2) }}</p>
                        <p class="text-[9px] text-slate-500 mt-1 font-mono">XAF · Classe 6</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT 1/3 --}}
        <div class="space-y-4">

            {{-- Net VAT to remit --}}
            <div class="glass-shimmer rounded-2xl p-5"
                 style="background:rgba(255,255,255,0.055);backdrop-filter:blur(28px) saturate(180%);-webkit-backdrop-filter:blur(28px) saturate(180%);border:1px solid rgba(255,255,255,0.13);box-shadow:0 4px 24px rgba(0,0,0,0.4)">
                <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'TVA Nette à Reverser' : 'Net VAT to Remit' }}
                </h2>
                <p class="text-3xl font-black font-mono" style="color:rgb(252,165,165)">
                    {{ number_format((float) $taxMetrics['net_vat_to_remit'], 2) }}
                </p>
                <p class="text-xs text-slate-500 mt-1">XAF</p>
                <div class="mt-4 space-y-1.5 text-[11px]">
                    <div class="flex justify-between text-slate-400">
                        <span>{{ $language === 'FR' ? 'Taxes Sortie' : 'Output Tax' }}</span>
                        <span class="font-mono font-bold text-white">{{ number_format((float) $taxMetrics['total_output_tax'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-emerald-400">
                        <span>{{ $language === 'FR' ? '− TVA Récupérable' : '− Input VAT' }}</span>
                        <span class="font-mono font-bold">{{ number_format((float) $taxMetrics['input_vat_recoverable'], 2) }}</span>
                    </div>
                    <div class="border-t pt-1.5 flex justify-between font-black" style="border-color:rgba(255,255,255,0.1);color:rgb(252,165,165)">
                        <span>Net DGI</span>
                        <span class="font-mono">{{ number_format((float) $taxMetrics['net_vat_to_remit'], 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Monthly IS Installment --}}
            <div class="glass-shimmer rounded-2xl p-5"
                 style="background:rgba(255,255,255,0.055);backdrop-filter:blur(28px) saturate(180%);-webkit-backdrop-filter:blur(28px) saturate(180%);border:1px solid rgba(255,255,255,0.13);box-shadow:0 4px 24px rgba(0,0,0,0.4)">
                <h2 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'Acompte IS Mensuel' : 'Monthly IS Installment' }}
                </h2>
                <p class="text-3xl font-black text-white font-mono">
                    {{ number_format((float) $taxMetrics['minimum_tax_installment'], 2) }}
                </p>
                <p class="text-xs text-slate-500 mt-1">XAF</p>
                <p class="text-[10px] text-slate-500 mt-2.5 font-medium">
                    {{ $language === 'FR' ? 'Taux :' : 'Rate:' }}
                    <span class="text-amber-400 font-black">{{ number_format((float) $taxMetrics['installment_rate'] * 100, 1) }}%</span>
                    ({{ $taxMetrics['tax_regime'] }})
                </p>
            </div>

            {{-- Total Fiscal Provision --}}
            <div class="glass-shimmer rounded-2xl p-5 relative overflow-hidden"
                 style="background:linear-gradient(145deg,rgba(201,155,14,0.12),rgba(10,25,47,0.85));backdrop-filter:blur(28px) saturate(180%);-webkit-backdrop-filter:blur(28px) saturate(180%);border:1px solid rgba(201,155,14,0.3);box-shadow:0 8px 32px rgba(201,155,14,0.15),0 4px 24px rgba(0,0,0,0.5)">
                <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full opacity-20 blur-2xl pointer-events-none"
                     style="background:radial-gradient(circle,rgba(201,155,14,1),transparent)"></div>
                <h2 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 relative z-10">
                    {{ $language === 'FR' ? 'Provision Fiscale Totale' : 'Total Fiscal Provision' }}
                </h2>
                <p class="text-4xl font-black text-amber-400 font-mono relative z-10">
                    {{ number_format((float) $taxMetrics['total_fiscal_provision'], 2) }}
                </p>
                <p class="text-xs text-slate-400 mt-1 relative z-10">XAF</p>
                <div class="mt-4 space-y-1.5 text-[11px] relative z-10">
                    <div class="flex justify-between text-slate-400">
                        <span>{{ $language === 'FR' ? 'TVA Nette' : 'Net VAT' }}</span>
                        <span class="font-mono font-bold text-slate-300">{{ number_format((float) $taxMetrics['net_vat_to_remit'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-400">
                        <span>{{ $language === 'FR' ? 'Acompte IS' : 'IS Installment' }}</span>
                        <span class="font-mono font-bold text-slate-300">{{ number_format((float) $taxMetrics['minimum_tax_installment'], 2) }}</span>
                    </div>
                    <div class="border-t pt-1.5 flex justify-between font-black text-amber-400" style="border-color:rgba(201,155,14,0.3)">
                        <span>TOTAL</span>
                        <span class="font-mono">{{ number_format((float) $taxMetrics['total_fiscal_provision'], 2) }}</span>
                    </div>
                </div>
                <div class="mt-4 px-3 py-2.5 rounded-xl text-[10px] font-bold relative z-10"
                     style="background:rgba(201,155,14,0.1);border:1px solid rgba(201,155,14,0.25);color:rgb(252,211,77)">
                    {{ $language === 'FR'
                        ? 'À déclarer avant le ' . \Carbon\Carbon::parse($taxMetrics['filing_deadline'])->format('d/m/Y') . ' (DGI Cameroun)'
                        : 'File before ' . \Carbon\Carbon::parse($taxMetrics['filing_deadline'])->format('d/m/Y') . ' (DGI Cameroon)'
                    }}
                </div>
            </div>

        </div>
    </div>

    {{-- Livewire loading overlay --}}
    <div wire:loading.flex class="fixed inset-0 items-center justify-center z-50"
         style="background:rgba(5,13,26,0.7);backdrop-filter:blur(8px)">
        <div class="glass-card rounded-2xl px-7 py-5 flex items-center gap-4">
            <svg class="spin-pulse h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span class="text-sm font-black text-white uppercase tracking-wider">
                {{ $language === 'FR' ? 'Calcul en cours…' : 'Recalculating…' }}
            </span>
        </div>
    </div>

</div>
