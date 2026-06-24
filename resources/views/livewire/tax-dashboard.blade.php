<div class="min-h-screen bg-gray-50">

    {{-- ── Header bar ─────────────────────────────────────────────────────────── --}}
    <div class="bg-[#0A192F] text-white px-6 py-4">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h1 class="text-xl font-bold text-[#F59E0B]">
                    {{ $language === 'FR' ? 'Tableau de Bord Fiscal' : 'Tax Dashboard' }}
                </h1>
                @if($taxMetrics['company_name'] ?? false)
                    <p class="text-sm text-slate-300 mt-0.5">
                        {{ $taxMetrics['company_name'] }}
                        &nbsp;·&nbsp;
                        {{ $taxMetrics['tax_center'] }}
                        &nbsp;·&nbsp;
                        {{ $taxMetrics['tax_regime'] }}
                    </p>
                @endif
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                {{-- Period selectors --}}
                <select wire:model.live="selectedMonth"
                        class="bg-slate-700 text-white text-sm rounded px-3 py-1.5 border border-slate-600 focus:outline-none focus:ring-1 focus:ring-[#F59E0B]">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}">
                            {{ $language === 'FR'
                                ? ['Janv','Févr','Mars','Avr','Mai','Juin','Juil','Août','Sept','Oct','Nov','Déc'][$m - 1]
                                : ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][$m - 1]
                            }}
                        </option>
                    @endforeach
                </select>

                <select wire:model.live="selectedYear"
                        class="bg-slate-700 text-white text-sm rounded px-3 py-1.5 border border-slate-600 focus:outline-none focus:ring-1 focus:ring-[#F59E0B]">
                    @foreach(range(now()->year - 3, now()->year) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>

                {{-- Language toggle --}}
                <button wire:click="toggleLanguage"
                        class="px-3 py-1.5 text-sm font-semibold rounded border border-[#F59E0B] text-[#F59E0B] hover:bg-[#F59E0B] hover:text-[#0A192F] transition-colors">
                    {{ $language === 'FR' ? 'EN' : 'FR' }}
                </button>
            </div>
        </div>
    </div>

    {{-- ── DGI Filing Deadline badge ──────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-6 py-3">
        <span class="inline-flex items-center gap-2 bg-amber-50 border border-amber-300 text-amber-800 text-xs font-semibold px-3 py-1.5 rounded-full">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
            </svg>
            {{ $language === 'FR' ? 'Échéance DGI :' : 'DGI Deadline:' }}
            {{ \Carbon\Carbon::parse($taxMetrics['filing_deadline'])->format('d/m/Y') }}
            &nbsp;—&nbsp;
            {{ $language === 'FR' ? '15 du Mois Suivant' : '15th of Following Month' }}
        </span>
    </div>

    {{-- ── Main content ────────────────────────────────────────────────────────── --}}
    <div class="max-w-7xl mx-auto px-6 pb-10 grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── LEFT 2/3 : VAT Matrix ──────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Turnover panel --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'Chiffre d\'Affaires' : 'Turnover' }}
                </h2>
                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-3xl font-bold text-[#0A192F]">
                            {{ number_format((float) $taxMetrics['base_turnover_ht'], 2) }}
                            <span class="text-base font-normal text-gray-400">XAF</span>
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $language === 'FR' ? 'Hors Taxes (HT)' : 'Excl. Tax (HT)' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">{{ $language === 'FR' ? 'Prorata' : 'Prorata' }}</p>
                        <p class="text-lg font-semibold text-[#10B981]">{{ $taxMetrics['prorata_coefficient'] }}%</p>
                    </div>
                </div>
            </div>

            {{-- Output tax breakdown --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'Taxes Collectées (Sortie)' : 'Output Taxes Collected' }}
                </h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-xs text-blue-600 font-semibold mb-1">
                            {{ $language === 'FR' ? 'TVA Collectée (17.5%)' : 'Output VAT (17.5%)' }}
                        </p>
                        <p class="text-xl font-bold text-[#0A192F]">
                            {{ number_format((float) $taxMetrics['output_vat_collected'], 2) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">XAF · Cpte 443100</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <p class="text-xs text-orange-600 font-semibold mb-1">
                            {{ $language === 'FR' ? 'CAC (10% TVA)' : 'CAC (10% of VAT)' }}
                        </p>
                        <p class="text-xl font-bold text-[#0A192F]">
                            {{ number_format((float) $taxMetrics['output_cac_collected'], 2) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">XAF · Cpte 448600</p>
                    </div>
                    <div class="bg-[#0A192F] rounded-lg p-4">
                        <p class="text-xs text-slate-300 font-semibold mb-1">
                            {{ $language === 'FR' ? 'Total Taxes Sortie' : 'Total Output Tax' }}
                        </p>
                        <p class="text-xl font-bold text-[#F59E0B]">
                            {{ number_format((float) $taxMetrics['total_output_tax'], 2) }}
                        </p>
                        <p class="text-xs text-slate-400 mt-0.5">XAF</p>
                    </div>
                </div>
            </div>

            {{-- Input VAT recovery --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'TVA Déductible (Entrée)' : 'Input VAT Recovery' }}
                </h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 font-semibold mb-1">
                            {{ $language === 'FR' ? 'TVA Brute Payée' : 'Gross Input VAT' }}
                        </p>
                        <p class="text-xl font-bold text-[#0A192F]">
                            {{ number_format((float) $taxMetrics['input_vat_gross'], 2) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">XAF · Cpte 445xxx</p>
                    </div>
                    <div class="bg-emerald-50 rounded-lg p-4">
                        <p class="text-xs text-emerald-700 font-semibold mb-1">
                            {{ $language === 'FR' ? 'TVA Récupérable' : 'Recoverable VAT' }}
                        </p>
                        <p class="text-xl font-bold text-[#10B981]">
                            {{ number_format((float) $taxMetrics['input_vat_recoverable'], 2) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">XAF (Prorata {{ $taxMetrics['prorata_coefficient'] }}%)</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-xs text-gray-600 font-semibold mb-1">
                            {{ $language === 'FR' ? 'Charges Déductibles HT' : 'Deductible Expenses HT' }}
                        </p>
                        <p class="text-xl font-bold text-[#0A192F]">
                            {{ number_format((float) $taxMetrics['deductible_expenses_ht'], 2) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">XAF · Classe 6</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── RIGHT 1/3 : DGI Liability panel ──────────────────────────────────── --}}
        <div class="space-y-5">

            {{-- Net VAT --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'TVA Nette à Reverser' : 'Net VAT to Remit' }}
                </h2>
                <p class="text-3xl font-bold text-red-600">
                    {{ number_format((float) $taxMetrics['net_vat_to_remit'], 2) }}
                </p>
                <p class="text-sm text-gray-400 mt-1">XAF</p>
                <div class="mt-3 text-xs text-gray-500 space-y-1">
                    <div class="flex justify-between">
                        <span>{{ $language === 'FR' ? 'Taxes Sortie' : 'Output Tax' }}</span>
                        <span class="font-medium">{{ number_format((float) $taxMetrics['total_output_tax'], 2) }}</span>
                    </div>
                    <div class="flex justify-between text-[#10B981]">
                        <span>{{ $language === 'FR' ? '− TVA Récupérable' : '− Input VAT' }}</span>
                        <span class="font-medium">{{ number_format((float) $taxMetrics['input_vat_recoverable'], 2) }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-1 flex justify-between font-semibold text-red-600">
                        <span>{{ $language === 'FR' ? 'Net DGI' : 'Net DGI' }}</span>
                        <span>{{ number_format((float) $taxMetrics['net_vat_to_remit'], 2) }}</span>
                    </div>
                </div>
            </div>

            {{-- Minimum Tax / Acompte IS --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'Acompte IS Mensuel' : 'Monthly Tax Installment' }}
                </h2>
                <p class="text-3xl font-bold text-[#0A192F]">
                    {{ number_format((float) $taxMetrics['minimum_tax_installment'], 2) }}
                </p>
                <p class="text-sm text-gray-400 mt-1">XAF</p>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $language === 'FR' ? 'Taux :' : 'Rate:' }}
                    {{ number_format((float) $taxMetrics['installment_rate'] * 100, 1) }}%
                    ({{ $taxMetrics['tax_regime'] }})
                </p>
            </div>

            {{-- Total Fiscal Provision --}}
            <div class="bg-[#0A192F] rounded-xl p-5 text-white">
                <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-4">
                    {{ $language === 'FR' ? 'Provision Fiscale Totale' : 'Total Fiscal Provision' }}
                </h2>
                <p class="text-4xl font-extrabold text-[#F59E0B]">
                    {{ number_format((float) $taxMetrics['total_fiscal_provision'], 2) }}
                </p>
                <p class="text-sm text-slate-400 mt-1">XAF</p>
                <div class="mt-4 text-xs text-slate-400 space-y-1">
                    <div class="flex justify-between">
                        <span>{{ $language === 'FR' ? 'TVA Nette' : 'Net VAT' }}</span>
                        <span>{{ number_format((float) $taxMetrics['net_vat_to_remit'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ $language === 'FR' ? 'Acompte IS' : 'IS Installment' }}</span>
                        <span>{{ number_format((float) $taxMetrics['minimum_tax_installment'], 2) }}</span>
                    </div>
                    <div class="border-t border-slate-700 pt-1 flex justify-between font-bold text-[#F59E0B]">
                        <span>TOTAL</span>
                        <span>{{ number_format((float) $taxMetrics['total_fiscal_provision'], 2) }}</span>
                    </div>
                </div>

                <div class="mt-5 bg-amber-900/30 border border-amber-600/40 rounded-lg px-3 py-2 text-xs text-amber-300">
                    {{ $language === 'FR'
                        ? 'À déclarer avant le ' . \Carbon\Carbon::parse($taxMetrics['filing_deadline'])->format('d/m/Y') . ' (DGI Cameroun)'
                        : 'File before ' . \Carbon\Carbon::parse($taxMetrics['filing_deadline'])->format('d/m/Y') . ' (DGI Cameroon)'
                    }}
                </div>
            </div>

        </div>
    </div>

    {{-- Livewire loading indicator --}}
    <div wire:loading.flex class="fixed inset-0 bg-black/20 items-center justify-center z-50">
        <div class="bg-white rounded-lg px-6 py-4 shadow-xl flex items-center gap-3">
            <svg class="animate-spin h-5 w-5 text-[#F59E0B]" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span class="text-sm font-medium text-[#0A192F]">
                {{ $language === 'FR' ? 'Calcul en cours…' : 'Recalculating…' }}
            </span>
        </div>
    </div>

</div>
