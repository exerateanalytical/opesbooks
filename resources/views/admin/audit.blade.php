@extends('admin.layout')

@section('title', 'Platform Audit')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Platform Audit</h1>
    <p class="text-slate-500 text-xs mt-1">Cross-tenant activity trail</p>
</div>

<!-- Filter bar -->
<form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search user, company, IP…"
           class="bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60 flex-1 min-w-[200px]">
    <select name="action"
            class="bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
        <option value="" @selected(request('action') === '' || request('action') === null)>All Actions</option>
        @foreach(['CREATE','UPDATE','DELETE','IMPERSONATE','LOGIN'] as $act)
            <option value="{{ $act }}" @selected(request('action') === $act)>{{ $act }}</option>
        @endforeach
    </select>
    <button type="submit"
            class="px-4 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all">
        Filter
    </button>
</form>

<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-slate-950/50">
                    <th class="py-3 px-6">Time</th>
                    <th class="py-3 px-4">User</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Action</th>
                    <th class="py-3 px-4">Target</th>
                    <th class="py-3 px-4">IP</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($logs as $log)
                    @php
                        $a = strtoupper($log->action);
                        $actionColor = match($a) {
                            'CREATE', 'CREATED' => 'text-emerald-400',
                            'UPDATE' => 'text-blue-400',
                            'DELETE' => 'text-red-400',
                            'IMPERSONATE' => 'text-amber-400',
                            'LOGIN' => 'text-slate-400',
                            default => 'text-slate-300',
                        };
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="py-3.5 px-6 font-mono text-[10px] text-slate-500">{{ $log->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="py-3.5 px-4 text-slate-300">{{ $log->user->name ?? 'system' }}</td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $log->company->name ?? '—' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="font-black uppercase text-[10px] {{ $actionColor }}">{{ $log->action }}</span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-400">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</td>
                        <td class="py-3.5 px-4 font-mono text-[10px] text-slate-500">{{ $log->ip_address }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500 text-sm">No audit activity.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-[#253347]">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@endsection
