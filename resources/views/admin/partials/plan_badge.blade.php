@php
    // $plan is a PlanConfig slug ('free','starter','business','enterprise').
    $slug = strtolower((string) ($plan ?? ''));
    $map = [
        'enterprise' => ['Enterprise', 'bg-purple-500/20 text-purple-300 border-purple-500/30'],
        'business'   => ['Business',   'bg-indigo-500/20 text-indigo-300 border-indigo-500/30'],
        'starter'    => ['Starter',    'bg-sky-500/20 text-sky-300 border-sky-500/30'],
        'free'       => ['Free',       'bg-slate-500/20 text-slate-400 border-slate-500/30'],
    ];
    [$label, $cls] = $map[$slug] ?? [($slug !== '' ? ucfirst($slug) : '—'), 'bg-slate-500/20 text-slate-300 border-slate-500/30'];
@endphp
<span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase border {{ $cls }}">{{ $label }}</span>
