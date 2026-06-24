<x-app-layout title="Tableau de Bord">

    {{-- Page Header --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-black text-slate-950 uppercase tracking-wide">Tableau de Bord</h1>
            <p class="text-xs font-medium text-slate-500 mt-0.5">
                Exercice fiscal {{ date('Y') }} • {{ $company->name ?? 'Entreprise' }} •
                NIU: <span class="font-mono font-bold text-slate-700">{{ $company->niu ?? '—' }}</span> •
                {{ $company->tax_center ?? '' }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-[11px] font-black uppercase tracking-wider text-slate-600 bg-slate-100 border-2 border-slate-200 px-3 py-1.5 rounded">
                Régime: <span class="text-slate-950">{{ $company->tax_regime ?? '—' }}</span>
            </span>
            <span class="text-[11px] font-black uppercase tracking-wider text-slate-600 bg-slate-100 border-2 border-slate-200 px-3 py-1.5 rounded">
                RCCM: <span class="font-mono text-slate-950">{{ $company->rccm ?? '—' }}</span>
            </span>
        </div>
    </div>

    {{-- KPI Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $kpis = [
                ['label' => 'Chiffre d\'affaires HT', 'sub' => 'Revenue (excl. tax)', 'value' => $stats['revenue_ht'] ?? 0, 'color' => 'emerald'],
                ['label' => 'TVA Collectée', 'sub' => 'Output VAT (443100)', 'value' => $stats['vat_collected'] ?? 0, 'color' => 'blue'],
                ['label' => 'CAC Dû', 'sub' => 'Municipal Surcharge (448600)', 'value' => $stats['cac_due'] ?? 0, 'color' => 'indigo'],
                ['label' => 'Charges Totales', 'sub' => 'Total Class 6 Expenses', 'value' => $stats['total_expenses'] ?? 0, 'color' => 'rose'],
            ];
        @endphp

        @foreach($kpis as $kpi)
            <div class="bg-white border-2 border-slate-200 rounded-xl p-4 shadow-sm">
                <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1">{{ $kpi['label'] }}</div>
                <div class="text-[10px] text-slate-400 font-medium mb-3">{{ $kpi['sub'] }}</div>
                <div class="font-mono font-black text-slate-950 text-lg leading-none">
                    {{ number_format($kpi['value'], 0, '.', ',') }}
                    <span class="text-xs text-slate-400 font-bold">XAF</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Main Two-Column Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

        {{-- Left: Intent Selector (2/5) --}}
        <div class="xl:col-span-2">
            <x-intent-selector
                :companyNiu="$company->niu ?? ''"
                :csrfToken="csrf_token()"
            />
        </div>

        {{-- Right: MoMo Feed (3/5) --}}
        <div class="xl:col-span-3">
            <x-momo-feed
                :transactions="$recentTransactions ?? []"
                :unresolvedCount="$unresolvedCount ?? 0"
                :pagination="$pagination ?? null"
            />
        </div>

    </div>

</x-app-layout>
