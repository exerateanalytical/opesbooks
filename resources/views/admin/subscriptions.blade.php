@extends('admin.layout')

@section('title', 'Subscriptions')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Subscriptions</h1>
    <p class="text-slate-500 text-xs mt-1">Across all tenants</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Active</div>
        <div class="text-3xl font-black text-emerald-400 mt-2">{{ number_format($stats['active']) }}</div>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">Suspended</div>
        <div class="text-3xl font-black text-amber-400 mt-2">{{ number_format($stats['suspended']) }}</div>
    </div>
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-5">
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
                     : 'bg-slate-800 text-slate-400 border-slate-700 hover:text-white' }}">
            {{ $filter['label'] }}
        </a>
    @endforeach
</div>

<div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-slate-800 bg-slate-950/50">
                    <th class="py-3 px-6">Company</th>
                    <th class="py-3 px-4">Plan</th>
                    <th class="py-3 px-4">Amount</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Renews</th>
                    <th class="py-3 px-4">Phone</th>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500 text-sm">No subscriptions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subscriptions->hasPages())
        <div class="px-6 py-4 border-t border-slate-800">
            {{ $subscriptions->links() }}
        </div>
    @endif
</div>
@endsection
