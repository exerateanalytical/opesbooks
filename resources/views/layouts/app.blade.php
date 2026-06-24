<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" x-data="opesApp()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Opes Books' }} — Plateforme Comptable Camerounaise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                'opes-navy':  '#0A192F',
                'opes-amber': '#F59E0B',
                'opes-green': '#10B981',
            }}}
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak]  { display: none !important; }
        @keyframes heartbeat  { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.2);opacity:.75} }
        @keyframes spin-frame { to{transform:rotate(360deg)} }
        .heartbeat  { animation: heartbeat  1.4s ease-in-out infinite; }
        .spin-pulse { animation: spin-frame 1.2s linear infinite; }
    </style>
</head>
<body class="h-full font-sans antialiased text-slate-950" style="background-color:#f8fafc">

    <!-- GLOBAL VIEWPORT HEADER -->
    <header style="background-color:#0A192F" class="border-b-2 border-slate-700/60 sticky top-0 z-50 shadow-md">
        <div class="max-w-screen-2xl mx-auto px-4 h-14 flex items-center justify-between gap-3">

            <!-- Brand -->
            <div class="flex items-center gap-3 flex-shrink-0">
                <div class="bg-amber-500 text-slate-950 font-black text-xs px-2.5 py-1 rounded tracking-widest uppercase">OB</div>
                <div class="leading-none">
                    <span class="text-white font-black text-sm tracking-widest uppercase">OPES BOOKS</span>
                    <span class="hidden sm:block text-slate-400 text-[10px] font-medium mt-0.5">Comptabilité SYSCOHADA • DGI Cameroun</span>
                </div>
            </div>

            <!-- Nav -->
            <nav class="hidden md:flex items-center gap-0.5 text-[11px] font-black uppercase tracking-wider flex-1 justify-center">
                <a href="/" class="text-slate-400 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded transition-colors">
                    <span x-show="lang==='FR'">Tableau de Bord</span><span x-show="lang==='EN'" x-cloak>Dashboard</span>
                </a>
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded transition-colors">
                    <span x-show="lang==='FR'">Transactions</span><span x-show="lang==='EN'" x-cloak>Transactions</span>
                </a>
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded transition-colors">
                    <span x-show="lang==='FR'">Grand Livre</span><span x-show="lang==='EN'" x-cloak>Ledger</span>
                </a>
                <a href="#" class="text-slate-400 hover:text-white hover:bg-white/10 px-3 py-1.5 rounded transition-colors">
                    <span x-show="lang==='FR'">Déclarations DGI</span><span x-show="lang==='EN'" x-cloak>DGI Exports</span>
                </a>
            </nav>

            <!-- Lang toggle + Connectivity -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <button @click="lang = lang==='FR' ? 'EN' : 'FR'"
                        class="text-[10px] font-black border-2 border-slate-600 text-slate-300 hover:border-amber-500 hover:text-amber-400 px-2 py-1 rounded transition-colors uppercase tracking-widest w-10">
                    <span x-text="lang==='FR' ? 'EN' : 'FR'"></span>
                </button>
                <x-connectivity-badge />
            </div>

        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="max-w-screen-2xl mx-auto px-4 py-5">
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
