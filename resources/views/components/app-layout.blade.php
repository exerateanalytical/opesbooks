@props(['title' => 'Opes Books'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="opesApp()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Plateforme Comptable Camerounaise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                'opes-navy':  '#010048',
                'opes-amber': '#C99B0E',
                'opes-green': '#10B981',
                amber: { 300:'#E3B420', 400:'#C99B0E', 500:'#B5890C', 600:'#A07C08', 700:'#866709' },
            }}}
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --glass-white:     rgba(255,255,255,0.07);
            --glass-white-mid: rgba(255,255,255,0.12);
            --glass-white-hi:  rgba(255,255,255,0.18);
            --glass-border:    rgba(255,255,255,0.14);
            --glass-border-hi: rgba(255,255,255,0.28);
            --glass-shadow:    0 8px 40px rgba(0,0,0,0.55), 0 2px 8px rgba(0,0,0,0.35);
            --glass-inset:     inset 0 1px 0 rgba(255,255,255,0.18), inset 0 -1px 0 rgba(0,0,0,0.15);
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Helvetica Neue', sans-serif;
            background: radial-gradient(ellipse 120% 80% at 20% -5%, #1a2d4f 0%, #010048 35%, #050d1a 65%, #0f0a1e 100%);
            background-attachment: fixed;
            min-height: 100vh;
            color: #e2e8f0;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            background:
                radial-gradient(ellipse 60% 40% at 10% 15%, rgba(201,155,14,0.07) 0%, transparent 60%),
                radial-gradient(ellipse 50% 35% at 90% 80%, rgba(16,185,129,0.06) 0%, transparent 55%),
                radial-gradient(ellipse 40% 30% at 50% 50%, rgba(99,102,241,0.04) 0%, transparent 60%);
        }
        .glass-nav {
            background: rgba(1,0,72,0.72);
            backdrop-filter: blur(40px) saturate(200%) brightness(1.1);
            -webkit-backdrop-filter: blur(40px) saturate(200%) brightness(1.1);
            border-bottom: 1px solid rgba(255,255,255,0.10);
            box-shadow: 0 1px 0 rgba(255,255,255,0.06), 0 4px 32px rgba(0,0,0,0.5);
        }
        .glass-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.10) 0%, rgba(255,255,255,0.04) 100%);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255,255,255,0.14);
            border-top-color: rgba(255,255,255,0.24);
            box-shadow: 0 4px 24px rgba(0,0,0,0.45), 0 1px 0 rgba(255,255,255,0.12) inset, 0 -1px 0 rgba(0,0,0,0.12) inset;
            transition: box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .glass-card:hover {
            border-color: rgba(255,255,255,0.22);
            box-shadow: 0 8px 40px rgba(0,0,0,0.55), 0 1px 0 rgba(255,255,255,0.16) inset, 0 -1px 0 rgba(0,0,0,0.12) inset;
        }
        .glass-input {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.14);
            color: #f1f5f9;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .glass-input::placeholder { color: rgba(148,163,184,0.6); }
        .glass-input:focus {
            outline: none;
            border-color: rgba(201,155,14,0.6);
            box-shadow: 0 0 0 3px rgba(201,155,14,0.12);
        }
        /* .input / .input-field aliases (were undefined → white default) */
        .input, .input-field {
            background: rgba(255,255,255,0.06);
            -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.14);
            color: #f1f5f9; width: 100%;
            border-radius: 0.75rem; padding: 0.6rem 1rem; font-size: 0.8125rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input::placeholder, .input-field::placeholder { color: rgba(148,163,184,0.6); }
        .input:focus, .input-field:focus, textarea.glass-input:focus, select.glass-input:focus {
            outline: none; border-color: rgba(201,155,14,0.6); box-shadow: 0 0 0 3px rgba(201,155,14,0.12);
        }
        .input option, .input-field option, select.glass-input option { background: #010048; color: #f1f5f9; }
        select.glass-input, select.input, select.input-field {
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 0.85rem center; padding-right: 2.25rem; cursor: pointer;
        }
        button:focus-visible, a:focus-visible { outline: 2px solid rgba(201,155,14,0.85); outline-offset: 2px; }
        .glass-btn {
            background: linear-gradient(135deg, rgba(201,155,14,0.9) 0%, rgba(160,124,8,0.9) 100%);
            border: 1px solid rgba(201,155,14,0.5);
            box-shadow: 0 4px 16px rgba(201,155,14,0.25), 0 1px 0 rgba(255,255,255,0.2) inset;
            transition: all 0.2s ease;
        }
        .glass-btn:hover { box-shadow: 0 6px 24px rgba(201,155,14,0.35), 0 1px 0 rgba(255,255,255,0.25) inset; }
        .glass-btn:active { transform: scale(0.98); }
        .glass-btn-dark {
            background: linear-gradient(135deg, rgba(30,58,100,0.85) 0%, rgba(15,30,58,0.9) 100%);
            border: 1px solid rgba(255,255,255,0.14);
            box-shadow: 0 4px 16px rgba(0,0,0,0.4), 0 1px 0 rgba(255,255,255,0.1) inset;
            transition: all 0.2s ease;
        }
        .glass-btn-dark:hover { border-color: rgba(255,255,255,0.2); }
        .glass-btn-dark:active { transform: scale(0.98); }
        .glass-shimmer { position: relative; }
        .glass-shimmer::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.35) 30%, rgba(255,255,255,0.5) 50%, rgba(255,255,255,0.35) 70%, transparent 100%);
            border-radius: inherit;
            z-index: 1;
        }
        .glass-pill {
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 9999px;
        }
        .nav-link { color: rgba(148,163,184,0.9); transition: color 0.2s, background 0.2s; }
        .nav-link:hover { color: #fff; background: rgba(255,255,255,0.08); }
        @keyframes heartbeat  { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.25);opacity:.7} }
        @keyframes spin-frame { to{transform:rotate(360deg)} }
        @keyframes float-in   { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .heartbeat  { animation: heartbeat  1.6s ease-in-out infinite; }
        .spin-pulse { animation: spin-frame 1.2s linear infinite; }
        .float-in   { animation: float-in 0.35s cubic-bezier(0.34,1.56,0.64,1) both; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(255,255,255,0.03); }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.25); }
    </style>
