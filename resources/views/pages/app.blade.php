<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opes Books — App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; overflow: hidden; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Helvetica Neue', sans-serif;
            background: radial-gradient(ellipse 130% 90% at 15% -5%, #1e3560 0%, #0a192f 30%, #060e1f 60%, #0d0820 100%);
            background-attachment: fixed;
            color: #e2e8f0;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: '';
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 55% 45% at 5% 10%,  rgba(245,158,11,0.08) 0%, transparent 60%),
                radial-gradient(ellipse 45% 35% at 92% 85%, rgba(16,185,129,0.06) 0%, transparent 55%),
                radial-gradient(ellipse 35% 30% at 55% 50%, rgba(99,102,241,0.05) 0%, transparent 60%);
        }

        /* ── Glass system ─────────────────────────────────────────────── */
        .glass {
            background: rgba(255,255,255,0.065);
            backdrop-filter: blur(28px) saturate(180%);
            -webkit-backdrop-filter: blur(28px) saturate(180%);
            border: 1px solid rgba(255,255,255,0.13);
            box-shadow: 0 8px 40px rgba(0,0,0,0.5), 0 1px 0 rgba(255,255,255,0.12) inset;
        }
        .glass-sidebar {
            background: rgba(8,18,36,0.82);
            backdrop-filter: blur(40px) saturate(200%);
            -webkit-backdrop-filter: blur(40px) saturate(200%);
            border-right: 1px solid rgba(255,255,255,0.09);
            box-shadow: 4px 0 32px rgba(0,0,0,0.4);
        }
        .glass-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.10) 0%, rgba(255,255,255,0.04) 100%);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            border: 1px solid rgba(255,255,255,0.13);
            border-top-color: rgba(255,255,255,0.22);
            box-shadow: 0 4px 24px rgba(0,0,0,0.45), 0 1px 0 rgba(255,255,255,0.12) inset;
            transition: box-shadow 0.2s, border-color 0.2s, transform 0.15s;
        }
        .glass-card:hover {
            border-color: rgba(255,255,255,0.2);
            box-shadow: 0 8px 40px rgba(0,0,0,0.55), 0 1px 0 rgba(255,255,255,0.16) inset;
        }
        .glass-input {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.13);
            color: #f1f5f9;
            width: 100%; border-radius: 0.75rem;
            padding: 0.6rem 1rem; font-size: 0.8125rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .glass-input::placeholder { color: rgba(148,163,184,0.55); }
        .glass-input:focus {
            outline: none;
            border-color: rgba(245,158,11,0.55);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.10);
        }
        select.glass-input option { background: #0a192f; }

        .glass-btn-amber {
            background: linear-gradient(135deg, rgba(245,158,11,0.95) 0%, rgba(217,119,6,0.95) 100%);
            border: 1px solid rgba(245,158,11,0.5);
            box-shadow: 0 4px 18px rgba(245,158,11,0.28), 0 1px 0 rgba(255,255,255,0.2) inset;
            color: #0a192f; font-weight: 900;
            transition: all 0.2s;
        }
        .glass-btn-amber:hover { box-shadow: 0 6px 26px rgba(245,158,11,0.38), 0 1px 0 rgba(255,255,255,0.25) inset; }
        .glass-btn-amber:active, .glass-btn-amber:disabled { transform: scale(0.98); opacity: 0.6; }

        .glass-btn-dark {
            background: linear-gradient(135deg, rgba(30,58,100,0.88) 0%, rgba(15,30,58,0.94) 100%);
            border: 1px solid rgba(255,255,255,0.14);
            box-shadow: 0 4px 16px rgba(0,0,0,0.5), 0 1px 0 rgba(255,255,255,0.1) inset;
            color: white; font-weight: 900;
            transition: all 0.2s;
        }
        .glass-btn-dark:hover { border-color: rgba(255,255,255,0.22); }
        .glass-btn-dark:active { transform: scale(0.98); }

        /* Sidebar nav item */
        .nav-item {
            display: flex; align-items: center; gap: 0.625rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.75rem;
            font-size: 0.75rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em;
            cursor: pointer; width: 100%;
            border: 1px solid transparent;
            transition: all 0.18s ease;
            color: rgba(148,163,184,0.85);
            background: transparent;
        }
        .nav-item:hover {
            color: #fff;
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.1);
        }
        .nav-item.active {
            color: #fff;
            background: rgba(245,158,11,0.15);
            border-color: rgba(245,158,11,0.35);
            box-shadow: 0 0 16px rgba(245,158,11,0.12);
        }
        .nav-item.active svg { color: rgb(245,158,11); }
        .nav-item.danger { color: rgba(252,165,165,0.75); }
        .nav-item.danger:hover { color: rgb(252,165,165); background: rgba(244,63,94,0.1); border-color: rgba(244,63,94,0.2); }

        /* Table rows */
        .glass-row {
            border-bottom: 1px solid rgba(255,255,255,0.055);
            transition: background 0.15s;
        }
        .glass-row:hover { background: rgba(255,255,255,0.04); }

        /* Shimmer top edge */
        .shimmer-top::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4) 50%, transparent);
            border-radius: inherit;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.14); border-radius: 99px; }

        /* Animations */
        @keyframes spin-frame { to { transform: rotate(360deg); } }
        @keyframes float-in   { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
        @keyframes pulse-dot  { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.3);opacity:.65} }
        .spin-pulse { animation: spin-frame 1.1s linear infinite; }
        .float-in   { animation: float-in 0.3s cubic-bezier(0.34,1.4,0.64,1) both; }
        .pulse-dot  { animation: pulse-dot 1.5s ease-in-out infinite; }
    </style>
</head>
<body x-data="opesApp()" x-cloak class="relative">

