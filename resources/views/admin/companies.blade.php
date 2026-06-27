@extends('admin.layout')

@section('title', 'Companies')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Companies</h1>
    <p class="text-slate-500 text-xs mt-1">All registered tenants</p>
</div>

<form method="GET" class="mb-6 flex items-center gap-3">
    <input type="text" name="search" value="{{ request('search') }}"
           placeholder="Search by name or NIU…"
           class="bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500/50 w-full max-w-sm">
    <button type="submit"
            class="px-4 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest bg-amber-500/15 hover:bg-amber-500/25 text-amber-300 border border-amber-500/30 transition-all">
        Search
    </button>
</form>

<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-[#0B1120]/50">
                    <th class="py-3 px-6">Name</th>
                    <th class="py-3 px-4">NIU</th>
                    <th class="py-3 px-4">Plan</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Renews</th>
                    <th class="py-3 px-4 text-center">Users</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($companies as $company)
                    @php $sub = $company->subscriptions->first(); @endphp
                    <tr class="hover:bg-[#1C2A3A]/40 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-white">{{ $company->name }}</td>
                        <td class="py-3.5 px-4 text-slate-400 font-mono text-[10px]">{{ $company->niu ?? '—' }}</td>
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
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[10px]">
                            {{ $sub?->period_end ? \Carbon\Carbon::parse($sub->period_end)->format('Y-m-d') : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-center text-slate-300 font-bold">{{ $company->users_count }}</td>
                        <td class="py-3.5 px-4">
                            <a href="{{ route('admin.company', $company) }}"
                               class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-[#1C2A3A] hover:bg-slate-700 text-slate-300 border border-[#334155] transition-all">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-500 text-sm">No companies found.</td>
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