</head>
<body class="h-full antialiased relative">

    <header class="glass-nav sticky top-0 z-50">
        <div class="max-w-screen-2xl mx-auto px-5 flex items-center justify-between gap-4" style="height:56px">
            <a href="/" class="flex items-center gap-3 flex-shrink-0 group">
                <div class="w-9 h-9 rounded-xl glass-card flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="text-amber-400 font-black text-xs tracking-widest">OB</span>
                </div>
                <div class="leading-none">
                    <span class="text-white font-black text-sm tracking-widest uppercase">OPES<span class="text-amber-400">BOOKS</span></span>
                    <span class="hidden sm:block text-slate-500 text-[9px] font-semibold mt-0.5 uppercase tracking-widest">SYSCOHADA • DGI Cameroun</span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-0.5 text-[11px] font-black uppercase tracking-wider flex-1 justify-center">
                <a href="/app"         class="nav-link px-3.5 py-2 rounded-xl font-bold uppercase tracking-wider text-[11px]">
                    <span x-show="lang==='FR'">Tableau de Bord</span><span x-show="lang==='EN'" x-cloak>Dashboard</span>
                </a>
                <a href="/app?page=journal" class="nav-link px-3.5 py-2 rounded-xl font-bold uppercase tracking-wider text-[11px]">Transactions</a>
                <a href="/app?page=ledger" class="nav-link px-3.5 py-2 rounded-xl font-bold uppercase tracking-wider text-[11px]">
                    <span x-show="lang==='FR'">Grand Livre</span><span x-show="lang==='EN'" x-cloak>Ledger</span>
                </a>
                <a href="/dgi-monitor" class="nav-link px-3.5 py-2 rounded-xl font-bold uppercase tracking-wider text-[11px]">
                    <span x-show="lang==='FR'">Suivi DGI</span><span x-show="lang==='EN'" x-cloak>DGI Monitor</span>
                </a>
                <a href="/tax-dashboard" class="nav-link px-3.5 py-2 rounded-xl font-bold uppercase tracking-wider text-[11px]">
                    <span x-show="lang==='FR'">Fiscalité</span><span x-show="lang==='EN'" x-cloak>Tax</span>
                </a>
                <a href="/about" class="nav-link px-3.5 py-2 rounded-xl font-bold uppercase tracking-wider text-[11px]">
                    <span x-show="lang==='FR'">À Propos</span><span x-show="lang==='EN'" x-cloak>About</span>
                </a>
            </nav>

            <div class="flex items-center gap-2 flex-shrink-0">
                <button @click="lang = lang==='FR' ? 'EN' : 'FR'"
                        class="glass-card text-[10px] font-black text-slate-300 hover:text-white px-3 py-1.5 rounded-lg transition-all uppercase tracking-widest w-10 text-center">
                    <span x-text="lang==='FR' ? 'EN' : 'FR'"></span>
                </button>
                <x-connectivity-badge />
            </div>
        </div>
    </header>

    <main class="relative z-10 max-w-screen-2xl mx-auto px-5 py-6">
        {{ $slot }}
    </main>

    <script>
        function opesApp() {
            return {
                lang:   localStorage.getItem('opes_lang') || 'FR',
                status: navigator.onLine ? 'ONLINE' : 'LOCAL_MODE',
                init() {
                    this.$watch('lang', v => localStorage.setItem('opes_lang', v));
                    window.addEventListener('online',  () => { this.status = 'SYNCING'; setTimeout(() => this.status = 'ONLINE', 2800); });
                    window.addEventListener('offline', () => { this.status = 'LOCAL_MODE'; });
                }
            }
        }
    </script>
</body>
</html>