<!-- ── Layout ──────────────────────────────────────────────────────── -->
<div class="flex h-screen relative z-10">

    <!-- ── SIDEBAR ──────────────────────────────────────────────────── -->
    <aside class="glass-sidebar w-56 flex flex-col py-5 px-3 shrink-0 z-20">

        <!-- Brand -->
        <div class="px-2 mb-6">
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[10px] font-black text-amber-400 flex-shrink-0"
                     style="background:rgba(245,158,11,0.14);border:1px solid rgba(245,158,11,0.3)">OB</div>
                <span class="text-white font-black text-sm tracking-widest uppercase">OPES<span class="text-amber-400">BOOKS</span></span>
            </div>
            <!-- User chip -->
            <div class="px-2.5 py-1.5 rounded-xl text-[10px] font-bold"
                 style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.09)">
                <div class="text-slate-300 font-black truncate" x-text="user?.name ?? '—'"></div>
                <div class="text-slate-500 mt-0.5 uppercase tracking-wider flex items-center gap-1.5">
                    <span x-text="user?.role ?? ''"></span>
                    <span class="opacity-40">·</span>
                    <span class="truncate" x-text="company?.name ?? '...'"></span>
                </div>
            </div>
        </div>

        <!-- Connectivity -->
        <div class="mx-1 mb-4 px-2.5 py-1.5 rounded-xl flex items-center gap-2 text-[10px] font-black uppercase tracking-widest"
             :style="connStatus==='ONLINE'
                ? 'background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)'
                : connStatus==='SYNCING'
                ? 'background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.25);color:rgb(165,180,252)'
                : 'background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);color:rgb(252,211,77)'">
            <span class="w-1.5 h-1.5 rounded-full pulse-dot flex-shrink-0"
                  :class="connStatus==='ONLINE' ? 'bg-emerald-400' : connStatus==='SYNCING' ? 'bg-indigo-400' : 'bg-amber-400'"></span>
            <span x-text="connStatus==='ONLINE' ? (lang==='FR'?'Connecté':'Online') : connStatus==='SYNCING' ? 'Sync…' : (lang==='FR'?'Hors ligne':'Offline')"></span>
        </div>

        <!-- Nav -->
        <nav class="space-y-0.5 flex-1">
            <button @click="setPage('dashboard')" :class="page==='dashboard' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span x-text="lang==='FR' ? 'Tableau de Bord' : 'Dashboard'"></span>
            </button>
            <button @click="setPage('journal')" :class="page==='journal' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span x-text="lang==='FR' ? 'Journal' : 'Journal'"></span>
            </button>
            <button @click="setPage('ledger')" :class="page==='ledger' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span x-text="lang==='FR' ? 'Grand Livre' : 'Ledger'"></span>
            </button>
            <button @click="setPage('invoice')" :class="page==='invoice' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-text="lang==='FR' ? 'Facturer' : 'Invoice'"></span>
            </button>
            <button @click="setPage('calculator')" :class="page==='calculator' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span x-text="lang==='FR' ? 'Calc. TVA' : 'VAT Calc'"></span>
            </button>

            <button @click="setPage('import')" :class="page==='import' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span x-text="lang==='FR' ? 'Import CSV' : 'CSV Import'"></span>
            </button>
            <button @click="setPage('subledgers')" :class="page==='subledgers' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span x-text="lang==='FR' ? 'Sous-Comptes' : 'Sub-Ledgers'"></span>
            </button>
            <button @click="setPage('sync')" :class="page==='sync' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span x-text="lang==='FR' ? 'Sync Hors Ligne' : 'Offline Sync'"></span>
            </button>
            <button @click="setPage('team')" :class="page==='team' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-text="lang==='FR' ? 'Équipe' : 'Team'"></span>
            </button>
            <button @click="setPage('settings')" :class="page==='settings' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-text="lang==='FR' ? 'Paramètres' : 'Settings'"></span>
            </button>

            <div class="my-2" style="height:1px;background:rgba(255,255,255,0.07)"></div>

            <a href="/tax-dashboard" class="nav-item">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span x-text="lang==='FR' ? 'Bilan Fiscal' : 'Tax Monitor'"></span>
            </a>
            <a href="/dgi-monitor" class="nav-item">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                <span x-text="lang==='FR' ? 'Suivi DGI' : 'DGI Monitor'"></span>
            </a>
            <a href="/about" class="nav-item">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-text="lang==='FR' ? 'À Propos' : 'About'"></span>
            </a>
        </nav>

        <!-- Footer -->
        <div class="space-y-0.5 mt-4 pt-4" style="border-top:1px solid rgba(255,255,255,0.07)">
            <button @click="toggleLang()" class="nav-item">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                <span x-text="lang==='FR' ? 'English' : 'Français'"></span>
            </button>
            <button @click="doLogout()" class="nav-item danger">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span x-text="lang==='FR' ? 'Déconnexion' : 'Logout'"></span>
            </button>
        </div>
    </aside>

    <!-- ── MAIN CONTENT ──────────────────────────────────────────────── -->
    <main class="flex-1 overflow-y-auto relative">

        <!-- Loading overlay -->
        <div x-show="loading" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center"
             style="background:rgba(5,13,26,0.6);backdrop-filter:blur(6px)">
            <div class="glass-card rounded-2xl px-8 py-5 flex items-center gap-4">
                <svg class="spin-pulse h-5 w-5 text-amber-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                <span class="text-sm font-black text-white uppercase tracking-wider" x-text="lang==='FR' ? 'Chargement…' : 'Loading…'"></span>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- DASHBOARD PAGE                                             -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='dashboard'" class="p-6 space-y-5 float-in">

            <!-- Header -->
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wide" x-text="lang==='FR' ? 'Tableau de Bord' : 'Dashboard'"></h2>
                    <p class="text-xs text-slate-400 mt-1" x-text="today"></p>
                </div>
                <div class="text-[10px] font-black text-slate-400 px-3 py-1.5 rounded-xl uppercase tracking-widest"
                     style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1)"
                     x-text="(company?.tax_regime ?? '—') + ' · NIU: ' + (company?.niu ?? '—')"></div>
            </div>

            <!-- KPI grid -->
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
                <template x-for="stat in kpiStats" :key="stat.key">
                    <div class="glass-card rounded-2xl p-4 relative overflow-hidden"
                         :style="'box-shadow:0 4px 24px rgba(0,0,0,0.45),0 0 28px ' + stat.glow + ',0 1px 0 rgba(255,255,255,0.12) inset'">
                        <div class="absolute -top-3 -right-3 w-16 h-16 rounded-full blur-2xl pointer-events-none opacity-25"
                             :style="'background:' + stat.blob"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400" x-text="lang==='FR' ? stat.labelFr : stat.labelEn"></span>
                                <span class="text-sm font-black opacity-60" :class="stat.textColor" x-text="stat.icon"></span>
                            </div>
                            <div class="font-mono font-black text-white text-lg leading-none" x-text="fmtXaf(stat.value)"></div>
                            <div class="text-[9px] text-slate-500 font-mono mt-1.5" x-text="stat.account"></div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- DGI Provision banner -->
            <div class="glass-card rounded-2xl p-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between shimmer-top relative overflow-hidden"
                 style="background:linear-gradient(135deg,rgba(245,158,11,0.12),rgba(10,25,47,0.7));border-color:rgba(245,158,11,0.28);box-shadow:0 8px 32px rgba(245,158,11,0.14),0 4px 24px rgba(0,0,0,0.5)">
                <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full blur-3xl pointer-events-none opacity-20"
                     style="background:radial-gradient(circle,rgba(245,158,11,1),transparent)"></div>
                <div class="relative z-10">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1"
                       x-text="lang==='FR' ? 'Mois en cours · Provision DGI Cameroun' : 'Current Month · DGI Provision'"></p>
                    <p class="text-3xl font-black text-amber-400 font-mono" x-text="fmtXaf(fiscalProvision)"></p>
                    <p class="text-[10px] text-slate-500 mt-1"
                       x-text="lang==='FR' ? 'À verser avant le 15 du mois prochain' : 'Due before the 15th of next month'"></p>
                </div>
                <a href="/tax-dashboard"
                   class="glass-btn-amber relative z-10 px-4 py-2 rounded-xl text-xs uppercase tracking-widest flex-shrink-0"
                   x-text="lang==='FR' ? 'Voir le Bilan Fiscal →' : 'View Tax Monitor →'"></a>
            </div>

            <!-- Quick actions -->
            <div class="glass rounded-2xl p-5">
                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4"
                   x-text="lang==='FR' ? 'Actions Rapides' : 'Quick Actions'"></p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <template x-for="action in quickActions" :key="action.page">
                        <button @click="action.href ? (window.location.href = action.href) : setPage(action.page)"
                                class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all text-center group active:scale-95"
                                style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.09)"
                                @mouseenter="$el.style.background='rgba(255,255,255,0.09)';$el.style.borderColor='rgba(245,158,11,0.3)'"
                                @mouseleave="$el.style.background='rgba(255,255,255,0.04)';$el.style.borderColor='rgba(255,255,255,0.09)'">
                            <span class="text-2xl" x-text="action.icon"></span>
                            <span class="text-[10px] font-black text-slate-300 uppercase tracking-wide leading-tight"
                                  x-text="lang==='FR' ? action.labelFr : action.labelEn"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- JOURNAL PAGE                                               -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='journal'" x-cloak class="p-6 space-y-4 float-in">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                    x-text="lang==='FR' ? 'Journal Comptable' : 'Accounting Journal'"></h2>
                <div class="flex flex-wrap gap-2 items-center">
                    <input type="date" x-model="journalFilter.from" @change="loadJournal()"
                           class="glass-input !w-auto px-3 py-1.5 text-[11px]">
                    <input type="date" x-model="journalFilter.to" @change="loadJournal()"
                           class="glass-input !w-auto px-3 py-1.5 text-[11px]">
                    <button @click="exportDgiFiscalis()"
                            :disabled="dgiExporting"
                            class="glass-btn-amber px-4 py-1.5 rounded-xl text-[10px] uppercase tracking-widest flex items-center gap-2 disabled:opacity-40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="!dgiExporting" x-text="lang==='FR' ? 'Export DGI Fiscalis' : 'DGI Export'"></span>
                        <span x-show="dgiExporting" x-cloak>…</span>
                    </button>
                </div>
            </div>

            <div class="glass rounded-2xl overflow-hidden">
                <div class="px-5 py-3 flex items-center justify-between border-b" style="border-color:rgba(255,255,255,0.07);background:rgba(0,0,0,0.15)">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                          x-text="lang==='FR' ? 'Écritures Comptables' : 'Journal Entries'"></span>
                    <span class="text-[10px] font-mono font-black px-2.5 py-0.5 rounded-full"
                          style="background:rgba(245,158,11,0.15);color:rgb(252,211,77);border:1px solid rgba(245,158,11,0.3)"
                          x-text="journalEntries.length + (lang==='FR' ? ' entrées' : ' entries')"></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black uppercase text-slate-500 tracking-widest border-b"
                                style="border-color:rgba(255,255,255,0.06);background:rgba(0,0,0,0.1)">
                                <th class="py-3 px-5 whitespace-nowrap" x-text="lang==='FR' ? 'Référence' : 'Reference'"></th>
                                <th class="py-3 px-5 whitespace-nowrap" x-text="lang==='FR' ? 'Date' : 'Date'"></th>
                                <th class="py-3 px-5 whitespace-nowrap" x-text="lang==='FR' ? 'Mémo' : 'Memo'"></th>
                                <th class="py-3 px-5 whitespace-nowrap" x-text="lang==='FR' ? 'Source' : 'Source'"></th>
                                <th class="py-3 px-5 whitespace-nowrap text-center" x-text="lang==='FR' ? 'Statut' : 'Status'"></th>
                                <th class="py-3 px-5 whitespace-nowrap text-center">PDF</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs font-medium">
                            <template x-if="journalEntries.length === 0">
                                <tr>
                                    <td colspan="6" class="py-14 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                                                 style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08)">📋</div>
                                            <div class="text-slate-500 text-[11px] font-black uppercase tracking-widest"
                                                 x-text="lang==='FR' ? 'Aucune écriture trouvée.' : 'No entries found.'"></div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="txn in journalEntries" :key="txn.id">
                                <tr class="glass-row">
                                    <td class="py-3 px-5 font-mono font-black text-amber-400 text-xs whitespace-nowrap" x-text="txn.reference_id"></td>
                                    <td class="py-3 px-5 text-[11px] text-slate-400 font-mono whitespace-nowrap" x-text="txn.posting_date"></td>
                                    <td class="py-3 px-5 text-[11px] text-slate-300 max-w-xs truncate" x-text="txn.memo"></td>
                                    <td class="py-3 px-5">
                                        <span class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider"
                                              style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.1);color:rgb(148,163,184)"
                                              x-text="txn.source_pipeline?.replace('_', ' ')"></span>
                                    </td>
                                    <td class="py-3 px-5 text-center">
                                        <span class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider"
                                              :style="txn.transaction_status==='SUCCESSFUL'
                                                ? 'background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);color:rgb(110,231,183)'
                                                : txn.transaction_status==='REVERSED'
                                                ? 'background:rgba(244,63,94,0.15);border:1px solid rgba(244,63,94,0.3);color:rgb(252,165,165)'
                                                : 'background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:rgb(252,211,77)'"
                                              x-text="txn.transaction_status"></span>
                                    </td>
                                    <td class="py-3 px-5 text-center whitespace-nowrap">
                                        <button @click="downloadInvoice(txn)"
                                                class="text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider transition-all active:scale-95"
                                                style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25);color:rgb(252,211,77)"
                                                x-text="lang==='FR' ? '↓ PDF' : '↓ PDF'"></button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div x-show="journalMeta.last_page > 1"
                     class="px-5 py-3 flex items-center justify-between border-t text-[11px]"
                     style="border-color:rgba(255,255,255,0.07);background:rgba(0,0,0,0.08)">
                    <span class="text-slate-500"
                          x-text="(lang==='FR'?'Page ':'Page ') + journalMeta.current_page + ' / ' + journalMeta.last_page"></span>
                    <div class="flex gap-1.5">
                        <button @click="journalPage--; loadJournal()" :disabled="journalMeta.current_page<=1"
                                class="glass-btn-dark px-3 py-1 rounded-lg text-[10px] uppercase tracking-wider disabled:opacity-30"
                                x-text="lang==='FR'?'← Préc.':'← Prev'"></button>
                        <button @click="journalPage++; loadJournal()" :disabled="journalMeta.current_page>=journalMeta.last_page"
                                class="glass-btn-amber px-3 py-1 rounded-lg text-[10px] uppercase tracking-wider disabled:opacity-30"
                                x-text="lang==='FR'?'Suiv. →':'Next →'"></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- GRAND LIVRE / LEDGER PAGE                                  -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='ledger'" x-cloak class="p-6 space-y-4 float-in">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                        x-text="lang==='FR' ? 'Grand Livre SYSCOHADA' : 'SYSCOHADA Ledger'"></h2>
                    <p class="text-xs text-slate-400 mt-1"
                       x-text="(lang==='FR'?'Balance de vérification — ':'Trial balance — ') + (ledgerMeta.balanced ? '✔ Équilibrée' : '⚠ Déséquilibrée')"></p>
                </div>
                <div class="flex gap-2">
                    <input type="date" x-model="ledgerFilter.from" @change="loadLedger()"
                           class="glass-input !w-auto px-3 py-1.5 text-[11px]">
                    <input type="date" x-model="ledgerFilter.to" @change="loadLedger()"
                           class="glass-input !w-auto px-3 py-1.5 text-[11px]">
                </div>
            </div>

            <!-- Totals summary -->
            <div class="grid grid-cols-3 gap-4">
                <div class="glass-card rounded-2xl p-4">
                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-2"
                         x-text="lang==='FR' ? 'Total Débit' : 'Total Debit'"></div>
                    <div class="font-mono font-black text-indigo-400 text-lg" x-text="fmtXaf(ledgerMeta.grand_debit)"></div>
                </div>
                <div class="glass-card rounded-2xl p-4">
                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-2"
                         x-text="lang==='FR' ? 'Total Crédit' : 'Total Credit'"></div>
                    <div class="font-mono font-black text-emerald-400 text-lg" x-text="fmtXaf(ledgerMeta.grand_credit)"></div>
                </div>
                <div class="glass-card rounded-2xl p-4">
                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-2">Balance</div>
                    <div class="font-mono font-black text-lg"
                         :class="ledgerMeta.balanced ? 'text-emerald-400' : 'text-rose-400'"
                         x-text="ledgerMeta.balanced ? '✔ ' + (lang==='FR'?'Équilibrée':'Balanced') : '⚠ ' + (lang==='FR'?'Déséquilibrée':'Unbalanced')"></div>
                </div>
            </div>

            <div class="glass rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b" style="border-color:rgba(255,255,255,0.07);background:rgba(0,0,0,0.15)">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                          x-text="lang==='FR' ? 'Comptes SYSCOHADA Révisé' : 'SYSCOHADA Revised Accounts'"></span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black uppercase text-slate-500 tracking-widest border-b"
                                style="border-color:rgba(255,255,255,0.06);background:rgba(0,0,0,0.1)">
                                <th class="py-3 px-5 whitespace-nowrap">Code</th>
                                <th class="py-3 px-5 whitespace-nowrap">Libellé / Label</th>
                                <th class="py-3 px-5 text-right whitespace-nowrap">Débit / Debit</th>
                                <th class="py-3 px-5 text-right whitespace-nowrap">Crédit / Credit</th>
                                <th class="py-3 px-5 text-right whitespace-nowrap">Solde</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs font-medium">
                            <template x-if="ledgerAccounts.length===0">
                                <tr>
                                    <td colspan="5" class="py-14 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                                                 style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08)">📊</div>
                                            <div class="text-slate-500 text-[11px] font-black uppercase tracking-widest"
                                                 x-text="lang==='FR'?'Aucun compte avec mouvement.':'No accounts with activity.'"></div>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="acc in ledgerAccounts.filter(a => Number(a.total_debit)>0 || Number(a.total_credit)>0)" :key="acc.id">
                                <tr class="glass-row">
                                    <td class="py-2.5 px-5 font-mono font-black text-amber-400 text-[11px] whitespace-nowrap" x-text="acc.code"></td>
                                    <td class="py-2.5 px-5 text-slate-300 text-[11px] max-w-xs truncate" x-text="acc.label"></td>
                                    <td class="py-2.5 px-5 text-right font-mono text-indigo-400 text-[11px] whitespace-nowrap" x-text="fmtXaf(acc.total_debit)"></td>
                                    <td class="py-2.5 px-5 text-right font-mono text-emerald-400 text-[11px] whitespace-nowrap" x-text="fmtXaf(acc.total_credit)"></td>
                                    <td class="py-2.5 px-5 text-right font-mono text-[11px] whitespace-nowrap font-black"
                                        :class="(Number(acc.total_debit)-Number(acc.total_credit)) >= 0 ? 'text-slate-200' : 'text-rose-400'"
                                        x-text="fmtXaf(Math.abs(Number(acc.total_debit)-Number(acc.total_credit))) + ((Number(acc.total_debit)-Number(acc.total_credit))>=0?' D':' C')"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- INVOICE PAGE                                               -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='invoice'" x-cloak class="p-6 space-y-5 float-in" x-data="invoiceForm()">
            <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                x-text="lang==='FR' ? 'Nouvelle Facture DGI' : 'New DGI Invoice'"></h2>

            <div class="glass rounded-2xl p-6 space-y-4">
                <!-- Meta row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2"
                               x-text="lang==='FR' ? 'N° Facture' : 'Invoice No.'"></label>
                        <input type="text" x-model="form.invoice_number" class="glass-input" placeholder="FAC-2026-001">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Date</label>
                        <input type="date" x-model="form.invoice_date" class="glass-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Langue</label>
                        <select x-model="form.language" class="glass-input">
                            <option value="FR">Français</option>
                            <option value="EN">English</option>
                        </select>
                    </div>
                </div>

                <!-- Client row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2"
                               x-text="lang==='FR' ? 'Client / Acheteur' : 'Client Name'"></label>
                        <input type="text" x-model="form.client_name" class="glass-input" placeholder="SARL Acheteur Douala">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">NIU Client</label>
                        <input type="text" x-model="form.client_niu" class="glass-input" placeholder="M082000012 (optionnel)">
                    </div>
                </div>

                <!-- Lines -->
                <div>
                    <div class="flex items-center justify-between mb-2.5">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                               x-text="lang==='FR' ? 'Lignes de Facturation' : 'Invoice Lines'"></label>
                        <button @click="addLine()"
                                class="text-[10px] font-black text-amber-400 hover:text-amber-300 uppercase tracking-wider transition-colors"
                                x-text="lang==='FR' ? '+ Ajouter ligne' : '+ Add line'"></button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(line, idx) in form.lines" :key="idx">
                            <div class="grid gap-2 items-center" style="grid-template-columns:1fr 80px 110px 80px 28px">
                                <input type="text" x-model="line.description" class="glass-input text-[12px]"
                                       :placeholder="lang==='FR' ? 'Description produit/service' : 'Product/service description'">
                                <input type="number" x-model="line.quantity" class="glass-input text-[12px]" placeholder="Qté" min="0.01" step="0.01">
                                <input type="number" x-model="line.unit_price_ht" class="glass-input text-[12px]" placeholder="Prix HT" min="0">
                                <div class="text-right font-mono font-black text-emerald-400 text-[11px]" x-text="fmtXaf(line.quantity * line.unit_price_ht)"></div>
                                <button @click="form.lines.splice(idx,1)" class="text-rose-400 hover:text-rose-300 font-black text-lg leading-none">×</button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Totals -->
                <div class="rounded-xl p-4" style="background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.07)">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center text-[11px]">
                        <div>
                            <div class="text-slate-500 font-black uppercase tracking-widest text-[9px] mb-1">Base HT</div>
                            <div class="font-mono font-black text-white" x-text="fmtXaf(totalHt)"></div>
                        </div>
                        <div>
                            <div class="text-slate-500 font-black uppercase tracking-widest text-[9px] mb-1">TVA 17.5%</div>
                            <div class="font-mono font-black text-indigo-400" x-text="fmtXaf(totalHt * 0.175)"></div>
                        </div>
                        <div>
                            <div class="text-slate-500 font-black uppercase tracking-widest text-[9px] mb-1">CAC 10%</div>
                            <div class="font-mono font-black text-purple-400" x-text="fmtXaf(totalHt * 0.175 * 0.10)"></div>
                        </div>
                        <div>
                            <div class="text-slate-500 font-black uppercase tracking-widest text-[9px] mb-1">TOTAL TTC</div>
                            <div class="font-mono font-black text-amber-400 text-base" x-text="fmtXaf(totalHt * 1.1925)"></div>
                        </div>
                    </div>
                </div>

                <div x-show="invoiceError" class="px-4 py-3 rounded-xl text-sm font-bold"
                     style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.25);color:rgb(252,165,165)"
                     x-text="invoiceError"></div>

                <button @click="generatePdf()" :disabled="generating"
                        class="glass-btn-amber px-6 py-3 rounded-xl text-xs uppercase tracking-widest flex items-center gap-2.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span x-show="!generating" x-text="lang==='FR' ? 'Générer PDF DGI' : 'Generate DGI PDF'"></span>
                    <span x-show="generating" x-cloak class="flex items-center gap-2">
                        <svg class="spin-pulse h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <span x-text="lang==='FR' ? 'Génération…' : 'Generating…'"></span>
                    </span>
                </button>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- VAT CALCULATOR PAGE                                        -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='calculator'" x-cloak class="p-6 float-in" x-data="vatCalc()">
            <div class="max-w-md space-y-5">
                <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                    x-text="lang==='FR' ? 'Calculateur TVA Camerounais' : 'Cameroonian VAT Calculator'"></h2>

                <div class="glass rounded-2xl p-6 space-y-5">
                    <!-- Mode toggle -->
                    <div class="flex gap-2 p-1 rounded-xl" style="background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.07)">
                        <button @click="mode='ht'" class="flex-1 py-2 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all"
                                :style="mode==='ht' ? 'background:rgba(245,158,11,0.2);border:1px solid rgba(245,158,11,0.35);color:rgb(252,211,77)' : 'color:rgba(148,163,184,0.7);border:1px solid transparent'"
                                x-text="lang==='FR' ? 'À partir du HT' : 'From HT'"></button>
                        <button @click="mode='ttc'" class="flex-1 py-2 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all"
                                :style="mode==='ttc' ? 'background:rgba(245,158,11,0.2);border:1px solid rgba(245,158,11,0.35);color:rgb(252,211,77)' : 'color:rgba(148,163,184,0.7);border:1px solid transparent'"
                                x-text="lang==='FR' ? 'À partir du TTC' : 'From TTC'"></button>
                    </div>

                    <!-- Amount input -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2"
                               x-text="mode==='ht' ? (lang==='FR'?'Montant Hors Taxes (XAF)':'Amount excl. tax (XAF)') : (lang==='FR'?'Montant TTC (XAF)':'Amount incl. tax (XAF)')"></label>
                        <div class="relative">
                            <input type="number" x-model="amount" @input="calculate()"
                                   class="glass-input !text-2xl !font-black !py-4" placeholder="0" min="0">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-black text-slate-500">XAF</span>
                        </div>
                    </div>

                    <!-- Result -->
                    <div x-show="result" x-cloak class="rounded-xl p-4 space-y-3 float-in"
                         style="background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.08)">
                        <div class="flex justify-between items-center py-1.5 border-b" style="border-color:rgba(255,255,255,0.06)">
                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider"
                                  x-text="lang==='FR' ? 'Base HT' : 'Amount HT'"></span>
                            <span class="font-mono font-black text-white" x-text="fmtXaf(result?.amount_ht)"></span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b" style="border-color:rgba(255,255,255,0.06)">
                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider">TVA (17.5%)</span>
                            <span class="font-mono font-black text-indigo-400" x-text="fmtXaf(result?.base_vat)"></span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b" style="border-color:rgba(255,255,255,0.06)">
                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-wider">CAC (10% TVA)</span>
                            <span class="font-mono font-black text-purple-400" x-text="fmtXaf(result?.cac)"></span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-[11px] font-black text-white uppercase tracking-wider"
                                  x-text="lang==='FR' ? 'Total TTC' : 'Total incl. tax'"></span>
                            <span class="font-mono font-black text-amber-400 text-xl" x-text="fmtXaf(result?.amount_ttc)"></span>
                        </div>
                    </div>

                    <!-- Rates info -->
                    <div class="text-[10px] text-slate-500 font-medium px-1">
                        <span class="text-slate-400 font-black">Taux Cameroun:</span>
                        TVA 17.5% + CAC 10% de la TVA = <span class="text-amber-400 font-black">19.25% TTC</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- BANK STATEMENT IMPORT PAGE                                 -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='import'" x-cloak class="p-6 space-y-5 float-in" x-data="bankImport()">
            <div>
                <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                    x-text="lang==='FR' ? 'Import Relevé Bancaire CSV' : 'Bank Statement CSV Import'"></h2>
                <p class="text-xs text-slate-400 mt-1"
                   x-text="lang==='FR' ? 'Compatible Afriland, Ecobank, SGBC, UBA — colonnes configurables' : 'Afriland, Ecobank, SGBC, UBA compatible — configurable columns'"></p>
            </div>

            <div class="glass-card rounded-2xl p-6 space-y-5">
                <!-- File picker -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2"
                           x-text="lang==='FR' ? 'Fichier CSV (.csv ou .txt)' : 'CSV File (.csv or .txt)'"></label>
                    <input type="file" accept=".csv,.txt" @change="onFileChange($event)"
                           class="block text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:cursor-pointer"
                           style="file:background:rgba(255,255,255,0.09);file:color:rgb(226,232,240);file:border:1px solid rgba(255,255,255,0.15)">
                    <p x-show="importFile" class="text-[10px] text-slate-400 mt-1.5" x-text="importFile?.name + ' — ' + Math.round((importFile?.size||0)/1024) + ' KB'"></p>
                </div>

                <!-- Column mapping -->
                <div>
                    <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-3"
                       x-text="lang==='FR' ? 'Mapping des Colonnes (indice 0-basé)' : 'Column Mapping (0-based index)'"></p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5"
                                   x-text="lang==='FR' ? 'Colonne Date' : 'Date Column'"></label>
                            <input type="number" x-model="importCols.date_col" min="0" class="glass-input" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5"
                                   x-text="lang==='FR' ? 'Colonne Référence' : 'Reference Column'"></label>
                            <input type="number" x-model="importCols.reference_col" min="0" class="glass-input" placeholder="1">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5"
                                   x-text="lang==='FR' ? 'Colonne Mémo' : 'Memo Column'"></label>
                            <input type="number" x-model="importCols.memo_col" min="0" class="glass-input" placeholder="2">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5"
                                   x-text="lang==='FR' ? 'Colonne Débit' : 'Debit Column'"></label>
                            <input type="number" x-model="importCols.debit_col" min="0" class="glass-input" placeholder="3">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5"
                                   x-text="lang==='FR' ? 'Colonne Crédit' : 'Credit Column'"></label>
                            <input type="number" x-model="importCols.credit_col" min="0" class="glass-input" placeholder="4">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5"
                                   x-text="lang==='FR' ? 'Lignes à ignorer (entête)' : 'Header rows to skip'"></label>
                            <input type="number" x-model="importCols.skip_rows" min="0" max="10" class="glass-input" placeholder="1">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3 mt-3">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5"
                                   x-text="lang==='FR' ? 'Séparateur' : 'Delimiter'"></label>
                            <select x-model="importCols.delimiter" class="glass-input">
                                <option value=",">, (virgule)</option>
                                <option value=";">; (point-virgule)</option>
                                <option value="&#9;">⇥ (tabulation)</option>
                                <option value="|">| (pipe)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Error / success -->
                <div x-show="importError" class="px-4 py-3 rounded-xl text-xs font-bold"
                     style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.25);color:rgb(252,165,165)"
                     x-text="importError"></div>

                <!-- Result summary -->
                <div x-show="importResult" x-cloak class="rounded-xl p-4 space-y-2 float-in"
                     style="background:rgba(16,185,129,0.07);border:1px solid rgba(16,185,129,0.2)">
                    <p class="text-xs font-black text-emerald-400 uppercase tracking-widest"
                       x-text="lang==='FR' ? 'Import Terminé' : 'Import Complete'"></p>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase tracking-widest font-black mb-1" x-text="lang==='FR' ? 'Lignes Traitées' : 'Rows Processed'"></div>
                            <div class="font-mono font-black text-white" x-text="importResult?.total_rows"></div>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase tracking-widest font-black mb-1" x-text="lang==='FR' ? 'Imputées' : 'Posted'"></div>
                            <div class="font-mono font-black text-emerald-400" x-text="importResult?.posted_count"></div>
                        </div>
                        <div>
                            <div class="text-[9px] text-slate-500 uppercase tracking-widest font-black mb-1" x-text="lang==='FR' ? 'Ignorées' : 'Skipped'"></div>
                            <div class="font-mono font-black text-amber-400" x-text="importResult?.skipped_count"></div>
                        </div>
                    </div>
                    <template x-if="importResult?.skipped?.length">
                        <div class="mt-2 text-[10px] text-slate-500 max-h-24 overflow-y-auto space-y-0.5">
                            <template x-for="s in importResult.skipped" :key="s">
                                <div x-text="s"></div>
                            </template>
                        </div>
                    </template>
                </div>

                <button @click="runImport()" :disabled="importLoading || !importFile"
                        class="glass-btn-amber px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest flex items-center gap-2.5 disabled:opacity-40">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span x-show="!importLoading" x-text="lang==='FR' ? 'Lancer l\'Import' : 'Run Import'"></span>
                    <span x-show="importLoading" x-cloak class="flex items-center gap-2">
                        <svg class="spin-pulse h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        <span x-text="lang==='FR' ? 'Import en cours…' : 'Importing…'"></span>
                    </span>
                </button>
            </div>

            <!-- Format guide -->
            <div class="glass-card rounded-2xl p-5">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3"
                   x-text="lang==='FR' ? 'Exemple de Format CSV Attendu' : 'Expected CSV Format Example'"></p>
                <pre class="text-[10px] text-slate-400 font-mono leading-relaxed overflow-x-auto"
                     style="background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.07);padding:0.75rem;border-radius:0.5rem">Date,Reference,Memo,Debit,Credit
