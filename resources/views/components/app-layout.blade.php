<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Opes Books' }} — Plateforme Comptable Camerounaise</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.15); opacity: 0.8; }
        }
        .heartbeat { animation: heartbeat 1.4s ease-in-out infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .spin-slow { animation: spin 1.5s linear infinite; }
    </style>
</head>
<body class="h-full bg-slate-50 font-sans antialiased text-slate-950" x-data="connectivityMonitor()">

    {{-- ===== GLOBAL VIEWPORT HEADER ===== --}}
    <header class="bg-slate-900 border-b-2 border-slate-700 sticky top-0 z-50">
        <div class="max-w-screen-2xl mx-auto px-4 h-14 flex items-center justify-between gap-4">

            {{-- Brand --}}
            <div class="flex items-center space-x-3 flex-shrink-0">
                <div class="bg-amber-500 text-slate-950 font-black text-sm px-2.5 py-1 rounded tracking-wider uppercase">OB</div>
                <div>
                    <span class="text-white font-black text-sm tracking-wide">OPES BOOKS</span>
                    <span class="hidden sm:inline text-slate-400 text-xs font-medium ml-2">• Comptabilité SYSCOHADA • DGI Cameroun</span>
                </div>
            </div>

            {{-- Global Nav --}}
            <nav class="hidden md:flex items-center space-x-1 text-xs font-bold uppercase tracking-wider">
                <a href="#" class="text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-1.5 rounded transition-colors">Tableau de Bord</a>
                <a href="#" class="text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-1.5 rounded transition-colors">Transactions</a>
                <a href="#" class="text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-1.5 rounded transition-colors">Grand Livre</a>
                <a href="#" class="text-slate-300 hover:text-white hover:bg-slate-800 px-3 py-1.5 rounded transition-colors">Déclarations</a>
            </nav>

            {{-- Connectivity Status Indicator (invariant per spec) --}}
            <x-connectivity-badge />

        </div>
    </header>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="max-w-screen-2xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    <script>
        function connectivityMonitor() {
            return {
                status: navigator.onLine ? 'ONLINE' : 'LOCAL_MODE',
                init() {
                    window.addEventListener('online',  () => { this.status = 'SYNCING'; setTimeout(() => { this.status = 'ONLINE'; }, 3000); });
                    window.addEventListener('offline', () => { this.status = 'LOCAL_MODE'; });
                }
            }
        }
    </script>

</body>
</html>
