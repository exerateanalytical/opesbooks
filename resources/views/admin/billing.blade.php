@extends('admin.layout')

@section('title', 'Billing & Revenue')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Billing &amp; Revenue</h1>
    <p class="text-slate-500 text-xs mt-1">Platform-wide subscription revenue</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">MRR</div>
        <div class="text-3xl font-black text-amber-400 mt-2">{{ number_format($metrics['mrr']) }} <span class="text-sm font-bold text-slate-500">XAF</span></div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">ARR</div>
        <div class="text-3xl font-black text-white mt-2">{{ number_format($metrics['arr']) }} <span class="text-sm font-bold text-slate-500">XAF</span></div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Active Subs</div>
        <div class="text-3xl font-black text-emerald-400 mt-2">{{ number_format($metrics['active']) }}</div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Avg / Company</div>
        <div class="text-3xl font-black text-white mt-2">{{ number_format($metrics['avg_per_co']) }} <span class="text-sm font-bold text-slate-500">XAF</span></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue last 6 months -->
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-6">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-5">Revenue (last 6 months)</div>
        @php $maxRevenue = count($byMonth) ? max($byMonth) : 0; @endphp
        <div class="space-y-3">
            @forelse($byMonth as $month => $amount)
                @php $pct = $maxRevenue > 0 ? ($amount / $maxRevenue) * 100 : 0; @endphp
                <div class="flex items-center gap-3">
                    <div class="w-16 shrink-0 text-[10px] font-mono font-bold text-slate-400">{{ $month }}</div>
                    <div class="flex-1 h-5 bg-slate-800/60 rounded-lg overflow-hidden">
                        <div class="h-full bg-amber-500/70 rounded-lg" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="w-32 shrink-0 text-right text-[10px] font-mono font-bold text-slate-300">{{ number_format($amount) }} XAF</div>
                </div>
            @empty
                <div class="text-center text-slate-500 text-sm py-8">No revenue data.</div>
            @endforelse
        </div>
    </div>

    <!-- By plan -->
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
        <div class="px-6 pt-6 pb-4 text-[9px] font-black uppercase tracking-widest text-slate-500">By plan</div>
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-slate-950/50">
                    <th class="py-3 px-6">Plan</th>
                    <th class="py-3 px-4 text-center">Count</th>
                    <th class="py-3 px-4 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($byPlan as $row)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-6">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $row->plan === 'ENTERPRISE' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' :
                                   ($row->plan === 'GROWTH' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' :
                                   'bg-slate-500/20 text-slate-300 border border-slate-500/30') }}">
                                {{ $row->plan }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center text-slate-300 font-bold">{{ number_format($row->n) }}</td>
                        <td class="py-3.5 px-4 text-right text-slate-300 font-mono text-[11px]">{{ number_format($row->total) }} XAF</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-12 text-center text-slate-500 text-sm">No plan data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Recent transactions -->
<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="px-6 pt-6 pb-4 text-[9px] font-black uppercase tracking-widest text-slate-500">Recent transactions</div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-slate-950/50">
                    <th class="py-3 px-6">Company</th>
                    <th class="py-3 px-4">Plan</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Date</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($recent as $tx)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-white">{{ $tx->company?->name ?? '—' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $tx->plan === 'ENTERPRISE' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' :
                                   ($tx->plan === 'GROWTH' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' :
                                   'bg-slate-500/20 text-slate-300 border border-slate-500/30') }}">
                                {{ $tx->plan }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-300 font-mono text-[11px]">{{ number_format($tx->amount_xaf) }} XAF</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $tx->status === 'ACTIVE' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
                                   ($tx->status === 'SUSPENDED' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' :
                                   'bg-red-500/20 text-red-300 border border-red-500/30') }}">
                                {{ $tx->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[10px]">{{ $tx->created_at->format('Y-m-d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-500 text-sm">No transactions yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Payments received (with receipts) -->
<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden mt-6">
    <div class="px-6 pt-6 pb-4 flex items-center justify-between">
        <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Paiements reçus</span>
        <span class="text-[10px] font-black px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30">Reçus PDF</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-slate-950/50">
                    <th class="py-3 px-6">Date</th><th class="py-3 px-4">Entreprise</th>
                    <th class="py-3 px-4">Montant</th><th class="py-3 px-4">Méthode</th>
                    <th class="py-3 px-4">Référence</th><th class="py-3 px-4">Reçu</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($payments as $p)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-6 font-mono text-[10px] text-slate-400">{{ $p->created_at?->format('Y-m-d') }}</td>
                        <td class="py-3.5 px-4 font-bold text-white">{{ $p->company?->name ?? '—' }}</td>
                        <td class="py-3.5 px-4 font-black text-amber-400">{{ number_format($p->amount_xaf) }} XAF</td>
                        <td class="py-3.5 px-4 text-slate-400">{{ str_replace('_', ' ', ucfirst($p->payment_method)) }}</td>
                        <td class="py-3.5 px-4 font-mono text-[10px] text-slate-500">{{ $p->reference ?? '—' }}</td>
                        <td class="py-3.5 px-4">
                            <a href="{{ route('admin.payments.receipt', $p) }}"
                               class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-[#1C2A3A] hover:bg-slate-700 text-slate-300 hover:text-white border border-[#334155]">
                                {{ $p->receipt_number }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-12 text-center text-slate-500 text-sm">Aucun paiement enregistré.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
