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

            <div class="my-2" style="height:1px;background:rgba(255,255,255,0.07)"></div>

            <a href="/tax-dashboard" class="nav-item">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span x-text="lang==='FR' ? 'Bilan Fiscal' : 'Tax Monitor'"></span>
            </a>
            <a href="/dgi-monitor" class="nav-item">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.14 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
                <span x-text="lang==='FR' ? 'Suivi DGI' : 'DGI Monitor'"></span>
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
                <div class="flex gap-2">
                    <input type="date" x-model="journalFilter.from" @change="loadJournal()"
                           class="glass-input !w-auto px-3 py-1.5 text-[11px]">
                    <input type="date" x-model="journalFilter.to" @change="loadJournal()"
                           class="glass-input !w-auto px-3 py-1.5 text-[11px]">
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
                            </tr>
                        </thead>
                        <tbody class="text-xs font-medium">
                            <template x-if="journalEntries.length === 0">
                                <tr>
                                    <td colspan="5" class="py-14 text-center">
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
            if (this.page==='journal') this.loadJournal();
            if (this.page==='ledger')  this.loadLedger();
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
            if (this.company) this.buildKpis();
        },

        buildKpis() {
            /* KPI values will be zero until ledger endpoint is wired with period;
               fiscal provision shown from session or 0 */
        },

        setPage(p) {
            this.page = p;
            history.replaceState(null,'','/app?page='+p);
            if (p==='journal' && !this.journalEntries.length) this.loadJournal();
            if (p==='ledger'  && !this.ledgerAccounts.length) this.loadLedger();
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
