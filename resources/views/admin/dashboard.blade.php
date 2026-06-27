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
                            @if($sub)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                    {{ $sub->plan === 'ENTERPRISE' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' :
                                       ($sub->plan === 'GROWTH' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' :
                                       'bg-slate-500/20 text-slate-300 border border-slate-500/30') }}">
                                    {{ $sub->plan }}
                                </span>
                            @else
                                <span class="text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            @if($sub)
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                    {{ $sub->status === 'ACTIVE' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
                                       ($sub->status === 'SUSPENDED' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' :
                                       'bg-red-500/20 text-red-300 border border-red-500/30') }}">
                                    {{ $sub->status }}
                                </span>
                            @else
                                <span class="text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-slate-400 font-mono text-[10px]">
                            {{ $sub?->period_end ? \Carbon\Carbon::parse($sub->period_end)->format('Y-m-d') : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-black text-slate-300">
                            {{ $company->users->count() }}
                        </td>
                        <td class="py-3.5 px-4">
                            <a href="{{ route('admin.company', $company) }}"
                               class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-[#1C2A3A] hover:bg-slate-700 text-slate-300 hover:text-white border border-[#334155] hover:border-slate-600 transition-all">
                                View
                            </a>
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
