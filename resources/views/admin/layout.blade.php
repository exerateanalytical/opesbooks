<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OpesBooks Admin — @yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { amber: {
            300:'#E3B420', 400:'#C99B0E', 500:'#B5890C', 600:'#A07C08', 700:'#866709'
        } } } } };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Helvetica Neue', sans-serif; }
        ::-webkit-scrollbar { width: 7px; height: 7px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.14); border-radius: 99px; }
    </style>
</head>
@php
    $nav = [
        'Overview' => [
            ['dashboard', 'Dashboard', 'M3 12l2-2 7-7 7 7 2 2M5 10v10a1 1 0 001 1h12a1 1 0 001-1V10'],
        ],
        'Tenants' => [
            ['companies', 'Companies', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1'],
            ['subscriptions', 'Subscriptions', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
            ['billing', 'Billing & Revenue', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2'],
        ],
        'People' => [
            ['users', 'All Users', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ],
        'API Product' => [
            ['api-keys', 'API Keys', 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z'],
            ['api-logs', 'Request Logs', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['api-docs', 'Developer Portal', 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
        ],
        'System' => [
            ['system', 'System Health', 'M22 12h-4l-3 9L9 3l-3 9H2'],
            ['audit', 'Platform Audit', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['announcements', 'Announcements', 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z'],
            ['settings', 'Settings', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['feature-flags', 'Feature Flags', 'M4 21v-7m0 0V5a2 2 0 0 1 2-2h11l-2 4 2 4H6a2 2 0 0 0-2 2z'],
        ],
    ];
@endphp
<body class="bg-slate-950 text-slate-200 min-h-screen" x-data="{ mobileNav: false }">

<!-- Topbar -->
<header class="fixed top-0 inset-x-0 h-14 z-40 bg-[#0A192F] border-b border-white/10 border-t-2 border-t-amber-400 flex items-center justify-between px-4">
    <div class="flex items-center gap-3">
        <button @click="mobileNav = !mobileNav" class="lg:hidden w-9 h-9 flex items-center justify-center rounded-lg text-slate-300 hover:bg-white/10">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="w-7 h-7 rounded-lg bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-[9px] font-black text-amber-400">OB</div>
        <span class="text-white font-black text-sm tracking-widest">OPES<span class="text-amber-400">ADMIN</span></span>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('app') }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-white">↩ App</a>
        <form method="POST" action="{{ route('admin.logout') }}">@csrf
            <button class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-300">Logout</button>
        </form>
    </div>
</header>

<!-- Sidebar -->
<aside :class="mobileNav ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
       class="fixed top-14 bottom-0 left-0 w-60 bg-[#0d1d33] border-r border-white/10 overflow-y-auto z-30 transition-transform duration-300 py-5 px-3">
    <nav class="space-y-5">
        @foreach($nav as $group => $items)
        <div>
            <div class="px-3 mb-1.5 text-[9px] font-black uppercase tracking-[0.15em] text-white/35">{{ $group }}</div>
            <div class="space-y-0.5">
                @foreach($items as [$route, $label, $icon])
                <a href="{{ route('admin.' . $route) }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-[11px] font-bold uppercase tracking-wide transition-all border-l-2
                          {{ request()->routeIs('admin.' . $route) || ($route === 'companies' && request()->routeIs('admin.company'))
                             ? 'bg-amber-500/10 text-amber-300 border-amber-400'
                             : 'text-slate-400 hover:text-white hover:bg-white/5 border-transparent' }}">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $icon }}"/></svg>
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
    </nav>
</aside>

<!-- Overlay (mobile) -->
<div x-show="mobileNav" @click="mobileNav = false" x-cloak class="fixed inset-0 top-14 bg-black/50 z-20 lg:hidden"></div>

<!-- Main -->
<main class="pt-14 lg:pl-60 min-h-screen">
    <div class="p-6 lg:p-8 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-6 px-4 py-3 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 px-4 py-3 rounded-xl bg-red-500/15 border border-red-500/30 text-red-300 text-sm font-semibold">
                {{ $errors->first() }}
            </div>
        @endif
        @yield('content')
    </div>
</main>

</body>
</html>
