@extends('admin.layout')

@section('title', 'Subscriptions')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Subscriptions</h1>
    <p class="text-slate-500 text-xs mt-1">Across all tenants</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Active</div>
        <div class="text-3xl font-black text-emerald-400 mt-2">{{ number_format($stats['active']) }}</div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Suspended</div>
        <div class="text-3xl font-black text-amber-400 mt-2">{{ number_format($stats['suspended']) }}</div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Cancelled</div>
        <div class="text-3xl font-black text-red-400 mt-2">{{ number_format($stats['cancelled']) }}</div>
    </div>
</div>

@php
    $filters = [
        ['label' => 'All', 'value' => null],
        ['label' => 'ACTIVE', 'value' => 'ACTIVE'],
        ['label' => 'SUSPENDED', 'value' => 'SUSPENDED'],
        ['label' => 'CANCELLED', 'value' => 'CANCELLED'],
    ];
@endphp
<div class="flex items-center gap-2 mb-6">
    @foreach($filters as $filter)
        <a href="{{ route('admin.subscriptions', $filter['value'] ? ['status' => $filter['value']] : []) }}"
           class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all
                  {{ request('status') === $filter['value']
                     ? 'bg-amber-500/15 text-amber-300 border-amber-500/30'
                     : 'bg-[#1C2A3A] text-slate-400 border-[#334155] hover:text-white' }}">
            {{ $filter['label'] }}
        </a>
    @endforeach
</div>

<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-slate-950/50">
                    <th class="py-3 px-6">Company</th>
                    <th class="py-3 px-4">Plan</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Renews</th>
                    <th class="py-3 px-4">Phone</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($subscriptions as $sub)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-white">{{ $sub->company?->name ?? '—' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $sub->plan === 'ENTERPRISE' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' :
                                   ($sub->plan === 'GROWTH' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' :
                                   'bg-slate-500/20 text-slate-300 border border-slate-500/30') }}">
                                {{ $sub->plan }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-300 font-mono text-[11px]">{{ number_format($sub->amount_xaf) }} XAF</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $sub->status === 'ACTIVE' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
                                   ($sub->status === 'SUSPENDED' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' :
                                   'bg-red-500/20 text-red-300 border border-red-500/30') }}">
                                {{ $sub->status }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[10px]">
                            {{ $sub->period_end ? \Carbon\Carbon::parse($sub->period_end)->format('Y-m-d') : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-slate-400 font-mono text-[10px]">{{ $sub->billing_phone ?? '—' }}</td>
                        <td class="py-3.5 px-4">
                            @if($sub->company_id)
                            <button type="button" @click="$dispatch('record-pay', { cid: {{ $sub->company_id }}, name: @js($sub->company?->name), amount: {{ (int) $sub->amount_xaf }} })"
                                    class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-amber-500/15 text-amber-300 border border-amber-500/30 hover:bg-amber-500/25">
                                + Paiement
                            </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500 text-sm">No subscriptions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subscriptions->hasPages())
        <div class="px-6 py-4 border-t border-[#253347]">
            {{ $subscriptions->links() }}
        </div>
    @endif
</div>

<!-- Record payment modal -->
<div x-data="{ open:false, cid:null, cname:'', amount:0 }"
     @record-pay.window="open=true; cid=$event.detail.cid; cname=$event.detail.name; amount=$event.detail.amount"
     x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,0.6)"
     @click.self="open=false">
    <div class="bg-[#151F2E] border border-[#334155] rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-sm font-black text-white uppercase tracking-widest mb-1">Enregistrer un paiement</h3>
        <p class="text-xs text-slate-500 mb-4" x-text="cname"></p>
        <form method="POST" :action="'/admin/companies/' + cid + '/payments'" class="space-y-3">
            @csrf
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5">Montant (XAF)</label>
                <input type="number" name="amount_xaf" :value="amount" required
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-1.5">Méthode</label>
                <select name="payment_method" class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
                    <option value="orange_money">Orange Money</option>
                    <option value="mtn_momo">MTN MoMo</option>
                    <option value="bank_transfer">Virement</option>
                    <option value="cash">Espèces</option>
                    <option value="manual">Manuel</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="date" name="period_start" class="bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
                <input type="date" name="period_end" class="bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            </div>
            <input type="text" name="reference" placeholder="Référence transaction"
                   class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            <div class="flex gap-2 pt-1">
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400">Enregistrer</button>
                <button type="button" @click="open=false" class="px-4 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest bg-[#1C2A3A] text-slate-300 border border-[#334155]">Annuler</button>
            </div>
        </form>
    </div>
</div>
@endsection
