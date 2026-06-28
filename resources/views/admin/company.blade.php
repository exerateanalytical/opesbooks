@extends('admin.layout')

@section('title', $company->name)

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.dashboard') }}" class="text-[10px] text-slate-500 hover:text-slate-300 uppercase tracking-widest font-bold transition-colors">
        ← Back to Dashboard
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.company.data', $company) }}"
           class="text-[10px] font-black uppercase tracking-widest text-amber-300 hover:text-amber-200 border border-amber-500/30 bg-amber-500/10 rounded-lg px-3 py-1.5 transition-colors">
            🔎 View tenant data
        </a>
        <a href="{{ route('admin.company.export', $company) }}"
           class="text-[10px] font-black uppercase tracking-widest text-slate-300 hover:text-white border border-[#253347] rounded-lg px-3 py-1.5 transition-colors">
            ⬇ Export (JSON)
        </a>
    </div>
</div>

<div class="mb-6">
    <div class="flex items-center gap-3">
        <h1 class="text-2xl font-black text-white uppercase tracking-wide">{{ $company->name }}</h1>
        @php $st = $company->subscription_status; @endphp
        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide
            {{ $st === 'ACTIVE' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
               ($st === 'SUSPENDED' ? 'bg-red-500/20 text-red-300 border border-red-500/30' :
               'bg-amber-500/20 text-amber-300 border border-amber-500/30') }}"
            title="API access state enforced by the subscription middleware">
            <span class="opacity-60">Access</span> {{ $st ?? 'ACTIVE' }}
        </span>
    </div>
    <div class="flex flex-wrap gap-3 mt-2">
        <span class="text-[10px] text-slate-400 font-mono">NIU: {{ $company->niu ?? '—' }}</span>
        <span class="text-[10px] text-slate-600">·</span>
        <span class="text-[10px] text-slate-400 font-mono">RCCM: {{ $company->rccm ?? '—' }}</span>
        <span class="text-[10px] text-slate-600">·</span>
        <span class="text-[10px] text-slate-400">Régime: {{ $company->tax_regime ?? '—' }}</span>
    </div>
</div>

