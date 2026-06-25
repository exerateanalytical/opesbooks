@extends('admin.layout')

@section('title', $company->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}" class="text-[10px] text-slate-500 hover:text-slate-300 uppercase tracking-widest font-bold transition-colors">
        ← Back to Dashboard
    </a>
</div>

<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">{{ $company->name }}</h1>
    <div class="flex flex-wrap gap-3 mt-2">
        <span class="text-[10px] text-slate-400 font-mono">NIU: {{ $company->niu ?? '—' }}</span>
        <span class="text-[10px] text-slate-600">·</span>
        <span class="text-[10px] text-slate-400 font-mono">RCCM: {{ $company->rccm ?? '—' }}</span>
        <span class="text-[10px] text-slate-600">·</span>
        <span class="text-[10px] text-slate-400">Régime: {{ $company->tax_regime ?? '—' }}</span>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    <!-- Subscription form -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
        <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-5">Update Subscription</h2>

        @php $sub = $company->subscriptions->first(); @endphp

        <form method="POST" action="{{ route('admin.company.subscription', $company) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Plan</label>
                <select name="plan" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500/60 transition-all">
                    @foreach(['STARTER','GROWTH','ENTERPRISE'] as $plan)
                        <option value="{{ $plan }}" {{ ($sub?->plan === $plan) ? 'selected' : '' }}>{{ $plan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Status</label>
                <select name="status" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500/60 transition-all">
                    @foreach(['ACTIVE','SUSPENDED','CANCELLED'] as $status)
                        <option value="{{ $status }}" {{ ($sub?->status === $status) ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Expires At</label>
                <input type="date" name="expires_at"
                       value="{{ $sub?->expires_at ? \Carbon\Carbon::parse($sub->expires_at)->format('Y-m-d') : '' }}"
                       class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-amber-500/60 transition-all">
            </div>
            @if($errors->any())
                <div class="px-4 py-3 rounded-xl bg-red-500/15 border border-red-500/30 text-red-300 text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif
            <button type="submit"
                    class="w-full py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 shadow-lg shadow-amber-500/20 transition-all">
                Update Subscription
            </button>
        </form>
    </div>

    <!-- Users list -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6">
        <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-5">
            Users ({{ $company->users->count() }})
        </h2>
        <div class="space-y-2">
            @forelse($company->users as $user)
                <div class="flex items-center justify-between px-4 py-3 rounded-xl bg-slate-800/60 border border-slate-700/60">
                    <div>
                        <div class="text-sm font-bold text-white">{{ $user->name }}</div>
                        <div class="text-[10px] text-slate-500 mt-0.5">{{ $user->email }}</div>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                        {{ $user->role === 'OWNER' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' :
                           ($user->role === 'ACCOUNTANT' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' :
                           'bg-slate-500/20 text-slate-300 border border-slate-500/30') }}">
                        {{ $user->role }}
                    </span>
                </div>
            @empty
                <div class="text-center text-slate-500 text-sm py-6">No users in this company.</div>
            @endforelse
        </div>
    </div>

</div>

<!-- Subscription history -->
@if($company->subscriptions->count() > 1)
<div class="mt-6 bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-slate-800">
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Subscription History</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs divide-y divide-slate-800/60">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 bg-slate-950/50">
                    <th class="py-3 px-6">Plan</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Amount (XAF)</th>
                    <th class="py-3 px-4">Expires</th>
                    <th class="py-3 px-4">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @foreach($company->subscriptions as $s)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="py-3 px-6 font-bold text-white">{{ $s->plan }}</td>
                        <td class="py-3 px-4 text-slate-400">{{ $s->status }}</td>
                        <td class="py-3 px-4 font-mono text-slate-400">{{ number_format($s->amount_xaf) }}</td>
                        <td class="py-3 px-4 font-mono text-slate-500 text-[10px]">
                            {{ $s->expires_at ? \Carbon\Carbon::parse($s->expires_at)->format('Y-m-d') : '—' }}
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-500 text-[10px]">{{ $s->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
