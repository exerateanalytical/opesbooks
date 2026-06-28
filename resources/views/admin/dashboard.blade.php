@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Platform Dashboard</h1>
    <p class="text-slate-500 text-xs mt-1">Real-time overview of all tenants</p>
</div>

<!-- Stat cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-10">
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Total Companies</div>
        <div class="text-3xl font-black text-white">{{ number_format($stats['total_companies']) }}</div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Total Users</div>
        <div class="text-3xl font-black text-white">{{ number_format($stats['total_users']) }}</div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Active Subscriptions</div>
        <div class="text-3xl font-black text-emerald-400">{{ number_format($stats['active_subs']) }}</div>
    </div>
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">Revenue This Month</div>
        <div class="text-2xl font-black text-amber-400">{{ number_format($stats['revenue_this_month']) }} XAF</div>
    </div>
</div>

<!-- Platform-wide tenant data totals -->
<div class="mb-10">
    <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-3">Across all tenants</div>
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4">
        @php
            $tcards = [
                ['Clients', $totals['customers']],
                ['Suppliers', $totals['suppliers']],
                ['Projects', $totals['projects']],
                ['Transactions', $totals['transactions']],
                ['Invoices', $totals['invoices']],
                ['Employees', $totals['employees']],
            ];
        @endphp
        @foreach($tcards as [$label, $value])
        <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-4">
            <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">{{ $label }}</div>
            <div class="text-2xl font-black text-white mt-1">{{ number_format($value) }}</div>
        </div>
        @endforeach
    </div>
</div>

<!-- Companies table -->
<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-[#253347] flex items-center justify-between">
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">All Companies</span>
        <span class="text-[10px] font-mono font-black px-2.5 py-0.5 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30">
            {{ $companies->total() }} total
        </span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-[#0B1120]/50">
                    <th class="py-3 px-6">Name</th>
                    <th class="py-3 px-4">NIU</th>
                    <th class="py-3 px-4">Plan</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Expires</th>
                    <th class="py-3 px-4 text-center">Users</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($companies as $company)
                    @php $sub = $company->subscriptions->first(); @endphp
                    <tr class="hover:bg-[#1C2A3A]/40 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-white">{{ $company->name }}</td>
                        <td class="py-3.5 px-4 font-mono text-slate-400">{{ $company->niu ?? '—' }}</td>
                        <td class="py-3.5 px-4">
                            @if($sub)@include('admin.partials.plan_badge', ['plan' => $sub->plan])@else<span class="text-slate-600">—</span>@endif
                        </td>
                        <td class="py-3.5 px-4">
                            @if($sub)@include('admin.partials.sub_status_badge', ['status' => $sub->status])@else<span class="text-slate-600">—</span>@endif
                        </td>
                        <td class="py-3.5 px-4 text-slate-400 font-mono text-[10px]">
                            {{ $sub?->period_end ? \Carbon\Carbon::parse($sub->period_end)->format('Y-m-d') : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-black text-slate-300">
                            {{ $company->users->count() }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.company', $company) }}"
                                   class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-[#1C2A3A] hover:bg-slate-700 text-slate-300 hover:text-white border border-[#334155] hover:border-slate-600 transition-all">
                                    View
                                </a>
                                <a href="{{ route('admin.company.data', $company) }}"
                                   class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-amber-500/10 text-amber-300 border border-amber-500/30 hover:bg-amber-500/20 transition-all">
                                    Data
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500 text-sm">No companies yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
        <div class="px-6 py-4 border-t border-[#253347]">
            {{ $companies->links() }}
        </div>
    @endif
</div>
@endsection