2026-01-15,VIR-001234,Paiement fournisseur SARL ABC,150000,
2026-01-16,REC-005678,Encaissement client XYZ,,320000
2026-01-17,VIR-001299,Salaires janvier 2026,850000,</pre>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- SUB-LEDGERS PAGE                                           -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='subledgers'" x-cloak class="p-6 space-y-5 float-in">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                        x-text="lang==='FR' ? 'Sous-Comptes Mobile Money & Caisse' : 'Mobile Money & Cash Sub-Ledgers'"></h2>
                    <p class="text-xs text-slate-400 mt-1"
                       x-text="lang==='FR' ? 'Comptes 571x — MTN MoMo, Orange Money, Caisses secondaires' : 'Accounts 571x — MTN MoMo, Orange Money, Cash registers'"></p>
                </div>
                <div class="flex gap-2 flex-wrap">
                    <button @click="provisionSub('MTN')"
                            class="glass-btn-dark px-3 py-1.5 rounded-xl text-[10px] uppercase tracking-widest"
                            style="border-color:rgba(255,176,0,0.3);color:rgb(252,211,77)">+ MTN MoMo</button>
                    <button @click="provisionSub('ORANGE')"
                            class="glass-btn-dark px-3 py-1.5 rounded-xl text-[10px] uppercase tracking-widest"
                            style="border-color:rgba(249,115,22,0.3);color:rgb(253,186,116)">+ Orange Money</button>
                    <button @click="provisionSub('CASH')"
                            class="glass-btn-dark px-3 py-1.5 rounded-xl text-[10px] uppercase tracking-widest"
                            style="border-color:rgba(16,185,129,0.3);color:rgb(110,231,183)">+ Caisse</button>
                </div>
            </div>

            <div x-show="subError" class="px-4 py-3 rounded-xl text-xs font-bold"
                 style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.25);color:rgb(252,165,165)"
                 x-text="subError"></div>
            <div x-show="subSuccess" class="px-4 py-3 rounded-xl text-xs font-bold float-in"
                 style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)"
                 x-text="subSuccess"></div>

            <div class="glass rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b flex items-center justify-between"
                     style="border-color:rgba(255,255,255,0.07);background:rgba(0,0,0,0.15)">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                          x-text="lang==='FR' ? 'Comptes Auxiliaires Actifs' : 'Active Auxiliary Accounts'"></span>
                    <span class="text-[10px] font-mono font-black px-2.5 py-0.5 rounded-full"
                          style="background:rgba(99,102,241,0.15);color:rgb(165,180,252);border:1px solid rgba(99,102,241,0.3)"
                          x-text="subAccounts.length + ' comptes'"></span>
                </div>
                <template x-if="subAccounts.length===0">
                    <div class="py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                                 style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08)">📲</div>
                            <div class="text-slate-500 text-[11px] font-black uppercase tracking-widest"
                                 x-text="lang==='FR' ? 'Aucun sous-compte actif. Cliquez + pour en créer.' : 'No sub-ledgers yet. Click + to create one.'"></div>
                        </div>
                    </div>
                </template>
                <div class="divide-y" style="border-color:rgba(255,255,255,0.06)">
                    <template x-for="acc in subAccounts" :key="acc.id">
                        <div class="px-5 py-3.5 flex items-center justify-between glass-row">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-mono font-black text-[11px] flex-shrink-0"
                                     :style="acc.code?.startsWith('5712')
                                        ? 'background:rgba(255,176,0,0.12);border:1px solid rgba(255,176,0,0.25);color:rgb(252,211,77)'
                                        : acc.code?.startsWith('5713')
                                        ? 'background:rgba(249,115,22,0.12);border:1px solid rgba(249,115,22,0.25);color:rgb(253,186,116)'
                                        : 'background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)'"
                                     x-text="acc.code"></div>
                                <div>
                                    <div class="text-sm font-black text-white" x-text="acc.label"></div>
                                    <div class="text-[10px] text-slate-500 font-medium">
                                        <span x-text="acc.code?.startsWith('5712') ? 'MTN MoMo' : acc.code?.startsWith('5713') ? 'Orange Money' : 'Caisse'"></span>
                                        · SYSCOHADA Classe 5
                                    </div>
                                </div>
                            </div>
                            <span class="text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest"
                                  :style="acc.code?.startsWith('5712')
                                    ? 'background:rgba(255,176,0,0.1);border:1px solid rgba(255,176,0,0.2);color:rgb(252,211,77)'
                                    : acc.code?.startsWith('5713')
                                    ? 'background:rgba(249,115,22,0.1);border:1px solid rgba(249,115,22,0.2);color:rgb(253,186,116)'
                                    : 'background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:rgb(110,231,183)'"
                                  x-text="acc.code?.startsWith('5712') ? 'MTN' : acc.code?.startsWith('5713') ? 'ORANGE' : 'CASH'"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- OFFLINE SYNC STATUS PAGE                                   -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='sync'" x-cloak class="p-6 space-y-5 float-in">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                        x-text="lang==='FR' ? 'Synchronisation Hors Ligne' : 'Offline Sync'"></h2>
                    <p class="text-xs text-slate-400 mt-1"
                       x-text="lang==='FR' ? 'File d\'attente de synchronisation — écritures en attente d\'envoi' : 'Sync queue — entries pending upload'"></p>
                </div>
                <div class="flex gap-2">
                    <button @click="loadSyncStatus()"
                            class="glass-btn-dark px-4 py-1.5 rounded-xl text-[10px] uppercase tracking-widest"
                            x-text="lang==='FR' ? '↺ Rafraîchir' : '↺ Refresh'"></button>
                    <button @click="pushSync()" :disabled="syncPushing || !syncStatus?.pending_count"
                            class="glass-btn-amber px-4 py-1.5 rounded-xl text-[10px] uppercase tracking-widest disabled:opacity-40"
                            x-text="syncPushing ? '…' : (lang==='FR' ? '↑ Envoyer Maintenant' : '↑ Push Now')"></button>
                </div>
            </div>

            <!-- Status cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="glass-card rounded-2xl p-4 text-center relative overflow-hidden"
                     style="box-shadow:0 4px 24px rgba(0,0,0,0.45),0 0 24px rgba(245,158,11,0.12)">
                    <div class="absolute -top-2 -right-2 w-12 h-12 rounded-full blur-2xl opacity-20"
                         style="background:rgb(245,158,11)"></div>
                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-2"
                         x-text="lang==='FR' ? 'En Attente' : 'Pending'"></div>
                    <div class="font-mono font-black text-amber-400 text-2xl" x-text="syncStatus?.pending_count ?? '—'"></div>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center relative overflow-hidden"
                     style="box-shadow:0 4px 24px rgba(0,0,0,0.45),0 0 24px rgba(16,185,129,0.12)">
                    <div class="absolute -top-2 -right-2 w-12 h-12 rounded-full blur-2xl opacity-20"
                         style="background:rgb(16,185,129)"></div>
                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-2"
                         x-text="lang==='FR' ? 'Synchronisées' : 'Synced'"></div>
                    <div class="font-mono font-black text-emerald-400 text-2xl" x-text="syncStatus?.synced_count ?? '—'"></div>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center relative overflow-hidden"
                     style="box-shadow:0 4px 24px rgba(0,0,0,0.45),0 0 24px rgba(244,63,94,0.12)">
                    <div class="absolute -top-2 -right-2 w-12 h-12 rounded-full blur-2xl opacity-20"
                         style="background:rgb(244,63,94)"></div>
                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-2"
                         x-text="lang==='FR' ? 'Erreurs' : 'Errors'"></div>
                    <div class="font-mono font-black text-rose-400 text-2xl" x-text="syncStatus?.error_count ?? '—'"></div>
                </div>
                <div class="glass-card rounded-2xl p-4 text-center">
                    <div class="text-[9px] text-slate-500 font-black uppercase tracking-widest mb-2"
                         x-text="lang==='FR' ? 'Dernière Sync' : 'Last Sync'"></div>
                    <div class="font-mono font-black text-slate-300 text-xs leading-snug"
                         x-text="syncStatus?.last_synced_at ?? (lang==='FR' ? 'Jamais' : 'Never')"></div>
                </div>
            </div>

            <!-- Connection status banner -->
            <div class="glass-card rounded-2xl p-4 flex items-center gap-4"
                 :style="connStatus==='ONLINE'
                    ? 'border-color:rgba(16,185,129,0.3);box-shadow:0 4px 20px rgba(16,185,129,0.1)'
                    : 'border-color:rgba(245,158,11,0.3);box-shadow:0 4px 20px rgba(245,158,11,0.1)'">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-lg"
                     :style="connStatus==='ONLINE'
                        ? 'background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3)'
                        : 'background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3)'"
                     x-text="connStatus==='ONLINE' ? '🌐' : '📴'"></div>
                <div>
                    <div class="text-sm font-black text-white"
                         x-text="connStatus==='ONLINE'
                            ? (lang==='FR' ? 'Connecté — Sync automatique active' : 'Connected — Auto-sync active')
                            : (lang==='FR' ? 'Hors ligne — Les écritures sont stockées localement' : 'Offline — Entries stored locally')"></div>
                    <div class="text-[10px] text-slate-400 mt-0.5"
                         x-text="lang==='FR' ? 'La synchronisation reprend automatiquement au retour de la connexion.' : 'Sync resumes automatically when connection is restored.'"></div>
                </div>
            </div>

            <!-- Pending items list -->
            <div x-show="syncQueue.length > 0" class="glass rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b" style="border-color:rgba(255,255,255,0.07);background:rgba(0,0,0,0.15)">
                    <span class="text-[10px] font-black text-amber-400 uppercase tracking-widest"
                          x-text="lang==='FR' ? 'File d\'Attente de Synchronisation' : 'Sync Queue'"></span>
                </div>
                <div class="divide-y" style="border-color:rgba(255,255,255,0.06)">
                    <template x-for="item in syncQueue" :key="item.id">
                        <div class="px-5 py-3 flex items-center justify-between glass-row">
                            <div>
                                <div class="text-xs font-black text-white font-mono" x-text="item.reference_id ?? item.id"></div>
                                <div class="text-[10px] text-slate-500 mt-0.5" x-text="item.memo ?? item.payload_type"></div>
                            </div>
                            <span class="text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider"
                                  style="background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:rgb(252,211,77)"
                                  x-text="lang==='FR' ? 'En attente' : 'Pending'"></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- TEAM MANAGEMENT PAGE                                       -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='team'" x-cloak class="p-6 space-y-5 float-in">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                        x-text="lang==='FR' ? 'Gestion de l\'Équipe' : 'Team Management'"></h2>
                    <p class="text-xs text-slate-400 mt-1"
                       x-text="lang==='FR' ? 'Invitez des comptables et agents de saisie' : 'Invite accountants and clerks'"></p>
                </div>
                <template x-if="user?.role === 'OWNER'">
                    <button @click="teamShowInvite=!teamShowInvite"
                            class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest"
                            x-text="lang==='FR' ? '+ Inviter un Membre' : '+ Invite Member'"></button>
                </template>
            </div>

            <!-- Invite form -->
            <div x-show="teamShowInvite" x-cloak class="glass-card rounded-2xl p-5 space-y-3 float-in">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest"
                   x-text="lang==='FR' ? 'Nouvel Utilisateur' : 'New User'"></p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Nom complet' : 'Full Name'"></label>
                        <input type="text" x-model="inviteForm.name" class="glass-input"
                               :placeholder="lang==='FR' ? 'Jean Dupont' : 'John Smith'">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Email</label>
                        <input type="email" x-model="inviteForm.email" class="glass-input" placeholder="jean@entreprise.cm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Rôle</label>
                        <select x-model="inviteForm.role" class="glass-input">
                            <option value="ACCOUNTANT">ACCOUNTANT</option>
                            <option value="CLERK">CLERK</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Mot de passe' : 'Password'"></label>
                        <input type="password" x-model="inviteForm.password" class="glass-input" placeholder="••••••••">
                    </div>
                </div>
                <div x-show="teamError" class="px-4 py-2.5 rounded-xl text-xs font-bold"
                     style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.25);color:rgb(252,165,165)"
                     x-text="teamError"></div>
                <div x-show="teamSuccess" class="px-4 py-2.5 rounded-xl text-xs font-bold"
                     style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)"
                     x-text="teamSuccess"></div>
                <div class="flex gap-2">
                    <button @click="doInvite()" :disabled="teamLoading"
                            class="glass-btn-amber px-5 py-2 rounded-xl text-xs uppercase tracking-widest disabled:opacity-40"
                            x-text="teamLoading ? '…' : (lang==='FR' ? 'Envoyer Invitation' : 'Send Invite')"></button>
                    <button @click="teamShowInvite=false" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest"
                            x-text="lang==='FR' ? 'Annuler' : 'Cancel'"></button>
                </div>
            </div>

            <!-- Members list -->
            <div class="glass rounded-2xl overflow-hidden">
                <div class="px-5 py-3 border-b flex items-center justify-between"
                     style="border-color:rgba(255,255,255,0.07);background:rgba(0,0,0,0.15)">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                          x-text="lang==='FR' ? 'Membres de l\'Entreprise' : 'Company Members'"></span>
                    <span class="text-[10px] font-mono font-black px-2.5 py-0.5 rounded-full"
                          style="background:rgba(245,158,11,0.15);color:rgb(252,211,77);border:1px solid rgba(245,158,11,0.3)"
                          x-text="teamMembers.length + (lang==='FR' ? ' membres' : ' members')"></span>
                </div>
                <template x-if="teamMembers.length === 0">
                    <div class="py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                                 style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08)">👥</div>
                            <div class="text-slate-500 text-[11px] font-black uppercase tracking-widest"
                                 x-text="lang==='FR' ? 'Aucun membre trouvé.' : 'No members found.'"></div>
                        </div>
                    </div>
                </template>
                <div class="divide-y" style="--tw-divide-opacity:1">
                    <template x-for="member in teamMembers" :key="member.id">
                        <div class="px-5 py-3.5 flex items-center justify-between glass-row">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-black"
                                     style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.2);color:rgb(252,211,77)"
                                     x-text="(member.name ?? '?')[0].toUpperCase()"></div>
                                <div>
                                    <div class="text-sm font-black text-white" x-text="member.name"></div>
                                    <div class="text-[10px] text-slate-500 font-medium" x-text="member.email"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest"
                                      :style="member.role==='OWNER'
                                        ? 'background:rgba(245,158,11,0.15);border:1px solid rgba(245,158,11,0.3);color:rgb(252,211,77)'
                                        : member.role==='ACCOUNTANT'
                                        ? 'background:rgba(99,102,241,0.15);border:1px solid rgba(99,102,241,0.3);color:rgb(165,180,252)'
                                        : 'background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);color:rgb(148,163,184)'"
                                      x-text="member.role"></span>
                                <span class="text-[9px] font-black px-2 py-0.5 rounded-full uppercase tracking-wider"
                                      :style="member.id===user?.id
                                        ? 'background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)'
                                        : 'display:none'"
                                      x-text="lang==='FR' ? 'Vous' : 'You'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- SETTINGS PAGE                                              -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='settings'" x-cloak class="p-6 space-y-6 float-in" x-data="settingsForm()">
            <div>
                <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                    x-text="lang==='FR' ? 'Paramètres Entreprise' : 'Company Settings'"></h2>
                <p class="text-xs text-slate-400 mt-1"
                   x-text="lang==='FR' ? 'Profil fiscal, en-tête de facture, logo' : 'Tax profile, invoice letterhead, logo'"></p>
            </div>

            <!-- Logo upload -->
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest"
                   x-text="lang==='FR' ? 'Logo & Identité Visuelle' : 'Logo & Visual Identity'"></p>
                <div class="flex items-center gap-5">
                    <!-- Logo preview -->
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center overflow-hidden flex-shrink-0"
                         style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.12)">
                        <template x-if="logoPreview || settingsData.logo_url">
                            <img :src="logoPreview || settingsData.logo_url" class="w-full h-full object-contain p-1">
                        </template>
                        <template x-if="!logoPreview && !settingsData.logo_url">
                            <span class="text-amber-400 font-black text-2xl tracking-widest"
                                  x-text="(settingsData.name ?? 'OB').substring(0,2).toUpperCase()"></span>
                        </template>
                    </div>
                    <div class="space-y-2 flex-1">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest"
                               x-text="lang==='FR' ? 'Logo Entreprise (PNG/JPG/SVG, max 2 Mo)' : 'Company Logo (PNG/JPG/SVG, max 2MB)'"></label>
                        <input type="file" accept="image/*" @change="onLogoChange($event)"
                               class="block text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:cursor-pointer"
                               style="file:background:rgba(245,158,11,0.15);file:color:rgb(252,211,77);file:border:1px solid rgba(245,158,11,0.3)">
                        <button x-show="logoFile" @click="uploadLogo()"
                                :disabled="logoUploading"
                                class="glass-btn-amber px-4 py-1.5 rounded-xl text-[10px] uppercase tracking-widest disabled:opacity-40"
                                x-text="logoUploading ? '…' : (lang==='FR' ? 'Enregistrer le Logo' : 'Save Logo')"></button>
                        <p x-show="logoMsg" class="text-[10px] font-bold text-emerald-400" x-text="logoMsg"></p>
                    </div>
                </div>
            </div>

            <!-- Fiscal profile -->
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest"
                   x-text="lang==='FR' ? 'Profil Fiscal DGI' : 'DGI Tax Profile'"></p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Raison Sociale' : 'Company Name'"></label>
                        <input type="text" x-model="settingsData.name" class="glass-input">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">NIU</label>
                        <input type="text" x-model="settingsData.niu" class="glass-input" placeholder="M08200001A">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">RCCM</label>
                        <input type="text" x-model="settingsData.rccm" class="glass-input" placeholder="RC/DLA/2020/B/01234">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Régime Fiscal' : 'Tax Regime'"></label>
                        <select x-model="settingsData.tax_regime" class="glass-input">
                            <option value="REEL">RÉEL</option>
                            <option value="SIMPLIFIE">SIMPLIFIÉ</option>
                            <option value="LIBERATOIRE">LIBÉRATOIRE</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Centre des Impôts' : 'Tax Center'"></label>
                        <input type="text" x-model="settingsData.tax_center" class="glass-input" placeholder="DGI Douala Akwa">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Téléphone' : 'Phone'"></label>
                        <input type="text" x-model="settingsData.phone" class="glass-input" placeholder="+237 6XX XXX XXX">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Email</label>
                        <input type="email" x-model="settingsData.email" class="glass-input">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Adresse Siège Social' : 'Registered Address'"></label>
                        <input type="text" x-model="settingsData.address" class="glass-input">
                    </div>
                </div>
            </div>

            <!-- Letterhead / Invoice config -->
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest"
                   x-text="lang==='FR' ? 'En-Tête de Facture' : 'Invoice Letterhead'"></p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Slogan / Tagline' : 'Tagline'"></label>
                        <input type="text" x-model="settingsData.letterhead_tagline" class="glass-input"
                               :placeholder="lang==='FR' ? 'Votre partenaire de confiance' : 'Your trusted partner'">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Site Web</label>
                        <input type="text" x-model="settingsData.letterhead_website" class="glass-input" placeholder="www.votreentreprise.cm">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Banque Domiciliataire' : 'Bank'"></label>
                        <input type="text" x-model="settingsData.bank_name" class="glass-input" placeholder="Afriland First Bank">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'N° Compte' : 'Account No.'"></label>
                        <input type="text" x-model="settingsData.bank_account" class="glass-input" placeholder="001 000 12345 67">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">RIB</label>
                        <input type="text" x-model="settingsData.bank_rib" class="glass-input" placeholder="10005 00001 00123456789 12">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                           x-text="lang==='FR' ? 'Note de Pied de Facture' : 'Invoice Footer Note'"></label>
                    <input type="text" x-model="settingsData.invoice_footer_note" class="glass-input"
                           :placeholder="lang==='FR' ? 'Paiement à 30 jours. En cas de retard, pénalité de 1,5%/mois.' : 'Payment due in 30 days. Late penalty: 1.5%/month.'">
                </div>
            </div>

            <!-- Save -->
            <div x-show="settingsError" class="px-4 py-3 rounded-xl text-sm font-bold"
                 style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.25);color:rgb(252,165,165)"
                 x-text="settingsError"></div>
            <div x-show="settingsSuccess" class="px-4 py-3 rounded-xl text-sm font-bold"
                 style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)"
                 x-text="settingsSuccess"></div>

            <div class="flex items-center gap-3">
                <button @click="saveSettings()" :disabled="settingsSaving"
                        class="glass-btn-amber px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest disabled:opacity-40"
                        x-text="settingsSaving ? '…' : (lang==='FR' ? 'Enregistrer les Modifications' : 'Save Changes')"></button>
                <a href="/about" class="glass-btn-dark px-5 py-2.5 rounded-xl text-xs uppercase tracking-widest"
                   x-text="lang==='FR' ? 'À Propos d\'Opes Books' : 'About Opes Books'"></a>
            </div>
        </div>

    </main>
