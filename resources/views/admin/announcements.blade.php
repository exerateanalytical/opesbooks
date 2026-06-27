@extends('admin.layout')

@section('title', 'Announcements')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Announcements</h1>
    <p class="text-slate-500 text-xs mt-1">System-wide messages to tenants</p>
</div>

<!-- Compose card -->
<div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-6 mb-6">
    <span class="text-[9px] font-black uppercase tracking-widest text-slate-500">Compose</span>
    <form method="POST" action="{{ route('admin.announcements.store') }}" class="mt-5">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 block mb-1.5">Title</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            </div>
            <div class="md:col-span-2">
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 block mb-1.5">Body</label>
                <textarea name="body" rows="3"
                          class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">{{ old('body') }}</textarea>
            </div>
            <div>
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 block mb-1.5">Type</label>
                <select name="type"
                        class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
                    @foreach(['INFO','WARNING','MAINTENANCE','FEATURE'] as $t)
                        <option value="{{ $t }}" @selected(old('type') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 block mb-1.5">Target Plan</label>
                <select name="target_plan"
                        class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
                    <option value="" @selected(old('target_plan') === '' || old('target_plan') === null)>All plans</option>
                    @foreach(['STARTER','GROWTH','ENTERPRISE'] as $p)
                        <option value="{{ $p }}" @selected(old('target_plan') === $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 block mb-1.5">Target Company</label>
                <select name="target_company_id"
                        class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
                    <option value="" @selected(old('target_company_id') === '' || old('target_company_id') === null)>All companies</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected((string) old('target_company_id') === (string) $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 block mb-1.5">Published At</label>
                <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            </div>
            <div>
                <label class="text-[9px] font-black uppercase tracking-widest text-slate-500 block mb-1.5">Expires At</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}"
                       class="w-full bg-[#1C2A3A] border border-[#334155] rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-[#F59E0B]/60">
            </div>
        </div>
        <div class="mt-5">
            <button type="submit"
                    class="px-4 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all">
                Publish
            </button>
        </div>
    </form>
</div>

<!-- Published list -->
<div class="space-y-4">
    @forelse($announcements as $a)
        @php
            $typeBadge = match(strtoupper($a->type)) {
                'INFO' => 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30',
                'WARNING' => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
                'MAINTENANCE' => 'bg-red-500/20 text-red-300 border border-red-500/30',
                'FEATURE' => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30',
                default => 'bg-slate-500/20 text-slate-300 border border-slate-500/30',
            };
        @endphp
        <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-5">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $typeBadge }}">{{ $a->type }}</span>
                        @if($a->active)
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-500/20 text-slate-300 border border-slate-500/30">Inactive</span>
                        @endif
                    </div>
                    <h3 class="font-bold text-white text-sm">{{ $a->title }}</h3>
                    <p class="text-slate-400 text-xs mt-1">{{ $a->body }}</p>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-3 text-[10px] text-slate-500 font-mono">
                        <span>{{ $a->target_plan ?: 'All plans' }} / {{ $a->targetCompany?->name ?? 'All companies' }}</span>
                        <span>Published: {{ $a->published_at?->format('Y-m-d H:i') ?? '—' }}</span>
                        <span>Expires: {{ $a->expires_at?->format('Y-m-d H:i') ?? '—' }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <form method="POST" action="{{ route('admin.announcements.toggle', $a) }}">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-[#1C2A3A] hover:bg-slate-700 text-slate-300 border border-[#334155] transition-all">
                            {{ $a->active ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.announcements.destroy', $a) }}"
                          onsubmit="return confirm('Delete this announcement?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-red-500/20 hover:bg-red-500/30 text-red-300 border border-red-500/30 transition-all">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="bg-[#151F2E] border border-[#253347] rounded-2xl p-12 text-center text-slate-500 text-sm">
            No announcements yet.
        </div>
    @endforelse
</div>

@if($announcements->hasPages())
    <div class="px-6 py-4 border-t border-[#253347] mt-4">
        {{ $announcements->links() }}
    </div>
@endif
@endsection
