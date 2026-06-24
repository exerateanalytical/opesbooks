<x-app-layout title="Tableau de Bord">

    {{-- Page Header --}}
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-white uppercase tracking-wide leading-none">
                Tableau de Bord
            </h1>
            <p class="text-xs font-medium text-slate-400 mt-1.5">
                Exercice fiscal {{ date('Y') }} •
                <span class="text-slate-300 font-semibold">{{ $company->name ?? 'Entreprise' }}</span> •
                NIU: <span class="font-mono font-bold text-amber-400">{{ $company->niu ?? '—' }}</span>
                @if($company->tax_center ?? false)
                    • {{ $company->tax_center }}
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="glass-card text-[11px] font-black uppercase tracking-wider text-slate-300 px-3 py-1.5 rounded-xl">
                Régime: <span class="text-amber-400 ml-1">{{ $company->tax_regime ?? '—' }}</span>
            </span>
            <span class="glass-card text-[11px] font-black uppercase tracking-wider text-slate-300 px-3 py-1.5 rounded-xl">
                RCCM: <span class="font-mono text-white ml-1">{{ $company->rccm ?? '—' }}</span>
            </span>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @php
            $kpis = [
                [
                    'label' => 'Chiffre d\'affaires HT',
                    'sub'   => 'Revenue excl. tax',
                    'value' => $stats['revenue_ht'] ?? 0,
                    'accent'=> 'rgba(16,185,129,',
                    'glow'  => 'rgba(16,185,129,0.2)',
                    'text'  => 'text-emerald-400',
                    'icon'  => '↑',
                ],
                [
                    'label' => 'TVA Collectée',
                    'sub'   => 'Output VAT 443100',
                    'value' => $stats['vat_collected'] ?? 0,
                    'accent'=> 'rgba(99,102,241,',
                    'glow'  => 'rgba(99,102,241,0.2)',
                    'text'  => 'text-indigo-400',
                    'icon'  => '⊕',
                ],
                [
                    'label' => 'CAC Dû',
                    'sub'   => 'Municipal Surtax 448600',
                    'value' => $stats['cac_due'] ?? 0,
                    'accent'=> 'rgba(168,85,247,',
                    'glow'  => 'rgba(168,85,247,0.2)',
                    'text'  => 'text-purple-400',
                    'icon'  => '◎',
                ],
                [
                    'label' => 'Charges Totales',
                    'sub'   => 'Total Class 6 Expenses',
                    'value' => $stats['total_expenses'] ?? 0,
                    'accent'=> 'rgba(244,63,94,',
                    'glow'  => 'rgba(244,63,94,0.2)',
                    'text'  => 'text-rose-400',
                    'icon'  => '↓',
                ],
            ];
        @endphp

        @foreach($kpis as $kpi)
        <div class="glass-card rounded-2xl p-4 relative overflow-hidden float-in"
             style="box-shadow: 0 4px 24px rgba(0,0,0,0.45), 0 0 32px {{ $kpi['glow'] }}, 0 1px 0 rgba(255,255,255,0.12) inset;">
            {{-- Accent glow blob --}}
            <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full opacity-20 blur-2xl pointer-events-none"
                 style="background:radial-gradient(circle, {{ $kpi['accent'] }}1) 0%, transparent 70%)"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400">{{ $kpi['label'] }}</span>
                    <span class="{{ $kpi['text'] }} text-sm font-black opacity-70">{{ $kpi['icon'] }}</span>
                </div>
                <div class="font-mono font-black text-white text-xl leading-none tracking-tight">
                    {{ number_format($kpi['value'], 0, '.', ' ') }}
                    <span class="text-[11px] text-slate-500 font-bold ml-1">XAF</span>
                </div>
                <div class="text-[9px] text-slate-500 font-medium mt-1.5 uppercase tracking-wider">{{ $kpi['sub'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Main Two-Column Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-5 gap-5">

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