<!-- Health snapshot -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    @php
        $cards = [
            ['Journal entries', number_format($health['journal_entries']), $health['last_entry'] ? 'last '.\Carbon\Carbon::parse($health['last_entry'])->diffForHumans() : 'no activity'],
            ['Users', number_format($health['users']), $health['last_login'] ? 'login '.\Carbon\Carbon::parse($health['last_login'])->diffForHumans() : 'never logged in'],
            ['Payments collected', number_format($health['payments_total']).' XAF', 'all-time'],
            ['Next billing', $company->next_billing_at ? \Carbon\Carbon::parse($company->next_billing_at)->format('Y-m-d') : '—', $company->plan_slug ?? '—'],
        ];
    @endphp
    @foreach($cards as [$label, $value, $sub])
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-4">
        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500">{{ $label }}</div>
        <div class="text-lg font-black text-white mt-1 truncate">{{ $value }}</div>
        <div class="text-[10px] text-slate-500 mt-0.5">{{ $sub }}</div>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    <!-- Subscription form -->
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-6">
        <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-5">Update Subscription</h2>

        @php $sub = $company->subscriptions->first(); @endphp

        <form method="POST" action="{{ route('admin.company.subscription', $company) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Plan</label>
                <select name="plan" class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60 transition-all">
                    @foreach($plans as $p)
                        <option value="{{ $p->slug }}" {{ (($sub?->plan ?? $company->plan_slug) === $p->slug) ? 'selected' : '' }}>
                            {{ $p->name }} — {{ number_format($p->price_xaf_monthly) }} XAF/mois
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Subscription status <span class="text-slate-600">· syncs access</span></label>
                <select name="status" class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60 transition-all">
                    @foreach(['ACTIVE','SUSPENDED','CANCELLED'] as $status)
                        <option value="{{ $status }}" {{ ($sub?->status === $status) ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Expires At</label>
                    <input type="date" name="expires_at"
                           value="{{ $sub?->period_end ? \Carbon\Carbon::parse($sub->period_end)->format('Y-m-d') : '' }}"
                           class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60 transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Custom price (XAF)</label>
                    <input type="number" name="custom_price_xaf" min="0" placeholder="plan price"
                           value="{{ $company->custom_price_xaf }}"
                           class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60 transition-all">
                </div>
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
        <a href="{{ route('admin.company.invoice', $company) }}" target="_blank"
           class="mt-3 block text-center py-2 rounded-xl text-xs font-black uppercase tracking-widest text-slate-300 bg-[#1C2A3A] border border-[#334155] hover:border-amber-500 transition-all">
            ↗ Proforma invoice (PDF)
        </a>
    </div>

    <!-- Users list + management -->
    <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-6" x-data="{ adding: false, editing: null }">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                Users ({{ $company->users->count() }})
            </h2>
            <button @click="adding = !adding" class="text-[10px] font-black uppercase tracking-widest text-amber-300 hover:text-amber-200">+ Add user</button>
        </div>

        <!-- Add user -->
        <form x-show="adding" x-cloak method="POST" action="{{ route('admin.company.users.store', $company) }}"
              class="mb-4 p-4 rounded-xl bg-[#1C2A3A]/60 border border-[#334155]/60 grid grid-cols-2 gap-3">
            @csrf
            <input type="text" name="name" placeholder="Name" required class="col-span-2 bg-[#0B1120] border border-[#334155] rounded-lg px-3 py-2 text-xs text-white outline-none focus:border-amber-500">
            <input type="email" name="email" placeholder="Email" required class="bg-[#0B1120] border border-[#334155] rounded-lg px-3 py-2 text-xs text-white outline-none focus:border-amber-500">
            <input type="password" name="password" placeholder="Password (min 8)" required class="bg-[#0B1120] border border-[#334155] rounded-lg px-3 py-2 text-xs text-white outline-none focus:border-amber-500">
            <select name="role" class="bg-[#0B1120] border border-[#334155] rounded-lg px-3 py-2 text-xs text-white outline-none focus:border-amber-500">
                @foreach(['OWNER','ACCOUNTANT','CLERK','AUDITOR'] as $r)<option value="{{ $r }}">{{ $r }}</option>@endforeach
            </select>
            <button class="bg-amber-500 text-slate-900 rounded-lg px-3 py-2 text-xs font-black uppercase tracking-wide">Create</button>
        </form>

        <div class="space-y-2">
            @forelse($company->users as $user)
                <div class="px-4 py-3 rounded-xl bg-[#1C2A3A]/60 border border-[#334155]/60">
                    <div class="flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-white truncate">
                                {{ $user->name }}
                                @if($user->disabled_at)<span class="ml-1.5 text-[9px] font-black uppercase text-red-400">Disabled</span>@endif
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5 truncate">{{ $user->email }}</div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $user->role === 'OWNER' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' :
                                   ($user->role === 'ACCOUNTANT' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' :
                                   'bg-slate-500/20 text-slate-300 border border-slate-500/30') }}">
                                {{ $user->role }}
                            </span>
                            <button @click="editing === {{ $user->id }} ? editing = null : editing = {{ $user->id }}" class="text-[10px] text-slate-400 hover:text-white" title="Edit">✎</button>
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="inline">@csrf
                                <button class="text-[10px] font-black {{ $user->disabled_at ? 'text-emerald-400 hover:text-emerald-300' : 'text-amber-400 hover:text-amber-300' }}" title="{{ $user->disabled_at ? 'Enable' : 'Disable' }}">{{ $user->disabled_at ? 'ON' : 'OFF' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline"
                                  onsubmit="return confirm('Supprimer {{ $user->email }} ?')">@csrf @method('DELETE')
                                <button class="text-[10px] text-red-400 hover:text-red-300" title="Delete">🗑</button>
                            </form>
                        </div>
                    </div>
                    <!-- Inline edit -->
                    <form x-show="editing === {{ $user->id }}" x-cloak method="POST" action="{{ route('admin.users.update', $user) }}"
                          class="mt-3 grid grid-cols-2 gap-2">
                        @csrf
                        <input type="text" name="name" value="{{ $user->name }}" required class="bg-[#0B1120] border border-[#334155] rounded-lg px-3 py-1.5 text-xs text-white outline-none focus:border-amber-500">
                        <input type="email" name="email" value="{{ $user->email }}" required class="bg-[#0B1120] border border-[#334155] rounded-lg px-3 py-1.5 text-xs text-white outline-none focus:border-amber-500">
                        <select name="role" class="bg-[#0B1120] border border-[#334155] rounded-lg px-3 py-1.5 text-xs text-white outline-none focus:border-amber-500">
                            @foreach(['OWNER','ACCOUNTANT','CLERK','AUDITOR'] as $r)<option value="{{ $r }}" {{ $user->role === $r ? 'selected' : '' }}>{{ $r }}</option>@endforeach
                        </select>
                        <button class="bg-[#0B1120] border border-amber-500/50 text-amber-300 rounded-lg px-3 py-1.5 text-xs font-black uppercase tracking-wide">Save</button>
                    </form>
                </div>
            @empty
                <div class="text-center text-slate-500 text-sm py-6">No users in this company.</div>
            @endforelse
        </div>
    </div>

</div>

<!-- Notify owners -->
<form method="POST" action="{{ route('admin.company.notify', $company) }}" class="mt-6 bg-[#151F2E] border border-[#253347] rounded-2xl p-6">
    @csrf
    <h2 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Notify owners</h2>
    <p class="text-xs text-slate-500 mb-4">Sends an email + in-app notification to this company's OWNER account(s).</p>
    <div class="grid gap-3">
        <input type="text" name="subject" placeholder="Subject" required
               class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-amber-500">
        <textarea name="message" rows="3" placeholder="Message…" required
                  class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-amber-500"></textarea>
        <div>
            <button class="px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest text-slate-900 bg-amber-400 hover:bg-amber-300 transition-all">Send notice</button>
        </div>
    </div>
</form>

<!-- Danger zone -->
<div class="mt-6 bg-[#151F2E] border border-red-500/25 rounded-2xl p-6">
    <h2 class="text-[10px] font-black uppercase tracking-widest text-red-400 mb-1">Danger Zone</h2>
    <p class="text-xs text-slate-500 mb-4">Suspension blocks all tenant API access immediately. Deletion is a soft-delete (recoverable in the DB).</p>
    <div class="flex flex-wrap gap-3">
        @if($company->subscription_status === 'SUSPENDED')
        <form method="POST" action="{{ route('admin.company.reactivate', $company) }}">@csrf
            <button class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest text-emerald-300 bg-emerald-500/10 border border-emerald-500/30 hover:bg-emerald-500/20 transition-all">Reactivate company</button>
        </form>
        @else
        <form method="POST" action="{{ route('admin.company.suspend', $company) }}"
              onsubmit="return confirm('Suspendre {{ $company->name }} ? Tout accès tenant sera bloqué.')">@csrf
            <button class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest text-amber-300 bg-amber-500/10 border border-amber-500/30 hover:bg-amber-500/20 transition-all">Suspend company</button>
        </form>
        @endif
        <form method="POST" action="{{ route('admin.company.destroy', $company) }}"
              onsubmit="return confirm('SUPPRIMER {{ $company->name }} ? Cette action retire l\'entreprise de la plateforme.')">@csrf @method('DELETE')
            <button class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest text-red-300 bg-red-500/10 border border-red-500/30 hover:bg-red-500/20 transition-all">Delete company</button>
        </form>
    </div>
</div>

<!-- Subscription history -->
@if($company->subscriptions->count() > 0)
<div class="mt-6 bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="px-6 py-4 border-b border-[#253347]">
        <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Subscription History</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs divide-y divide-slate-800/60">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 bg-[#0B1120]/50">
                    <th class="py-3 px-6">Plan</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4">Amount (XAF)</th>
                    <th class="py-3 px-4">Expires</th>
                    <th class="py-3 px-4">Created</th>
                    <th class="py-3 px-4"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @foreach($company->subscriptions as $s)
                    <tr class="hover:bg-[#1C2A3A]/30 transition-colors {{ $loop->first ? 'bg-amber-500/[0.04]' : '' }}">
                        <td class="py-3 px-6">@include('admin.partials.plan_badge', ['plan' => $s->plan])</td>
                        <td class="py-3 px-4">@include('admin.partials.sub_status_badge', ['status' => $s->status])</td>
                        <td class="py-3 px-4 font-mono text-slate-400">{{ number_format($s->amount_xaf) }}</td>
                        <td class="py-3 px-4 font-mono text-slate-500 text-[10px]">
                            {{ $s->period_end ? \Carbon\Carbon::parse($s->period_end)->format('Y-m-d') : '—' }}
                        </td>
                        <td class="py-3 px-4 font-mono text-slate-500 text-[10px]">{{ $s->created_at->format('Y-m-d') }}</td>
                        <td class="py-3 px-4">
                            @if($loop->first)<span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Current</span>@endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
