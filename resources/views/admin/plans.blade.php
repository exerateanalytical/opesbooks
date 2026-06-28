@extends('admin.layout')

@section('title', 'Plans & Pricing')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Plans &amp; Pricing</h1>
    <p class="text-slate-500 text-xs mt-1">Edit plan prices and limits — applied to new subscriptions and the public pricing page</p>
</div>

<div class="space-y-4 max-w-4xl">
    @foreach($plans as $plan)
    <form method="POST" action="{{ route('admin.plans.update', $plan) }}"
          class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
        @csrf
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <span class="text-sm font-black text-white uppercase tracking-wide">{{ $plan->name }}</span>
                <span class="text-[10px] font-mono text-slate-500">{{ $plan->slug }}</span>
            </div>
            <label class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest text-slate-400 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ $plan->is_active ? 'checked' : '' }}
                       class="w-4 h-4 accent-amber-500">
                Active
            </label>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div class="col-span-2 lg:col-span-1">
                <label class="block text-[9px] font-black uppercase tracking-widest mb-1 text-slate-500">Name</label>
                <input type="text" name="name" value="{{ $plan->name }}" required
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-lg px-3 py-2 text-xs text-white outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest mb-1 text-slate-500">Monthly (XAF)</label>
                <input type="number" name="price_xaf_monthly" value="{{ $plan->price_xaf_monthly }}" min="0" required
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-lg px-3 py-2 text-xs text-white outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest mb-1 text-slate-500">Yearly (XAF)</label>
                <input type="number" name="price_xaf_yearly" value="{{ $plan->price_xaf_yearly }}" min="0" required
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-lg px-3 py-2 text-xs text-white outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-[9px] font-black uppercase tracking-widest mb-1 text-slate-500">Max users (-1=∞)</label>
                <input type="number" name="max_users" value="{{ $plan->max_users }}" min="-1" required
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-lg px-3 py-2 text-xs text-white outline-none focus:border-amber-500">
            </div>
            <button class="px-4 py-2 rounded-lg text-xs font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all">
                Save
            </button>
        </div>
    </form>
    @endforeach
</div>
@endsection