</div>

<script>
function opesApp() {
    return {
        page: new URLSearchParams(location.search).get('page') || 'dashboard',
        lang: localStorage.getItem('opes_lang') || 'FR',
        connStatus: navigator.onLine ? 'ONLINE' : 'OFFLINE',
        loading: false,
        user: null,
        company: null,
        fiscalProvision: 0,
        today: new Date().toLocaleDateString('fr-CM', { weekday:'long', year:'numeric', month:'long', day:'numeric' }),

        /* DGI export */
        dgiExporting: false,

        /* Subledgers */
        subAccounts: [],
        subError: '',
        subSuccess: '',

        /* Sync */
        syncStatus: null,
        syncQueue: [],
        syncPushing: false,

        /* Team */
        teamMembers: [],
        teamShowInvite: false,
        teamLoading: false,
        teamError: '',
        teamSuccess: '',
        inviteForm: { name:'', email:'', role:'ACCOUNTANT', password:'' },

        /* Journal */
        journalEntries: [],
        journalMeta: { current_page:1, last_page:1 },
        journalPage: 1,
        journalFilter: { from: '', to: '' },

        /* Ledger */
        ledgerAccounts: [],
        ledgerMeta: { grand_debit:'0', grand_credit:'0', balanced: true },
        ledgerFilter: { from: '', to: '' },

        kpiStats: [
            { key:'revenue', labelFr:"CA HT",        labelEn:"Revenue HT",   value:0, account:'701100', icon:'↑', textColor:'text-emerald-400', glow:'rgba(16,185,129,0.15)',   blob:'rgb(16,185,129)' },
            { key:'vat',     labelFr:"TVA Collectée", labelEn:"Output VAT",   value:0, account:'443100', icon:'⊕', textColor:'text-indigo-400',  glow:'rgba(99,102,241,0.15)',  blob:'rgb(99,102,241)' },
            { key:'cac',     labelFr:"CAC Dû",        labelEn:"CAC Due",      value:0, account:'448600', icon:'◎', textColor:'text-purple-400',  glow:'rgba(168,85,247,0.15)',  blob:'rgb(168,85,247)' },
            { key:'charges', labelFr:"Charges HT",   labelEn:"Expenses HT",  value:0, account:'Classe 6', icon:'↓', textColor:'text-rose-400', glow:'rgba(244,63,94,0.15)',   blob:'rgb(244,63,94)' },
        ],

        quickActions: [
            { page:'invoice',    icon:'📄', labelFr:'Nouvelle Facture', labelEn:'New Invoice' },
            { page:'calculator', icon:'🧮', labelFr:'Calc. TVA',        labelEn:'VAT Calc' },
            { page:'journal',    icon:'📋', labelFr:'Journal',          labelEn:'Journal' },
            { page:'ledger',     icon:'📊', labelFr:'Grand Livre',      labelEn:'Ledger' },
            { page:'team',        icon:'👥', labelFr:'Équipe',           labelEn:'Team' },
            { page:'settings',    icon:'⚙️', labelFr:'Paramètres',       labelEn:'Settings' },
            { page:'import',      icon:'📂', labelFr:'Import CSV',        labelEn:'CSV Import' },
            { page:'sync',        icon:'🔄', labelFr:'Sync Hors Ligne',   labelEn:'Offline Sync' },
            { page:null, href:'/dgi-monitor',   icon:'📡', labelFr:'Suivi DGI',    labelEn:'DGI Monitor' },
            { page:null, href:'/tax-dashboard', icon:'📈', labelFr:'Bilan Fiscal', labelEn:'Tax Monitor' },
        ],

        async init() {
            const token = localStorage.getItem('opes_token');
            if (!token) { window.location.href = '/login'; return; }
            window.addEventListener('online',  () => { this.connStatus='SYNCING'; setTimeout(()=>this.connStatus='ONLINE', 2800); });
            window.addEventListener('offline', () => { this.connStatus='OFFLINE'; });
            this.$watch('lang', v => localStorage.setItem('opes_lang', v));
            this.loading = true;
            await this.loadMe();
            this.loading = false;
            /* Auto-load data for initial page */
            if (this.page==='journal')  this.loadJournal();
            if (this.page==='ledger')   this.loadLedger();
            if (this.page==='team')     this.loadTeam();
            if (this.page==='settings') {} // data comes from loadMe()
        },

        async api(path, opts={}) {
            const token = localStorage.getItem('opes_token');
            const res = await fetch('/api/v1/' + path, {
                headers: { 'Authorization':'Bearer '+token, 'Accept':'application/json', 'Content-Type':'application/json', ...opts.headers },
                ...opts,
            });
            if (res.status===401) { localStorage.clear(); window.location.href='/login'; }
            return res.json();
        },

        async loadMe() {
            const data = await this.api('auth/me');
            this.user    = data.user;
            this.company = data.company;
            if (this.company) {
                this.buildKpis();
                // Refresh KPI totals from trial balance
                this.loadDashboardKpis();
            }
        },

        async loadDashboardKpis() {
            if (!this.company) return;
            try {
                const now = new Date();
                const from = `${now.getFullYear()}-01-01`;
                const to   = now.toISOString().slice(0,10);
                const data = await this.api(`companies/${this.company.id}/trial-balance?from=${from}&to=${to}`);
                const accounts = data.accounts ?? [];
                const find = (code) => accounts.find(a => a.code === code);
                const credit = (code) => parseFloat(find(code)?.total_credit ?? 0);
                const debit6 = accounts.filter(a => a.code?.startsWith('6')).reduce((s,a) => s + parseFloat(a.total_debit ?? 0), 0);
                this.kpiStats[0].value = credit('701100');
                this.kpiStats[1].value = credit('443100');
                this.kpiStats[2].value = credit('448600');
                this.kpiStats[3].value = debit6;
                this.fiscalProvision = credit('443100') + credit('448600');
            } catch(e) {}
        },

        buildKpis() {
            /* KPI values will be zero until ledger endpoint is wired with period;
               fiscal provision shown from session or 0 */
        },

        setPage(p) {
            this.page = p;
            history.replaceState(null,'','/app?page='+p);
            if (p==='journal'    && !this.journalEntries.length) this.loadJournal();
            if (p==='ledger'     && !this.ledgerAccounts.length) this.loadLedger();
            if (p==='team'       && !this.teamMembers.length)    this.loadTeam();
            if (p==='subledgers' && !this.subAccounts.length)    this.loadSubledgers();
            if (p==='sync')                                       this.loadSyncStatus();
        },

        async loadJournal() {
            if (!this.company) return;
            this.loading = true;
            const params = new URLSearchParams({ per_page:30, page: this.journalPage });
            if (this.journalFilter.from) params.append('from', this.journalFilter.from);
            if (this.journalFilter.to)   params.append('to',   this.journalFilter.to);
            const data = await this.api(`companies/${this.company.id}/ledger?${params}`);
            this.journalEntries = data.data ?? [];
            this.journalMeta    = { current_page: data.current_page??1, last_page: data.last_page??1 };
            this.loading = false;
        },

        async loadLedger() {
            if (!this.company) return;
            this.loading = true;
            const params = new URLSearchParams();
            if (this.ledgerFilter.from) params.append('from', this.ledgerFilter.from);
            if (this.ledgerFilter.to)   params.append('to',   this.ledgerFilter.to);
            const data = await this.api(`companies/${this.company.id}/trial-balance?${params}`);
            this.ledgerAccounts = data.accounts ?? [];
            this.ledgerMeta = {
                grand_debit:  data.grand_debit  ?? '0',
                grand_credit: data.grand_credit ?? '0',
                balanced:     data.balanced     ?? true,
            };
            this.loading = false;
        },

        async exportDgiFiscalis() {
            if (!this.company) return;
            this.dgiExporting = true;
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/companies/${this.company.id}/exports/dgi-fiscalis`, {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' },
                });
                if (!res.ok) throw new Error('Export failed');
                const blob = await res.blob();
                const ct = res.headers.get('Content-Type') || '';
                const ext = ct.includes('csv') ? 'csv' : 'json';
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a'); a.href = url;
                a.download = `DGI-Fiscalis-${this.company.niu ?? 'export'}-${new Date().toISOString().slice(0,10)}.${ext}`;
                a.click(); URL.revokeObjectURL(url);
            } catch(e) { alert(e.message); }
            finally { this.dgiExporting = false; }
        },

        async downloadInvoice(txn) {
            if (!this.company) return;
            const token = localStorage.getItem('opes_token');
            const res = await fetch(`/api/v1/companies/${this.company.id}/invoice/${txn.id}/download`, {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/pdf' },
            });
            if (!res.ok) { alert('Invoice PDF not available'); return; }
            const blob = await res.blob();
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a'); a.href = url;
            a.download = `FACTURE-${txn.reference_id}.pdf`; a.click();
            URL.revokeObjectURL(url);
        },

        async loadSubledgers() {
            if (!this.company) return;
            const data = await this.api(`companies/${this.company.id}/subledgers`);
            this.subAccounts = Array.isArray(data) ? data : [];
        },

        async provisionSub(type) {
            if (!this.company) return;
            this.subError = ''; this.subSuccess = '';
            try {
                const data = await this.api(`companies/${this.company.id}/subledgers`, {
                    method:'POST', body: JSON.stringify({ type }),
                });
                if (data.errors) throw new Error(Object.values(data.errors).flat().join(' '));
                if (!data.account) throw new Error(data.message || 'Failed');
                this.subSuccess = `${data.account.code} — ${data.account.label} créé ✔`;
                await this.loadSubledgers();
            } catch(e) { this.subError = e.message; }
        },

        async loadSyncStatus() {
            if (!this.company) return;
            const niu = this.company.niu;
            const data = await this.api(`sync/status?company_niu=${encodeURIComponent(niu)}`);
            this.syncStatus = data;
            // Pull queue: entries pending upload (last 30 days)
            const since = new Date(Date.now() - 30*24*60*60*1000).toISOString();
            const q = await this.api(`sync/pull?company_niu=${encodeURIComponent(niu)}&since=${encodeURIComponent(since)}`);
            this.syncQueue = Array.isArray(q) ? q : (q.data ?? q.entries ?? []);
        },

        async pushSync() {
            if (!this.company) return;
            this.syncPushing = true;
            try {
                await this.api('sync/push', {
                    method: 'POST',
                    body: JSON.stringify({
                        client_id: 'web-spa',
                        company_niu: this.company.niu,
                        synced_at: new Date().toISOString(),
                        journal_entries: [],
                    }),
                });
                await this.loadSyncStatus();
            } catch(e) {}
            finally { this.syncPushing = false; }
        },

        async loadTeam() {
            const data = await this.api('auth/users');
            this.teamMembers = Array.isArray(data) ? data : (data.data ?? []);
        },

        async doInvite() {
            this.teamLoading=true; this.teamError=''; this.teamSuccess='';
            try {
                const data = await this.api('auth/users', {
                    method:'POST',
                    body: JSON.stringify(this.inviteForm),
                });
                if (data.errors) throw new Error(Object.values(data.errors).flat().join(' | '));
                if (!data.user) throw new Error(data.message || 'Invite failed');
                this.teamSuccess = this.lang==='FR' ? `${data.user.name} ajouté(e) avec succès.` : `${data.user.name} added successfully.`;
                this.inviteForm = { name:'', email:'', role:'ACCOUNTANT', password:'' };
                this.teamShowInvite = false;
                await this.loadTeam();
            } catch(e) {
                this.teamError = e.message;
            } finally {
                this.teamLoading = false;
            }
        },

        toggleLang() {
            this.lang = this.lang==='FR' ? 'EN' : 'FR';
        },

        async doLogout() {
            await this.api('auth/logout', { method:'POST' });
            localStorage.clear();
            window.location.href = '/login';
        },

        fmtXaf(v) {
            if (v===null||v===undefined) return '— XAF';
            return Number(v).toLocaleString('fr-CM', { minimumFractionDigits:0, maximumFractionDigits:0 }) + ' XAF';
        },
    };
}

function invoiceForm() {
    return {
        generating: false,
        invoiceError: '',
        form: {
            invoice_number: 'FAC-' + new Date().getFullYear() + '-001',
            invoice_date: new Date().toISOString().split('T')[0],
            language: localStorage.getItem('opes_lang') || 'FR',
            client_name: '',
            client_niu: '',
            lines: [{ description:'', quantity:1, unit_price_ht:0 }],
        },
        get totalHt() {
            return this.form.lines.reduce((s,l) => s + Number(l.quantity)*Number(l.unit_price_ht), 0);
        },
        addLine() {
            this.form.lines.push({ description:'', quantity:1, unit_price_ht:0 });
        },
        fmtXaf(v) {
            if (!v && v!==0) return '0 XAF';
            return Number(v).toLocaleString('fr-CM', { minimumFractionDigits:0 }) + ' XAF';
        },
        async generatePdf() {
            this.generating=true; this.invoiceError='';
            try {
                const token = localStorage.getItem('opes_token');
                const me = await (await fetch('/api/v1/auth/me', { headers:{'Authorization':'Bearer '+token,'Accept':'application/json'} })).json();
                const companyId = me.company?.id;
                if (!companyId) throw new Error('Company not found');
                const payload = {
                    ...this.form,
                    lines: this.form.lines.map(l => ({ ...l, quantity:Number(l.quantity), unit_price_ht:Number(l.unit_price_ht) }))
                };
                const res = await fetch(`/api/v1/companies/${companyId}/invoice/generate`, {
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/pdf'},
                    body: JSON.stringify(payload),
                });
                if (!res.ok) { const err=await res.json(); throw new Error(err.message||'PDF generation failed'); }
                const blob = await res.blob();
                const url  = URL.createObjectURL(blob);
                const a    = document.createElement('a');
                a.href=url; a.download='OPES-'+this.form.invoice_number+'.pdf'; a.click();
                URL.revokeObjectURL(url);
            } catch(e) {
                this.invoiceError = e.message;
            } finally {
                this.generating = false;
            }
        },
    };
}

function bankImport() {
    return {
        importFile: null,
        importLoading: false,
        importError: '',
        importResult: null,
        importCols: { date_col:0, reference_col:1, memo_col:2, debit_col:3, credit_col:4, skip_rows:1, delimiter:',' },

        onFileChange(e) {
            this.importFile = e.target.files[0] || null;
            this.importResult = null; this.importError = '';
        },

        async runImport() {
            if (!this.importFile) return;
            this.importLoading=true; this.importError=''; this.importResult=null;
            try {
                const token = localStorage.getItem('opes_token');
                const me = await (await fetch('/api/v1/auth/me', {
                    headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}
                })).json();
                const companyId = me.company?.id;
                if (!companyId) throw new Error('Company not found');
                const fd = new FormData();
                fd.append('csv_file', this.importFile);
                Object.entries(this.importCols).forEach(([k,v]) => fd.append(k, v));
                const res = await fetch(`/api/v1/companies/${companyId}/bank-statement/import`, {
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Accept':'application/json'},
                    body: fd,
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.importResult = data;
            } catch(e) {
                this.importError = e.message;
            } finally {
                this.importLoading = false;
            }
        },
    };
}

function settingsForm() {
    return {
        settingsData: {},
        settingsSaving: false,
        settingsError: '',
        settingsSuccess: '',
        logoFile: null,
        logoPreview: null,
        logoUploading: false,
        logoMsg: '',

        async init() {
            // Pull current company data from parent scope
            const token = localStorage.getItem('opes_token');
            const res = await fetch('/api/v1/auth/me', {
                headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}
            });
            const data = await res.json();
            this.settingsData = { ...(data.company ?? {}) };
        },

        onLogoChange(e) {
            this.logoFile = e.target.files[0] || null;
            if (this.logoFile) {
                const reader = new FileReader();
                reader.onload = ev => { this.logoPreview = ev.target.result; };
                reader.readAsDataURL(this.logoFile);
            }
        },

        async uploadLogo() {
            if (!this.logoFile || !this.settingsData.id) return;
            this.logoUploading = true; this.logoMsg = '';
            const companyId = this.settingsData.id;
            try {
                const token = localStorage.getItem('opes_token');
                const fd = new FormData();
                fd.append('logo', this.logoFile);
                const res = await fetch(`/api/v1/companies/${companyId}/logo`, {
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Accept':'application/json'},
                    body: fd,
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Upload failed');
                this.settingsData.logo_url = data.logo_url;
                this.logoMsg = 'Logo enregistré ✔';
                this.logoFile = null;
            } catch(e) {
                this.settingsError = e.message;
            } finally {
                this.logoUploading = false;
            }
        },

        async saveSettings() {
            if (!this.settingsData.id) return;
            this.settingsSaving=true; this.settingsError=''; this.settingsSuccess='';
            const companyId = this.settingsData.id;
            try {
                const token = localStorage.getItem('opes_token');
                const payload = { ...this.settingsData };
                delete payload.logo_path; delete payload.logo_url;
                delete payload.id; delete payload.created_at; delete payload.updated_at; delete payload.deleted_at;
                delete payload.vat_prorata_coefficient; delete payload.subscription_status;
                const res = await fetch(`/api/v1/companies/${companyId}`, {
                    method:'PUT',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.settingsSuccess = document.documentElement.lang==='fr'
                    ? 'Paramètres enregistrés avec succès ✔'
                    : 'Settings saved successfully ✔';
            } catch(e) {
                this.settingsError = e.message;
            } finally {
                this.settingsSaving = false;
            }
        },
    };
}

function vatCalc() {
    return {
        mode: 'ht',
        amount: '',
        result: null,
        async calculate() {
            if (!this.amount || this.amount<=0) { this.result=null; return; }
            const token    = localStorage.getItem('opes_token');
            const endpoint = this.mode==='ht' ? 'tax/from-ht' : 'tax/from-ttc';
            const field    = this.mode==='ht' ? 'amount_ht' : 'amount_ttc';
            const res = await fetch('/api/v1/'+endpoint, {
                method:'POST',
                headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                body: JSON.stringify({ [field]: this.amount }),
            });
            this.result = await res.json();
        },
        fmtXaf(v) {
            if (v===null||v===undefined) return '—';
            return Number(v).toLocaleString('fr-CM', { minimumFractionDigits:2 }) + ' XAF';
        },
    };
}
</script>
</body>
</html>
