@php
    // Distinguish every subscription state (ACTIVE/PENDING/PAST_DUE/SUSPENDED/CANCELLED).
    $s = strtoupper((string) ($status ?? ''));
    $map = [
        'ACTIVE'    => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        'PENDING'   => 'bg-sky-500/20 text-sky-300 border-sky-500/30',
        'PAST_DUE'  => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        'SUSPENDED' => 'bg-orange-500/20 text-orange-300 border-orange-500/30',
        'CANCELLED' => 'bg-red-500/20 text-red-300 border-red-500/30',
    ];
    $cls = $map[$s] ?? 'bg-slate-500/20 text-slate-300 border-slate-500/30';
@endphp
<span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border {{ $cls }}">{{ $status ?: '—' }}</span>
