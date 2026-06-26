@props(['title' => 'Opes Books'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="opesApp()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Plateforme Comptable Camerounaise</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --c-bg:            #0B1120;
            --c-surface:       #151F2E;
            --c-raised:        #1C2A3A;
            --c-border:        #253347;
            --c-border-strong: #334155;
            --c-accent:        #F59E0B;
            --c-accent-dim:    #D97706;
            --c-text:          #F0F4FA;
            --c-muted:         #8B9EC0;
            --c-faint:         #4E647E;
        }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', sans-serif;
            background: var(--c-bg);
            background-attachment: fixed;
            min-height: 100vh;
            color: var(--c-text);
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: '';
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 60% 40% at 10% 15%, rgba(245,158,11,0.06) 0%, transparent 60%),
                radial-gradient(ellipse 50% 35% at 90% 80%, rgba(16,185,129,0.04) 0%, transparent 55%);
        }
        .glass-nav {
            background: rgba(11,17,32,0.92);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--c-border);
            box-shadow: 0 1px 0 var(--c-border), 0 4px 24px rgba(0,0,0,0.4);
        }
        .glass-card {
            background: var(--c-surface);
            border: 1px solid var(--c-border);
            box-shadow: 0 1px 3px rgba(0,0,0,0.4), 0 4px 16px rgba(0,0,0,0.25);
            transition: box-shadow 0.2s, border-color 0.2s;
        }
        .glass-card:hover {
            border-color: var(--c-border-strong);
            box-shadow: 0 4px 24px rgba(0,0,0,0.45);
        }
        .glass { background: var(--c-surface); border: 1px solid var(--c-border); }
        .glass-input {
            background: var(--c-raised);
            border: 1.5px solid var(--c-border);
            color: var(--c-text);
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .glass-input::placeholder { color: var(--c-faint); }
        .glass-input:focus {
            outline: none;
            border-color: var(--c-accent);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
        }
        .input, .input-field, textarea.glass-input, select.glass-input {
            background: var(--c-raised);
            border: 1.5px solid var(--c-border);
            color: var(--c-text);
            width: 100%;
            border-radius: 0.75rem;
            padding: 0.6rem 1rem;
            font-size: 0.8125rem;
            font-family: inherit;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .input::placeholder, .input-field::placeholder { color: var(--c-faint); }
        .input:focus, .input-field:focus, textarea.glass-input:focus, select.glass-input:focus {
            outline: none;
            border-color: var(--c-accent);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
        }
        .input option, .input-field option, select.glass-input option { background: var(--c-surface); color: var(--c-text); }
        select.glass-input, select.input, select.input-field {
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238B9EC0' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.85rem center;
            padding-right: 2.25rem;
            cursor: pointer;
        }
        button:focus-visible, a:focus-visible { outline: 2px solid rgba(245,158,11,0.85); outline-offset: 2px; }
        .glass-btn {
            background: var(--c-accent);
            border: 1px solid rgba(245,158,11,0.5);
            color: #0B1120; font-weight: 900;
            box-shadow: 0 2px 8px rgba(245,158,11,0.3);
            transition: background 0.15s, box-shadow 0.15s;
        }
        .glass-btn:hover { background: var(--c-accent-dim); box-shadow: 0 4px 16px rgba(245,158,11,0.4); }
        .glass-btn:active { transform: scale(0.98); }
        .glass-btn-dark {
            background: var(--c-raised);
            border: 1px solid var(--c-border);
            color: var(--c-text);
            transition: background 0.15s, border-color 0.15s;
        }
        .glass-btn-dark:hover { background: var(--c-border); border-color: var(--c-border-strong); }
        .glass-btn-dark:active { transform: scale(0.98); }
        .glass-shimmer { position: relative; }
        .glass-shimmer::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.12) 50%, transparent);
            border-radius: inherit; z-index: 1;
        }
        .glass-pill { border-radius: 9999px; }
        .nav-link { color: var(--c-muted); transition: color 0.15s, background 0.15s; }
        .nav-link:hover { color: var(--c-text); background: var(--c-raised); }
        @keyframes heartbeat  { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.25);opacity:.7} }
        @keyframes spin-frame { to{transform:rotate(360deg)} }
        @keyframes float-in   { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .heartbeat  { animation: heartbeat  1.6s ease-in-out infinite; }
        .spin-pulse { animation: spin-frame 1.2s linear infinite; }
        .float-in   { animation: float-in 0.35s cubic-bezier(0.34,1.56,0.64,1) both; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--c-border-strong); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }
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
