<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opes Books — App</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- PWA -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#010048">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="OPESBooks">
    <link rel="apple-touch-icon" href="/icon.svg">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Brand gold — overrides Tailwind's default amber so utility classes match #C99B0E
        tailwind.config = { theme: { extend: { colors: { amber: {
            300:'#E3B420', 400:'#C99B0E', 500:'#B5890C', 600:'#A07C08', 700:'#866709'
        } } } } };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        /* ── Design tokens ────────────────────────────────────────────────
           The whole app is built on amber; --gold maps to it so the brand
           can be re-themed by editing these values in one place. */
        :root {
            --gold:           201,155,14;          /* rgb triplet */
            --gold-strong:    160,124,8;
            --danger:         244, 63, 94;
            --success:        16, 185, 129;
            --info:           99, 102, 241;
            --text-primary:   #f1f5f9;
            --text-secondary: rgba(148,163,184,0.85);
            --border-subtle:  rgba(255,255,255,0.08);
            --border-normal:  rgba(255,255,255,0.13);
            --radius-card:    1rem;
            --radius-input:   0.75rem;
        }
        [x-cloak] { display: none !important; }
        * { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; overflow: hidden; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Helvetica Neue', sans-serif;
            background: radial-gradient(ellipse 130% 90% at 15% -5%, #1e3560 0%, #0a192f 30%, #060e1f 60%, #0d0820 100%);
            background-attachment: fixed;
            color: #e2e8f0;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: '';
            position: fixed; inset: 0; pointer-events: none; z-index: 0;
            background:
                radial-gradient(ellipse 55% 45% at 5% 10%,  rgba(201,155,14,0.08) 0%, transparent 60%),
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
            height: 100vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
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
            border-color: rgba(var(--gold),0.28);
            box-shadow: 0 0 26px rgba(var(--gold),0.12), 0 8px 40px rgba(0,0,0,0.55), 0 1px 0 rgba(255,255,255,0.16) inset;
            transform: translateY(-1px);
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
            border-color: rgba(201,155,14,0.55);
            box-shadow: 0 0 0 3px rgba(201,155,14,0.10);
        }
        select.glass-input option { background: #0a192f; }

        /* ── Form controls (.input / .input-field were referenced ~79× but
              never defined — they rendered as white browser defaults on the
              dark theme). Alias them to the glass-input look. ────────────── */
        .input, .input-field,
        textarea.glass-input, select.glass-input {
            background: rgba(255,255,255,0.06);
            -webkit-backdrop-filter: blur(8px);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border-normal);
            color: var(--text-primary);
            width: 100%;
            border-radius: var(--radius-input);
            padding: 0.6rem 1rem;
            font-size: 0.8125rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input::placeholder, .input-field::placeholder { color: rgba(148,163,184,0.55); }
        .input:focus, .input-field:focus,
        textarea.glass-input:focus, select.glass-input:focus {
            outline: none;
            border-color: rgba(var(--gold),0.55);
            box-shadow: 0 0 0 3px rgba(var(--gold),0.10);
        }
        .input option, .input-field option { background: #0a192f; color: var(--text-primary); }
        textarea.glass-input, textarea.input, textarea.input-field { resize: vertical; min-height: 4.5rem; }

        /* Native <select> chevron so dropdowns match the dark theme */
        select.glass-input, select.input, select.input-field {
            -webkit-appearance: none; -moz-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.85rem center;
            padding-right: 2.25rem;
            cursor: pointer;
        }

        /* Keyboard focus visibility for nav + interactive buttons */
        .nav-item:focus-visible, button:focus-visible, a:focus-visible {
            outline: 2px solid rgba(var(--gold),0.85);
            outline-offset: 2px;
        }
        /* Tap feedback on any enabled button */
        button:active:not(:disabled) { transform: scale(0.97); }

        .glass-btn-amber {
            background: linear-gradient(135deg, rgba(201,155,14,0.95) 0%, rgba(160,124,8,0.95) 100%);
            border: 1px solid rgba(201,155,14,0.5);
            box-shadow: 0 4px 18px rgba(201,155,14,0.28), 0 1px 0 rgba(255,255,255,0.2) inset;
            color: #0a192f; font-weight: 900;
            transition: all 0.2s;
        }
        .glass-btn-amber:hover { box-shadow: 0 6px 26px rgba(201,155,14,0.38), 0 1px 0 rgba(255,255,255,0.25) inset; }
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
            background: rgba(201,155,14,0.15);
            border-color: rgba(201,155,14,0.35);
            box-shadow: 0 0 16px rgba(201,155,14,0.12);
        }
        .nav-item.active svg { color: rgb(201,155,14); }
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

        /* ── Responsive sidebar ───────────────────────────────────────── */
        .mobile-topbar  { display: none; }
        .sidebar-overlay { display: none; }
        @media (max-width: 767px) {
            .mobile-topbar {
                display: flex; align-items: center; gap: 0.75rem;
                position: sticky; top: 0; z-index: 30;
                padding: 0.625rem 1rem; height: 52px;
                background: rgba(8,18,36,0.92);
                -webkit-backdrop-filter: blur(20px); backdrop-filter: blur(20px);
                border-bottom: 1px solid rgba(255,255,255,0.09);
            }
            .glass-sidebar {
                position: fixed; top: 0; left: 0; bottom: 0;
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
                z-index: 60;
            }
            .glass-sidebar.is-open { transform: translateX(0); }
            .sidebar-overlay.is-open {
                display: block; position: fixed; inset: 0;
                background: rgba(0,0,0,0.55); z-index: 55;
            }
        }
    </style>
</head>
<body x-data="opesApp()" x-cloak class="relative">

<!-- Admin impersonation banner -->
<div x-show="impersonation" x-cloak class="relative z-[100] flex items-center justify-between gap-3 px-4 py-2 text-black"
     style="background:#C99B0E">
    <span class="text-xs font-black uppercase tracking-wider flex items-center gap-2">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/></svg>
        <span x-text="(lang==='FR'?'Mode Admin — Vous consultez : ':'Admin Mode — Viewing: ') + (impersonation?.name || '')"></span>
    </span>
    <button @click="leaveImpersonation()" class="text-xs font-black uppercase tracking-wider underline hover:no-underline">
        <span x-text="lang==='FR'?'Quitter l\'impersonnification →':'Exit impersonation →'"></span>
    </button>
</div>

<!-- Global toast notifications -->
<div x-data="toastManager()" @toast.window="addToast($event.detail)"
     class="fixed bottom-6 right-6 z-[120] flex flex-col gap-3 pointer-events-none" style="max-width:380px">
    <template x-for="t in toasts" :key="t.id">
        <div x-show="t.show" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="backdrop-blur border rounded-xl px-4 py-3 flex items-start gap-3 pointer-events-auto shadow-lg shadow-black/40"
             :style="({
                success:'background:rgba(16,185,129,0.18);border:1px solid rgba(16,185,129,0.35);color:rgb(110,231,183)',
                error:'background:rgba(244,63,94,0.18);border:1px solid rgba(244,63,94,0.35);color:rgb(252,165,165)',
                warning:'background:rgba(201,155,14,0.18);border:1px solid rgba(201,155,14,0.35);color:#E3B420',
                info:'background:rgba(99,102,241,0.18);border:1px solid rgba(99,102,241,0.35);color:rgb(165,180,252)'
             })[t.type] || 'background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.15);color:#fff'">
            <div class="flex-1 min-w-0">
                <p class="font-black text-sm" x-text="t.title"></p>
                <p x-show="t.message" class="text-xs opacity-80 mt-0.5" x-text="t.message"></p>
            </div>
            <button @click="removeToast(t.id)" class="opacity-60 hover:opacity-100 shrink-0 mt-0.5">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </template>
</div>

<!-- PWA install banner -->
<div x-data="pwaInstall()" x-init="init()" x-show="showBanner" x-cloak
     class="fixed bottom-6 left-4 right-4 lg:left-auto lg:right-6 lg:w-80 z-[110] rounded-xl p-4 shadow-2xl"
     style="background:#0d1d33;border:1px solid rgba(201,155,14,0.3)">
    <div class="flex items-start gap-3">
        <img src="/icon.svg" class="w-11 h-11 rounded-xl" alt="OPESBooks">
        <div class="flex-1 min-w-0">
            <p class="text-white font-black text-sm">Installer OPESBooks</p>
            <p class="text-slate-400 text-[11px] mt-0.5">Accès rapide + fonctionne hors ligne</p>
        </div>
        <button @click="dismiss()" class="text-slate-500 hover:text-slate-300"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
    </div>
    <div class="flex gap-2 mt-3">
        <button @click="dismiss()" class="flex-1 glass-btn-dark py-2 rounded-lg text-[11px] uppercase tracking-wider">Plus tard</button>
        <button @click="install()" class="flex-1 glass-btn-amber py-2 rounded-lg text-[11px] uppercase tracking-wider">Installer</button>
    </div>
</div>

<!-- ── Layout ──────────────────────────────────────────────────────── -->
<div class="flex relative z-10" :class="impersonation ? 'h-[calc(100vh-2.5rem)]' : 'h-screen'">

    <!-- Mobile drawer overlay -->
    <div class="sidebar-overlay" :class="{ 'is-open': sidebarOpen }" @click="sidebarOpen = false"></div>

    <!-- ── SIDEBAR ──────────────────────────────────────────────────── -->
    <aside class="glass-sidebar w-56 flex flex-col py-5 px-3 shrink-0 z-20" :class="{ 'is-open': sidebarOpen }">

        <!-- Brand -->
        <div class="px-2 mb-6">
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center text-[10px] font-black text-amber-400 flex-shrink-0"
                     style="background:rgba(201,155,14,0.14);border:1px solid rgba(201,155,14,0.3)">OB</div>
                <span class="text-white font-black text-sm tracking-widest uppercase flex-1">OPES<span class="text-amber-400">BOOKS</span></span>
                <!-- Notification bell -->
                <div class="relative" @click.outside="notifOpen=false">
                    <button @click="notifOpen=!notifOpen; if(notifOpen) loadNotifications()" class="relative p-1.5 text-slate-400 hover:text-white">
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <span x-show="notifUnread>0" x-cloak class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[8px] font-black rounded-full min-w-[14px] h-[14px] px-0.5 flex items-center justify-center" x-text="notifUnread>9?'9+':notifUnread"></span>
                    </button>
                    <div x-show="notifOpen" x-cloak class="absolute left-0 mt-2 w-72 rounded-xl overflow-hidden z-[90]" style="background:rgba(13,29,51,0.99);border:1px solid rgba(255,255,255,0.12);box-shadow:0 12px 40px rgba(0,0,0,0.6)">
                        <div class="flex items-center justify-between px-3 py-2.5 border-b" style="border-color:rgba(255,255,255,0.08)">
                            <span class="text-xs font-black text-white uppercase tracking-wider">Notifications</span>
                            <button @click="markAllNotifsRead()" x-show="notifUnread>0" class="text-[10px] text-amber-400 font-bold uppercase">Tout lire</button>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            <template x-for="n in notifications" :key="n.id">
                                <a :href="n.action_url || '#'" class="flex items-start gap-2.5 px-3 py-2.5 hover:bg-white/5 transition-colors border-b" style="border-color:rgba(255,255,255,0.05)" :style="!n.read_at ? 'background:rgba(201,155,14,0.05)' : ''">
                                    <span class="w-1.5 h-1.5 rounded-full mt-1.5 shrink-0" :style="!n.read_at ? 'background:#C99B0E' : 'background:transparent'"></span>
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-bold text-slate-200 truncate" x-text="n.title"></div>
                                        <div class="text-[10px] text-slate-500 truncate" x-text="n.body"></div>
                                    </div>
                                </a>
                            </template>
                            <div x-show="notifications.length===0" class="px-3 py-8 text-center text-[10px] text-slate-500 uppercase tracking-widest">Aucune notification</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- User chip -->
            <div class="px-2.5 py-1.5 rounded-xl text-[10px] font-bold"
                 style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.09)">
                <div class="text-slate-300 font-black truncate" x-text="user?.name ?? '—'"></div>
                <!-- Role badge -->
                <div class="mt-1 mb-0.5">
                    <span x-show="user?.role === 'OWNER'"
                          class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest"
                          style="background:rgba(201,155,14,0.18);color:rgb(252,211,77);border:1px solid rgba(201,155,14,0.35)">OWNER</span>
                    <span x-show="user?.role === 'ACCOUNTANT'"
                          class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest"
                          style="background:rgba(99,102,241,0.18);color:rgb(165,180,252);border:1px solid rgba(99,102,241,0.35)">ACCOUNTANT</span>
                    <span x-show="user?.role === 'CLERK'"
                          class="inline-block px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest"
                          style="background:rgba(100,116,139,0.18);color:rgb(148,163,184);border:1px solid rgba(100,116,139,0.35)">CLERK</span>
                </div>
                <div class="text-slate-500 mt-0.5 uppercase tracking-wider relative" @click.outside="companySwitcherOpen=false">
                    <!-- Single company: static; multiple: clickable switcher -->
                    <button type="button" @click="companies.length > 1 && (companySwitcherOpen = !companySwitcherOpen)"
                            class="flex items-center gap-1.5 w-full text-left"
                            :class="companies.length > 1 ? 'hover:text-slate-300 transition-colors' : 'cursor-default'">
                        <span class="truncate flex-1" x-text="company?.name ?? '...'"></span>
                        <svg x-show="companies.length > 1" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>

                    <!-- Switcher dropdown -->
                    <div x-show="companySwitcherOpen" x-cloak x-transition.opacity
                         class="absolute left-0 right-0 mt-2 z-50 rounded-xl overflow-hidden"
                         style="background:rgba(13,29,51,0.98);border:1px solid rgba(255,255,255,0.12);box-shadow:0 12px 40px rgba(0,0,0,0.6)">
                        <template x-for="c in companies" :key="c.id">
                            <button type="button" @click="switchCompany(c.id)"
                                    class="w-full flex items-center gap-2 px-3 py-2.5 text-left hover:bg-white/5 transition-colors"
                                    :class="c.is_active ? 'bg-white/5' : ''">
                                <svg x-show="c.is_active" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="rgb(201,155,14)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><polyline points="20 6 9 17 4 12"/></svg>
                                <span x-show="!c.is_active" class="w-[13px] shrink-0"></span>
                                <span class="flex-1 min-w-0">
                                    <span class="block truncate text-slate-200 font-black normal-case tracking-normal" x-text="c.name"></span>
                                    <span class="block text-[9px] text-slate-500 uppercase tracking-widest" x-text="c.role"></span>
                                </span>
                            </button>
                        </template>
                        <button type="button" @click="companySwitcherOpen=false; showAddCompany=true"
                                class="w-full flex items-center gap-2 px-3 py-2.5 text-left border-t hover:bg-white/5 transition-colors text-amber-400"
                                style="border-color:rgba(255,255,255,0.08)">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-wider normal-case" x-text="lang==='FR' ? 'Ajouter une entreprise' : 'Add company'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add-company modal -->
        <div x-show="showAddCompany" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4"
             style="background:rgba(5,13,26,0.7);backdrop-filter:blur(6px)" @click.self="showAddCompany=false">
            <div class="glass-card rounded-2xl p-6 w-full max-w-md space-y-3">
                <h3 class="text-sm font-black text-white uppercase tracking-widest" x-text="lang==='FR' ? 'Nouvelle Entreprise' : 'New Company'"></h3>
                <div x-show="addCompanyError" x-cloak class="px-3 py-2 rounded-lg text-xs font-bold" style="background:rgba(244,63,94,0.12);border:1px solid rgba(244,63,94,0.28);color:rgb(252,165,165)" x-text="addCompanyError"></div>
                <input x-model="addCompanyForm.name" class="glass-input" :placeholder="lang==='FR' ? 'Nom de l\'entreprise' : 'Company name'">
                <div class="grid grid-cols-2 gap-2">
                    <input x-model="addCompanyForm.niu" class="glass-input" placeholder="NIU">
                    <input x-model="addCompanyForm.rccm" class="glass-input" placeholder="RCCM">
                    <select x-model="addCompanyForm.tax_regime" class="glass-input">
                        <option value="REEL">REEL</option>
                        <option value="SIMPLIFIE">SIMPLIFIÉ</option>
                        <option value="LIBERATOIRE">LIBÉRATOIRE</option>
                    </select>
                    <input x-model="addCompanyForm.tax_center" class="glass-input" :placeholder="lang==='FR' ? 'Centre fiscal' : 'Tax center'">
                </div>
                <div class="flex gap-2 pt-1">
                    <button @click="createCompany()" :disabled="addCompanyLoading" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest flex-1 disabled:opacity-50"
                            x-text="addCompanyLoading ? '…' : (lang==='FR' ? 'Créer' : 'Create')"></button>
                    <button @click="showAddCompany=false" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR' ? 'Annuler' : 'Cancel'"></button>
                </div>
            </div>
        </div>

        <!-- Connectivity -->
        <div class="mx-1 mb-4 px-2.5 py-1.5 rounded-xl flex items-center gap-2 text-[10px] font-black uppercase tracking-widest"
             :style="connStatus==='ONLINE'
                ? 'background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)'
                : connStatus==='SYNCING'
                ? 'background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.25);color:rgb(165,180,252)'
                : 'background:rgba(201,155,14,0.1);border:1px solid rgba(201,155,14,0.25);color:rgb(252,211,77)'">
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
            <button @click="setPage('ai-assistant')" :class="page==='ai-assistant' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9L12 3zM5 16l.9 2.1L8 19l-2.1.9L5 22l-.9-2.1L2 19l2.1-.9L5 16z"/></svg>
                <span>Assistant IA</span>
            </button>
            <button @click="setPage('journal')" :class="page==='journal' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span x-text="lang==='FR' ? 'Journal' : 'Journal'"></span>
            </button>
            <button @click="setPage('ledger')" :class="page==='ledger' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
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

            <button @click="setPage('import')" :class="page==='import' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span x-text="lang==='FR' ? 'Import CSV' : 'CSV Import'"></span>
            </button>
            <button @click="setPage('subledgers')" :class="page==='subledgers' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span x-text="lang==='FR' ? 'Sous-Comptes' : 'Sub-Ledgers'"></span>
            </button>
            <button @click="setPage('sync')" :class="page==='sync' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span x-text="lang==='FR' ? 'Sync Hors Ligne' : 'Offline Sync'"></span>
            </button>
            <button @click="setPage('team')" :class="page==='team' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-text="lang==='FR' ? 'Équipe' : 'Team'"></span>
            </button>
            <button @click="setPage('settings')" :class="page==='settings' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-text="lang==='FR' ? 'Paramètres' : 'Settings'"></span>
            </button>
            <button @click="setPage('subscription')" :class="page==='subscription' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                <span x-text="lang==='FR' ? 'Abonnement' : 'Subscription'"></span>
            </button>
            <button @click="setPage('profile')" :class="page==='profile' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span x-text="lang==='FR' ? 'Mon Profil' : 'My Profile'"></span>
            </button>

            <div class="my-1" style="height:1px;background:rgba(255,255,255,0.07)"></div>

            <button @click="setPage('customer-invoices')" :class="page==='customer-invoices' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-text="lang==='FR' ? 'Fact. Clients' : 'Cust. Invoices'"></span>
            </button>
            <button @click="setPage('customers')" :class="page==='customers' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span x-text="lang==='FR' ? 'Clients' : 'Customers'"></span>
            </button>
            <button @click="setPage('crm')" :class="page==='crm' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><polyline points="22 7 13.5 15.5 8.5 10.5 2 17" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/><polyline points="16 7 22 7 22 13" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                <span>CRM</span>
            </button>
            <button @click="setPage('projects')" :class="page==='projects' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                <span x-text="lang==='FR' ? 'Projets' : 'Projects'"></span>
            </button>
            <button @click="setPage('suppliers')" :class="page==='suppliers' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                <span x-text="lang==='FR' ? 'Fournisseurs' : 'Suppliers'"></span>
            </button>
            <button @click="setPage('reports')" :class="page==='reports' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-text="lang==='FR' ? 'Rapports' : 'Reports'"></span>
            </button>
            <button @click="setPage('recurring')" :class="page==='recurring' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                <span x-text="lang==='FR' ? 'Récurrents' : 'Recurring'"></span>
            </button>
            <button @click="setPage('payroll')" :class="page==='payroll' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span x-text="lang==='FR' ? 'Paie' : 'Payroll'"></span>
            </button>

            <button @click="setPage('supplier-invoices')" :class="page==='supplier-invoices' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-text="lang==='FR' ? 'Fact. Fournisseurs' : 'Supplier Invoices'"></span>
            </button>
            <button @click="setPage('fixed-assets')" :class="page==='fixed-assets' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span x-text="lang==='FR' ? 'Immobilisations' : 'Fixed Assets'"></span>
            </button>
            <button @click="setPage('reconciliation')" :class="page==='reconciliation' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <span x-text="lang==='FR' ? 'Rapprochement' : 'Reconciliation'"></span>
            </button>
            <button @click="setPage('budget')" :class="page==='budget' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                <span x-text="lang==='FR' ? 'Budget' : 'Budget'"></span>
            </button>
            <button @click="setPage('dsf-export')" :class="page==='dsf-export' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-text="lang==='FR' ? 'DSF / TVA D10' : 'DSF / TVA D10'"></span>
            </button>
            <button @click="setPage('audit-log')" :class="page==='audit-log' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span x-text="lang==='FR' ? 'Journal d\'Audit' : 'Audit Log'"></span>
            </button>
            <button @click="setPage('fiscal-year')" :class="page==='fiscal-year' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span x-text="lang==='FR' ? 'Clôture Exercice' : 'Fiscal Year'"></span>
            </button>
            <button @click="setPage('accounts')" :class="page==='accounts' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                <span x-text="lang==='FR' ? 'Plan Comptable' : 'Chart of Accounts'"></span>
            </button>
            <button @click="setPage('stock')" :class="page==='stock' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 10V7"/></svg>
                <span x-text="lang==='FR' ? 'Stocks' : 'Inventory'"></span>
            </button>
            <button @click="setPage('quotations')" :class="page==='quotations' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span x-text="lang==='FR' ? 'Devis' : 'Quotations'"></span>
            </button>
            <button @click="setPage('purchase-orders')" :class="page==='purchase-orders' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span x-text="lang==='FR' ? 'Bons de Cmd.' : 'Purchase Orders'"></span>
            </button>
            <button @click="setPage('credit-notes')" :class="page==='credit-notes' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                <span x-text="lang==='FR' ? 'Avoirs' : 'Credit Notes'"></span>
            </button>
            <button @click="setPage('patente')" :class="page==='patente' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                <span x-text="lang==='FR' ? 'Patente' : 'Patente Tax'"></span>
            </button>
            <button @click="setPage('delivery-notes')" :class="page==='delivery-notes' ? 'nav-item active' : 'nav-item'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                <span x-text="lang==='FR' ? 'Bons Livraison' : 'Delivery Notes'"></span>
            </button>
            <button @click="setPage('cashflow')" :class="page==='cashflow' ? 'nav-item active' : 'nav-item'" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                <span x-text="lang==='FR' ? 'Trésorerie Prev.' : 'Cashflow Forecast'"></span>
            </button>

            <div class="my-2" style="height:1px;background:rgba(255,255,255,0.07)"></div>

            <a href="/tax-dashboard" class="nav-item" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span x-text="lang==='FR' ? 'Bilan Fiscal' : 'Tax Monitor'"></span>
            </a>
            <a href="/dgi-monitor" class="nav-item" x-show="user?.role === 'OWNER' || user?.role === 'ACCOUNTANT'">
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

        <!-- Mobile top bar (hamburger) -->
        <div class="mobile-topbar">
            <button @click="sidebarOpen = !sidebarOpen" aria-label="Menu"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-300 hover:text-white"
                    style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1)">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <span class="text-white font-black text-sm tracking-widest uppercase">OPES<span class="text-amber-400">BOOKS</span></span>
        </div>

        <!-- Platform announcement banners -->
        <div x-show="announcements.length" x-cloak class="px-6 pt-5 space-y-2">
            <template x-for="a in announcements" :key="a.id">
                <div class="flex items-start gap-3 px-4 py-3 rounded-xl text-xs"
                     :style="({
                        INFO:        'background:rgba(99,102,241,0.10);border:1px solid rgba(99,102,241,0.30)',
                        WARNING:     'background:rgba(201,155,14,0.10);border:1px solid rgba(201,155,14,0.30)',
                        MAINTENANCE: 'background:rgba(244,63,94,0.10);border:1px solid rgba(244,63,94,0.30)',
                        FEATURE:     'background:rgba(16,185,129,0.10);border:1px solid rgba(16,185,129,0.30)'
                     })[a.type] || 'background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.12)'">
                    <div class="flex-1">
                        <div class="font-black uppercase tracking-wider text-white" x-text="a.title"></div>
                        <div class="text-slate-300 mt-0.5 leading-relaxed" x-text="a.body"></div>
                    </div>
                    <button @click="dismissAnnouncement(a.id)" class="text-slate-400 hover:text-white shrink-0" aria-label="Dismiss">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </template>
        </div>

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

            <!-- Onboarding checklist -->
            <div x-show="onboarding" x-cloak class="glass-card rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-black text-white uppercase tracking-wide" x-text="lang==='FR'?'Premiers pas':'Getting started'"></p>
                        <p class="text-[10px] text-slate-400" x-text="(onboarding?.checklist_done||0)+'/'+(onboarding?.checklist_total||5)+' '+(lang==='FR'?'complétés':'completed')"></p>
                    </div>
                    <button @click="dismissChecklist()" class="text-[10px] text-slate-500 hover:text-slate-300 uppercase tracking-wider" x-text="lang==='FR'?'Fermer':'Dismiss'"></button>
                </div>
                <div class="h-2 rounded-full overflow-hidden mb-4" style="background:rgba(255,255,255,0.08)">
                    <div class="h-full rounded-full transition-all" :style="'width:'+(((onboarding?.checklist_done||0)/(onboarding?.checklist_total||5))*100)+'%;background:#C99B0E'"></div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <template x-for="item in [
                        {k:'profile', fr:'Profil entreprise', en:'Company profile', page:'settings'},
                        {k:'client',  fr:'Premier client',    en:'First client',    page:'customers'},
                        {k:'invoice', fr:'Première facture',   en:'First invoice',   page:'customer-invoices'},
                        {k:'team',    fr:'Équipe invitée',     en:'Team invited',     page:'team'},
                        {k:'report',  fr:'Rapport consulté',   en:'Report viewed',    page:'reports'}
                    ]" :key="item.k">
                        <button @click="setPage(item.page)" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-left transition-all"
                                :style="onboarding?.checklist?.[item.k] ? 'background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2)' : 'background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.08)'">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0"
                                  :style="onboarding?.checklist?.[item.k] ? 'background:rgb(16,185,129);color:#0a192f' : 'background:rgba(255,255,255,0.08);color:rgba(148,163,184,0.6)'">
                                <svg x-show="onboarding?.checklist?.[item.k]" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5"><polyline points="20 6 9 17 4 12"/></svg>
                            </span>
                            <span class="text-xs font-bold" :class="onboarding?.checklist?.[item.k] ? 'text-slate-300 line-through opacity-60' : 'text-white'" x-text="lang==='FR'?item.fr:item.en"></span>
                        </button>
                    </template>
                </div>
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
                 style="background:linear-gradient(135deg,rgba(201,155,14,0.12),rgba(10,25,47,0.7));border-color:rgba(201,155,14,0.28);box-shadow:0 8px 32px rgba(201,155,14,0.14),0 4px 24px rgba(0,0,0,0.5)">
                <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full blur-3xl pointer-events-none opacity-20"
                     style="background:radial-gradient(circle,rgba(201,155,14,1),transparent)"></div>
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
                    <template x-for="action in quickActions" :key="action.labelEn">
                        <button @click="action.href ? (window.location.href = action.href) : setPage(action.page)"
                                class="flex flex-col items-center gap-2 p-4 rounded-xl transition-all text-center group active:scale-95"
                                style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.09)"
                                @mouseenter="$el.style.background='rgba(255,255,255,0.09)';$el.style.borderColor='rgba(201,155,14,0.3)'"
                                @mouseleave="$el.style.background='rgba(255,255,255,0.04)';$el.style.borderColor='rgba(255,255,255,0.09)'">
                            <span class="text-amber-400 group-hover:scale-110 transition-transform"
                                  x-html="`<svg width='26' height='26' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='1.6' stroke-linecap='round' stroke-linejoin='round'>${action.icon}</svg>`"></span>
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
                          style="background:rgba(201,155,14,0.15);color:rgb(252,211,77);border:1px solid rgba(201,155,14,0.3)"
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
                                <th class="py-3 px-5 whitespace-nowrap text-center">DGI</th>
                                <th class="py-3 px-5 whitespace-nowrap text-center" x-text="lang==='FR' ? 'P.J.' : 'Attach.'"></th>
                            </tr>
                        </thead>
                        <tbody class="text-xs font-medium">
                            <template x-if="journalEntries.length === 0">
                                <tr>
                                    <td colspan="8" class="py-14 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70"
                                                 style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)">
                                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/></svg>
                                            </div>
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
                                                : 'background:rgba(201,155,14,0.15);border:1px solid rgba(201,155,14,0.3);color:rgb(252,211,77)'"
                                              x-text="txn.transaction_status"></span>
                                    </td>
                                    <td class="py-3 px-5 text-center whitespace-nowrap">
                                        <button @click="downloadInvoice(txn)"
                                                class="text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider transition-all active:scale-95"
                                                style="background:rgba(201,155,14,0.1);border:1px solid rgba(201,155,14,0.25);color:rgb(252,211,77)"
                                                x-text="lang==='FR' ? '↓ PDF' : '↓ PDF'"></button>
                                    </td>
                                    <td class="py-3 px-5 text-center whitespace-nowrap">
                                        <button @click="forceDgiSync(txn)"
                                                class="text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider transition-all active:scale-95"
                                                :class="txn.dgi_sync_status==='SYNCED' ? 'opacity-40' : ''"
                                                style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.25);color:rgb(165,180,252)"
                                                :title="txn.dgi_sync_status??'NOT_SYNCED'"
                                                x-text="txn.dgi_sync_status==='SYNCED' ? '✔ DGI' : '⟳ DGI'"></button>
                                    </td>
                                    <td class="py-3 px-5 text-center whitespace-nowrap">
                                        <button @click="openAttachments(txn)"
                                                class="text-[9px] font-black px-2.5 py-1 rounded-lg uppercase tracking-wider transition-all active:scale-95"
                                                style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)"
                                                x-text="'📎 '+(txn._attachCount??'…')"></button>
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

        <!-- Attachments modal (shown over journal page) -->
        <div x-show="attachModal.open" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px)">
            <div class="glass-card rounded-2xl p-6 w-full max-w-lg space-y-4" @click.stop>
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-sm" x-text="(lang==='FR'?'Pièces jointes — ':'Attachments — ')+attachModal.ref"></h3>
                    <button @click="attachModal.open=false" class="opacity-50 hover:opacity-100 text-lg leading-none">✕</button>
                </div>
                <div class="space-y-2 max-h-56 overflow-y-auto">
                    <template x-if="attachModal.files.length===0">
                        <p class="text-xs opacity-40 text-center py-4" x-text="lang==='FR'?'Aucune pièce jointe.':'No attachments.'"></p>
                    </template>
                    <template x-for="f in attachModal.files" :key="f.id">
                        <div class="flex items-center justify-between glass-card px-3 py-2 rounded-xl text-xs">
                            <a :href="f.file_url" target="_blank" class="text-amber-400 hover:underline truncate max-w-xs" x-text="f.original_name??f.file_path"></a>
                            <button @click="deleteAttachment(f)" class="ml-3 opacity-50 hover:opacity-100 text-red-400 text-xs">✕</button>
                        </div>
                    </template>
                </div>
                <div class="border-t pt-4" style="border-color:rgba(255,255,255,0.07)">
                    <label class="block text-xs opacity-60 mb-2" x-text="lang==='FR'?'Ajouter une pièce jointe':'Add attachment'"></label>
                    <input type="file" @change="attachFile=$event.target.files[0]"
                           accept=".pdf,.jpg,.jpeg,.png,.webp,.xlsx,.csv,.doc,.docx"
                           class="glass-input w-full px-3 py-2 rounded-xl text-xs mb-2">
                    <div x-show="attachError" class="text-xs text-red-400 mb-2" x-text="attachError"></div>
                    <button @click="uploadAttachment()"
                            class="glass-btn-dark px-5 py-2 rounded-xl text-xs uppercase tracking-widest"
                            x-text="attachUploading ? '…' : (lang==='FR'?'Téléverser':'Upload')"></button>
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
                                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70"
                                                 style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)">
                                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                                            </div>
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
                                :style="mode==='ht' ? 'background:rgba(201,155,14,0.2);border:1px solid rgba(201,155,14,0.35);color:rgb(252,211,77)' : 'color:rgba(148,163,184,0.7);border:1px solid transparent'"
                                x-text="lang==='FR' ? 'À partir du HT' : 'From HT'"></button>
                        <button @click="mode='ttc'" class="flex-1 py-2 rounded-lg text-[11px] font-black uppercase tracking-wider transition-all"
                                :style="mode==='ttc' ? 'background:rgba(201,155,14,0.2);border:1px solid rgba(201,155,14,0.35);color:rgb(252,211,77)' : 'color:rgba(148,163,184,0.7);border:1px solid transparent'"
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
                                 style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400/70"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                            </div>
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
                     style="box-shadow:0 4px 24px rgba(0,0,0,0.45),0 0 24px rgba(201,155,14,0.12)">
                    <div class="absolute -top-2 -right-2 w-12 h-12 rounded-full blur-2xl opacity-20"
                         style="background:rgb(201,155,14)"></div>
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
                    : 'border-color:rgba(201,155,14,0.3);box-shadow:0 4px 20px rgba(201,155,14,0.1)'">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 text-lg"
                     :style="connStatus==='ONLINE'
                        ? 'background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3)'
                        : 'background:rgba(201,155,14,0.15);border:1px solid rgba(201,155,14,0.3)'"
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
                                  style="background:rgba(201,155,14,0.15);border:1px solid rgba(201,155,14,0.3);color:rgb(252,211,77)"
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
                          style="background:rgba(201,155,14,0.15);color:rgb(252,211,77);border:1px solid rgba(201,155,14,0.3)"
                          x-text="teamMembers.length + (lang==='FR' ? ' membres' : ' members')"></span>
                </div>
                <template x-if="teamMembers.length === 0">
                    <div class="py-14 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                                 style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400/70"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
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
                                     style="background:rgba(201,155,14,0.12);border:1px solid rgba(201,155,14,0.2);color:rgb(252,211,77)"
                                     x-text="(member.name ?? '?')[0].toUpperCase()"></div>
                                <div>
                                    <div class="text-sm font-black text-white" x-text="member.name"></div>
                                    <div class="text-[10px] text-slate-500 font-medium" x-text="member.email"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-[9px] font-black px-2.5 py-1 rounded-full uppercase tracking-widest"
                                      :style="member.role==='OWNER'
                                        ? 'background:rgba(201,155,14,0.15);border:1px solid rgba(201,155,14,0.3);color:rgb(252,211,77)'
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

            <!-- Pays & Fiscalité (CEMAC country config — read only) -->
            <div class="glass-card rounded-2xl p-6 space-y-4" x-show="company?.country_config">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest"
                   x-text="lang==='FR' ? 'Pays & Fiscalité' : 'Country & Taxation'"></p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1" x-text="lang==='FR'?'Pays':'Country'"></div>
                        <div class="text-white font-bold"><span x-text="company?.country_config?.flag"></span> <span x-text="company?.country_config?.country_name_fr"></span></div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1" x-text="lang==='FR'?'Monnaie':'Currency'"></div>
                        <div class="text-white font-bold" x-text="company?.country_config?.currency_code + ' (Franc CFA)'"></div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1">TVA standard</div>
                        <div class="text-amber-400 font-black" x-text="company?.country_config?.vat_standard_rate + ' %'"></div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1" x-text="lang==='FR'?'TVA réduite':'Reduced VAT'"></div>
                        <div class="text-white font-bold" x-text="(company?.country_config?.vat_reduced_rate ?? '—') + (company?.country_config?.vat_reduced_rate ? ' %' : '')"></div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1" x-text="lang==='FR'?'Identifiant fiscal':'Tax ID'"></div>
                        <div class="text-white font-bold" x-text="company?.country_config?.company_id_label"></div>
                    </div>
                    <div class="col-span-2">
                        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1" x-text="lang==='FR'?'Autorité fiscale':'Tax authority'"></div>
                        <div class="text-white font-bold" x-text="company?.country_config?.regulatory_body_name"></div>
                    </div>
                    <div>
                        <div class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-1" x-text="lang==='FR'?'Déclaration':'Filing'"></div>
                        <div class="text-white font-bold" x-text="company?.country_config?.dsf_equivalent_name"></div>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500" x-text="lang==='FR' ? 'Le pays est défini à l\'inscription. Contactez le support pour le modifier.' : 'Country is set at sign-up. Contact support to change it.'"></p>
            </div>

            <!-- IA & Automatisation -->
            <div class="glass-card rounded-2xl p-6 space-y-4" x-show="user?.role==='OWNER'"
                 x-data="{ cfg:{ has_gemini_key:false, ollama_enabled:false, ollama_model:'gemma3:1b', auto_categorize:false, auto_dsf_check:false, anomaly_scan:false }, key:'', saving:false, saved:false,
                    async load(){ try{ this.cfg = await (await fetch('/api/v1/ai/config',{headers:{'Authorization':'Bearer '+localStorage.getItem('opes_token'),'Accept':'application/json'}})).json(); }catch(e){} },
                    async save(){ this.saving=true; this.saved=false; try{ const body={ gemini_api_key:this.key, ollama_enabled:this.cfg.ollama_enabled, ollama_model:this.cfg.ollama_model, auto_categorize:this.cfg.auto_categorize, auto_dsf_check:this.cfg.auto_dsf_check, anomaly_scan:this.cfg.anomaly_scan }; await fetch('/api/v1/ai/config',{method:'PUT',headers:{'Authorization':'Bearer '+localStorage.getItem('opes_token'),'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(body)}); this.key=''; this.saved=true; await this.load(); }catch(e){}finally{this.saving=false;} } }"
                 x-init="load()">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest">IA & Automatisation</p>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Clé API Gemini</label>
                    <input type="password" x-model="key" class="input-field" :placeholder="cfg.has_gemini_key ? '•••••••••• (configurée — laisser vide pour garder)' : 'AIza…'">
                </div>
                <label class="flex items-center justify-between text-xs cursor-pointer">
                    <span class="text-slate-300" x-text="lang==='FR'?'Catégorisation automatique':'Auto-categorize'"></span>
                    <input type="checkbox" x-model="cfg.auto_categorize" class="accent-amber-500 w-4 h-4">
                </label>
                <label class="flex items-center justify-between text-xs cursor-pointer">
                    <span class="text-slate-300" x-text="lang==='FR'?'Vérification DSF automatique':'Auto DSF check'"></span>
                    <input type="checkbox" x-model="cfg.auto_dsf_check" class="accent-amber-500 w-4 h-4">
                </label>
                <label class="flex items-center justify-between text-xs cursor-pointer">
                    <span class="text-slate-300" x-text="lang==='FR'?'Détection d\'anomalies':'Anomaly detection'"></span>
                    <input type="checkbox" x-model="cfg.anomaly_scan" class="accent-amber-500 w-4 h-4">
                </label>
                <label class="flex items-center justify-between text-xs cursor-pointer">
                    <span class="text-slate-300" x-text="lang==='FR'?'Modèle local Ollama (hors ligne)':'Local Ollama model (offline)'"></span>
                    <input type="checkbox" x-model="cfg.ollama_enabled" class="accent-amber-500 w-4 h-4">
                </label>
                <p class="text-[10px] text-slate-500" x-text="lang==='FR' ? 'Ollama nécessite un serveur dédié — souvent indisponible en hébergement mutualisé. L\'IA en ligne (Gemini) est recommandée.' : 'Ollama needs a dedicated server — usually unavailable on shared hosting. Online AI (Gemini) is recommended.'"></p>
                <div class="flex items-center gap-3">
                    <button @click="save()" :disabled="saving" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest disabled:opacity-50" x-text="saving?'…':(lang==='FR'?'Enregistrer':'Save')"></button>
                    <span x-show="saved" x-cloak class="text-emerald-400 text-xs font-bold" x-text="lang==='FR'?'Enregistré ✔':'Saved ✔'"></span>
                </div>
            </div>

            <!-- MECeF / DGI -->
            <div class="glass-card rounded-2xl p-6 space-y-4" x-show="user?.role==='OWNER'"
                 x-data="{ cfg:{ nim:'', api_endpoint:'', has_token:false, is_active:false, sandbox_mode:true }, token:'', saving:false, saved:false,
                    async load(){ try{ this.cfg = await (await fetch('/api/v1/mecef/config',{headers:{'Authorization':'Bearer '+localStorage.getItem('opes_token'),'Accept':'application/json'}})).json(); }catch(e){} },
                    async save(){ this.saving=true; this.saved=false; try{ const body={ nim:this.cfg.nim, api_endpoint:this.cfg.api_endpoint, api_token:this.token, is_active:this.cfg.is_active, sandbox_mode:this.cfg.sandbox_mode }; await fetch('/api/v1/mecef/config',{method:'PUT',headers:{'Authorization':'Bearer '+localStorage.getItem('opes_token'),'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(body)}); this.token=''; this.saved=true; await this.load(); }catch(e){}finally{this.saving=false;} } }"
                 x-init="load()">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest">MECeF · DGI Cameroun</p>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest"
                          :style="cfg.is_active ? (cfg.sandbox_mode ? 'background:rgba(201,155,14,0.18);color:#E3B420;border:1px solid rgba(201,155,14,0.35)' : 'background:rgba(16,185,129,0.18);color:rgb(110,231,183);border:1px solid rgba(16,185,129,0.35)') : 'background:rgba(100,116,139,0.18);color:rgb(148,163,184);border:1px solid rgba(100,116,139,0.35)'"
                          x-text="cfg.is_active ? (cfg.sandbox_mode ? 'SANDBOX' : 'LIVE') : (lang==='FR'?'INACTIF':'INACTIVE')"></span>
                </div>
                <p class="text-[10px] text-slate-500" x-text="lang==='FR' ? 'Certifiez vos factures auprès de la DGI. Contactez la DGI pour obtenir votre NIM et vos identifiants API.' : 'Certify invoices with the DGI. Contact the DGI to obtain your NIM and API credentials.'"></p>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">NIM</label>
                        <input x-model="cfg.nim" class="input-field" placeholder="CM-MECeF-…"></div>
                    <div><label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Endpoint DGI</label>
                        <input x-model="cfg.api_endpoint" class="input-field" placeholder="https://…"></div>
                    <div class="col-span-2"><label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Token API</label>
                        <input type="password" x-model="token" class="input-field" :placeholder="cfg.has_token ? '•••••••• (configuré — laisser vide pour garder)' : '—'"></div>
                </div>
                <label class="flex items-center justify-between text-xs cursor-pointer">
                    <span class="text-slate-300" x-text="lang==='FR'?'Activer MECeF':'Enable MECeF'"></span>
                    <input type="checkbox" x-model="cfg.is_active" class="accent-amber-500 w-4 h-4">
                </label>
                <label class="flex items-center justify-between text-xs cursor-pointer">
                    <span class="text-slate-300" x-text="lang==='FR'?'Mode sandbox (test)':'Sandbox mode (test)'"></span>
                    <input type="checkbox" x-model="cfg.sandbox_mode" class="accent-amber-500 w-4 h-4">
                </label>
                <div class="flex items-center gap-3">
                    <button @click="save()" :disabled="saving" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest disabled:opacity-50" x-text="saving?'…':(lang==='FR'?'Enregistrer':'Save')"></button>
                    <span x-show="saved" x-cloak class="text-emerald-400 text-xs font-bold" x-text="lang==='FR'?'Enregistré ✔':'Saved ✔'"></span>
                </div>
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
                               style="file:background:rgba(201,155,14,0.15);file:color:rgb(252,211,77);file:border:1px solid rgba(201,155,14,0.3)">
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

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- SUBSCRIPTION PAGE                                          -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='subscription'" x-cloak class="p-6 space-y-6 float-in" x-data="subscriptionPanel()">

            <div class="flex items-end justify-between">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                        x-text="lang==='FR' ? 'Abonnement' : 'Subscription'"></h2>
                    <p class="text-xs text-slate-400 mt-1"
                       x-text="lang==='FR' ? 'Gérez votre plan et votre facturation.' : 'Manage your plan and billing.'"></p>
                </div>
                <!-- Current status chip -->
                <div x-show="subStatus" class="text-[10px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl"
                     :style="subStatus?.status==='ACTIVE'
                        ? 'background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);color:rgb(110,231,183)'
                        : 'background:rgba(244,63,94,0.12);border:1px solid rgba(244,63,94,0.3);color:rgb(252,165,165)'"
                     x-text="subStatus?.status ?? ''"></div>
            </div>

            <!-- Current plan info -->
            <div x-show="subStatus" class="glass-card rounded-2xl p-5 space-y-2">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                   x-text="lang==='FR' ? 'Plan Actuel' : 'Current Plan'"></p>
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="text-lg font-black text-amber-400" x-text="subStatus?.plan ?? '—'"></span>
                    <div class="text-xs text-slate-400">
                        <span x-show="subStatus?.expires_at">
                            <span x-text="lang==='FR' ? 'Expire le ' : 'Expires '"></span>
                            <span class="text-white font-bold" x-text="subStatus?.expires_at ? new Date(subStatus.expires_at).toLocaleDateString('fr-CM') : ''"></span>
                        </span>
                    </div>
                </div>
                <div x-show="subStatus?.days_remaining !== undefined" class="text-xs text-slate-400">
                    <span x-text="lang==='FR' ? 'Jours restants : ' : 'Days remaining: '"></span>
                    <span class="font-bold text-white" x-text="subStatus?.days_remaining ?? '—'"></span>
                </div>
                <div class="pt-2">
                    <button @click="downloadReceipt()" :disabled="receiptLoading"
                            class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest font-black inline-flex items-center gap-2 disabled:opacity-50">
                        <svg x-show="!receiptLoading" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <svg x-show="receiptLoading" x-cloak class="spin-pulse" width="14" height="14" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        <span x-text="receiptLoading ? (lang==='FR' ? 'Génération…' : 'Generating…') : (lang==='FR' ? 'Télécharger Reçu' : 'Download Receipt')"></span>
                    </button>
                </div>
            </div>

            <!-- Plan cards -->
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3"
                   x-text="lang==='FR' ? 'Choisir un Plan' : 'Choose a Plan'"></p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <template x-for="plan in subPlans" :key="plan.id">
                        <div @click="selectedPlan = plan.id"
                             class="glass-card rounded-2xl p-5 cursor-pointer transition-all"
                             :style="selectedPlan===plan.id
                                ? 'border-color:rgba(201,155,14,0.5);box-shadow:0 0 24px rgba(201,155,14,0.18)'
                                : ''">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[10px] font-black text-amber-400 uppercase tracking-widest" x-text="plan.name"></span>
                                <div x-show="selectedPlan===plan.id" class="w-4 h-4 rounded-full flex items-center justify-center"
                                     style="background:rgb(201,155,14)">
                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                </div>
                            </div>
                            <div class="text-2xl font-black text-white mb-1">
                                <span x-text="Number(plan.price).toLocaleString('fr-CM')"></span>
                                <span class="text-sm text-slate-400"> XAF/mois</span>
                            </div>
                            <ul class="space-y-1 mt-3">
                                <template x-for="feat in plan.features" :key="feat">
                                    <li class="text-xs text-slate-300 flex items-center gap-1.5">
                                        <span class="text-emerald-400 text-[10px]">✓</span>
                                        <span x-text="feat"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Billing phone -->
            <div class="glass-card rounded-2xl p-5 space-y-4">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest"
                   x-text="lang==='FR' ? 'Paiement Mobile Money' : 'Mobile Money Payment'"></p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Numéro de Téléphone' : 'Phone Number'"></label>
                        <input type="tel" x-model="subPhone" class="glass-input"
                               :placeholder="lang==='FR' ? '6XXXXXXXX (MTN / Orange)' : '6XXXXXXXX (MTN / Orange)'">
                    </div>
                    <div class="flex items-end">
                        <button @click="initiateSubscription()" :disabled="subLoading || !selectedPlan || !subPhone"
                                class="w-full glass-btn-amber py-2.5 rounded-xl text-xs uppercase tracking-widest disabled:opacity-40"
                                x-text="subLoading ? '…' : (lang==='FR' ? 'Payer Maintenant' : 'Pay Now')"></button>
                    </div>
                </div>
                <div x-show="subError" class="px-4 py-3 rounded-xl text-sm font-bold"
                     style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.25);color:rgb(252,165,165)"
                     x-text="subError"></div>
                <div x-show="subSuccess" class="px-4 py-3 rounded-xl text-sm font-bold"
                     style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)"
                     x-text="subSuccess"></div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- PROFILE PAGE                                               -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='profile'" x-cloak class="p-6 space-y-6 float-in" x-data="profilePanel()">

            <div>
                <h2 class="text-2xl font-black text-white uppercase tracking-wide"
                    x-text="lang==='FR' ? 'Mon Profil' : 'My Profile'"></h2>
                <p class="text-xs text-slate-400 mt-1"
                   x-text="lang==='FR' ? 'Gérez vos informations personnelles et votre mot de passe.' : 'Manage your personal info and password.'"></p>
            </div>

            <!-- Sécurité — 2FA -->
            <div class="glass-card rounded-2xl p-6 space-y-4"
                 x-data="{ enabled:false, busy:false, step:'idle', secret:'', qr:'', code:'', err:'', recovery:[], pwd:'',
                    _h(){ return {'Authorization':'Bearer '+localStorage.getItem('opes_token'),'Content-Type':'application/json','Accept':'application/json'}; },
                    async load(){ try{ const r=await(await fetch('/api/v1/auth/two-factor',{headers:this._h()})).json(); this.enabled=!!r.enabled; }catch(e){} },
                    async start(){ this.err=''; this.busy=true; try{ const r=await(await fetch('/api/v1/auth/two-factor/setup',{method:'POST',headers:this._h()})).json(); this.secret=r.secret; this.qr=r.qr; this.step='scan'; }catch(e){this.err='Erreur'}finally{this.busy=false} },
                    async confirm(){ this.err=''; this.busy=true; try{ const res=await fetch('/api/v1/auth/two-factor/confirm',{method:'POST',headers:this._h(),body:JSON.stringify({code:this.code})}); const d=await res.json(); if(!res.ok){this.err=d.message||'Code invalide';return;} this.recovery=d.recovery_codes; this.enabled=true; this.step='codes'; }catch(e){this.err='Erreur'}finally{this.busy=false} },
                    async disable(){ this.err=''; this.busy=true; try{ const res=await fetch('/api/v1/auth/two-factor/disable',{method:'POST',headers:this._h(),body:JSON.stringify({password:this.pwd})}); const d=await res.json(); if(!res.ok){this.err=d.message||'Erreur';return;} this.enabled=false; this.step='idle'; this.pwd=''; }catch(e){this.err='Erreur'}finally{this.busy=false} } }"
                 x-init="load()">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest" x-text="lang==='FR'?'Sécurité · Double Authentification':'Security · Two-Factor Auth'"></p>
                    <span x-show="enabled" class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase" style="background:rgba(16,185,129,0.18);color:rgb(110,231,183);border:1px solid rgba(16,185,129,0.35)">✓ Activée</span>
                </div>
                <div x-show="err" x-cloak class="px-3 py-2 rounded-lg text-xs font-bold" style="background:rgba(244,63,94,0.12);color:rgb(252,165,165)" x-text="err"></div>

                <!-- Not enabled, idle -->
                <template x-if="!enabled && step==='idle'">
                    <div>
                        <p class="text-xs text-slate-400 mb-3" x-text="lang==='FR'?'Protégez votre compte avec une application d\'authentification (Google Authenticator, Authy).':'Protect your account with an authenticator app.'"></p>
                        <button @click="start()" :disabled="busy" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest disabled:opacity-50" x-text="busy?'…':(lang==='FR'?'Activer la 2FA':'Enable 2FA')"></button>
                    </div>
                </template>

                <!-- Setup: scan QR + confirm -->
                <template x-if="step==='scan'">
                    <div class="space-y-3">
                        <p class="text-xs text-slate-400" x-text="lang==='FR'?'Scannez ce QR code, puis saisissez le code à 6 chiffres.':'Scan this QR code, then enter the 6-digit code.'"></p>
                        <div class="bg-white rounded-xl p-3 inline-block" x-html="qr"></div>
                        <p class="text-[10px] text-slate-500 font-mono">Secret: <span x-text="secret"></span></p>
                        <div class="flex gap-2">
                            <input x-model="code" inputmode="numeric" maxlength="6" class="input-field text-center tracking-[0.3em] font-black w-40" placeholder="000000">
                            <button @click="confirm()" :disabled="busy" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest disabled:opacity-50" x-text="busy?'…':(lang==='FR'?'Vérifier':'Verify')"></button>
                        </div>
                    </div>
                </template>

                <!-- Recovery codes (once) -->
                <template x-if="step==='codes'">
                    <div class="space-y-2">
                        <p class="text-xs text-amber-300 font-bold" x-text="lang==='FR'?'Sauvegardez ces codes de récupération — ils ne seront plus affichés.':'Save these recovery codes — they won\'t be shown again.'"></p>
                        <div class="grid grid-cols-2 gap-2 font-mono text-sm">
                            <template x-for="c in recovery" :key="c"><div class="px-3 py-1.5 rounded-lg text-amber-400" style="background:rgba(255,255,255,0.04)" x-text="c"></div></template>
                        </div>
                        <button @click="step='idle'" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest mt-1" x-text="lang==='FR'?'J\'ai sauvegardé':'Done'"></button>
                    </div>
                </template>

                <!-- Enabled: disable -->
                <template x-if="enabled && step==='idle'">
                    <div class="flex flex-wrap items-end gap-2">
                        <div>
                            <label class="block text-[10px] text-slate-500 uppercase tracking-widest mb-1" x-text="lang==='FR'?'Mot de passe pour désactiver':'Password to disable'"></label>
                            <input type="password" x-model="pwd" class="input-field w-56" placeholder="••••••••">
                        </div>
                        <button @click="disable()" :disabled="busy||!pwd" class="px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest disabled:opacity-50" style="background:rgba(244,63,94,0.15);color:rgb(252,165,165);border:1px solid rgba(244,63,94,0.3)" x-text="busy?'…':(lang==='FR'?'Désactiver':'Disable')"></button>
                    </div>
                </template>
            </div>

            <!-- Profile info card -->
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest"
                   x-text="lang==='FR' ? 'Informations Personnelles' : 'Personal Information'"></p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Nom Complet' : 'Full Name'"></label>
                        <input type="text" x-model="profileForm.name" class="glass-input"
                               :placeholder="lang==='FR' ? 'Votre nom complet' : 'Your full name'">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Adresse Email' : 'Email Address'"></label>
                        <input type="email" x-model="profileForm.email" class="glass-input" placeholder="email@exemple.cm">
                    </div>
                </div>

                <!-- Role badge (read-only) -->
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest"
                          x-text="lang==='FR' ? 'Rôle :' : 'Role:'"></span>
                    <span class="text-[10px] font-black px-2.5 py-1 rounded-lg"
                          style="background:rgba(201,155,14,0.15);border:1px solid rgba(201,155,14,0.3);color:rgb(252,211,77)"
                          x-text="profileForm.role ?? '—'"></span>
                </div>

                <div x-show="profileError" class="px-4 py-3 rounded-xl text-sm font-bold"
                     style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.25);color:rgb(252,165,165)"
                     x-text="profileError"></div>
                <div x-show="profileSuccess" class="px-4 py-3 rounded-xl text-sm font-bold"
                     style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)"
                     x-text="profileSuccess"></div>

                <button @click="saveProfile()" :disabled="profileSaving"
                        class="glass-btn-amber px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest disabled:opacity-40"
                        x-text="profileSaving ? '…' : (lang==='FR' ? 'Enregistrer le Profil' : 'Save Profile')"></button>
            </div>

            <!-- Password change card -->
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest"
                   x-text="lang==='FR' ? 'Changer le Mot de Passe' : 'Change Password'"></p>

                <div class="space-y-3">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                               x-text="lang==='FR' ? 'Mot de Passe Actuel' : 'Current Password'"></label>
                        <input type="password" x-model="pwdForm.current_password" class="glass-input" placeholder="••••••••">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                                   x-text="lang==='FR' ? 'Nouveau Mot de Passe' : 'New Password'"></label>
                            <input type="password" x-model="pwdForm.password" class="glass-input" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5"
                                   x-text="lang==='FR' ? 'Confirmer' : 'Confirm'"></label>
                            <input type="password" x-model="pwdForm.password_confirmation" class="glass-input" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div x-show="pwdError" class="px-4 py-3 rounded-xl text-sm font-bold"
                     style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.25);color:rgb(252,165,165)"
                     x-text="pwdError"></div>
                <div x-show="pwdSuccess" class="px-4 py-3 rounded-xl text-sm font-bold"
                     style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)"
                     x-text="pwdSuccess"></div>

                <button @click="changePassword()" :disabled="pwdSaving"
                        class="glass-btn-dark px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest disabled:opacity-40"
                        x-text="pwdSaving ? '…' : (lang==='FR' ? 'Changer le Mot de Passe' : 'Change Password')"></button>
            </div>

            <!-- 2FA / OTP card -->
            <div class="glass-card rounded-2xl p-6 space-y-4">
                <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest" x-text="lang==='FR' ? 'Double Authentification (2FA)' : 'Two-Factor Authentication (2FA)'"></p>
                <p class="text-xs opacity-60" x-text="lang==='FR' ? 'Recevez un code par email pour sécuriser votre connexion.' : 'Receive a one-time code by email to secure your login.'"></p>

                <div x-show="!otpSent" class="flex gap-3 flex-wrap">
                    <button @click="generateOtp()" :disabled="otpLoading" class="glass-btn-dark px-5 py-2 rounded-xl text-xs uppercase tracking-widest disabled:opacity-40"
                        x-text="otpLoading ? '…' : (lang==='FR' ? 'Envoyer un code OTP par email' : 'Send OTP code by email')"></button>
                </div>

                <div x-show="otpSent" class="space-y-3">
                    <p class="text-xs text-emerald-400" x-text="lang==='FR' ? 'Code envoyé ! Vérifiez votre boîte email.' : 'Code sent! Check your email.'"></p>
                    <div class="flex gap-3 items-end">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5" x-text="lang==='FR' ? 'Code OTP (6 chiffres)' : 'OTP Code (6 digits)'"></label>
                            <input x-model="otpCode" type="text" maxlength="6" pattern="[0-9]*" class="glass-input w-32 text-center font-mono tracking-widest text-lg" placeholder="000000">
                        </div>
                        <button @click="verifyOtp()" :disabled="otpLoading" class="glass-btn-dark px-5 py-2 rounded-xl text-xs uppercase tracking-widest disabled:opacity-40"
                            x-text="otpLoading ? '…' : (lang==='FR' ? 'Vérifier et Activer 2FA' : 'Verify & Enable 2FA')"></button>
                    </div>
                </div>

                <div x-show="otpError" class="text-red-400 text-xs" x-text="otpError"></div>
                <div x-show="otpSuccess" class="text-emerald-400 text-xs font-bold" x-text="otpSuccess"></div>
            </div>
        </div>

        <!-- ── Customer Invoices ─────────────────────────────────────────────── -->
        <div x-show="page==='customer-invoices'" x-cloak class="p-6 space-y-5 float-in" x-data="customerInvoicesPanel()">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <h2 class="text-xl font-bold tracking-tight" x-text="lang==='FR' ? 'Factures Clients' : 'Customer Invoices'"></h2>
                <button @click="showForm=!showForm" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest"
                    x-text="showForm ? (lang==='FR'?'Annuler':'Cancel') : (lang==='FR'?'+ Nouvelle Facture':'+ New Invoice')"></button>
            </div>

            <div x-show="showForm" class="glass-card p-5 rounded-2xl space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR'?'Client *':'Customer *'"></label>
                        <select x-model="form.customer_id" class="glass-input w-full px-3 py-2 rounded-xl text-sm">
                            <option value="" x-text="lang==='FR'?'— Choisir —':'— Select —'"></option>
                            <template x-for="c in customers" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR'?'Date facture *':'Invoice date *'"></label>
                        <input x-model="form.invoice_date" type="date" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR'?'Date échéance *':'Due date *'"></label>
                        <input x-model="form.due_date" type="date" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR'?'Montant HT (XAF) *':'Amount HT (XAF) *'"></label>
                        <input x-model="form.amount_ht" type="number" min="0" class="glass-input w-full px-3 py-2 rounded-xl text-sm" @input="calcTtc()"></div>
                    <div class="col-span-2 grid grid-cols-3 gap-3 text-xs opacity-70">
                        <div class="glass-card px-3 py-2 rounded-xl"><span x-text="lang==='FR'?'TVA 17.5%:':'VAT 17.5%:'"></span> <strong x-text="fmtXaf(ttcPreview.tva)"></strong></div>
                        <div class="glass-card px-3 py-2 rounded-xl"><span x-text="lang==='FR'?'CAC 1.75%:':'CAC 1.75%:'"></span> <strong x-text="fmtXaf(ttcPreview.cac)"></strong></div>
                        <div class="glass-card px-3 py-2 rounded-xl"><span x-text="lang==='FR'?'TTC:':'TTC:'"></span> <strong class="text-amber-400" x-text="fmtXaf(ttcPreview.ttc)"></strong></div>
                    </div>
                    <div class="col-span-2"><label class="block text-xs opacity-60 mb-1">Notes</label>
                        <input x-model="form.notes" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                </div>
                <div x-show="formError" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(244,63,94,0.1);color:rgb(252,165,165)" x-text="formError"></div>
                <button @click="save()" class="glass-btn-dark px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest"
                    x-text="saving ? '…' : (lang==='FR'?'Enregistrer':'Save')"></button>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead><tr style="background:rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.07)">
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR'?'N° Facture':'Invoice #'"></th>
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR'?'Client':'Customer'"></th>
                        <th class="text-right px-4 py-3 text-xs uppercase tracking-widest opacity-50">TTC</th>
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR'?'Éch.':'Due'"></th>
                        <th class="text-center px-4 py-3 text-xs uppercase tracking-widest opacity-50">Statut</th>
                        <th class="text-center px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR'?'Actions':'Actions'"></th>
                    </tr></thead>
                    <tbody>
                        <template x-if="invoices.length===0">
                            <tr><td colspan="6" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
                                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Aucune facture.':'No invoices yet.'"></span>
                                </div>
                            </td></tr>
                        </template>
                        <template x-for="inv in invoices" :key="inv.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 font-mono text-amber-400 text-xs" x-text="inv.invoice_number"></td>
                                <td class="px-4 py-3 font-medium" x-text="inv.customer?.name??'—'"></td>
                                <td class="px-4 py-3 text-right font-bold" x-text="fmtXaf(inv.amount_ttc)"></td>
                                <td class="px-4 py-3 opacity-70 text-xs" x-text="inv.due_date"></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                        :class="{
                                            'bg-emerald-900/50 text-emerald-300': inv.status==='PAID',
                                            'bg-amber-900/50 text-amber-300': inv.status==='SENT',
                                            'bg-slate-800 text-slate-400': inv.status==='DRAFT',
                                            'bg-red-900/50 text-red-300': inv.status==='OVERDUE'||inv.status==='CANCELLED',
                                            'bg-indigo-900/50 text-indigo-300': inv.status==='CREDIT_NOTE',
                                        }" x-text="inv.status"></span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex gap-1 justify-center flex-wrap">
                                        <button x-show="inv.status==='DRAFT'" @click="markSent(inv)"
                                            class="px-2 py-1 rounded-lg text-xs" style="background:rgba(201,155,14,0.1);border:1px solid rgba(201,155,14,0.2);color:rgb(252,211,77)"
                                            x-text="lang==='FR'?'Envoyer':'Send'"></button>
                                        <button x-show="inv.status==='SENT'||inv.status==='OVERDUE'" @click="markPaid(inv)"
                                            class="px-2 py-1 rounded-lg text-xs" style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:rgb(110,231,183)"
                                            x-text="lang==='FR'?'Payée':'Mark Paid'"></button>
                                        <button x-show="inv.status==='PAID'||inv.status==='SENT'" @click="creditNote(inv)"
                                            class="px-2 py-1 rounded-lg text-xs" style="background:rgba(99,102,241,0.1);border:1px solid rgba(99,102,241,0.2);color:rgb(165,180,252)"
                                            x-text="lang==='FR'?'Avoir':'Credit Note'"></button>
                                        <!-- MECeF DGI certification -->
                                        <span x-show="inv.mecef_status==='certified'" class="px-2 py-1 rounded-lg text-xs inline-flex items-center gap-1" style="background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.25);color:rgb(110,231,183)" :title="'NIM '+(inv.mecef_nim||'')">
                                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                            <span x-text="(lang==='FR'?'Certifiée ':'Certified ')+(inv.mecef_counter||'')"></span>
                                        </span>
                                        <button x-show="inv.mecef_status!=='certified' && (inv.status==='SENT'||inv.status==='PAID')" @click="certifyMecef(inv)" :disabled="mecefLoading===inv.id"
                                            class="px-2 py-1 rounded-lg text-xs disabled:opacity-50" style="background:rgba(201,155,14,0.12);border:1px solid rgba(201,155,14,0.25);color:#E3B420"
                                            x-text="mecefLoading===inv.id ? '…' : (lang==='FR'?'Certifier DGI':'Certify DGI')"></button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Customers ─────────────────────────────────────────────────────── -->
        <div x-show="page==='customers'" x-cloak class="p-6 space-y-5 float-in" x-data="customersPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold tracking-tight" x-text="lang==='FR' ? 'Clients' : 'Customers'"></h2>
                <button @click="showForm=!showForm" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest"
                    x-text="showForm ? (lang==='FR' ? 'Annuler' : 'Cancel') : (lang==='FR' ? '+ Nouveau Client' : '+ New Customer')"></button>
            </div>

            <div x-show="showForm" class="glass-card p-5 rounded-2xl space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Nom *' : 'Name *'"></label>
                        <input x-model="form.name" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1">NIU</label>
                        <input x-model="form.niu" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1">Email</label>
                        <input x-model="form.email" type="email" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Téléphone' : 'Phone'"></label>
                        <input x-model="form.phone" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Délai paiement (jours)' : 'Payment terms (days)'"></label>
                        <input x-model="form.payment_terms_days" type="number" class="glass-input w-full px-3 py-2 rounded-xl text-sm" value="30"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Plafond crédit (XAF)' : 'Credit limit (XAF)'"></label>
                        <input x-model="form.credit_limit_xaf" type="number" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div class="col-span-2"><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Adresse' : 'Address'"></label>
                        <input x-model="form.address" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                </div>
                <div x-show="formError" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(244,63,94,0.1);color:rgb(252,165,165)" x-text="formError"></div>
                <button @click="save()" class="glass-btn-dark px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest"
                    x-text="saving ? '…' : (lang==='FR' ? 'Enregistrer' : 'Save')"></button>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead><tr style="background:rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.07)">
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Nom' : 'Name'"></th>
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50">NIU</th>
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50">Email</th>
                        <th class="text-right px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Délai (j)' : 'Terms (d)'"></th>
                        <th class="text-right px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Plafond' : 'Limit'"></th>
                    </tr></thead>
                    <tbody>
                        <template x-if="customers.length===0">
                            <tr><td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg></div>
                                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun client enregistré.' : 'No customers yet.'"></span>
                                </div>
                            </td></tr>
                        </template>
                        <template x-for="c in customers" :key="c.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 font-medium" x-text="c.name"></td>
                                <td class="px-4 py-3 opacity-70" x-text="c.niu||'—'"></td>
                                <td class="px-4 py-3 opacity-70" x-text="c.email||'—'"></td>
                                <td class="px-4 py-3 text-right opacity-70" x-text="c.payment_terms_days||'30'"></td>
                                <td class="px-4 py-3 text-right opacity-70" x-text="c.credit_limit_xaf ? fmtXaf(c.credit_limit_xaf) : '—'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Suppliers ──────────────────────────────────────────────────────── -->
        <div x-show="page==='suppliers'" x-cloak class="p-6 space-y-5 float-in" x-data="suppliersPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold tracking-tight" x-text="lang==='FR' ? 'Fournisseurs' : 'Suppliers'"></h2>
                <button @click="showForm=!showForm" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest"
                    x-text="showForm ? (lang==='FR' ? 'Annuler' : 'Cancel') : (lang==='FR' ? '+ Nouveau Fournisseur' : '+ New Supplier')"></button>
            </div>

            <div x-show="showForm" class="glass-card p-5 rounded-2xl space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Nom *' : 'Name *'"></label>
                        <input x-model="form.name" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1">NIU</label>
                        <input x-model="form.niu" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1">Email</label>
                        <input x-model="form.email" type="email" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Téléphone' : 'Phone'"></label>
                        <input x-model="form.phone" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Délai paiement (jours)' : 'Payment terms (days)'"></label>
                        <input x-model="form.payment_terms_days" type="number" class="glass-input w-full px-3 py-2 rounded-xl text-sm" value="30"></div>
                    <div class="col-span-2"><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Adresse' : 'Address'"></label>
                        <input x-model="form.address" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                </div>
                <div x-show="formError" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(244,63,94,0.1);color:rgb(252,165,165)" x-text="formError"></div>
                <button @click="save()" class="glass-btn-dark px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest"
                    x-text="saving ? '…' : (lang==='FR' ? 'Enregistrer' : 'Save')"></button>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead><tr style="background:rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.07)">
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Nom' : 'Name'"></th>
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50">NIU</th>
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50">Email</th>
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Téléphone' : 'Phone'"></th>
                    </tr></thead>
                    <tbody>
                        <template x-if="suppliers.length===0">
                            <tr><td colspan="4" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></div>
                                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun fournisseur enregistré.' : 'No suppliers yet.'"></span>
                                </div>
                            </td></tr>
                        </template>
                        <template x-for="s in suppliers" :key="s.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 font-medium" x-text="s.name"></td>
                                <td class="px-4 py-3 opacity-70" x-text="s.niu||'—'"></td>
                                <td class="px-4 py-3 opacity-70" x-text="s.email||'—'"></td>
                                <td class="px-4 py-3 opacity-70" x-text="s.phone||'—'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Delivery Notes ────────────────────────────────────────────────── -->
        <div x-show="page==='delivery-notes'" x-cloak class="p-6 space-y-5 float-in" x-data="deliveryNotesPanel()">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <h2 class="text-xl font-bold tracking-tight" x-text="lang==='FR' ? 'Bons de Livraison' : 'Delivery Notes'"></h2>
                <button @click="showForm=!showForm" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR' ? '+ Nouveau BL' : '+ New DN'"></button>
            </div>

            <!-- Filters -->
            <div class="flex gap-3 flex-wrap">
                <select x-model="filterType" @change="load()" class="glass-input px-3 py-2 rounded-xl text-sm">
                    <option value="" x-text="lang==='FR' ? 'Tous les types' : 'All types'"></option>
                    <option value="OUT" x-text="lang==='FR' ? 'Expédition (OUT)' : 'Outbound (OUT)'"></option>
                    <option value="IN" x-text="lang==='FR' ? 'Réception (IN)' : 'Inbound (IN)'"></option>
                </select>
                <select x-model="filterStatus" @change="load()" class="glass-input px-3 py-2 rounded-xl text-sm">
                    <option value="" x-text="lang==='FR' ? 'Tous les statuts' : 'All statuses'"></option>
                    <option value="DRAFT">DRAFT</option>
                    <option value="DELIVERED">DELIVERED</option>
                    <option value="SIGNED">SIGNED</option>
                </select>
            </div>

            <!-- New DN form -->
            <div x-show="showForm" class="glass-card rounded-2xl p-5 space-y-4">
                <h3 class="text-sm font-semibold opacity-70" x-text="lang==='FR' ? 'Nouveau bon de livraison' : 'New delivery note'"></h3>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Type' : 'Type'"></label>
                        <select x-model="form.dn_type" class="w-full mt-1 input-field text-sm">
                            <option value="OUT" x-text="lang==='FR' ? 'Expédition Client (OUT)' : 'Outbound (OUT)'"></option>
                            <option value="IN" x-text="lang==='FR' ? 'Réception Fournisseur (IN)' : 'Inbound (IN)'"></option>
                        </select>
                    </div>
                    <div x-show="form.dn_type==='OUT'">
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Client *' : 'Customer *'"></label>
                        <select x-model="form.customer_id" class="w-full mt-1 input-field text-sm">
                            <option value="" x-text="lang==='FR' ? '— Choisir client —' : '— Select customer —'"></option>
                            <template x-for="c in customers" :key="c.id">
                                <option :value="c.id" x-text="c.name"></option>
                            </template>
                        </select>
                    </div>
                    <div x-show="form.dn_type==='IN'">
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Fournisseur *' : 'Supplier *'"></label>
                        <select x-model="form.supplier_id" class="w-full mt-1 input-field text-sm">
                            <option value="" x-text="lang==='FR' ? '— Choisir fournisseur —' : '— Select supplier —'"></option>
                            <template x-for="s in suppliers" :key="s.id">
                                <option :value="s.id" x-text="s.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Date livraison *' : 'Delivery date *'"></label>
                        <input x-model="form.delivery_date" type="date" class="w-full mt-1 input-field text-sm">
                    </div>
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Adresse livraison' : 'Delivery address'"></label>
                        <input x-model="form.delivery_address" class="w-full mt-1 input-field text-sm">
                    </div>
                    <div class="col-span-2 sm:col-span-3">
                        <label class="text-xs opacity-60">Notes</label>
                        <input x-model="form.notes" class="w-full mt-1 input-field text-sm">
                    </div>
                </div>

                <!-- Lines -->
                <div>
                    <div class="text-xs opacity-60 mb-2 font-semibold uppercase tracking-widest" x-text="lang==='FR' ? 'Articles / Lignes' : 'Items / Lines'"></div>
                    <table class="w-full text-xs mb-2">
                        <thead><tr class="text-left opacity-50 border-b border-white/10">
                            <th class="pb-1 pr-2" x-text="lang==='FR' ? 'Désignation *' : 'Description *'"></th>
                            <th class="pb-1 pr-2 w-24" x-text="lang==='FR' ? 'Réf.' : 'Ref.'"></th>
                            <th class="pb-1 pr-2 w-20" x-text="lang==='FR' ? 'Qté *' : 'Qty *'"></th>
                            <th class="pb-1 pr-2 w-16" x-text="lang==='FR' ? 'Unité' : 'Unit'"></th>
                            <th class="pb-1 w-8"></th>
                        </tr></thead>
                        <tbody>
                            <template x-for="(line, i) in form.lines" :key="i">
                                <tr class="border-b border-white/5">
                                    <td class="py-1 pr-2"><input x-model="line.description" class="w-full input-field text-xs" :placeholder="lang==='FR' ? 'Désignation' : 'Description'"></td>
                                    <td class="py-1 pr-2"><input x-model="line.product_code" class="w-full input-field text-xs font-mono" placeholder="PROD-001"></td>
                                    <td class="py-1 pr-2"><input x-model.number="line.quantity" type="number" min="0.001" step="0.001" class="w-full input-field text-xs text-right"></td>
                                    <td class="py-1 pr-2"><input x-model="line.unit" class="w-full input-field text-xs" placeholder="pcs"></td>
                                    <td class="py-1 text-center"><button @click="removeLine(i)" class="text-red-400 hover:text-red-300 text-base leading-none">×</button></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <button @click="addLine()" class="glass-btn px-3 py-1 rounded-lg text-xs" x-text="lang==='FR' ? '+ Ligne' : '+ Line'"></button>
                </div>

                <div x-show="err" class="text-red-400 text-xs font-semibold" x-text="err"></div>
                <button @click="save()" :disabled="saving" class="glass-btn-dark px-5 py-2 rounded-xl text-xs uppercase tracking-widest disabled:opacity-40" x-text="saving ? '…' : (lang==='FR' ? 'Créer BL' : 'Create DN')"></button>
            </div>

            <!-- List -->
            <div class="glass-card rounded-2xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-white/10 text-xs opacity-50 uppercase tracking-widest">
                            <th class="px-4 py-3 text-left" x-text="lang==='FR' ? 'Numéro' : 'Number'"></th>
                            <th class="px-4 py-3 text-left" x-text="lang==='FR' ? 'Type' : 'Type'"></th>
                            <th class="px-4 py-3 text-left" x-text="lang==='FR' ? 'Date' : 'Date'"></th>
                            <th class="px-4 py-3 text-left" x-text="lang==='FR' ? 'Contrepartie' : 'Counterparty'"></th>
                            <th class="px-4 py-3 text-left">Statut</th>
                            <th class="px-4 py-3 text-right" x-text="lang==='FR' ? 'Actions' : 'Actions'"></th>
                        </tr></thead>
                        <tbody>
                            <template x-if="loading"><tr><td colspan="6" class="px-4 py-8 text-center opacity-40" x-text="lang==='FR' ? 'Chargement…' : 'Loading…'"></td></tr></template>
                            <template x-for="dn in items" :key="dn.id">
                                <tr class="border-b border-white/5 hover:bg-white/5 transition">
                                    <td class="px-4 py-3 font-mono text-xs" x-text="dn.dn_number"></td>
                                    <td class="px-4 py-3"><span :class="dn.dn_type==='OUT' ? 'badge-blue' : 'badge-amber'" x-text="dn.dn_type"></span></td>
                                    <td class="px-4 py-3 text-xs opacity-70" x-text="dn.delivery_date"></td>
                                    <td class="px-4 py-3 text-xs" x-text="dn.customer?.name || dn.supplier?.name || '—'"></td>
                                    <td class="px-4 py-3">
                                        <span :class="{
                                            'text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded text-xs font-bold': dn.status==='DRAFT',
                                            'text-blue-400 bg-blue-400/10 px-2 py-0.5 rounded text-xs font-bold': dn.status==='DELIVERED',
                                            'text-emerald-400 bg-emerald-400/10 px-2 py-0.5 rounded text-xs font-bold': dn.status==='SIGNED',
                                        }" x-text="dn.status"></span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex gap-1 justify-end flex-wrap">
                                            <template x-if="dn.status==='DRAFT'">
                                                <button @click="markStatus(dn,'DELIVERED')" class="glass-btn px-2 py-1 rounded-lg text-xs" x-text="lang==='FR' ? 'Livré' : 'Delivered'"></button>
                                            </template>
                                            <template x-if="dn.status==='DELIVERED'">
                                                <button @click="markStatus(dn,'SIGNED')" class="glass-btn px-2 py-1 rounded-lg text-xs" x-text="lang==='FR' ? 'Signé' : 'Signed'"></button>
                                            </template>
                                            <a :href="`/api/v1/companies/${_cid}/delivery-notes/${dn.id}/pdf`" target="_blank" class="glass-btn-dark px-2 py-1 rounded-lg text-xs">PDF</a>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!loading && items.length===0">
                                <tr><td colspan="6" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 2 2 4-4"/><path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/><path d="M3.29 7 12 12l8.71-5"/><path d="M12 22V12"/></svg></div>
                                        <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun bon de livraison.' : 'No delivery notes.'"></span>
                                    </div>
                                </td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── Cashflow Projection ─────────────────────────────────────────────── -->
        <div x-show="page==='cashflow'" x-cloak class="p-6 space-y-5 float-in" x-data="cashflowPanel()">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <h2 class="text-xl font-bold tracking-tight" x-text="lang==='FR' ? 'Projection de Trésorerie (90 jours)' : 'Cash Flow Forecast (90 days)'"></h2>
                <button @click="load()" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="loading ? '…' : (lang==='FR' ? 'Actualiser' : 'Refresh')"></button>
            </div>

            <div x-show="err" class="text-red-400 text-xs" x-text="err"></div>

            <!-- Summary cards -->
            <div x-show="data" class="grid grid-cols-3 gap-4">
                <div class="glass-card p-4 rounded-2xl text-center">
                    <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Entrées Prévues' : 'Expected Inflows'"></div>
                    <div class="text-lg font-bold text-emerald-400" x-text="fmtXaf(data?.summary?.total_inflow)"></div>
                </div>
                <div class="glass-card p-4 rounded-2xl text-center">
                    <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Sorties Prévues' : 'Expected Outflows'"></div>
                    <div class="text-lg font-bold text-red-400" x-text="fmtXaf(data?.summary?.total_outflow)"></div>
                </div>
                <div class="glass-card p-4 rounded-2xl text-center">
                    <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Position Nette' : 'Net Position'"></div>
                    <div class="text-lg font-bold" :class="(data?.summary?.net_position??0)>=0 ? 'text-emerald-400' : 'text-red-400'" x-text="fmtXaf(data?.summary?.net_position)"></div>
                </div>
            </div>

            <!-- Buckets -->
            <div x-show="data" class="grid grid-cols-3 gap-4">
                <template x-for="[key, bucket] in Object.entries(data?.buckets ?? {})" :key="key">
                    <div class="glass-card p-4 rounded-2xl space-y-2">
                        <div class="text-xs font-bold uppercase tracking-widest opacity-60" x-text="(lang==='FR' ? 'Jours ' : 'Days ') + key"></div>
                        <div class="flex justify-between text-xs"><span class="text-emerald-400" x-text="lang==='FR' ? 'Entrées' : 'In'"></span><span x-text="fmtXaf(bucket.inflow)"></span></div>
                        <div class="flex justify-between text-xs"><span class="text-red-400" x-text="lang==='FR' ? 'Sorties' : 'Out'"></span><span x-text="fmtXaf(bucket.outflow)"></span></div>
                        <div class="flex justify-between text-xs font-bold border-t border-white/10 pt-2"><span x-text="lang==='FR' ? 'Net' : 'Net'"></span><span :class="bucket.net>=0 ? 'text-emerald-400' : 'text-red-400'" x-text="fmtXaf(bucket.net)"></span></div>
                    </div>
                </template>
            </div>

            <!-- Receivables table -->
            <div x-show="data?.receivables?.length" class="glass-card rounded-2xl overflow-hidden">
                <div class="px-4 py-3 text-xs font-bold uppercase tracking-widest opacity-60" x-text="lang==='FR' ? 'Créances Clients à Recevoir' : 'Receivables Due'"></div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b border-white/10 text-xs opacity-50">
                            <th class="px-4 py-2 text-left">N° Facture</th><th class="px-4 py-2 text-left">Client</th>
                            <th class="px-4 py-2 text-left">Échéance</th><th class="px-4 py-2 text-right">Montant</th>
                        </tr></thead>
                        <tbody>
                            <template x-for="r in (data?.receivables ?? [])" :key="r.invoice_number">
                                <tr class="border-b border-white/5 hover:bg-white/5">
                                    <td class="px-4 py-2 font-mono text-xs" x-text="r.invoice_number"></td>
                                    <td class="px-4 py-2 text-xs" x-text="r.counterparty"></td>
                                    <td class="px-4 py-2 text-xs" x-text="r.due_date"></td>
                                    <td class="px-4 py-2 text-right text-xs text-emerald-400" x-text="fmtXaf(r.net_receivable)"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ── Financial Reports ──────────────────────────────────────────────── -->
        <div x-show="page==='reports'" x-cloak class="p-6 space-y-5 float-in" x-data="reportsPanel()">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <h2 class="text-xl font-bold tracking-tight" x-text="lang==='FR' ? 'Rapports Financiers' : 'Financial Reports'"></h2>
                <div class="flex gap-2 flex-wrap">
                    <button @click="tab='pl'" :class="tab==='pl' ? 'glass-btn-dark' : 'glass-btn'" class="px-4 py-2 rounded-xl text-xs uppercase tracking-widest">P&L</button>
                    <button @click="tab='bs'" :class="tab==='bs' ? 'glass-btn-dark' : 'glass-btn'" class="px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR' ? 'Bilan' : 'Balance Sheet'"></button>
                    <button @click="tab='cf'" :class="tab==='cf' ? 'glass-btn-dark' : 'glass-btn'" class="px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR' ? 'Trésorerie' : 'Cash Flow'"></button>
                    <button @click="tab='ar'" :class="tab==='ar' ? 'glass-btn-dark' : 'glass-btn'" class="px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR' ? 'Créances' : 'Aged Rec.'"></button>
                    <button @click="tab='ap'" :class="tab==='ap' ? 'glass-btn-dark' : 'glass-btn'" class="px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR' ? 'Dettes' : 'Aged Pay.'"></button>
                </div>
            </div>

            <!-- Date range for P&L / Cash Flow -->
            <div x-show="tab==='pl'||tab==='cf'" class="flex gap-3 items-end flex-wrap">
                <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Du' : 'From'"></label>
                    <input x-model="from" type="date" class="glass-input px-3 py-2 rounded-xl text-sm"></div>
                <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Au' : 'To'"></label>
                    <input x-model="to" type="date" class="glass-input px-3 py-2 rounded-xl text-sm"></div>
                <button @click="load()" class="glass-btn-dark px-5 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="loading ? '…' : (lang==='FR' ? 'Générer' : 'Generate')"></button>
            </div>
            <div x-show="tab==='bs'" class="flex gap-3 items-end flex-wrap">
                <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Au' : 'As of'"></label>
                    <input x-model="asOf" type="date" class="glass-input px-3 py-2 rounded-xl text-sm"></div>
                <button @click="load()" class="glass-btn-dark px-5 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="loading ? '…' : (lang==='FR' ? 'Générer' : 'Generate')"></button>
            </div>
            <div x-show="tab==='ar'||tab==='ap'">
                <button @click="load()" class="glass-btn-dark px-5 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="loading ? '…' : (lang==='FR' ? 'Actualiser' : 'Refresh')"></button>
            </div>

            <div x-show="error" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(244,63,94,0.1);color:rgb(252,165,165)" x-text="error"></div>

            <!-- P&L -->
            <div x-show="tab==='pl' && result" class="glass-card p-5 rounded-2xl space-y-4">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div class="glass-card p-4 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Chiffre d\'Affaires' : 'Revenue'"></div>
                        <div class="text-lg font-bold text-emerald-400" x-text="fmtXaf(result?.revenue)"></div>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Charges' : 'Expenses'"></div>
                        <div class="text-lg font-bold text-red-400" x-text="fmtXaf(result?.expenses)"></div>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Résultat Net' : 'Net Profit'"></div>
                        <div class="text-lg font-bold" :class="(result?.net_profit??0)>=0 ? 'text-emerald-400' : 'text-red-400'" x-text="fmtXaf(result?.net_profit)"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="text-xs uppercase tracking-widest opacity-50 mb-2" x-text="lang==='FR' ? 'Produits (Classe 7)' : 'Income (Class 7)'"></div>
                        <template x-for="row in (result?.revenue_lines??[])" :key="row.code">
                            <div class="flex justify-between py-1 border-b border-white/5 text-sm">
                                <span x-text="row.code+' '+row.label" class="opacity-70"></span>
                                <span class="text-emerald-400" x-text="fmtXaf(row.net)"></span>
                            </div>
                        </template>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-widest opacity-50 mb-2" x-text="lang==='FR' ? 'Charges (Classe 6)' : 'Expenses (Class 6)'"></div>
                        <template x-for="row in (result?.expense_lines??[])" :key="row.code">
                            <div class="flex justify-between py-1 border-b border-white/5 text-sm">
                                <span x-text="row.code+' '+row.label" class="opacity-70"></span>
                                <span class="text-red-400" x-text="fmtXaf(row.net)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Balance Sheet -->
            <div x-show="tab==='bs' && result" class="glass-card p-5 rounded-2xl space-y-4">
                <div class="grid grid-cols-3 gap-4 text-center mb-2">
                    <div class="glass-card p-4 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Total Actif' : 'Total Assets'"></div>
                        <div class="text-lg font-bold text-amber-400" x-text="fmtXaf(result?.total_assets)"></div>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Total Passif' : 'Total Liabilities'"></div>
                        <div class="text-lg font-bold text-red-400" x-text="fmtXaf(result?.total_liabilities)"></div>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Capitaux Propres' : 'Equity'"></div>
                        <div class="text-lg font-bold text-emerald-400" x-text="fmtXaf(result?.equity)"></div>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="text-xs uppercase tracking-widest opacity-50 mb-2" x-text="lang==='FR' ? 'Actif' : 'Assets'"></div>
                        <template x-for="row in (result?.assets??[])" :key="row.code">
                            <div class="flex justify-between py-1 border-b border-white/5 text-sm">
                                <span x-text="row.code+' '+row.label" class="opacity-70"></span>
                                <span x-text="fmtXaf(row.balance)"></span>
                            </div>
                        </template>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-widest opacity-50 mb-2" x-text="lang==='FR' ? 'Passif' : 'Liabilities'"></div>
                        <template x-for="row in (result?.liabilities??[])" :key="row.code">
                            <div class="flex justify-between py-1 border-b border-white/5 text-sm">
                                <span x-text="row.code+' '+row.label" class="opacity-70"></span>
                                <span x-text="fmtXaf(row.balance)"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Cash Flow -->
            <div x-show="tab==='cf' && result" class="glass-card p-5 rounded-2xl space-y-3">
                <div class="grid grid-cols-2 gap-4">
                    <div class="glass-card p-4 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Flux d\'exploitation' : 'Operating Cash Flow'"></div>
                        <div class="text-lg font-bold" :class="(result?.operating??0)>=0 ? 'text-emerald-400' : 'text-red-400'" x-text="fmtXaf(result?.operating)"></div>
                    </div>
                    <div class="glass-card p-4 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Trésorerie nette' : 'Net Cash'"></div>
                        <div class="text-lg font-bold" :class="(result?.net_cash??0)>=0 ? 'text-emerald-400' : 'text-red-400'" x-text="fmtXaf(result?.net_cash)"></div>
                    </div>
                </div>
                <div>
                    <div class="text-xs uppercase tracking-widest opacity-50 mb-2" x-text="lang==='FR' ? 'Détail Comptes 5xx' : 'Class 5 Detail'"></div>
                    <template x-for="row in (result?.treasury_lines??[])" :key="row.code">
                        <div class="flex justify-between py-1 border-b border-white/5 text-sm">
                            <span x-text="row.code+' '+row.label" class="opacity-70"></span>
                            <span :class="(row.balance??0)>=0 ? 'text-emerald-400' : 'text-red-400'" x-text="fmtXaf(row.balance)"></span>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Aged Receivables / Aged Payables shared layout -->
            <div x-show="(tab==='ar'||tab==='ap') && result" class="glass-card p-5 rounded-2xl space-y-4">
                <div class="grid grid-cols-5 gap-3 text-center">
                    <div class="glass-card p-3 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Courant' : 'Current'"></div>
                        <div class="text-base font-bold text-emerald-400" x-text="fmtXaf(result?.current)"></div>
                    </div>
                    <div class="glass-card p-3 rounded-xl">
                        <div class="text-xs opacity-50 mb-1">1–30j</div>
                        <div class="text-base font-bold text-amber-400" x-text="fmtXaf(result?.['1_30'])"></div>
                    </div>
                    <div class="glass-card p-3 rounded-xl">
                        <div class="text-xs opacity-50 mb-1">31–60j</div>
                        <div class="text-base font-bold text-orange-400" x-text="fmtXaf(result?.['31_60'])"></div>
                    </div>
                    <div class="glass-card p-3 rounded-xl">
                        <div class="text-xs opacity-50 mb-1">61–90j</div>
                        <div class="text-base font-bold text-red-400" x-text="fmtXaf(result?.['61_90'])"></div>
                    </div>
                    <div class="glass-card p-3 rounded-xl">
                        <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? '+90j' : '>90d'"></div>
                        <div class="text-base font-bold text-red-600" x-text="fmtXaf(result?.over_90)"></div>
                    </div>
                </div>
                <table class="w-full text-sm mt-2">
                    <thead><tr style="border-bottom:1px solid rgba(255,255,255,0.07)">
                        <th class="text-left px-3 py-2 text-xs opacity-50" x-text="lang==='FR' ? 'Client' : 'Customer'"></th>
                        <th class="text-left px-3 py-2 text-xs opacity-50" x-text="lang==='FR' ? 'Facture' : 'Invoice'"></th>
                        <th class="text-right px-3 py-2 text-xs opacity-50" x-text="lang==='FR' ? 'Montant TTC' : 'Amount TTC'"></th>
                        <th class="text-right px-3 py-2 text-xs opacity-50" x-text="lang==='FR' ? 'Éch.' : 'Due'"></th>
                        <th class="text-right px-3 py-2 text-xs opacity-50" x-text="lang==='FR' ? 'Retard (j)' : 'Overdue (d)'"></th>
                    </tr></thead>
                    <tbody>
                        <template x-for="inv in (result?.invoices??[])" :key="inv.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5">
                                <td class="px-3 py-2 font-medium" x-text="inv.customer?.name??'—'"></td>
                                <td class="px-3 py-2 opacity-70" x-text="inv.invoice_number"></td>
                                <td class="px-3 py-2 text-right" x-text="fmtXaf(inv.amount_ttc)"></td>
                                <td class="px-3 py-2 text-right opacity-70" x-text="inv.due_date"></td>
                                <td class="px-3 py-2 text-right" :class="(inv.days_overdue??0)>30?'text-red-400':'text-amber-400'" x-text="inv.days_overdue??0"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Recurring Transactions ─────────────────────────────────────────── -->
        <div x-show="page==='recurring'" x-cloak class="p-6 space-y-5 float-in" x-data="recurringPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold tracking-tight" x-text="lang==='FR' ? 'Transactions Récurrentes' : 'Recurring Transactions'"></h2>
                <div class="flex gap-2">
                    <button @click="runAll()" class="glass-btn px-4 py-2 rounded-xl text-xs uppercase tracking-widest"
                        x-text="running ? '…' : (lang==='FR' ? '▶ Exécuter maintenant' : '▶ Run Now')"></button>
                    <button @click="showForm=!showForm" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest"
                        x-text="showForm ? (lang==='FR' ? 'Annuler' : 'Cancel') : '+ Nouveau'"></button>
                </div>
            </div>

            <div x-show="runMsg" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(16,185,129,0.1);color:rgb(110,231,183)" x-text="runMsg"></div>

            <div x-show="showForm" class="glass-card p-5 rounded-2xl space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Nom *' : 'Name *'"></label>
                        <input x-model="form.name" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Fréquence *' : 'Frequency *'"></label>
                        <select x-model="form.frequency" class="glass-input w-full px-3 py-2 rounded-xl text-sm">
                            <option value="DAILY" x-text="lang==='FR' ? 'Quotidien' : 'Daily'"></option>
                            <option value="WEEKLY" x-text="lang==='FR' ? 'Hebdomadaire' : 'Weekly'"></option>
                            <option value="MONTHLY" selected x-text="lang==='FR' ? 'Mensuel' : 'Monthly'"></option>
                            <option value="QUARTERLY" x-text="lang==='FR' ? 'Trimestriel' : 'Quarterly'"></option>
                            <option value="YEARLY" x-text="lang==='FR' ? 'Annuel' : 'Yearly'"></option>
                        </select></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Montant (XAF) *' : 'Amount (XAF) *'"></label>
                        <input x-model="form.amount_xaf" type="number" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Prochaine exécution *' : 'Next run date *'"></label>
                        <input x-model="form.next_run_date" type="date" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Compte Débit (code) *' : 'Debit Account (code) *'"></label>
                        <input x-model="form.debit_account" type="text" placeholder="ex: 601000" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Compte Crédit (code) *' : 'Credit Account (code) *'"></label>
                        <input x-model="form.credit_account" type="text" placeholder="ex: 521000" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div class="col-span-2"><label class="block text-xs opacity-60 mb-1">Mémo</label>
                        <input x-model="form.memo" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Date de fin (optionnel)' : 'End date (optional)'"></label>
                        <input x-model="form.end_date" type="date" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                </div>
                <div x-show="formError" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(244,63,94,0.1);color:rgb(252,165,165)" x-text="formError"></div>
                <button @click="save()" class="glass-btn-dark px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest"
                    x-text="saving ? '…' : (lang==='FR' ? 'Enregistrer' : 'Save')"></button>
            </div>

            <div class="glass-card rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead><tr style="background:rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.07)">
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Nom' : 'Name'"></th>
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Fréquence' : 'Frequency'"></th>
                        <th class="text-right px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Montant' : 'Amount'"></th>
                        <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Prochain' : 'Next Run'"></th>
                        <th class="text-center px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Actif' : 'Active'"></th>
                    </tr></thead>
                    <tbody>
                        <template x-if="items.length===0">
                            <tr><td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg></div>
                                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucune transaction récurrente.' : 'No recurring transactions.'"></span>
                                </div>
                            </td></tr>
                        </template>
                        <template x-for="rt in items" :key="rt.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5 transition-colors">
                                <td class="px-4 py-3 font-medium" x-text="rt.name"></td>
                                <td class="px-4 py-3 opacity-70 text-xs" x-text="rt.frequency"></td>
                                <td class="px-4 py-3 text-right" x-text="fmtXaf(rt.amount_xaf)"></td>
                                <td class="px-4 py-3 opacity-70" x-text="rt.next_run_date"></td>
                                <td class="px-4 py-3 text-center">
                                    <span :class="rt.is_active ? 'text-emerald-400' : 'text-red-400'" x-text="rt.is_active ? '●' : '○'"></span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ── Payroll ─────────────────────────────────────────────────────────── -->
        <div x-show="page==='payroll'" x-cloak class="p-6 space-y-5 float-in" x-data="payrollPanel()">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <h2 class="text-xl font-bold tracking-tight" x-text="lang==='FR' ? 'Gestion de la Paie' : 'Payroll'"></h2>
                <div class="flex gap-2">
                    <button @click="tab='employees'" :class="tab==='employees' ? 'glass-btn-dark' : 'glass-btn'" class="px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR' ? 'Employés' : 'Employees'"></button>
                    <button @click="tab='periods'" :class="tab==='periods' ? 'glass-btn-dark' : 'glass-btn'" class="px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR' ? 'Périodes' : 'Pay Periods'"></button>
                </div>
            </div>

            <!-- Employees Tab -->
            <div x-show="tab==='employees'" class="space-y-4">
                <div class="flex justify-end">
                    <button @click="showEmpForm=!showEmpForm" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest"
                        x-text="showEmpForm ? (lang==='FR' ? 'Annuler' : 'Cancel') : (lang==='FR' ? '+ Nouvel Employé' : '+ New Employee')"></button>
                </div>
                <div x-show="showEmpForm" class="glass-card p-5 rounded-2xl space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Nom *' : 'Name *'"></label>
                            <input x-model="empForm.name" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                        <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Poste' : 'Position'"></label>
                            <input x-model="empForm.position" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                        <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Salaire brut (XAF) *' : 'Gross Salary (XAF) *'"></label>
                            <input x-model="empForm.gross_salary_xaf" type="number" min="36270" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                        <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Date d\'embauche *' : 'Hire Date *'"></label>
                            <input x-model="empForm.hire_date" type="date" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                        <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'N° CNPS' : 'CNPS No.'"></label>
                            <input x-model="empForm.cnps_number" type="text" class="glass-input w-full px-3 py-2 rounded-xl text-sm"></div>
                    </div>
                    <div x-show="empError" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(244,63,94,0.1);color:rgb(252,165,165)" x-text="empError"></div>
                    <button @click="saveEmployee()" class="glass-btn-dark px-6 py-2.5 rounded-xl text-xs uppercase tracking-widest"
                        x-text="empSaving ? '…' : (lang==='FR' ? 'Enregistrer' : 'Save')"></button>
                </div>
                <div class="glass-card rounded-2xl overflow-hidden">
                    <table class="w-full text-sm">
                        <thead><tr style="background:rgba(255,255,255,0.04);border-bottom:1px solid rgba(255,255,255,0.07)">
                            <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Nom' : 'Name'"></th>
                            <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Poste' : 'Position'"></th>
                            <th class="text-right px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Salaire Brut' : 'Gross Salary'"></th>
                            <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'Embauché le' : 'Hired'"></th>
                            <th class="text-left px-4 py-3 text-xs uppercase tracking-widest opacity-50" x-text="lang==='FR' ? 'N° CNPS' : 'CNPS No.'"></th>
                        </tr></thead>
                        <tbody>
                            <template x-if="employees.length===0">
                                <tr><td colspan="5" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                                        <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun employé enregistré.' : 'No employees yet.'"></span>
                                    </div>
                                </td></tr>
                            </template>
                            <template x-for="e in employees" :key="e.id">
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5 transition-colors">
                                    <td class="px-4 py-3 font-medium" x-text="e.name"></td>
                                    <td class="px-4 py-3 opacity-70" x-text="e.position||'—'"></td>
                                    <td class="px-4 py-3 text-right" x-text="fmtXaf(e.gross_salary_xaf)"></td>
                                    <td class="px-4 py-3 opacity-70" x-text="e.hire_date"></td>
                                    <td class="px-4 py-3 opacity-70" x-text="e.cnps_number||'—'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pay Periods Tab -->
            <div x-show="tab==='periods'" class="space-y-4">
                <div class="flex gap-3 items-end flex-wrap">
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Mois' : 'Month'"></label>
                        <select x-model="periodForm.period_month" class="glass-input px-3 py-2 rounded-xl text-sm">
                            <template x-for="m in 12" :key="m">
                                <option :value="m" x-text="m"></option>
                            </template>
                        </select></div>
                    <div><label class="block text-xs opacity-60 mb-1" x-text="lang==='FR' ? 'Année' : 'Year'"></label>
                        <input x-model="periodForm.period_year" type="number" min="2020" class="glass-input px-3 py-2 rounded-xl text-sm" style="width:90px"></div>
                    <button @click="calculatePeriod()" class="glass-btn-dark px-5 py-2 rounded-xl text-xs uppercase tracking-widest"
                        x-text="periodCalc ? '…' : (lang==='FR' ? 'Calculer la Paie' : 'Calculate Payroll')"></button>
                </div>
                <div x-show="periodError" class="px-4 py-2 rounded-xl text-sm" style="background:rgba(244,63,94,0.1);color:rgb(252,165,165)" x-text="periodError"></div>
                <div x-show="periods.length===0" class="glass-card p-10 rounded-2xl flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucune période de paie.' : 'No pay periods yet.'"></span>
                </div>
                <template x-for="p in periods" :key="p.id">
                    <div class="glass-card p-5 rounded-2xl space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="font-bold" x-text="(lang==='FR'?'Paie ':'Payroll ')+p.period_month+'/'+p.period_year"></div>
                            <div class="flex items-center gap-3">
                                <span class="px-3 py-1 rounded-full text-xs font-bold" :class="p.status==='POSTED'?'bg-emerald-900/50 text-emerald-300':'bg-amber-900/50 text-amber-300'" x-text="p.status"></span>
                                <button x-show="p.status==='DRAFT'" @click="postPeriod(p)"
                                    class="glass-btn-dark px-4 py-1.5 rounded-xl text-xs uppercase tracking-widest"
                                    x-text="lang==='FR' ? 'Comptabiliser' : 'Post to Journal'"></button>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-sm">
                            <div class="text-center glass-card p-3 rounded-xl">
                                <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Masse Brute' : 'Gross Total'"></div>
                                <div class="font-bold text-amber-400" x-text="fmtXaf(p.total_gross)"></div>
                            </div>
                            <div class="text-center glass-card p-3 rounded-xl">
                                <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Net à Payer' : 'Net to Pay'"></div>
                                <div class="font-bold text-emerald-400" x-text="fmtXaf(p.total_net)"></div>
                            </div>
                            <div class="text-center glass-card p-3 rounded-xl">
                                <div class="text-xs opacity-50 mb-1" x-text="lang==='FR' ? 'Charges Sociales' : 'Social Charges'"></div>
                                <div class="font-bold text-red-400" x-text="fmtXaf((p.total_cnps_employee??0)+(p.total_cnps_employer??0)+(p.total_irpp??0)+(p.total_cac_irpp??0))"></div>
                            </div>
                        </div>
                        <div x-show="p.lines && p.lines.length">
                            <table class="w-full text-xs mt-2">
                                <thead><tr style="border-bottom:1px solid rgba(255,255,255,0.07)">
                                    <th class="text-left px-3 py-1.5 opacity-50" x-text="lang==='FR' ? 'Employé' : 'Employee'"></th>
                                    <th class="text-right px-3 py-1.5 opacity-50" x-text="lang==='FR' ? 'Brut' : 'Gross'"></th>
                                    <th class="text-right px-3 py-1.5 opacity-50">CNPS Sal.</th>
                                    <th class="text-right px-3 py-1.5 opacity-50">IRPP+CAC</th>
                                    <th class="text-right px-3 py-1.5 opacity-50" x-text="lang==='FR' ? 'Net' : 'Net'"></th>
                                </tr></thead>
                                <tbody>
                                    <template x-for="l in p.lines" :key="l.id">
                                        <tr style="border-bottom:1px solid rgba(255,255,255,0.04)">
                                            <td class="px-3 py-1.5 font-medium" x-text="l.employee?.name??'—'"></td>
                                            <td class="px-3 py-1.5 text-right opacity-70" x-text="fmtXaf(l.gross_salary)"></td>
                                            <td class="px-3 py-1.5 text-right text-red-400" x-text="fmtXaf(l.cnps_employee)"></td>
                                            <td class="px-3 py-1.5 text-right text-red-400" x-text="fmtXaf((l.irpp??0)+(l.cac_irpp??0))"></td>
                                            <td class="px-3 py-1.5 text-right text-emerald-400 font-bold" x-text="fmtXaf(l.net_salary)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- SUPPLIER INVOICES PAGE                                        -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='supplier-invoices'" x-cloak class="p-6 space-y-5 float-in" x-data="supplierInvoicesPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Factures Fournisseurs' : 'Supplier Invoices'"></h2>
                <button @click="showForm=!showForm" class="btn-primary text-xs px-4 py-2">
                    + <span x-text="lang==='FR' ? 'Nouvelle Facture' : 'New Invoice'"></span>
                </button>
            </div>

            <div x-show="showForm" x-cloak class="glass-card rounded-2xl p-5 space-y-3 float-in">
                <h3 class="text-sm font-semibold opacity-70" x-text="lang==='FR' ? 'Nouvelle Facture Fournisseur' : 'New Supplier Invoice'"></h3>
                <div class="grid grid-cols-2 gap-3">
                    <select x-model="form.supplier_id" class="input col-span-2">
                        <option value="">-- <span x-text="lang==='FR' ? 'Sélectionner Fournisseur' : 'Select Supplier'"></span> --</option>
                        <template x-for="s in suppliers" :key="s.id">
                            <option :value="s.id" x-text="s.name"></option>
                        </template>
                    </select>
                    <input x-model="form.invoice_number" :placeholder="lang==='FR' ? 'Nº Facture' : 'Invoice #'" class="input" />
                    <input x-model="form.supplier_ref" :placeholder="lang==='FR' ? 'Réf. Fournisseur' : 'Supplier Ref'" class="input" />
                    <input x-model="form.invoice_date" type="date" class="input" />
                    <input x-model="form.due_date" type="date" class="input" />
                    <input x-model.number="form.amount_ht" type="number" :placeholder="lang==='FR' ? 'Montant HT' : 'Amount HT'" class="input" @input="calcTva()" />
                    <input x-model.number="form.tva_amount" type="number" :placeholder="'TVA (17.5%)'" class="input" />
                    <input x-model="form.expense_account" :placeholder="lang==='FR' ? 'Compte charge (ex: 601100)' : 'Expense account (e.g. 601100)'" class="input" />
                    <input x-model="form.notes" :placeholder="lang==='FR' ? 'Notes' : 'Notes'" class="input" />
                </div>
                <div x-show="formError" class="text-red-400 text-xs px-2 py-1" x-text="formError"></div>
                <button @click="submitInvoice()" :disabled="submitting" class="btn-primary text-xs px-4 py-2 w-full">
                    <span x-show="!submitting" x-text="lang==='FR' ? 'Enregistrer' : 'Save'"></span>
                    <span x-show="submitting">...</span>
                </button>
            </div>

            <div x-show="loading" class="text-center opacity-50 py-6">...</div>
            <div x-show="!loading" class="glass rounded-2xl overflow-hidden">
                <table class="w-full text-xs">
                    <thead><tr style="border-bottom:1px solid rgba(255,255,255,0.07)">
                        <th class="text-left px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Fournisseur' : 'Supplier'"></th>
                        <th class="text-left px-4 py-2.5 opacity-50">Nº</th>
                        <th class="text-right px-4 py-2.5 opacity-50">HT</th>
                        <th class="text-right px-4 py-2.5 opacity-50">TTC</th>
                        <th class="text-center px-4 py-2.5 opacity-50">Statut</th>
                        <th class="text-center px-4 py-2.5 opacity-50"></th>
                    </tr></thead>
                    <tbody>
                        <template x-for="inv in invoices" :key="inv.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5">
                                <td class="px-4 py-2.5 font-medium" x-text="inv.supplier?.name ?? '—'"></td>
                                <td class="px-4 py-2.5 opacity-70" x-text="inv.invoice_number"></td>
                                <td class="px-4 py-2.5 text-right opacity-70" x-text="fmtXaf(inv.amount_ht)"></td>
                                <td class="px-4 py-2.5 text-right font-bold" x-text="fmtXaf(inv.amount_ttc)"></td>
                                <td class="px-4 py-2.5 text-center">
                                    <span :class="inv.status==='PAID'?'text-emerald-400':inv.status==='OVERDUE'?'text-red-400':'text-yellow-400'" x-text="inv.status"></span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <button x-show="inv.status!=='PAID'" @click="payInvoice(inv)" class="text-emerald-400 hover:text-emerald-300 text-xs underline" x-text="lang==='FR' ? 'Payer' : 'Pay'"></button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="invoices.length===0" class="py-12 flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h14a2 2 0 0 0 2-2V7.5L14.5 2H6a2 2 0 0 0-2 2v4"/><polyline points="14 2 14 8 20 8"/><path d="M2 15h10"/><path d="m9 18 3-3-3-3"/></svg></div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucune facture fournisseur.' : 'No supplier invoices.'"></span>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- FIXED ASSETS PAGE                                             -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='fixed-assets'" x-cloak class="p-6 space-y-5 float-in" x-data="fixedAssetsPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Registre des Immobilisations' : 'Fixed Asset Register'"></h2>
                <div class="flex gap-2">
                    <button @click="runDepreciation()" class="btn-secondary text-xs px-3 py-2" x-text="lang==='FR' ? 'Amortir ce mois' : 'Run Depreciation'"></button>
                    <button @click="showForm=!showForm" class="btn-primary text-xs px-4 py-2">+ <span x-text="lang==='FR' ? 'Ajouter' : 'Add'"></span></button>
                </div>
            </div>

            <div x-show="showForm" x-cloak class="glass-card rounded-2xl p-5 space-y-3 float-in">
                <div class="grid grid-cols-2 gap-3">
                    <input x-model="form.name" :placeholder="lang==='FR' ? 'Désignation' : 'Asset Name'" class="input col-span-2" />
                    <select x-model="form.category" class="input">
                        <option value="">-- Catégorie --</option>
                        <option value="BUILDING">Bâtiment</option>
                        <option value="MACHINERY">Matériel Industriel</option>
                        <option value="VEHICLE">Véhicule</option>
                        <option value="FURNITURE">Mobilier</option>
                        <option value="IT_EQUIPMENT">Informatique</option>
                        <option value="OTHER">Autre</option>
                    </select>
                    <input x-model="form.syscohada_account_code" placeholder="Compte SYSCOHADA (ex: 244100)" class="input" />
                    <input x-model="form.acquisition_date" type="date" class="input" />
                    <input x-model.number="form.acquisition_cost" type="number" :placeholder="lang==='FR' ? 'Coût d\'acquisition' : 'Acquisition Cost'" class="input" />
                    <input x-model.number="form.useful_life_months" type="number" :placeholder="lang==='FR' ? 'Durée (mois)' : 'Useful Life (months)'" class="input" />
                    <input x-model.number="form.residual_value" type="number" :placeholder="lang==='FR' ? 'Valeur résiduelle' : 'Residual Value'" class="input" />
                    <input x-model="form.credit_account" :placeholder="lang==='FR' ? 'Compte créditeur (401100 ou 521100)' : 'Credit account'" class="input col-span-2" />
                </div>
                <div x-show="formError" class="text-red-400 text-xs px-2 py-1" x-text="formError"></div>
                <button @click="addAsset()" :disabled="submitting" class="btn-primary text-xs w-full py-2">
                    <span x-show="!submitting" x-text="lang==='FR' ? 'Enregistrer' : 'Save'"></span>
                    <span x-show="submitting">...</span>
                </button>
            </div>

            <div x-show="depMsg" class="text-emerald-400 text-xs glass-card px-4 py-2 rounded-xl" x-text="depMsg"></div>

            <div x-show="!loading" class="glass rounded-2xl overflow-hidden">
                <table class="w-full text-xs">
                    <thead><tr style="border-bottom:1px solid rgba(255,255,255,0.07)">
                        <th class="text-left px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Immobilisation' : 'Asset'"></th>
                        <th class="text-left px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Catégorie' : 'Category'"></th>
                        <th class="text-right px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Coût' : 'Cost'"></th>
                        <th class="text-right px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Amort. Cumulés' : 'Acc. Depreciation'"></th>
                        <th class="text-right px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Val. Nette' : 'Book Value'"></th>
                        <th class="text-center px-4 py-2.5 opacity-50"></th>
                    </tr></thead>
                    <tbody>
                        <template x-for="a in assets" :key="a.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5">
                                <td class="px-4 py-2.5 font-medium" x-text="a.name"></td>
                                <td class="px-4 py-2.5 opacity-70" x-text="a.category"></td>
                                <td class="px-4 py-2.5 text-right opacity-70" x-text="fmtXaf(a.acquisition_cost)"></td>
                                <td class="px-4 py-2.5 text-right text-red-400" x-text="fmtXaf(a.accumulated_depreciation)"></td>
                                <td class="px-4 py-2.5 text-right font-bold text-emerald-400" x-text="fmtXaf(a.book_value)"></td>
                                <td class="px-4 py-2.5 text-center">
                                    <button x-show="a.is_active" @click="disposeAsset(a)" class="text-red-400 hover:text-red-300 text-xs underline" x-text="lang==='FR' ? 'Céder' : 'Dispose'"></button>
                                    <span x-show="!a.is_active" class="opacity-40 text-xs">Cédé</span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="assets.length===0" class="py-12 flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg></div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucune immobilisation enregistrée.' : 'No assets registered.'"></span>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- BANK RECONCILIATION PAGE                                      -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='reconciliation'" x-cloak class="p-6 space-y-5 float-in" x-data="reconciliationPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Rapprochement Bancaire' : 'Bank Reconciliation'"></h2>
                <button @click="showForm=!showForm" class="btn-primary text-xs px-4 py-2">+ <span x-text="lang==='FR' ? 'Nouveau Rapprochement' : 'New Session'"></span></button>
            </div>

            <div x-show="showForm" x-cloak class="glass-card rounded-2xl p-5 space-y-3 float-in">
                <div class="grid grid-cols-2 gap-3">
                    <input x-model="form.bank_account_code" value="521100" :placeholder="lang==='FR' ? 'Compte bancaire (521100)' : 'Bank account code'" class="input" />
                    <input x-model="form.statement_date" type="date" class="input" />
                    <input x-model.number="form.statement_balance" type="number" :placeholder="lang==='FR' ? 'Solde relevé' : 'Statement balance'" class="input col-span-2" />
                </div>
                <p class="text-xs opacity-50" x-text="lang==='FR' ? 'Importez vos lignes de relevé bancaire via l\'onglet Import, puis lancez le rapprochement.' : 'Import your bank statement lines via the Import tab, then start the reconciliation.'"></p>
                <button @click="submitSession()" :disabled="submitting" class="btn-primary text-xs w-full py-2">
                    <span x-show="!submitting" x-text="lang==='FR' ? 'Créer Session' : 'Create Session'"></span>
                    <span x-show="submitting">...</span>
                </button>
            </div>

            <div class="glass rounded-2xl overflow-hidden">
                <template x-for="s in sessions" :key="s.id">
                    <div class="px-4 py-3 flex items-center justify-between" style="border-bottom:1px solid rgba(255,255,255,0.05)">
                        <div>
                            <div class="text-sm font-medium" x-text="s.bank_account_code + ' — ' + s.statement_date"></div>
                            <div class="text-xs opacity-50 mt-0.5" x-text="fmtXaf(s.statement_balance) + ' | Diff: ' + fmtXaf(s.difference)"></div>
                        </div>
                        <span :class="s.is_reconciled ? 'text-emerald-400' : 'text-yellow-400'" x-text="s.is_reconciled ? '✔ Réconcilié' : 'En cours'"></span>
                    </div>
                </template>
                <div x-show="sessions.length===0" class="py-12 flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="18" r="3"/><circle cx="6" cy="6" r="3"/><path d="M6 21V9a9 9 0 0 0 9 9"/></svg></div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucune session de rapprochement.' : 'No reconciliation sessions.'"></span>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- BUDGET PAGE                                                    -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='budget'" x-cloak class="p-6 space-y-5 float-in" x-data="budgetPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Budget & Forecast' : 'Budget & Forecast'"></h2>
                <button @click="showForm=!showForm" class="btn-primary text-xs px-4 py-2">+ <span x-text="lang==='FR' ? 'Nouveau Budget' : 'New Budget'"></span></button>
            </div>

            <div x-show="showForm" x-cloak class="glass-card rounded-2xl p-5 space-y-3 float-in">
                <div class="grid grid-cols-2 gap-3">
                    <input x-model="form.name" :placeholder="lang==='FR' ? 'Nom du budget' : 'Budget name'" class="input col-span-2" />
                    <input x-model.number="form.fiscal_year" type="number" :placeholder="lang==='FR' ? 'Exercice (ex: 2026)' : 'Fiscal year'" class="input" />
                </div>
                <p class="text-xs opacity-50" x-text="lang==='FR' ? 'Après création, ajoutez les lignes via l\'API ou importez-les depuis un tableur.' : 'After creation, add lines via API or import from a spreadsheet.'"></p>
                <button @click="createBudget()" :disabled="submitting" class="btn-primary text-xs w-full py-2">
                    <span x-show="!submitting" x-text="lang==='FR' ? 'Créer' : 'Create'"></span>
                    <span x-show="submitting">...</span>
                </button>
            </div>

            <div x-show="selectedBudget" x-cloak class="glass-card rounded-2xl p-5 space-y-4 float-in">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-sm" x-text="selectedBudget?.name + ' — ' + selectedBudget?.fiscal_year"></h3>
                    <button @click="selectedBudget=null" class="text-xs opacity-50">✕</button>
                </div>
                <div x-show="variance" class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead><tr style="border-bottom:1px solid rgba(255,255,255,0.07)">
                            <th class="text-left px-3 py-1.5 opacity-50" x-text="lang==='FR' ? 'Compte' : 'Account'"></th>
                            <th class="text-right px-3 py-1.5 opacity-50" x-text="lang==='FR' ? 'Budgété' : 'Budgeted'"></th>
                            <th class="text-right px-3 py-1.5 opacity-50" x-text="lang==='FR' ? 'Réel' : 'Actual'"></th>
                            <th class="text-right px-3 py-1.5 opacity-50">Écart</th>
                            <th class="text-right px-3 py-1.5 opacity-50">%</th>
                        </tr></thead>
                        <tbody>
                            <template x-for="l in variance?.lines ?? []" :key="l.account_code">
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.04)">
                                    <td class="px-3 py-1.5 font-mono" x-text="l.account_code"></td>
                                    <td class="px-3 py-1.5 text-right opacity-70" x-text="fmtXaf(l.total_budgeted)"></td>
                                    <td class="px-3 py-1.5 text-right opacity-70" x-text="fmtXaf(l.total_actual)"></td>
                                    <td class="px-3 py-1.5 text-right" :class="l.total_variance>=0?'text-emerald-400':'text-red-400'" x-text="fmtXaf(l.total_variance)"></td>
                                    <td class="px-3 py-1.5 text-right" :class="l.total_pct>=0?'text-emerald-400':'text-red-400'" x-text="l.total_pct !== null ? l.total_pct + '%' : '—'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="glass rounded-2xl overflow-hidden">
                <template x-for="b in budgets" :key="b.id">
                    <div class="px-4 py-3 flex items-center justify-between cursor-pointer hover:bg-white/5" @click="loadVariance(b)" style="border-bottom:1px solid rgba(255,255,255,0.05)">
                        <div>
                            <div class="text-sm font-medium" x-text="b.name"></div>
                            <div class="text-xs opacity-50 mt-0.5" x-text="b.fiscal_year + ' — ' + b.status"></div>
                        </div>
                        <svg class="w-4 h-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </div>
                </template>
                <div x-show="budgets.length===0" class="py-12 flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg></div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun budget créé.' : 'No budgets yet.'"></span>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- DSF / TVA D10 EXPORT PAGE                                     -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='dsf-export'" x-cloak class="p-6 space-y-5 float-in" x-data="dsfExportPanel()">
            <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Exports Fiscaux DSF & TVA D10' : 'DSF & TVA D10 Fiscal Exports'"></h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- DSF Annual -->
                <div class="glass-card rounded-2xl p-5 space-y-3">
                    <h3 class="font-bold text-sm" x-text="lang==='FR' ? 'DSF — Liasse Fiscale Annuelle' : 'DSF — Annual Tax Return'"></h3>
                    <p class="text-xs opacity-50" x-text="lang==='FR' ? 'Génère les tableaux 1 à 5 de la DSF pour le DGI Cameroun.' : 'Generates DSF tables 1–5 for Cameroon DGI filing.'"></p>
                    <input x-model.number="dsfYear" type="number" :placeholder="lang==='FR' ? 'Exercice (ex: 2025)' : 'Fiscal Year'" class="input" />
                    <button @click="generateDsf()" :disabled="dsfLoading" class="btn-primary text-xs w-full py-2">
                        <span x-show="!dsfLoading" x-text="lang==='FR' ? 'Générer DSF' : 'Generate DSF'"></span>
                        <span x-show="dsfLoading">...</span>
                    </button>
                    <div x-show="dsfData" class="text-xs opacity-70">
                        <p x-text="lang==='FR' ? 'CA HT: ' : 'Revenue: '" class="inline"></p>
                        <span class="text-emerald-400 font-bold" x-text="fmtXaf(dsfData?.table_1_compte_resultat?.chiffre_affaires_ht)"></span>
                        <p class="mt-1" x-text="lang==='FR' ? 'Résultat Net: ' : 'Net Profit: '" ></p>
                        <span class="text-emerald-400 font-bold" x-text="fmtXaf(dsfData?.table_1_compte_resultat?.resultat_net)"></span>
                        <button @click="downloadDsf()" class="mt-2 text-sky-400 underline block" x-text="lang==='FR' ? 'Télécharger JSON' : 'Download JSON'"></button>
                    </div>
                </div>

                <!-- TVA Monthly D10 -->
                <div class="glass-card rounded-2xl p-5 space-y-3">
                    <h3 class="font-bold text-sm">TVA D10 — <span x-text="lang==='FR' ? 'Déclaration Mensuelle' : 'Monthly Return'"></span></h3>
                    <p class="text-xs opacity-50" x-text="lang==='FR' ? 'Calcule la TVA nette due + CAC pour un mois donné.' : 'Calculates net TVA due + CAC for a given month.'"></p>
                    <div class="grid grid-cols-2 gap-2">
                        <input x-model.number="tvaMonth" type="number" min="1" max="12" :placeholder="lang==='FR' ? 'Mois' : 'Month'" class="input" />
                        <input x-model.number="tvaYear" type="number" :placeholder="lang==='FR' ? 'Année' : 'Year'" class="input" />
                    </div>
                    <button @click="generateTva()" :disabled="tvaLoading" class="btn-primary text-xs w-full py-2">
                        <span x-show="!tvaLoading" x-text="lang==='FR' ? 'Calculer TVA D10' : 'Calculate TVA D10'"></span>
                        <span x-show="tvaLoading">...</span>
                    </button>
                    <div x-show="tvaData" class="text-xs space-y-1">
                        <div class="flex justify-between"><span class="opacity-50">TVA Collectée</span><span class="text-yellow-400 font-bold" x-text="fmtXaf(tvaData?.tva_collectee)"></span></div>
                        <div class="flex justify-between"><span class="opacity-50">TVA Déductible</span><span class="text-emerald-400 font-bold" x-text="fmtXaf(tvaData?.tva_deductible)"></span></div>
                        <div class="flex justify-between border-t pt-1" style="border-color:rgba(255,255,255,0.1)"><span class="font-bold" x-text="lang==='FR' ? 'TVA Nette Due' : 'Net TVA Due'"></span><span class="text-red-400 font-bold" x-text="fmtXaf(tvaData?.tva_nette_due)"></span></div>
                        <div class="flex justify-between"><span class="opacity-50">CAC (10% TVA)</span><span class="text-red-400" x-text="fmtXaf(tvaData?.cac_net_du)"></span></div>
                        <div class="flex justify-between bg-white/5 rounded-lg px-2 py-1 mt-1"><span class="font-bold">Total à Payer</span><span class="text-red-400 font-bold text-sm" x-text="fmtXaf(tvaData?.total_a_payer)"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- AUDIT LOG PAGE                                                -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='audit-log'" x-cloak class="p-6 space-y-5 float-in" x-data="auditLogPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Journal d\'Audit' : 'Audit Log'"></h2>
                <button @click="load()" class="btn-secondary text-xs px-3 py-2" x-text="lang==='FR' ? 'Actualiser' : 'Refresh'"></button>
            </div>
            <div class="glass rounded-2xl overflow-hidden">
                <table class="w-full text-xs">
                    <thead><tr style="border-bottom:1px solid rgba(255,255,255,0.07)">
                        <th class="text-left px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Date' : 'Date'"></th>
                        <th class="text-left px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Utilisateur' : 'User'"></th>
                        <th class="text-left px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Action' : 'Action'"></th>
                        <th class="text-left px-4 py-2.5 opacity-50">IP</th>
                    </tr></thead>
                    <tbody>
                        <template x-for="log in logs" :key="log.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5">
                                <td class="px-4 py-2 opacity-60 whitespace-nowrap" x-text="log.created_at?.substring(0,19).replace('T',' ')"></td>
                                <td class="px-4 py-2 font-medium" x-text="log.user?.name ?? '—'"></td>
                                <td class="px-4 py-2 font-mono text-sky-300" x-text="log.action"></td>
                                <td class="px-4 py-2 opacity-50" x-text="log.ip_address ?? '—'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="logs.length===0 && !loading" class="py-12 flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucune activité enregistrée.' : 'No audit activity recorded.'"></span>
                </div>
                <div x-show="loading" class="text-center py-8 opacity-40 text-sm">...</div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- FISCAL YEAR CLOSE / OPENING BALANCES PAGE                    -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='fiscal-year'" x-cloak class="p-6 space-y-5 float-in" x-data="fiscalYearPanel()">
            <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Gestion des Exercices Comptables' : 'Fiscal Year Management'"></h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Fiscal Year Close -->
                <div class="glass-card rounded-2xl p-5 space-y-3">
                    <h3 class="font-bold text-sm text-yellow-300" x-text="lang==='FR' ? 'Clôture de l\'exercice' : 'Close Fiscal Year'"></h3>
                    <p class="text-xs opacity-50" x-text="lang==='FR' ? 'Passe le résultat net au report à nouveau (131000 → 121000 ou 129000). Action irréversible.' : 'Carries net profit/loss to retained earnings. Irreversible action.'"></p>
                    <input x-model.number="closeYear" type="number" :placeholder="lang==='FR' ? 'Exercice à clôturer' : 'Fiscal year to close'" class="input" />
                    <div x-show="closeMsg" class="text-xs px-3 py-2 rounded-xl" :class="closeError ? 'text-red-400' : 'text-emerald-400'" x-text="closeMsg"></div>
                    <button @click="closeFiscalYear()" :disabled="closing" class="btn-primary text-xs w-full py-2" style="background:rgba(251,191,36,0.15);border-color:rgba(251,191,36,0.4);">
                        <span x-show="!closing" x-text="lang==='FR' ? 'Clôturer l\'exercice' : 'Close Fiscal Year'"></span>
                        <span x-show="closing">...</span>
                    </button>
                </div>

                <!-- Opening Balances -->
                <div class="glass-card rounded-2xl p-5 space-y-3">
                    <h3 class="font-bold text-sm text-sky-300" x-text="lang==='FR' ? 'Soldes d\'ouverture' : 'Opening Balances'"></h3>
                    <p class="text-xs opacity-50" x-text="lang==='FR' ? 'Importez les soldes de départ lors du passage de votre ancien système. L\'écriture doit être équilibrée.' : 'Import starting balances when migrating from a previous system. Entry must balance.'"></p>
                    <input x-model.number="obYear" type="number" :placeholder="lang==='FR' ? 'Exercice' : 'Fiscal year'" class="input" />
                    <textarea x-model="obJson" rows="5" class="input font-mono text-xs" :placeholder="lang==='FR' ? 'JSON: [{&quot;account_code&quot;:&quot;521100&quot;,&quot;debit&quot;:1000000,&quot;credit&quot;:0},...]' : 'JSON array of {account_code, debit, credit}'"></textarea>
                    <div x-show="obMsg" class="text-xs px-3 py-2 rounded-xl" :class="obError ? 'text-red-400' : 'text-emerald-400'" x-text="obMsg"></div>
                    <button @click="importOpeningBalances()" :disabled="obLoading" class="btn-primary text-xs w-full py-2">
                        <span x-show="!obLoading" x-text="lang==='FR' ? 'Importer Soldes d\'Ouverture' : 'Import Opening Balances'"></span>
                        <span x-show="obLoading">...</span>
                    </button>
                </div>
            </div>

            <!-- CSV Exports -->
            <div class="glass-card rounded-2xl p-5 space-y-3">
                <h3 class="font-bold text-sm" x-text="lang==='FR' ? 'Exports CSV / Excel' : 'CSV / Excel Exports'"></h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-2">
                        <label class="text-xs opacity-50" x-text="lang==='FR' ? 'Période (du — au)' : 'Period (from — to)'"></label>
                        <input x-model="exportFrom" type="date" class="input text-xs" />
                        <input x-model="exportTo" type="date" class="input text-xs" />
                    </div>
                    <div class="space-y-2 flex flex-col justify-end">
                        <button @click="downloadCsv('trial-balance-csv')" class="btn-secondary text-xs py-1.5" x-text="lang==='FR' ? 'Balance Générale CSV' : 'Trial Balance CSV'"></button>
                        <button @click="downloadCsv('journal-csv')" class="btn-secondary text-xs py-1.5" x-text="lang==='FR' ? 'Journal CSV' : 'Journal CSV'"></button>
                        <button @click="downloadCsv('aged-receivables-csv')" class="btn-secondary text-xs py-1.5" x-text="lang==='FR' ? 'Créances CSV' : 'Receivables CSV'"></button>
                        <button @click="downloadCsv('aged-payables-csv')" class="btn-secondary text-xs py-1.5" x-text="lang==='FR' ? 'Dettes CSV' : 'Payables CSV'"></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- CHART OF ACCOUNTS PAGE                                        -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='accounts'" x-cloak class="p-6 space-y-5 float-in" x-data="chartOfAccountsPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Plan Comptable SYSCOHADA' : 'Chart of Accounts'"></h2>
                <button @click="showForm=!showForm" class="btn-primary text-xs px-4 py-2">+ <span x-text="lang==='FR' ? 'Nouveau Compte' : 'New Account'"></span></button>
            </div>

            <div x-show="showForm" x-cloak class="glass-card rounded-2xl p-5 space-y-3 float-in">
                <div class="grid grid-cols-3 gap-3">
                    <input x-model="form.code" :placeholder="lang==='FR' ? 'Code (ex: 621200)' : 'Code (e.g. 621200)'" class="input" maxlength="10" />
                    <input x-model.number="form.class_digit" type="number" min="1" max="9" :placeholder="lang==='FR' ? 'Classe (1-9)' : 'Class (1-9)'" class="input" />
                    <input x-model="form.label" :placeholder="lang==='FR' ? 'Intitulé du compte' : 'Account label'" class="input col-span-3" />
                </div>
                <div x-show="formError" class="text-red-400 text-xs" x-text="formError"></div>
                <button @click="addAccount()" :disabled="submitting" class="btn-primary text-xs w-full py-2">
                    <span x-show="!submitting" x-text="lang==='FR' ? 'Créer' : 'Create'"></span>
                    <span x-show="submitting">...</span>
                </button>
            </div>

            <input x-model="search" :placeholder="lang==='FR' ? 'Rechercher par code ou intitulé...' : 'Search by code or label...'" class="input w-full text-sm" />

            <div class="glass rounded-2xl overflow-hidden" style="max-height:60vh;overflow-y:auto">
                <table class="w-full text-xs">
                    <thead class="sticky top-0" style="background:rgba(15,23,42,0.95)"><tr style="border-bottom:1px solid rgba(255,255,255,0.07)">
                        <th class="text-left px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Code' : 'Code'"></th>
                        <th class="text-left px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Intitulé' : 'Label'"></th>
                        <th class="text-center px-4 py-2.5 opacity-50" x-text="lang==='FR' ? 'Cl.' : 'Cl.'"></th>
                    </tr></thead>
                    <tbody>
                        <template x-for="a in filtered" :key="a.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.03)" class="hover:bg-white/5">
                                <td class="px-4 py-1.5 font-mono text-sky-300" x-text="a.code"></td>
                                <td class="px-4 py-1.5 opacity-80" x-text="a.label"></td>
                                <td class="px-4 py-1.5 text-center opacity-50" x-text="a.class_digit"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <div x-show="filtered.length===0" class="py-12 flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></div>
                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun compte trouvé.' : 'No accounts found.'"></span>
                </div>
            </div>
            <p class="text-xs opacity-40" x-text="filtered.length + ' ' + (lang==='FR' ? 'compte(s) affiché(s)' : 'account(s) shown') + ' / ' + accounts.length + ' total'"></p>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- QUOTATIONS / DEVIS PAGE                                       -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='quotations'" x-cloak class="p-6 space-y-5 float-in" x-data="quotationsPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Devis Clients / Pro-Forma' : 'Customer Quotations'"></h2>
                <button @click="showForm=!showForm" class="btn-primary text-xs px-4 py-2">+ <span x-text="lang==='FR' ? 'Nouveau Devis' : 'New Quotation'"></span></button>
            </div>
            <div x-show="showForm" class="glass-card rounded-2xl p-5 space-y-3">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'Client' : 'Customer'"></label>
                        <select x-model="form.customer_id" class="w-full mt-1 input-field text-sm">
                            <option value="" x-text="lang==='FR' ? '— Choisir —' : '— Select —'"></option>
                            <template x-for="c in customers" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                        </select></div>
                    <div><label class="text-xs opacity-60">Date</label><input x-model="form.quotation_date" type="date" class="w-full mt-1 input-field text-sm"></div>
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'Valable jusqu\'au' : 'Valid until'"></label><input x-model="form.valid_until" type="date" class="w-full mt-1 input-field text-sm"></div>
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'Notes' : 'Notes'"></label><input x-model="form.notes" class="w-full mt-1 input-field text-sm"></div>
                </div>
                <div class="space-y-2">
                    <p class="text-xs opacity-60 font-semibold" x-text="lang==='FR' ? 'Lignes' : 'Lines'"></p>
                    <template x-for="(line, i) in form.lines" :key="i">
                        <div class="flex gap-2 items-center">
                            <input x-model="line.description" :placeholder="lang==='FR' ? 'Description' : 'Description'" class="flex-1 input-field text-xs">
                            <input x-model.number="line.quantity" type="number" min="0.001" step="0.001" placeholder="Qté" class="w-20 input-field text-xs">
                            <input x-model.number="line.unit_price_ht" type="number" min="0" placeholder="PU HT" class="w-28 input-field text-xs">
                            <button @click="form.lines.splice(i,1)" class="text-red-400 text-xs px-2">✕</button>
                        </div>
                    </template>
                    <button @click="form.lines.push({description:'',quantity:1,unit_price_ht:0})" class="text-xs text-sky-400 hover:text-sky-300">+ <span x-text="lang==='FR' ? 'Ajouter ligne' : 'Add line'"></span></button>
                </div>
                <div x-show="formError" class="text-red-400 text-xs" x-text="formError"></div>
                <div class="flex gap-2">
                    <button @click="submitQuotation()" :disabled="saving" class="btn-primary text-sm px-5 py-2" x-text="saving ? '...' : (lang==='FR' ? 'Enregistrer' : 'Save')"></button>
                    <button @click="showForm=false" class="text-xs opacity-60 hover:opacity-100 px-4 py-2" x-text="lang==='FR' ? 'Annuler' : 'Cancel'"></button>
                </div>
            </div>
            <div class="glass-card rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead><tr style="background:rgba(15,23,42,0.9)">
                        <th class="text-left px-4 py-3 opacity-60 text-xs" x-text="lang==='FR' ? 'N° Devis' : 'Ref.'"></th>
                        <th class="text-left px-4 py-3 opacity-60 text-xs" x-text="lang==='FR' ? 'Client' : 'Customer'"></th>
                        <th class="text-left px-4 py-3 opacity-60 text-xs">Date</th>
                        <th class="text-right px-4 py-3 opacity-60 text-xs">TTC</th>
                        <th class="text-left px-4 py-3 opacity-60 text-xs">Statut</th>
                        <th class="text-center px-4 py-3 opacity-60 text-xs">Actions</th>
                    </tr></thead>
                    <tbody>
                        <template x-for="q in quotations" :key="q.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5">
                                <td class="px-4 py-2 font-mono text-sky-300 text-xs" x-text="q.quotation_number"></td>
                                <td class="px-4 py-2 text-xs" x-text="q.customer?.name ?? '—'"></td>
                                <td class="px-4 py-2 text-xs opacity-70" x-text="q.quotation_date"></td>
                                <td class="px-4 py-2 text-right text-xs" x-text="Number(q.amount_ttc).toLocaleString('fr-CM') + ' XAF'"></td>
                                <td class="px-4 py-2 text-xs">
                                    <span :class="{'text-emerald-400':q.status==='ACCEPTED','text-amber-400':q.status==='SENT','text-rose-400':q.status==='REJECTED','text-sky-400':q.status==='CONVERTED','opacity-50':q.status==='DRAFT'}" x-text="q.status"></span>
                                </td>
                                <td class="px-4 py-2 text-center text-xs">
                                    <button x-show="q.status==='ACCEPTED'" @click="convertQuotation(q)" class="text-emerald-400 hover:underline text-xs mr-2" x-text="lang==='FR' ? 'Facturer' : 'Invoice'"></button>
                                    <button x-show="q.status==='DRAFT'" @click="markSent(q)" class="text-amber-400 hover:underline text-xs" x-text="lang==='FR' ? 'Marquer Envoyé' : 'Mark Sent'"></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="quotations.length===0"><td colspan="6" class="text-center py-12">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
                                <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun devis.' : 'No quotations.'"></span>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- PURCHASE ORDERS PAGE                                          -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='purchase-orders'" x-cloak class="p-6 space-y-5 float-in" x-data="purchaseOrdersPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Bons de Commande Fournisseurs' : 'Purchase Orders'"></h2>
                <button @click="showForm=!showForm" class="btn-primary text-xs px-4 py-2">+ <span x-text="lang==='FR' ? 'Nouveau BC' : 'New PO'"></span></button>
            </div>
            <div x-show="showForm" class="glass-card rounded-2xl p-5 space-y-3">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'Fournisseur' : 'Supplier'"></label>
                        <select x-model="form.supplier_id" class="w-full mt-1 input-field text-sm">
                            <option value="" x-text="lang==='FR' ? '— Choisir —' : '— Select —'"></option>
                            <template x-for="s in suppliers" :key="s.id"><option :value="s.id" x-text="s.name"></option></template>
                        </select></div>
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'Date commande' : 'Order date'"></label><input x-model="form.order_date" type="date" class="w-full mt-1 input-field text-sm"></div>
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'Livraison prévue' : 'Expected delivery'"></label><input x-model="form.expected_delivery_date" type="date" class="w-full mt-1 input-field text-sm"></div>
                    <div class="col-span-2"><label class="text-xs opacity-60">Notes</label><input x-model="form.notes" class="w-full mt-1 input-field text-sm"></div>
                </div>
                <div class="space-y-2">
                    <p class="text-xs opacity-60 font-semibold" x-text="lang==='FR' ? 'Lignes' : 'Lines'"></p>
                    <template x-for="(line, i) in form.lines" :key="i">
                        <div class="flex gap-2 items-center">
                            <input x-model="line.description" :placeholder="lang==='FR' ? 'Description' : 'Description'" class="flex-1 input-field text-xs">
                            <input x-model.number="line.quantity" type="number" min="0.001" step="0.001" placeholder="Qté" class="w-20 input-field text-xs">
                            <input x-model.number="line.unit_price_ht" type="number" min="0" placeholder="PU HT" class="w-28 input-field text-xs">
                            <button @click="form.lines.splice(i,1)" class="text-red-400 text-xs px-2">✕</button>
                        </div>
                    </template>
                    <button @click="form.lines.push({description:'',quantity:1,unit_price_ht:0})" class="text-xs text-sky-400 hover:text-sky-300">+ <span x-text="lang==='FR' ? 'Ajouter ligne' : 'Add line'"></span></button>
                </div>
                <div x-show="poError" class="text-red-400 text-xs" x-text="poError"></div>
                <div class="flex gap-2">
                    <button @click="submitPO()" :disabled="saving" class="btn-primary text-sm px-5 py-2" x-text="saving ? '...' : (lang==='FR' ? 'Enregistrer' : 'Save')"></button>
                    <button @click="showForm=false" class="text-xs opacity-60 hover:opacity-100 px-4 py-2" x-text="lang==='FR' ? 'Annuler' : 'Cancel'"></button>
                </div>
            </div>
            <div class="glass-card rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead><tr style="background:rgba(15,23,42,0.9)">
                        <th class="text-left px-4 py-3 opacity-60 text-xs">N° BC</th>
                        <th class="text-left px-4 py-3 opacity-60 text-xs" x-text="lang==='FR' ? 'Fournisseur' : 'Supplier'"></th>
                        <th class="text-left px-4 py-3 opacity-60 text-xs">Date</th>
                        <th class="text-right px-4 py-3 opacity-60 text-xs">TTC</th>
                        <th class="text-left px-4 py-3 opacity-60 text-xs">Statut</th>
                    </tr></thead>
                    <tbody>
                        <template x-for="po in orders" :key="po.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5">
                                <td class="px-4 py-2 font-mono text-sky-300 text-xs" x-text="po.po_number"></td>
                                <td class="px-4 py-2 text-xs" x-text="po.supplier?.name ?? '—'"></td>
                                <td class="px-4 py-2 text-xs opacity-70" x-text="po.order_date"></td>
                                <td class="px-4 py-2 text-right text-xs" x-text="Number(po.amount_ttc).toLocaleString('fr-CM') + ' XAF'"></td>
                                <td class="px-4 py-2 text-xs">
                                    <span :class="{'text-emerald-400':po.status==='RECEIVED','text-amber-400':po.status==='PARTIAL'||po.status==='SENT','text-rose-400':po.status==='CANCELLED','opacity-50':po.status==='DRAFT'}" x-text="po.status"></span>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="orders.length===0"><td colspan="5" class="text-center py-12">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></div>
                                <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun bon de commande.' : 'No purchase orders.'"></span>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- CREDIT NOTES / AVOIRS PAGE                                    -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='credit-notes'" x-cloak class="p-6 space-y-5 float-in" x-data="creditNotesPanel()">
            <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Avoirs (Notes de Crédit)' : 'Credit Notes'"></h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <!-- Customer credit note -->
                <div class="glass-card rounded-2xl p-5 space-y-3">
                    <h3 class="text-sm font-semibold" x-text="lang==='FR' ? 'Avoir Client' : 'Customer Credit Note'"></h3>
                    <div class="space-y-2">
                        <select x-model="cnForm.customer_id" class="w-full input-field text-sm">
                            <option value="" x-text="lang==='FR' ? '— Client —' : '— Customer —'"></option>
                            <template x-for="c in customers" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                        </select>
                        <input x-model="cnForm.credit_note_date" type="date" class="w-full input-field text-sm">
                        <input x-model.number="cnForm.amount_ht" type="number" min="0" :placeholder="lang==='FR' ? 'Montant HT à annuler (XAF)' : 'Amount HT to reverse (XAF)'" class="w-full input-field text-sm">
                        <textarea x-model="cnForm.reason" :placeholder="lang==='FR' ? 'Motif de l\'avoir' : 'Reason'" class="w-full input-field text-sm" rows="2"></textarea>
                    </div>
                    <div x-show="cnError" class="text-red-400 text-xs" x-text="cnError"></div>
                    <button @click="submitCustomerCN()" :disabled="cnSaving" class="btn-primary text-sm px-5 py-2 w-full" x-text="cnSaving ? '...' : (lang==='FR' ? 'Émettre l\'avoir' : 'Issue Credit Note')"></button>
                    <div x-show="cnSuccess" class="text-emerald-400 text-xs" x-text="cnSuccess"></div>
                </div>
                <!-- Supplier credit note -->
                <div class="glass-card rounded-2xl p-5 space-y-3">
                    <h3 class="text-sm font-semibold" x-text="lang==='FR' ? 'Avoir Fournisseur' : 'Supplier Credit Note'"></h3>
                    <div class="space-y-2">
                        <select x-model="snForm.supplier_id" class="w-full input-field text-sm">
                            <option value="" x-text="lang==='FR' ? '— Fournisseur —' : '— Supplier —'"></option>
                            <template x-for="s in suppliers" :key="s.id"><option :value="s.id" x-text="s.name"></option></template>
                        </select>
                        <input x-model="snForm.credit_note_date" type="date" class="w-full input-field text-sm">
                        <input x-model.number="snForm.amount_ht" type="number" min="0" :placeholder="lang==='FR' ? 'Montant HT (XAF)' : 'Amount HT (XAF)'" class="w-full input-field text-sm">
                        <textarea x-model="snForm.reason" :placeholder="lang==='FR' ? 'Motif' : 'Reason'" class="w-full input-field text-sm" rows="2"></textarea>
                    </div>
                    <div x-show="snError" class="text-red-400 text-xs" x-text="snError"></div>
                    <button @click="submitSupplierCN()" :disabled="snSaving" class="btn-primary text-sm px-5 py-2 w-full" x-text="snSaving ? '...' : (lang==='FR' ? 'Enregistrer l\'avoir' : 'Record Credit Note')"></button>
                    <div x-show="snSuccess" class="text-emerald-400 text-xs" x-text="snSuccess"></div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- PATENTE PAGE                                                   -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='patente'" x-cloak class="p-6 space-y-5 float-in" x-data="patentePanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Patente & Taxe d\'Affaires' : 'Business Licence Tax'"></h2>
                <button @click="showForm=!showForm" class="btn-primary text-xs px-4 py-2">+ <span x-text="lang==='FR' ? 'Nouvelle Patente' : 'New Record'"></span></button>
            </div>
            <div x-show="showForm" class="glass-card rounded-2xl p-5 space-y-3">
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'Exercice' : 'Tax year'"></label><input x-model.number="form.tax_year" type="number" min="2020" max="2099" class="w-full mt-1 input-field text-sm" :placeholder="new Date().getFullYear()"></div>
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'N° Patente' : 'Patente no.'"></label><input x-model="form.patente_number" class="w-full mt-1 input-field text-sm"></div>
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'Montant dû (XAF)' : 'Amount due (XAF)'"></label><input x-model.number="form.amount_due_xaf" type="number" min="0" class="w-full mt-1 input-field text-sm"></div>
                    <div><label class="text-xs opacity-60" x-text="lang==='FR' ? 'Échéance' : 'Due date'"></label><input x-model="form.due_date" type="date" class="w-full mt-1 input-field text-sm"></div>
                    <div><label class="text-xs opacity-60">Notes</label><input x-model="form.notes" class="w-full mt-1 input-field text-sm"></div>
                </div>
                <div x-show="formError" class="text-red-400 text-xs" x-text="formError"></div>
                <div class="flex gap-2">
                    <button @click="submitPatente()" :disabled="saving" class="btn-primary text-sm px-5 py-2" x-text="saving ? '...' : (lang==='FR' ? 'Enregistrer' : 'Save')"></button>
                    <button @click="showForm=false" class="text-xs opacity-60 hover:opacity-100 px-4 py-2">Annuler</button>
                </div>
            </div>
            <div class="glass-card rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead><tr style="background:rgba(15,23,42,0.9)">
                        <th class="text-left px-4 py-3 opacity-60 text-xs" x-text="lang==='FR' ? 'Exercice' : 'Year'"></th>
                        <th class="text-left px-4 py-3 opacity-60 text-xs">N° Patente</th>
                        <th class="text-right px-4 py-3 opacity-60 text-xs" x-text="lang==='FR' ? 'Montant Dû' : 'Due'"></th>
                        <th class="text-right px-4 py-3 opacity-60 text-xs" x-text="lang==='FR' ? 'Payé' : 'Paid'"></th>
                        <th class="text-left px-4 py-3 opacity-60 text-xs">Statut</th>
                        <th class="text-center px-4 py-3 opacity-60 text-xs">Actions</th>
                    </tr></thead>
                    <tbody>
                        <template x-for="p in records" :key="p.id">
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.04)" class="hover:bg-white/5">
                                <td class="px-4 py-2 font-semibold" x-text="p.tax_year"></td>
                                <td class="px-4 py-2 text-xs font-mono opacity-70" x-text="p.patente_number ?? '—'"></td>
                                <td class="px-4 py-2 text-right text-xs" x-text="Number(p.amount_due_xaf).toLocaleString('fr-CM') + ' XAF'"></td>
                                <td class="px-4 py-2 text-right text-xs text-emerald-400" x-text="Number(p.amount_paid_xaf).toLocaleString('fr-CM') + ' XAF'"></td>
                                <td class="px-4 py-2 text-xs">
                                    <span :class="{'text-emerald-400':p.status==='PAID','text-rose-400':p.status==='OVERDUE','text-amber-400':p.status==='PENDING'}" x-text="p.status"></span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button x-show="p.status!=='PAID'" @click="payPatente(p)" class="text-amber-400 hover:underline text-xs" x-text="lang==='FR' ? 'Payer' : 'Pay'"></button>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="records.length===0"><td colspan="6" class="text-center py-12">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg></div>
                                <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun enregistrement.' : 'No records.'"></span>
                            </div>
                        </td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- STOCK / INVENTORY PAGE                                        -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <div x-show="page==='stock'" x-cloak class="p-6 space-y-5 float-in" x-data="stockPanel()">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold" x-text="lang==='FR' ? 'Gestion des Stocks' : 'Inventory Management'"></h2>
                <button @click="showForm=!showForm" class="btn-primary text-xs px-4 py-2">+ <span x-text="lang==='FR' ? 'Mouvement' : 'Movement'"></span></button>
            </div>

            <!-- New movement form -->
            <div x-show="showForm" class="glass-card rounded-2xl p-5 space-y-3">
                <h3 class="text-sm font-semibold opacity-70" x-text="lang==='FR' ? 'Enregistrer un mouvement de stock' : 'Record stock movement'"></h3>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Code Produit' : 'Product Code'"></label>
                        <input x-model="form.product_code" class="w-full mt-1 input-field text-sm" placeholder="ex: PROD-001">
                    </div>
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Désignation' : 'Product Name'"></label>
                        <input x-model="form.product_name" class="w-full mt-1 input-field text-sm">
                    </div>
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Compte Stock (Cl.3)' : 'Stock Account (Cl.3)'"></label>
                        <input x-model="form.account_code" class="w-full mt-1 input-field text-sm font-mono" placeholder="310000">
                    </div>
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Type de Mouvement' : 'Movement Type'"></label>
                        <select x-model="form.movement_type" class="w-full mt-1 input-field text-sm">
                            <option value="IN" x-text="lang==='FR' ? 'Entrée (IN)' : 'In'"></option>
                            <option value="OUT" x-text="lang==='FR' ? 'Sortie (OUT)' : 'Out'"></option>
                            <option value="ADJUSTMENT" x-text="lang==='FR' ? 'Ajustement' : 'Adjustment'"></option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Quantité' : 'Quantity'"></label>
                        <input x-model.number="form.quantity" type="number" step="0.001" min="0" class="w-full mt-1 input-field text-sm">
                    </div>
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Coût Unitaire (XAF)' : 'Unit Cost (XAF)'"></label>
                        <input x-model.number="form.unit_cost_xaf" type="number" min="0" class="w-full mt-1 input-field text-sm">
                    </div>
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Date' : 'Date'"></label>
                        <input x-model="form.movement_date" type="date" class="w-full mt-1 input-field text-sm">
                    </div>
                    <div>
                        <label class="text-xs opacity-60" x-text="lang==='FR' ? 'Référence' : 'Reference'"></label>
                        <input x-model="form.reference" class="w-full mt-1 input-field text-sm">
                    </div>
                    <div class="flex items-end pb-1 gap-2">
                        <label class="flex items-center gap-1 text-xs cursor-pointer">
                            <input type="checkbox" x-model="form.post_to_gl" class="accent-amber-400">
                            <span x-text="lang==='FR' ? 'Passer en GL' : 'Post to GL'"></span>
                        </label>
                    </div>
                </div>
                <div x-show="formError" class="text-red-400 text-xs" x-text="formError"></div>
                <div class="flex gap-2">
                    <button @click="submitMovement()" :disabled="saving" class="btn-primary text-sm px-5 py-2" x-text="saving ? '...' : (lang==='FR' ? 'Enregistrer' : 'Save')"></button>
                    <button @click="showForm=false" class="text-xs opacity-60 hover:opacity-100 px-4 py-2" x-text="lang==='FR' ? 'Annuler' : 'Cancel'"></button>
                </div>
            </div>

            <!-- Valuation summary -->
            <div class="glass-card rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold opacity-70" x-text="lang==='FR' ? 'Valorisation du Stock' : 'Stock Valuation'"></h3>
                    <span class="text-base font-bold text-amber-400" x-text="totalValue.toLocaleString('fr-CM') + ' XAF'"></span>
                </div>
                <div style="overflow-x:auto">
                    <table class="w-full text-sm">
                        <thead><tr style="border-bottom:1px solid rgba(255,255,255,0.07)">
                            <th class="text-left px-3 py-2 opacity-50" x-text="lang==='FR' ? 'Code' : 'Code'"></th>
                            <th class="text-left px-3 py-2 opacity-50" x-text="lang==='FR' ? 'Désignation' : 'Product'"></th>
                            <th class="text-left px-3 py-2 opacity-50" x-text="lang==='FR' ? 'Compte' : 'Account'"></th>
                            <th class="text-right px-3 py-2 opacity-50" x-text="lang==='FR' ? 'Qté en stock' : 'Qty in stock'"></th>
                            <th class="text-right px-3 py-2 opacity-50" x-text="lang==='FR' ? 'Valeur XAF' : 'Value XAF'"></th>
                        </tr></thead>
                        <tbody>
                            <template x-for="item in valuation" :key="item.product_code">
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.03)" class="hover:bg-white/5 cursor-pointer" @click="loadLedger(item.product_code)">
                                    <td class="px-3 py-1.5 font-mono text-sky-300" x-text="item.product_code"></td>
                                    <td class="px-3 py-1.5" x-text="item.product_name"></td>
                                    <td class="px-3 py-1.5 font-mono opacity-60" x-text="item.account_code"></td>
                                    <td class="px-3 py-1.5 text-right" x-text="Number(item.qty_in_stock).toLocaleString('fr-CM')"></td>
                                    <td class="px-3 py-1.5 text-right font-semibold" x-text="Number(item.stock_value).toLocaleString('fr-CM') + ' XAF'"></td>
                                </tr>
                            </template>
                            <tr x-show="valuation.length===0"><td colspan="5" class="text-center py-12">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><line x1="16.5" y1="9.4" x2="7.5" y2="4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></div>
                                    <span class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR' ? 'Aucun stock enregistré.' : 'No stock recorded.'"></span>
                                </div>
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Stock card (ledger) for selected product -->
            <div x-show="ledger" class="glass-card rounded-2xl p-5 space-y-3">
                <h3 class="text-sm font-semibold" x-text="(lang==='FR' ? 'Fiche de Stock — ' : 'Stock Card — ') + (ledger?.product_name ?? '')"></h3>
                <div class="flex gap-4 text-xs opacity-70">
                    <span x-text="(lang==='FR' ? 'Qté : ' : 'Qty: ') + ledger?.current_qty"></span>
                    <span x-text="(lang==='FR' ? 'CMUP : ' : 'Avg cost: ') + Number(ledger?.avg_cost_xaf).toLocaleString('fr-CM') + ' XAF'"></span>
                    <span x-text="(lang==='FR' ? 'Valeur : ' : 'Value: ') + Number(ledger?.stock_value).toLocaleString('fr-CM') + ' XAF'"></span>
                </div>
                <div style="overflow-x:auto">
                    <table class="w-full text-xs">
                        <thead><tr style="border-bottom:1px solid rgba(255,255,255,0.07)">
                            <th class="text-left px-3 py-2 opacity-50">Date</th>
                            <th class="text-left px-3 py-2 opacity-50">Type</th>
                            <th class="text-left px-3 py-2 opacity-50">Réf.</th>
                            <th class="text-right px-3 py-2 opacity-50">Qté</th>
                            <th class="text-right px-3 py-2 opacity-50">PU XAF</th>
                            <th class="text-right px-3 py-2 opacity-50">Stock</th>
                            <th class="text-right px-3 py-2 opacity-50">CMUP</th>
                        </tr></thead>
                        <tbody>
                            <template x-for="m in ledger.movements" :key="m.id">
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.03)">
                                    <td class="px-3 py-1" x-text="m.movement_date"></td>
                                    <td class="px-3 py-1" :class="m.movement_type==='IN' ? 'text-emerald-400' : m.movement_type==='OUT' ? 'text-rose-400' : 'text-amber-400'" x-text="m.movement_type"></td>
                                    <td class="px-3 py-1 opacity-60" x-text="m.reference ?? '—'"></td>
                                    <td class="px-3 py-1 text-right" x-text="Number(m.quantity).toLocaleString('fr-CM')"></td>
                                    <td class="px-3 py-1 text-right" x-text="Number(m.unit_cost_xaf).toLocaleString('fr-CM')"></td>
                                    <td class="px-3 py-1 text-right font-mono" x-text="Number(m.balance_qty).toLocaleString('fr-CM')"></td>
                                    <td class="px-3 py-1 text-right" x-text="Number(m.avg_cost_xaf).toLocaleString('fr-CM')"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- CRM PAGE                                                   -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='crm'" x-cloak class="p-6 space-y-5 float-in" x-data="crmPanel()" x-init="init()">

            <!-- Header + view tabs -->
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wide">CRM</h2>
                    <p class="text-xs text-slate-400 mt-1" x-text="lang==='FR' ? 'Pipeline commercial — du lead à la facture' : 'Sales pipeline — lead to invoice'"></p>
                </div>
                <button @click="openAdd()" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest flex items-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span x-text="lang==='FR' ? 'Ajouter un Lead' : 'Add Lead'"></span>
                </button>
            </div>

            <!-- KPI stats -->
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="glass-card rounded-2xl p-4">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Leads actifs':'Active leads'"></div>
                    <div class="text-2xl font-black text-white mt-1" x-text="stats.active_leads ?? 0"></div>
                </div>
                <div class="glass-card rounded-2xl p-4">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Valeur pipeline':'Pipeline value'"></div>
                    <div class="text-2xl font-black text-amber-400 mt-1" x-text="fmtXaf(stats.pipeline_value ?? 0)"></div>
                </div>
                <div class="glass-card rounded-2xl p-4">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Taux de conversion':'Conversion rate'"></div>
                    <div class="text-2xl font-black text-emerald-400 mt-1" x-text="(stats.conversion_rate ?? 0) + ' %'"></div>
                </div>
                <div class="glass-card rounded-2xl p-4">
                    <div class="text-[10px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Gagnés ce mois':'Won this month'"></div>
                    <div class="text-2xl font-black text-white mt-1"><span x-text="stats.won_this_month ?? 0"></span>
                        <span class="text-xs text-slate-400" x-text="'· ' + fmtXaf(stats.won_value_month ?? 0)"></span></div>
                </div>
            </div>

            <!-- View switcher -->
            <div class="flex gap-1">
                <template x-for="v in [['pipeline','Pipeline'],['list', lang==='FR'?'Liste':'List'],['activities', lang==='FR'?'Activités':'Activities']]" :key="v[0]">
                    <button @click="view=v[0]"
                            class="px-4 py-2 rounded-xl text-[11px] font-black uppercase tracking-wider transition-all"
                            :style="view===v[0] ? 'background:rgba(201,155,14,0.15);color:#C99B0E;border:1px solid rgba(201,155,14,0.4)' : 'background:rgba(255,255,255,0.04);color:rgba(148,163,184,0.85);border:1px solid rgba(255,255,255,0.1)'"
                            x-text="v[1]"></button>
                </template>
            </div>

            <!-- PIPELINE (kanban) -->
            <div x-show="view==='pipeline'" class="overflow-x-auto pb-2">
                <div class="flex gap-3 min-w-max">
                    <template x-for="col in columns" :key="col.key">
                        <div class="w-64 shrink-0">
                            <div class="flex items-center justify-between px-1 mb-2">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest" :style="col.badge" x-text="col.label"></span>
                                <span class="text-[10px] font-black text-slate-500" x-text="leadsByStatus(col.key).length"></span>
                            </div>
                            <div class="space-y-2 min-h-[80px]">
                                <template x-for="lead in leadsByStatus(col.key)" :key="lead.id">
                                    <div class="glass-card rounded-xl p-3 space-y-2">
                                        <div class="font-black text-white text-sm truncate" x-text="lead.contact_name"></div>
                                        <div class="text-[11px] text-slate-400 truncate" x-text="lead.company_name || '—'"></div>
                                        <div class="text-amber-400 font-black text-sm" x-text="fmtXaf(lead.estimated_value)"></div>
                                        <div class="text-[9px] text-slate-500" x-text="daysInStage(lead)"></div>
                                        <div class="flex items-center gap-1.5 pt-1">
                                            <select @change="moveStage(lead, $event.target.value); $event.target.value=''"
                                                    class="input-field text-[10px] py-1 px-2 flex-1">
                                                <option value="" x-text="lang==='FR'?'Déplacer…':'Move…'"></option>
                                                <template x-for="c in columns" :key="c.key">
                                                    <option :value="c.key" x-show="c.key!==lead.status" x-text="c.label"></option>
                                                </template>
                                            </select>
                                            <button @click="openActivity(lead)" :title="lang==='FR'?'Activité':'Activity'" class="p-1.5 rounded-lg text-slate-400 hover:text-white" style="background:rgba(255,255,255,0.05)">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                            </button>
                                            <a :href="waLink(lead)" target="_blank" x-show="lead.contact_phone" title="WhatsApp" class="p-1.5 rounded-lg text-emerald-400 hover:text-emerald-300" style="background:rgba(16,185,129,0.1)">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="leadsByStatus(col.key).length===0" class="flex flex-col items-center gap-1.5 py-6 opacity-30">
                                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-dasharray="3 3"><circle cx="12" cy="12" r="10"/></svg>
                                    <span class="text-[10px] uppercase tracking-widest" x-text="lang==='FR'?'Aucun lead':'No leads'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- LIST -->
            <div x-show="view==='list'" class="glass-card rounded-2xl overflow-hidden">
                <div class="flex flex-wrap gap-2 p-3 border-b" style="border-color:rgba(255,255,255,0.08)">
                    <input x-model="search" @input.debounce.400ms="load()" class="input-field text-xs flex-1 min-w-[160px]" :placeholder="lang==='FR'?'Rechercher…':'Search…'">
                    <select x-model="filterStatus" @change="load()" class="input-field text-xs">
                        <option value="" x-text="lang==='FR'?'Tous statuts':'All statuses'"></option>
                        <template x-for="c in columns" :key="c.key"><option :value="c.key" x-text="c.label"></option></template>
                    </select>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead><tr class="text-[9px] font-black uppercase tracking-widest text-slate-500" style="border-bottom:1px solid rgba(255,255,255,0.08)">
                            <th class="py-2.5 px-4">Contact</th><th class="py-2.5 px-3" x-text="lang==='FR'?'Entreprise':'Company'"></th>
                            <th class="py-2.5 px-3" x-text="lang==='FR'?'Valeur':'Value'"></th><th class="py-2.5 px-3">Source</th>
                            <th class="py-2.5 px-3" x-text="lang==='FR'?'Statut':'Status'"></th><th class="py-2.5 px-3"></th>
                        </tr></thead>
                        <tbody>
                            <template x-for="lead in leads" :key="lead.id">
                                <tr class="glass-row">
                                    <td class="py-2.5 px-4 font-bold text-white" x-text="lead.contact_name"></td>
                                    <td class="py-2.5 px-3 text-slate-400" x-text="lead.company_name || '—'"></td>
                                    <td class="py-2.5 px-3 text-amber-400 font-black" x-text="fmtXaf(lead.estimated_value)"></td>
                                    <td class="py-2.5 px-3 text-slate-400" x-text="lead.source"></td>
                                    <td class="py-2.5 px-3"><span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase" :style="statusBadge(lead.status)" x-text="statusLabel(lead.status)"></span></td>
                                    <td class="py-2.5 px-3 text-right">
                                        <a :href="waLink(lead)" target="_blank" x-show="lead.contact_phone" class="text-emerald-400 text-[10px] font-black uppercase mr-2">WhatsApp</a>
                                        <button @click="openActivity(lead)" class="text-slate-400 hover:text-white text-[10px] font-black uppercase" x-text="lang==='FR'?'+ Activité':'+ Activity'"></button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="leads.length===0"><td colspan="6" class="text-center py-10 opacity-40 text-xs" x-text="lang==='FR'?'Aucun lead.':'No leads.'"></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ACTIVITIES -->
            <div x-show="view==='activities'" class="glass-card rounded-2xl p-5 space-y-3">
                <template x-for="a in activities" :key="a.id">
                    <div class="flex items-start gap-3 pb-3 border-b" style="border-color:rgba(255,255,255,0.06)">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-amber-400" style="background:rgba(201,155,14,0.1)">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-xs text-slate-200" x-text="a.description"></div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                <span class="text-amber-400 font-black uppercase" x-text="a.type"></span> ·
                                <span x-text="a.lead?.contact_name"></span> ·
                                <span x-text="a.user?.name"></span> ·
                                <span x-text="fmtDate(a.created_at)"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="activities.length===0" class="text-center py-10 opacity-40 text-xs" x-text="lang==='FR'?'Aucune activité.':'No activities.'"></div>
            </div>

            <!-- Add Lead modal -->
            <div x-show="showAdd" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" style="background:rgba(5,13,26,0.7);backdrop-filter:blur(6px)" @click.self="showAdd=false">
                <div class="glass-card rounded-2xl p-6 w-full max-w-md space-y-3">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" x-text="lang==='FR'?'Nouveau Lead':'New Lead'"></h3>
                    <div x-show="addError" x-cloak class="px-3 py-2 rounded-lg text-xs font-bold" style="background:rgba(244,63,94,0.12);color:rgb(252,165,165)" x-text="addError"></div>
                    <input x-model="addForm.contact_name" class="input-field" :placeholder="(lang==='FR'?'Nom du contact':'Contact name')+' *'">
                    <div class="grid grid-cols-2 gap-2">
                        <input x-model="addForm.contact_phone" class="input-field" :placeholder="lang==='FR'?'Téléphone':'Phone'">
                        <input x-model="addForm.contact_email" class="input-field" placeholder="Email">
                    </div>
                    <input x-model="addForm.company_name" class="input-field" :placeholder="lang==='FR'?'Entreprise':'Company'">
                    <div class="grid grid-cols-2 gap-2">
                        <select x-model="addForm.source" class="input-field">
                            <option value="referral" x-text="lang==='FR'?'Recommandation':'Referral'"></option>
                            <option value="cold_call" x-text="lang==='FR'?'Appel à froid':'Cold call'"></option>
                            <option value="walk_in" x-text="lang==='FR'?'Visite':'Walk-in'"></option>
                            <option value="social_media" x-text="lang==='FR'?'Réseaux sociaux':'Social media'"></option>
                            <option value="other" x-text="lang==='FR'?'Autre':'Other'"></option>
                        </select>
                        <input x-model="addForm.estimated_value" type="number" class="input-field" :placeholder="lang==='FR'?'Valeur (XAF)':'Value (XAF)'">
                    </div>
                    <textarea x-model="addForm.notes" rows="2" class="input-field" :placeholder="lang==='FR'?'Notes':'Notes'"></textarea>
                    <div class="flex gap-2 pt-1">
                        <button @click="saveLead()" :disabled="addSaving" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest flex-1 disabled:opacity-50" x-text="addSaving?'…':(lang==='FR'?'Créer':'Create')"></button>
                        <button @click="showAdd=false" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR'?'Annuler':'Cancel'"></button>
                    </div>
                </div>
            </div>

            <!-- Activity modal -->
            <div x-show="showActivity" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" style="background:rgba(5,13,26,0.7);backdrop-filter:blur(6px)" @click.self="showActivity=false">
                <div class="glass-card rounded-2xl p-6 w-full max-w-md space-y-3">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest"><span x-text="lang==='FR'?'Activité':'Activity'"></span> · <span class="text-amber-400" x-text="activityLead?.contact_name"></span></h3>
                    <select x-model="activityForm.type" class="input-field">
                        <option value="call" x-text="lang==='FR'?'Appel':'Call'"></option>
                        <option value="meeting" x-text="lang==='FR'?'Réunion':'Meeting'"></option>
                        <option value="email">Email</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="note" x-text="lang==='FR'?'Note':'Note'"></option>
                    </select>
                    <textarea x-model="activityForm.description" rows="3" class="input-field" :placeholder="lang==='FR'?'Description':'Description'"></textarea>
                    <div class="flex gap-2">
                        <button @click="saveActivity()" :disabled="activitySaving" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest flex-1 disabled:opacity-50" x-text="activitySaving?'…':(lang==='FR'?'Enregistrer':'Save')"></button>
                        <button @click="showActivity=false" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR'?'Annuler':'Cancel'"></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- PROJECTS PAGE                                              -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='projects'" x-cloak class="p-6 space-y-5 float-in" x-data="projectsPanel()" x-init="init()">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wide" x-text="lang==='FR'?'Projets':'Projects'"></h2>
                    <p class="text-xs text-slate-400 mt-1" x-text="lang==='FR'?'Comptabilité par projet — coûts, revenus, rentabilité':'Project accounting — costs, revenue, profitability'"></p>
                </div>
                <button @click="openAdd()" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest flex items-center gap-2">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span x-text="lang==='FR'?'Nouveau Projet':'New Project'"></span>
                </button>
            </div>

            <!-- Project cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <template x-for="p in projects" :key="p.id">
                    <div class="glass-card rounded-2xl p-5 space-y-3 cursor-pointer" @click="openProject(p)">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <div class="text-lg font-black text-white truncate" x-text="p.name"></div>
                                <div class="text-[11px] text-slate-400 truncate"><span x-text="p.client?.name || (lang==='FR'?'Sans client':'No client')"></span></div>
                            </div>
                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-widest text-amber-400" style="background:rgba(201,155,14,0.12);border:1px solid rgba(201,155,14,0.3)" x-text="p.code"></span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase" :style="statusBadge(p.status)" x-text="p.status"></span>
                            </div>
                        </div>
                        <!-- budget progress -->
                        <div x-show="p.budget_amount">
                            <div class="flex justify-between text-[10px] text-slate-500 mb-1">
                                <span x-text="lang==='FR'?'Coûts / Budget':'Costs / Budget'"></span>
                                <span x-text="fmtXaf(p.costs)+' / '+fmtXaf(p.budget_amount)"></span>
                            </div>
                            <div class="h-2 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.08)">
                                <div class="h-full rounded-full" :style="'width:'+Math.min(100,(p.budget_amount? p.costs/p.budget_amount*100:0))+'%;background:'+(p.costs>p.budget_amount?'rgb(244,63,94)':'rgb(201,155,14)')"></div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 pt-1">
                            <div><div class="text-[9px] uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Revenus':'Revenue'"></div><div class="text-xs font-black text-white" x-text="fmtXaf(p.revenue)"></div></div>
                            <div><div class="text-[9px] uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Coûts':'Costs'"></div><div class="text-xs font-black text-white" x-text="fmtXaf(p.costs)"></div></div>
                            <div><div class="text-[9px] uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Bénéfice':'Profit'"></div><div class="text-xs font-black" :class="p.profit>=0?'text-emerald-400':'text-red-400'" x-text="fmtXaf(p.profit)"></div></div>
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="projects.length===0" class="glass-card rounded-2xl py-16 flex flex-col items-center gap-3">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-amber-400/70" style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.18)">
                    <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                </div>
                <p class="text-[11px] font-black uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Aucun projet':'No projects'"></p>
                <p class="text-xs text-slate-600" x-text="lang==='FR'?'Créez votre premier projet pour suivre coûts et revenus.':'Create your first project to track costs and revenue.'"></p>
            </div>

            <!-- Add project modal -->
            <div x-show="showAdd" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" style="background:rgba(5,13,26,0.7);backdrop-filter:blur(6px)" @click.self="showAdd=false">
                <div class="glass-card rounded-2xl p-6 w-full max-w-md space-y-3">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest" x-text="lang==='FR'?'Nouveau Projet':'New Project'"></h3>
                    <div x-show="addError" x-cloak class="px-3 py-2 rounded-lg text-xs font-bold" style="background:rgba(244,63,94,0.12);color:rgb(252,165,165)" x-text="addError"></div>
                    <input x-model="addForm.name" class="input-field" :placeholder="(lang==='FR'?'Nom du projet':'Project name')+' *'">
                    <textarea x-model="addForm.description" rows="2" class="input-field" :placeholder="lang==='FR'?'Description':'Description'"></textarea>
                    <div class="grid grid-cols-2 gap-2">
                        <input x-model="addForm.start_date" type="date" class="input-field">
                        <input x-model="addForm.end_date" type="date" class="input-field">
                        <input x-model="addForm.contract_value" type="number" class="input-field" :placeholder="lang==='FR'?'Valeur contrat (XAF)':'Contract value'">
                        <input x-model="addForm.budget_amount" type="number" class="input-field" :placeholder="lang==='FR'?'Budget (XAF)':'Budget'">
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button @click="saveProject()" :disabled="addSaving" class="glass-btn-amber px-4 py-2 rounded-xl text-xs uppercase tracking-widest flex-1 disabled:opacity-50" x-text="addSaving?'…':(lang==='FR'?'Créer':'Create')"></button>
                        <button @click="showAdd=false" class="glass-btn-dark px-4 py-2 rounded-xl text-xs uppercase tracking-widest" x-text="lang==='FR'?'Annuler':'Cancel'"></button>
                    </div>
                </div>
            </div>

            <!-- Project detail modal -->
            <div x-show="showDetail" x-cloak class="fixed inset-0 z-[80] flex items-start justify-center p-4 overflow-y-auto" style="background:rgba(5,13,26,0.7);backdrop-filter:blur(6px)" @click.self="showDetail=false">
                <div class="glass-card rounded-2xl p-6 w-full max-w-2xl space-y-4 my-8" x-show="detail">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-black text-white" x-text="detail?.project?.name"></h3>
                            <span class="text-[10px] text-amber-400 font-black" x-text="detail?.project?.code"></span>
                        </div>
                        <button @click="showDetail=false" class="text-slate-400 hover:text-white"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
                    </div>
                    <!-- KPIs -->
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl p-3" style="background:rgba(255,255,255,0.04)"><div class="text-[9px] uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Revenus':'Revenue'"></div><div class="text-sm font-black text-white" x-text="fmtXaf(detail?.project?.revenue)"></div></div>
                        <div class="rounded-xl p-3" style="background:rgba(255,255,255,0.04)"><div class="text-[9px] uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Coûts':'Costs'"></div><div class="text-sm font-black text-white" x-text="fmtXaf(detail?.project?.costs)"></div></div>
                        <div class="rounded-xl p-3" style="background:rgba(255,255,255,0.04)"><div class="text-[9px] uppercase tracking-widest text-slate-500" x-text="lang==='FR'?'Bénéfice':'Profit'"></div><div class="text-sm font-black" :class="(detail?.project?.profit>=0)?'text-emerald-400':'text-red-400'" x-text="fmtXaf(detail?.project?.profit)+' ('+(detail?.project?.margin)+'%)'"></div></div>
                    </div>
                    <!-- Add entry -->
                    <div class="rounded-xl p-3 space-y-2" style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08)">
                        <div class="text-[10px] font-black uppercase tracking-widest text-amber-400" x-text="lang==='FR'?'Ajouter une ligne':'Add entry'"></div>
                        <div class="grid grid-cols-2 gap-2">
                            <select x-model="entryForm.direction" class="input-field text-xs">
                                <option value="revenue" x-text="lang==='FR'?'Revenu':'Revenue'"></option>
                                <option value="cost" x-text="lang==='FR'?'Coût':'Cost'"></option>
                            </select>
                            <select x-model="entryForm.entry_type" class="input-field text-xs">
                                <option value="invoice" x-text="lang==='FR'?'Facture':'Invoice'"></option>
                                <option value="supplier_invoice" x-text="lang==='FR'?'Fournisseur':'Supplier'"></option>
                                <option value="payroll_cost" x-text="lang==='FR'?'Paie':'Payroll'"></option>
                                <option value="journal_entry">Journal</option>
                                <option value="expense" x-text="lang==='FR'?'Dépense':'Expense'"></option>
                            </select>
                            <input x-model="entryForm.description" class="input-field text-xs col-span-2" :placeholder="lang==='FR'?'Description':'Description'">
                            <input x-model="entryForm.amount" type="number" class="input-field text-xs" :placeholder="lang==='FR'?'Montant (XAF)':'Amount'">
                            <button @click="addEntry()" :disabled="entrySaving" class="glass-btn-amber px-3 py-2 rounded-xl text-[10px] uppercase tracking-widest disabled:opacity-50" x-text="entrySaving?'…':(lang==='FR'?'Ajouter':'Add')"></button>
                        </div>
                    </div>
                    <!-- Transactions -->
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2" x-text="lang==='FR'?'Transactions':'Transactions'"></div>
                        <div class="space-y-1 max-h-52 overflow-y-auto">
                            <template x-for="e in (detail?.entries||[])" :key="e.id">
                                <div class="flex items-center justify-between text-xs py-1.5 px-2 rounded-lg" style="background:rgba(255,255,255,0.03)">
                                    <span class="text-slate-300 truncate flex-1" x-text="e.description"></span>
                                    <span class="text-[9px] uppercase tracking-wider text-slate-500 mx-2" x-text="e.entry_type"></span>
                                    <span class="font-black shrink-0" :class="e.amount>=0?'text-emerald-400':'text-red-400'" x-text="fmtXaf(e.amount)"></span>
                                </div>
                            </template>
                            <div x-show="(detail?.entries||[]).length===0" class="text-center py-4 opacity-40 text-xs" x-text="lang==='FR'?'Aucune transaction.':'No transactions.'"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════ -->
        <!-- AI ASSISTANT PAGE                                          -->
        <!-- ══════════════════════════════════════════════════════════ -->
        <div x-show="page==='ai-assistant'" x-cloak class="p-6 float-in flex flex-col" style="height:calc(100vh - 0px)" x-data="aiPanel()" x-init="init()">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgb(201,155,14)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l1.9 4.6L18.5 9.5l-4.6 1.9L12 16l-1.9-4.6L5.5 9.5l4.6-1.9L12 3z"/></svg>
                    <h2 class="text-2xl font-black text-white uppercase tracking-wide" x-text="lang==='FR'?'Assistant IA':'AI Assistant'"></h2>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest"
                      :style="aiStatus.mode==='online' ? 'background:rgba(16,185,129,0.2);color:rgb(110,231,183);border:1px solid rgba(16,185,129,0.3)' : aiStatus.mode==='offline' ? 'background:rgba(201,155,14,0.2);color:#E3B420;border:1px solid rgba(201,155,14,0.3)' : 'background:rgba(100,116,139,0.2);color:rgb(148,163,184);border:1px solid rgba(100,116,139,0.3)'"
                      x-text="aiStatus.mode==='online'?'IA: En ligne':(aiStatus.mode==='offline'?'IA: Hors ligne':(lang==='FR'?'IA: Indisponible':'AI: Unavailable'))"></span>
            </div>

            <!-- Unavailable banner -->
            <div x-show="aiStatus.mode==='unavailable'" x-cloak class="glass-card rounded-xl p-4 mb-4 text-xs text-slate-300" style="border-color:rgba(201,155,14,0.3)">
                <span x-text="lang==='FR' ? 'IA non configurée. Ajoutez une clé API Gemini dans Paramètres › IA pour activer l\'assistant.' : 'AI not configured. Add a Gemini API key in Settings › AI to enable the assistant.'"></span>
                <button @click="setPage('settings')" class="text-amber-400 font-black uppercase tracking-wider ml-1" x-text="lang==='FR'?'Configurer →':'Configure →'"></button>
            </div>

            <!-- Chat -->
            <div class="glass-card rounded-2xl flex-1 flex flex-col overflow-hidden" style="min-height:300px">
                <div class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="chat">
                    <template x-for="(m,i) in messages" :key="i">
                        <div :class="m.role==='user' ? 'flex justify-end' : 'flex justify-start'">
                            <div class="max-w-[80%] px-4 py-2.5 rounded-2xl text-sm"
                                 :style="m.role==='user' ? 'background:rgba(201,155,14,0.18);border:1px solid rgba(201,155,14,0.3);color:#fff' : 'background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:rgb(226,232,240)'">
                                <div x-text="m.text" style="white-space:pre-wrap"></div>
                                <template x-if="m.figures && m.figures.length">
                                    <div class="mt-2 space-y-1">
                                        <template x-for="f in m.figures" :key="f.label">
                                            <div class="flex justify-between gap-4 text-xs"><span class="text-slate-400" x-text="f.label"></span><span class="font-black text-amber-400" x-text="f.value"></span></div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <div x-show="thinking" class="flex justify-start"><div class="px-4 py-2.5 rounded-2xl text-sm" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1)"><span class="text-slate-400">…</span></div></div>
                    <!-- Suggested chips (empty state) -->
                    <div x-show="messages.length===0 && aiStatus.mode!=='unavailable'" class="flex flex-wrap gap-2 pt-2">
                        <template x-for="q in chips" :key="q">
                            <button @click="send(q)" class="px-3 py-2 rounded-xl text-xs text-left" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1)" x-text="q"></button>
                        </template>
                    </div>
                </div>
                <div class="p-3 border-t flex gap-2" style="border-color:rgba(255,255,255,0.08)">
                    <input x-model="input" @keydown.enter="send()" :disabled="aiStatus.mode==='unavailable'||thinking" class="input-field flex-1" :placeholder="lang==='FR'?'Posez une question sur vos finances…':'Ask about your finances…'">
                    <button @click="send()" :disabled="aiStatus.mode==='unavailable'||thinking" class="glass-btn-amber px-5 rounded-xl text-xs uppercase tracking-widest disabled:opacity-40" x-text="lang==='FR'?'Envoyer':'Send'"></button>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
// ── Global toast system ───────────────────────────────────────────────────
function toastManager() {
    return {
        toasts: [],
        addToast({ type = 'info', title = '', message = '', duration = 4000 } = {}) {
            const id = Date.now() + Math.floor(performance.now());
            this.toasts.push({ id, type, title, message, show: true });
            setTimeout(() => this.removeToast(id), duration);
        },
        removeToast(id) {
            const t = this.toasts.find(x => x.id === id);
            if (t) t.show = false;
            setTimeout(() => { this.toasts = this.toasts.filter(x => x.id !== id); }, 250);
        },
    };
}
// Fire from anywhere: opesToast('success','Titre','Message')
window.opesToast = (type, title, message = '') =>
    window.dispatchEvent(new CustomEvent('toast', { detail: { type, title, message } }));

// ── PWA: install prompt + service worker ──────────────────────────────────
function pwaInstall() {
    return {
        showBanner: false, deferredPrompt: null,
        init() {
            if (localStorage.getItem('pwa-dismissed') || window.matchMedia('(display-mode: standalone)').matches) return;
            window.addEventListener('beforeinstallprompt', e => { e.preventDefault(); this.deferredPrompt = e; this.showBanner = true; });
        },
        async install() {
            if (!this.deferredPrompt) { this.showBanner = false; return; }
            this.deferredPrompt.prompt();
            await this.deferredPrompt.userChoice;
            this.showBanner = false; this.deferredPrompt = null;
        },
        dismiss() { this.showBanner = false; localStorage.setItem('pwa-dismissed', Date.now()); },
    };
}
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => setInterval(() => reg.update(), 60000))
            .catch(() => {});
    });
}

// Global helper: extract readable message from Laravel error responses
function extractError(data) {
    if (!data) return 'Erreur inconnue';
    if (data.errors) {
        return Object.values(data.errors).flat()
            .map(m => String(m).replace(/^validation\.(\w+)$/, (_, k) => ({
                required: 'Champ obligatoire',
                string: 'Doit être du texte',
                numeric: 'Doit être un nombre',
                min: 'Valeur trop petite',
                max: 'Valeur trop grande',
                date: 'Date invalide',
                email: 'Email invalide',
                unique: 'Déjà utilisé',
            }[k] ?? k)))
            .join(' | ');
    }
    return data.message ?? 'Erreur';
}

function opesApp() {
    return {
        page: new URLSearchParams(location.search).get('page') || 'dashboard',
        lang: localStorage.getItem('opes_lang') || 'FR',
        connStatus: navigator.onLine ? 'ONLINE' : 'OFFLINE',
        loading: false,
        sidebarOpen: false,
        /* Admin impersonation */
        impersonation: (() => { try { return JSON.parse(localStorage.getItem('opes_impersonation') || 'null'); } catch(e) { return null; } })(),
        leaveImpersonation() {
            localStorage.removeItem('opes_impersonation');
            localStorage.removeItem('opes_token');
            localStorage.removeItem('opes_user');
            localStorage.removeItem('opes_company');
            window.location.href = '/admin/impersonate/leave';
        },
        /* Multi-company switcher */
        companies: [],
        companySwitcherOpen: false,
        showAddCompany: false,
        addCompanyForm: { name:'', niu:'', rccm:'', tax_regime:'REEL', tax_center:'' },
        addCompanyLoading: false,
        addCompanyError: '',
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
            { page:'invoice',    icon:'<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>', labelFr:'Nouvelle Facture', labelEn:'New Invoice' },
            { page:'calculator', icon:'<rect x="4" y="2" width="16" height="20" rx="2"/><line x1="8" y1="6" x2="16" y2="6"/><line x1="8" y1="10" x2="10" y2="10"/><line x1="14" y1="10" x2="16" y2="10"/><line x1="8" y1="14" x2="10" y2="14"/><line x1="14" y1="14" x2="16" y2="14"/><line x1="8" y1="18" x2="16" y2="18"/>', labelFr:'Calc. TVA',        labelEn:'VAT Calc' },
            { page:'journal',    icon:'<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>', labelFr:'Journal',          labelEn:'Journal' },
            { page:'ledger',     icon:'<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>', labelFr:'Grand Livre',      labelEn:'Ledger' },
            { page:'team',        icon:'<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>', labelFr:'Équipe',           labelEn:'Team' },
            { page:'settings',    icon:'<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>', labelFr:'Paramètres',       labelEn:'Settings' },
            { page:'import',      icon:'<path d="M2 9V5a2 2 0 0 1 2-2h3.9a2 2 0 0 1 1.69.9l.81 1.2a2 2 0 0 0 1.67.9H20a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1"/><path d="M2 13h10"/><path d="m9 16 3-3-3-3"/>', labelFr:'Import CSV',        labelEn:'CSV Import' },
            { page:'sync',        icon:'<polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/>', labelFr:'Sync Hors Ligne',   labelEn:'Offline Sync' },
            { page:null, href:'/dgi-monitor',   icon:'<path d="M5 12.55a11 11 0 0 1 14.08 0"/><path d="M1.42 9a16 16 0 0 1 21.16 0"/><path d="M8.53 16.11a6 6 0 0 1 6.95 0"/><line x1="12" y1="20" x2="12.01" y2="20"/>', labelFr:'Suivi DGI',    labelEn:'DGI Monitor' },
            { page:null, href:'/tax-dashboard', icon:'<polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/><polyline points="16 7 22 7 22 13"/>', labelFr:'Bilan Fiscal', labelEn:'Tax Monitor' },
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
            this.loadAnnouncements();
            this.loadCompanies();
            this.loadOnboarding();
            this.loadNotifications();
            /* Auto-load data for initial page */
            if (this.page==='journal')  this.loadJournal();
            if (this.page==='ledger')   this.loadLedger();
            if (this.page==='team')     this.loadTeam();
            if (this.page==='settings') {} // data comes from loadMe()
        },

        /* Platform announcements (banners) */
        announcements: [],
        async loadAnnouncements() {
            try {
                const items = await this.api('announcements');
                const dismissed = JSON.parse(localStorage.getItem('opes_dismissed_ann') || '[]');
                this.announcements = (Array.isArray(items) ? items : []).filter(a => !dismissed.includes(a.id));
            } catch(e) { this.announcements = []; }
        },
        dismissAnnouncement(id) {
            const dismissed = JSON.parse(localStorage.getItem('opes_dismissed_ann') || '[]');
            dismissed.push(id);
            localStorage.setItem('opes_dismissed_ann', JSON.stringify(dismissed));
            this.announcements = this.announcements.filter(a => a.id !== id);
        },

        /* Notifications (bell) */
        notifications: [],
        notifUnread: 0,
        notifOpen: false,
        async loadNotifications() {
            try { const d = await this.api('notifications'); this.notifications = d.notifications ?? []; this.notifUnread = d.unread ?? 0; } catch(e) {}
        },
        async markAllNotifsRead() {
            this.notifUnread = 0;
            this.notifications = this.notifications.map(n => ({ ...n, read_at: n.read_at || new Date().toISOString() }));
            try { await this.api('notifications/read-all', { method:'POST', body:'{}' }); } catch(e) {}
        },

        /* Onboarding checklist */
        onboarding: null,
        async loadOnboarding() {
            try {
                const s = await this.api('onboarding/status');
                this.onboarding = (s && !s.completed && !s.dismissed) ? s : null;
            } catch(e) { this.onboarding = null; }
        },
        async dismissChecklist() {
            this.onboarding = null;
            try { await this.api('onboarding/dismiss-checklist', { method:'POST', body: '{}' }); } catch(e) {}
        },

        /* Multi-company switcher */
        async loadCompanies() {
            try {
                const list = await this.api('companies/mine');
                this.companies = Array.isArray(list) ? list : [];
            } catch(e) { this.companies = []; }
        },
        async switchCompany(id) {
            if (this.company && id === this.company.id) { this.companySwitcherOpen = false; return; }
            this.companySwitcherOpen = false;
            this.loading = true;
            try {
                const res = await this.api('companies/switch', { method:'POST', body: JSON.stringify({ company_id: id }) });
                if (res.company) {
                    localStorage.setItem('opes_company', JSON.stringify(res.company));
                    // Reload so every page re-fetches data scoped to the new company.
                    window.location.reload();
                }
            } catch(e) { this.loading = false; }
        },
        async createCompany() {
            this.addCompanyError = '';
            if (!this.addCompanyForm.name || !this.addCompanyForm.niu || !this.addCompanyForm.rccm || !this.addCompanyForm.tax_center) {
                this.addCompanyError = this.lang==='FR' ? 'Tous les champs sont requis.' : 'All fields are required.';
                return;
            }
            this.addCompanyLoading = true;
            try {
                const res = await this.api('companies/additional', { method:'POST', body: JSON.stringify(this.addCompanyForm) });
                if (res.company) {
                    await this.loadCompanies();
                    this.showAddCompany = false;
                    this.addCompanyForm = { name:'', niu:'', rccm:'', tax_regime:'REEL', tax_center:'' };
                    // Switch into the new company immediately.
                    this.switchCompany(res.company.id);
                } else {
                    this.addCompanyError = (res.errors ? Object.values(res.errors).flat().join(' ') : res.message) || 'Erreur';
                }
            } catch(e) {
                this.addCompanyError = e.message;
            } finally {
                this.addCompanyLoading = false;
            }
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
            // RBAC guard — redirect unauthorized access to dashboard
            const role = this.user?.role;
            const ownerOnly = ['team','settings','subscription','audit-log','fiscal-year'];
            const ownerAccountant = ['journal','ledger','import','subledgers','suppliers','reports','recurring',
                'payroll','supplier-invoices','fixed-assets','reconciliation','budget','dsf-export',
                'accounts','purchase-orders','credit-notes','patente','cashflow'];
            if (role === 'CLERK' && (ownerOnly.includes(p) || ownerAccountant.includes(p))) {
                p = 'dashboard';
            } else if (role === 'ACCOUNTANT' && ownerOnly.includes(p)) {
                p = 'dashboard';
            }

            this.page = p;
            this.sidebarOpen = false;   // close mobile drawer on navigation
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

        // ── Attachments ──────────────────────────────────────────────
        attachModal: { open:false, entryId:null, ref:'', files:[] },
        attachFile: null,
        attachUploading: false,
        attachError: '',

        async openAttachments(txn) {
            this.attachModal = { open:true, entryId:txn.id, ref:txn.reference_id, files:[] };
            this.attachError = '';
            await this.loadAttachments(txn.id);
        },
        async loadAttachments(entryId) {
            const token = localStorage.getItem('opes_token');
            const res = await fetch(`/api/v1/companies/${this.company.id}/journal/${entryId}/attachments`,{
                headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}
            });
            const data = await res.json();
            this.attachModal.files = Array.isArray(data) ? data : (data.data ?? []);
            // update badge on journal row
            const txn = this.journalEntries.find(t => t.id===entryId);
            if (txn) txn._attachCount = this.attachModal.files.length;
        },
        async uploadAttachment() {
            if (!this.attachFile) return;
            this.attachUploading=true; this.attachError='';
            try {
                const token = localStorage.getItem('opes_token');
                const fd = new FormData();
                fd.append('file', this.attachFile);
                const res = await fetch(`/api/v1/companies/${this.company.id}/journal/${this.attachModal.entryId}/attachments`,{
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Accept':'application/json'},
                    body: fd,
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.attachFile = null;
                await this.loadAttachments(this.attachModal.entryId);
            } catch(e) { this.attachError = e.message; }
            finally { this.attachUploading = false; }
        },
        async deleteAttachment(f) {
            const token = localStorage.getItem('opes_token');
            await fetch(`/api/v1/companies/${this.company.id}/journal/${this.attachModal.entryId}/attachments/${f.id}`,{
                method:'DELETE',
                headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}
            });
            await this.loadAttachments(this.attachModal.entryId);
        },

        // ── DGI Force Sync ───────────────────────────────────────────
        async forceDgiSync(txn) {
            if (!this.company) return;
            const token = localStorage.getItem('opes_token');
            await fetch(`/api/v1/companies/${this.company.id}/journal/${txn.id}/dgi-sync`,{
                method:'POST',
                headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}
            });
            txn.dgi_sync_status = 'PENDING';
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
            this.invoiceError = '';
            // Client-side guard
            if (!this.form.invoice_number) { this.invoiceError = 'N° Facture obligatoire.'; return; }
            if (!this.form.client_name)    { this.invoiceError = 'Nom du client obligatoire.'; return; }
            if (!this.form.invoice_date)   { this.invoiceError = 'Date de facture obligatoire.'; return; }
            const badLines = this.form.lines.filter(l => !l.description || Number(l.quantity) <= 0 || Number(l.unit_price_ht) < 0);
            if (this.form.lines.length === 0 || badLines.length) { this.invoiceError = 'Chaque ligne doit avoir une désignation et une quantité > 0.'; return; }

            this.generating = true;
            try {
                const token = localStorage.getItem('opes_token');
                const me = await (await fetch('/api/v1/auth/me', { headers:{'Authorization':'Bearer '+token,'Accept':'application/json'} })).json();
                const companyId = me.company?.id;
                if (!companyId) throw new Error('Entreprise introuvable — reconnectez-vous.');
                const payload = {
                    ...this.form,
                    lines: this.form.lines.map(l => ({ ...l, quantity:Number(l.quantity), unit_price_ht:Number(l.unit_price_ht) }))
                };
                const res = await fetch(`/api/v1/companies/${companyId}/invoice/generate`, {
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/pdf'},
                    body: JSON.stringify(payload),
                });
                if (!res.ok) { const err = await res.json(); throw new Error(extractError(err)); }
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

function subscriptionPanel() {
    return {
        subStatus: null,
        subLoading: false,
        subError: '',
        subSuccess: '',
        selectedPlan: 'GROWTH',
        subPhone: '',
        subPlans: [
            { id:'STARTER',    name:'Starter',    price:5000,  features:['1 utilisateur','100 factures/mois','Export DGI','Support email'] },
            { id:'GROWTH',     name:'Growth',     price:15000, features:['5 utilisateurs','Factures illimitées','DGI Live-Link','Import CSV','Support prioritaire'] },
            { id:'ENTERPRISE', name:'Enterprise', price:45000, features:['Utilisateurs illimités','Tout inclus','API dédiée','Account manager dédié'] },
        ],

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await (await fetch('/api/v1/auth/me', {headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}})).json();
            const companyId = me.company?.id;
            if (!companyId) return;
            try {
                const res = await fetch(`/api/v1/companies/${companyId}/subscriptions/status`, {
                    headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}
                });
                if (res.ok) this.subStatus = await res.json();
            } catch(e) {}
        },

        receiptLoading: false,
        async downloadReceipt() {
            this.subError = ''; this.receiptLoading = true;
            try {
                const token = localStorage.getItem('opes_token');
                const me = await (await fetch('/api/v1/auth/me', {headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}})).json();
                const companyId = me.user?.company_id;
                if (!companyId) { this.subError = this.lang==='FR' ? 'Société introuvable.' : 'Company not found.'; return; }
                const res = await fetch(`/api/v1/companies/${companyId}/subscriptions/receipt`, {
                    headers:{'Authorization':'Bearer '+token,'Accept':'application/pdf'}
                });
                if (!res.ok) { this.subError = this.lang==='FR' ? 'Aucun reçu disponible (abonnement inactif).' : 'No receipt available (inactive subscription).'; return; }
                const blob = await res.blob();
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a'); a.href = url; a.download = 'recu-abonnement.pdf'; a.click();
                URL.revokeObjectURL(url);
            } catch(e) {
                this.subError = e.message;
            } finally {
                this.receiptLoading = false;
            }
        },

        async initiateSubscription() {
            if (!this.selectedPlan || !this.subPhone) return;
            this.subLoading=true; this.subError=''; this.subSuccess='';
            try {
                const token = localStorage.getItem('opes_token');
                const me = await (await fetch('/api/v1/auth/me', {headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}})).json();
                const companyId = me.company?.id;
                if (!companyId) throw new Error('Company not found');
                const res = await fetch(`/api/v1/companies/${companyId}/subscriptions/initiate`, {
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify({ plan: this.selectedPlan, phone: this.subPhone }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.subSuccess = data.message || 'Demande de paiement envoyée. Confirmez sur votre téléphone.';
                // Re-fetch status after a short delay
                setTimeout(async () => {
                    const s = await fetch(`/api/v1/companies/${companyId}/subscriptions/status`, {
                        headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}
                    });
                    if (s.ok) this.subStatus = await s.json();
                }, 4000);
            } catch(e) {
                this.subError = e.message;
            } finally {
                this.subLoading = false;
            }
        },
    };
}

function profilePanel() {
    return {
        profileForm: { name:'', email:'', role:'' },
        profileSaving: false,
        profileError: '',
        profileSuccess: '',
        pwdForm: { current_password:'', password:'', password_confirmation:'' },
        pwdSaving: false,
        pwdError: '',
        pwdSuccess: '',
        otpSent: false,
        otpCode: '',
        otpLoading: false,
        otpError: '',
        otpSuccess: '',

        async init() {
            const token = localStorage.getItem('opes_token');
            const res = await fetch('/api/v1/auth/me', {headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            const data = await res.json();
            this.profileForm.name  = data.user?.name  ?? '';
            this.profileForm.email = data.user?.email ?? '';
            this.profileForm.role  = data.user?.role  ?? '';
        },

        async saveProfile() {
            this.profileSaving=true; this.profileError=''; this.profileSuccess='';
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch('/api/v1/auth/profile', {
                    method:'PUT',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify({ name: this.profileForm.name, email: this.profileForm.email }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.profileSuccess = document.documentElement.lang==='fr'
                    ? 'Profil mis à jour avec succès ✔'
                    : 'Profile updated successfully ✔';
                // Update local storage
                const stored = JSON.parse(localStorage.getItem('opes_user') || '{}');
                stored.name  = this.profileForm.name;
                stored.email = this.profileForm.email;
                localStorage.setItem('opes_user', JSON.stringify(stored));
            } catch(e) {
                this.profileError = e.message;
            } finally {
                this.profileSaving = false;
            }
        },

        async changePassword() {
            if (!this.pwdForm.current_password || !this.pwdForm.password) return;
            this.pwdSaving=true; this.pwdError=''; this.pwdSuccess='';
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch('/api/v1/auth/password', {
                    method:'PUT',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify(this.pwdForm),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.pwdSuccess = document.documentElement.lang==='fr'
                    ? 'Mot de passe changé avec succès ✔'
                    : 'Password changed successfully ✔';
                this.pwdForm = { current_password:'', password:'', password_confirmation:'' };
            } catch(e) {
                this.pwdError = e.message;
            } finally {
                this.pwdSaving = false;
            }
        },

        async generateOtp() {
            this.otpLoading=true; this.otpError=''; this.otpSuccess='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch('/api/v1/auth/otp/generate', {method:'POST',headers:{Authorization:'Bearer '+token}});
            const d = await r.json();
            if(r.ok) { this.otpSent=true; }
            else { this.otpError = d.message ?? 'Erreur'; }
            this.otpLoading=false;
        },

        async verifyOtp() {
            if(!this.otpCode) return;
            this.otpLoading=true; this.otpError=''; this.otpSuccess='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch('/api/v1/auth/otp/verify', {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({code: this.otpCode})
            });
            const d = await r.json();
            if(r.ok) { this.otpSuccess = d.message; this.otpSent=false; this.otpCode=''; }
            else { this.otpError = d.message ?? 'Code invalide'; }
            this.otpLoading=false;
        },
    };
}

function customerInvoicesPanel() {
    return {
        invoices: [],
        customers: [],
        showForm: false,
        saving: false,
        formError: '',
        ttcPreview: { tva:0, cac:0, ttc:0 },
        form: { customer_id:'', invoice_date:'', due_date:'', amount_ht:'', notes:'' },
        _cid: null,
        mecefLoading: null,
        async certifyMecef(inv) {
            this.mecefLoading = inv.id;
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/mecef/invoices/${inv.id}/certify`, { method:'POST', headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'} });
                const d = await res.json();
                if (d.ok && d.invoice) {
                    inv.mecef_status   = d.invoice.mecef_status;
                    inv.mecef_counter  = d.invoice.mecef_counter;
                    inv.mecef_nim      = d.invoice.mecef_nim;
                    window.opesToast('success', 'Facture certifiée DGI', 'Compteur ' + (d.invoice.mecef_counter || ''));
                } else {
                    window.opesToast('error', 'MECeF', d.message || 'Échec de la certification.');
                }
            } catch(e) {} finally { this.mecefLoading = null; }
        },

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await (await fetch('/api/v1/auth/me',{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}})).json();
            this._cid = me.company?.id;
            await Promise.all([this.load(), this.loadCustomers()]);
        },
        async load() {
            if (!this._cid) return;
            const token = localStorage.getItem('opes_token');
            const res = await fetch(`/api/v1/companies/${this._cid}/customer-invoices`,{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            const data = await res.json();
            this.invoices = Array.isArray(data) ? data : (data.data ?? []);
        },
        async loadCustomers() {
            if (!this._cid) return;
            const token = localStorage.getItem('opes_token');
            const res = await fetch(`/api/v1/companies/${this._cid}/customers`,{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            const data = await res.json();
            this.customers = Array.isArray(data) ? data : (data.data ?? []);
        },
        calcTtc() {
            const ht = Number(this.form.amount_ht) || 0;
            const tva = Math.round(ht * 0.175);
            const cac = Math.round(ht * 0.0175);
            this.ttcPreview = { tva, cac, ttc: ht + tva + cac };
        },
        async save() {
            this.saving=true; this.formError='';
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/companies/${this._cid}/customer-invoices`,{
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify({...this.form, amount_ht: Number(this.form.amount_ht)}),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.form = { customer_id:'', invoice_date:'', due_date:'', amount_ht:'', notes:'' };
                this.ttcPreview = { tva:0, cac:0, ttc:0 };
                this.showForm = false;
                await this.load();
            } catch(e) { this.formError = e.message; }
            finally { this.saving = false; }
        },
        async markSent(inv) {
            const token = localStorage.getItem('opes_token');
            await fetch(`/api/v1/companies/${this._cid}/customer-invoices/${inv.id}/send`,{method:'POST',headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            await this.load();
        },
        async markPaid(inv) {
            const token = localStorage.getItem('opes_token');
            await fetch(`/api/v1/companies/${this._cid}/customer-invoices/${inv.id}/pay`,{method:'POST',headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            await this.load();
        },
        async creditNote(inv) {
            if (!confirm(document.documentElement.lang==='fr'?'Créer un avoir pour cette facture ?':'Create a credit note for this invoice?')) return;
            const token = localStorage.getItem('opes_token');
            await fetch(`/api/v1/companies/${this._cid}/customer-invoices/${inv.id}/credit-note`,{method:'POST',headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            await this.load();
        },
        fmtXaf(v) { if (v===null||v===undefined) return '—'; return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:0})+' XAF'; },
    };
}

function customersPanel() {
    return {
        customers: [],
        showForm: false,
        saving: false,
        formError: '',
        form: { name:'', niu:'', email:'', phone:'', address:'', payment_terms_days:30, credit_limit_xaf:'' },

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await (await fetch('/api/v1/auth/me',{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}})).json();
            this._cid = me.company?.id;
            await this.load();
        },
        async load() {
            if (!this._cid) return;
            const token = localStorage.getItem('opes_token');
            const res = await fetch(`/api/v1/companies/${this._cid}/customers`,{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            const data = await res.json();
            this.customers = Array.isArray(data) ? data : (data.data ?? []);
        },
        async save() {
            this.saving=true; this.formError='';
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/companies/${this._cid}/customers`,{
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.form = { name:'',niu:'',email:'',phone:'',address:'',payment_terms_days:30,credit_limit_xaf:'' };
                this.showForm = false;
                await this.load();
            } catch(e) { this.formError = e.message; }
            finally { this.saving = false; }
        },
        fmtXaf(v) { return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:0})+' XAF'; },
    };
}

function aiPanel() {
    return {
        aiStatus: { mode:'unavailable', model:null },
        messages: [], input: '', thinking: false,
        _tok() { return localStorage.getItem('opes_token'); },
        _lang() { return localStorage.getItem('opes_lang') || 'FR'; },
        get chips() {
            return this._lang()==='FR'
                ? ['Combien ai-je facturé ce mois?', 'Quel est mon total TVA collectée?', 'Combien de factures ai-je émises?', 'Suis-je prêt pour ma déclaration TVA?']
                : ['How much did I invoice this month?', 'What is my total VAT collected?', 'How many invoices have I issued?', 'Am I ready for my VAT return?'];
        },
        async init() {
            try { this.aiStatus = await (await fetch('/api/v1/ai/status', { headers:{'Authorization':'Bearer '+this._tok(),'Accept':'application/json'} })).json(); } catch(e) {}
        },
        async send(text) {
            const q = (text ?? this.input).trim();
            if (!q || this.thinking) return;
            this.input = '';
            this.messages.push({ role:'user', text:q });
            this.thinking = true;
            this.$nextTick(() => { if (this.$refs.chat) this.$refs.chat.scrollTop = this.$refs.chat.scrollHeight; });
            try {
                const res = await fetch('/api/v1/ai/query', { method:'POST', headers:{'Authorization':'Bearer '+this._tok(),'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify({ question:q }) });
                const d = await res.json();
                if (d.ok && d.data) {
                    this.messages.push({ role:'ai', text: d.data.answer || '…', figures: d.data.figures || [] });
                } else {
                    this.messages.push({ role:'ai', text: d.reason || (this._lang()==='FR'?'IA indisponible.':'AI unavailable.') });
                }
            } catch(e) {
                this.messages.push({ role:'ai', text: this._lang()==='FR'?'Erreur de connexion.':'Connection error.' });
            } finally {
                this.thinking = false;
                this.$nextTick(() => { if (this.$refs.chat) this.$refs.chat.scrollTop = this.$refs.chat.scrollHeight; });
            }
        },
    };
}

function projectsPanel() {
    return {
        projects: [],
        showAdd: false, addSaving: false, addError: '',
        addForm: { name:'', description:'', start_date:'', end_date:'', contract_value:'', budget_amount:'' },
        showDetail: false, detail: null,
        entryForm: { direction:'cost', entry_type:'expense', description:'', amount:'' },
        entrySaving: false,
        _tok() { return localStorage.getItem('opes_token'); },
        async _get(path) { const r = await fetch('/api/v1/'+path, { headers:{'Authorization':'Bearer '+this._tok(),'Accept':'application/json'} }); return r.json(); },
        async _send(path, method, body) {
            const r = await fetch('/api/v1/'+path, { method, headers:{'Authorization':'Bearer '+this._tok(),'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify(body) });
            const d = await r.json();
            if (!r.ok) throw new Error(d.message || Object.values(d.errors ?? {}).flat().join(' | '));
            return d;
        },
        _lang() { return localStorage.getItem('opes_lang') || 'FR'; },
        async init() { await this.load(); },
        async load() { const d = await this._get('projects'); this.projects = Array.isArray(d) ? d : []; },
        statusBadge(s) {
            return ({
                draft:     'background:rgba(100,116,139,0.18);color:rgb(148,163,184);border:1px solid rgba(100,116,139,0.35)',
                active:    'background:rgba(16,185,129,0.18);color:rgb(110,231,183);border:1px solid rgba(16,185,129,0.35)',
                completed: 'background:rgba(59,130,246,0.18);color:rgb(147,197,253);border:1px solid rgba(59,130,246,0.35)',
                cancelled: 'background:rgba(244,63,94,0.18);color:rgb(252,165,165);border:1px solid rgba(244,63,94,0.35)',
            })[s] || '';
        },
        openAdd() { this.addForm = { name:'', description:'', start_date:'', end_date:'', contract_value:'', budget_amount:'' }; this.addError=''; this.showAdd=true; },
        async saveProject() {
            this.addError = '';
            if (!this.addForm.name) { this.addError = this._lang()==='FR' ? 'Nom requis.' : 'Name required.'; return; }
            this.addSaving = true;
            try {
                const payload = Object.fromEntries(Object.entries(this.addForm).filter(([, v]) => v !== '' && v !== null));
                await this._send('projects', 'POST', payload);
                this.showAdd = false; await this.load();
                window.opesToast('success', this._lang()==='FR'?'Projet créé':'Project created');
            } catch(e) { this.addError = e.message; }
            finally { this.addSaving = false; }
        },
        async openProject(p) {
            this.detail = null; this.showDetail = true;
            this.entryForm = { direction:'cost', entry_type:'expense', description:'', amount:'' };
            this.detail = await this._get('projects/' + p.id);
        },
        async addEntry() {
            if (!this.entryForm.description || !this.entryForm.amount || !this.detail) return;
            this.entrySaving = true;
            try {
                await this._send(`projects/${this.detail.project.id}/entries`, 'POST', this.entryForm);
                this.detail = await this._get('projects/' + this.detail.project.id);
                this.entryForm = { direction:'cost', entry_type:'expense', description:'', amount:'' };
                this.load(); // refresh card aggregates
            } catch(e) {}
            finally { this.entrySaving = false; }
        },
        fmtXaf(v) { return Number(v || 0).toLocaleString('fr-CM', { minimumFractionDigits:0 }) + ' XAF'; },
    };
}

function crmPanel() {
    return {
        view: 'pipeline',
        leads: [], activities: [], stats: {},
        search: '', filterStatus: '',
        showAdd: false, addSaving: false, addError: '',
        addForm: { contact_name:'', contact_phone:'', contact_email:'', company_name:'', source:'other', estimated_value:'', notes:'' },
        showActivity: false, activitySaving: false, activityLead: null,
        activityForm: { type:'note', description:'' },
        columns: [
            { key:'new',           label:'Nouveau',     badge:'background:rgba(59,130,246,0.18);color:rgb(147,197,253);border:1px solid rgba(59,130,246,0.35)' },
            { key:'contacted',     label:'Contacté',    badge:'background:rgba(234,179,8,0.18);color:rgb(253,224,71);border:1px solid rgba(234,179,8,0.35)' },
            { key:'qualified',     label:'Qualifié',    badge:'background:rgba(168,85,247,0.18);color:rgb(216,180,254);border:1px solid rgba(168,85,247,0.35)' },
            { key:'proposal_sent', label:'Devis envoyé',badge:'background:rgba(249,115,22,0.18);color:rgb(253,186,116);border:1px solid rgba(249,115,22,0.35)' },
            { key:'won',           label:'Gagné',       badge:'background:rgba(16,185,129,0.18);color:rgb(110,231,183);border:1px solid rgba(16,185,129,0.35)' },
            { key:'lost',          label:'Perdu',       badge:'background:rgba(244,63,94,0.18);color:rgb(252,165,165);border:1px solid rgba(244,63,94,0.35)' },
        ],
        _lang() { return localStorage.getItem('opes_lang') || 'FR'; },
        _company() { try { return JSON.parse(localStorage.getItem('opes_company') || '{}'); } catch(e) { return {}; } },
        _tok() { return localStorage.getItem('opes_token'); },
        async _get(path) { const r = await fetch('/api/v1/'+path, { headers:{'Authorization':'Bearer '+this._tok(),'Accept':'application/json'} }); return r.json(); },
        async _send(path, method, body) {
            const r = await fetch('/api/v1/'+path, { method, headers:{'Authorization':'Bearer '+this._tok(),'Content-Type':'application/json','Accept':'application/json'}, body: JSON.stringify(body) });
            const d = await r.json();
            if (!r.ok) throw new Error(d.message || Object.values(d.errors ?? {}).flat().join(' | '));
            return d;
        },
        async init() { await this.load(); this.loadStats(); this.loadActivities(); },
        async load() {
            const qs = new URLSearchParams();
            if (this.search) qs.set('search', this.search);
            if (this.filterStatus) qs.set('status', this.filterStatus);
            const data = await this._get('crm/leads' + (qs.toString() ? '?'+qs.toString() : ''));
            this.leads = Array.isArray(data) ? data : [];
        },
        async loadStats() { this.stats = (await this._get('crm/stats')) || {}; },
        async loadActivities() { const d = await this._get('crm/activities'); this.activities = Array.isArray(d) ? d : []; },
        leadsByStatus(s) { return this.leads.filter(l => l.status === s); },
        statusLabel(s) { return (this.columns.find(c => c.key === s) || {}).label || s; },
        statusBadge(s) { return (this.columns.find(c => c.key === s) || {}).badge || ''; },
        daysInStage(lead) {
            const d = lead.stage_changed_at || lead.created_at; if (!d) return '';
            const days = Math.floor((Date.now() - new Date(d).getTime()) / 86400000);
            return this._lang()==='FR' ? `depuis ${days} jour${days!==1?'s':''}` : `${days}d in stage`;
        },
        waLink(lead) {
            const phone = (lead.contact_phone || '').replace(/[^0-9]/g, '');
            const msg = `Bonjour ${lead.contact_name}, je reviens vers vous concernant notre proposition d'un montant de ${this.fmtXaf(lead.estimated_value)}. Êtes-vous disponible pour en discuter? — ${this._company()?.name || 'Opes Books'}`;
            return `https://wa.me/${phone}?text=${encodeURIComponent(msg)}`;
        },
        openAdd() { this.addForm = { contact_name:'', contact_phone:'', contact_email:'', company_name:'', source:'other', estimated_value:'', notes:'' }; this.addError=''; this.showAdd=true; },
        async saveLead() {
            this.addError = '';
            if (!this.addForm.contact_name) { this.addError = this._lang()==='FR' ? 'Nom du contact requis.' : 'Contact name required.'; return; }
            this.addSaving = true;
            try { await this._send('crm/leads', 'POST', this.addForm); this.showAdd=false; await this.load(); this.loadStats(); window.opesToast('success', this._lang()==='FR'?'Lead créé':'Lead created'); }
            catch(e) { this.addError = e.message; }
            finally { this.addSaving = false; }
        },
        async moveStage(lead, status) {
            if (!status) return;
            try { await this._send(`crm/leads/${lead.id}/stage`, 'PATCH', { status }); await this.load(); this.loadStats(); this.loadActivities(); } catch(e) {}
        },
        openActivity(lead) { this.activityLead = lead; this.activityForm = { type:'note', description:'' }; this.showActivity = true; },
        async saveActivity() {
            if (!this.activityForm.description) return;
            this.activitySaving = true;
            try { await this._send(`crm/leads/${this.activityLead.id}/activities`, 'POST', this.activityForm); this.showActivity=false; this.loadActivities(); }
            catch(e) {}
            finally { this.activitySaving = false; }
        },
        fmtXaf(v) { return Number(v || 0).toLocaleString('fr-CM', { minimumFractionDigits:0 }) + ' XAF'; },
        fmtDate(d) { return d ? new Date(d).toLocaleDateString('fr-CM', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' }) : ''; },
    };
}

function suppliersPanel() {
    return {
        suppliers: [],
        showForm: false,
        saving: false,
        formError: '',
        form: { name:'', niu:'', email:'', phone:'', address:'', payment_terms_days:30 },

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await (await fetch('/api/v1/auth/me',{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}})).json();
            this._cid = me.company?.id;
            await this.load();
        },
        async load() {
            if (!this._cid) return;
            const token = localStorage.getItem('opes_token');
            const res = await fetch(`/api/v1/companies/${this._cid}/suppliers`,{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            const data = await res.json();
            this.suppliers = Array.isArray(data) ? data : (data.data ?? []);
        },
        async save() {
            this.saving=true; this.formError='';
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/companies/${this._cid}/suppliers`,{
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.form = { name:'',niu:'',email:'',phone:'',address:'',payment_terms_days:30 };
                this.showForm = false;
                await this.load();
            } catch(e) { this.formError = e.message; }
            finally { this.saving = false; }
        },
        fmtXaf(v) { return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:0})+' XAF'; },
    };
}

function reportsPanel() {
    return {
        tab: 'pl',
        from: new Date().getFullYear()+'-01-01',
        to: new Date().toISOString().slice(0,10),
        asOf: new Date().toISOString().slice(0,10),
        loading: false,
        result: null,
        error: '',
        _cid: null,

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await (await fetch('/api/v1/auth/me',{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}})).json();
            this._cid = me.company?.id;
        },
        async load() {
            if (!this._cid) return;
            this.loading=true; this.error=''; this.result=null;
            try {
                const token = localStorage.getItem('opes_token');
                let url;
                if (this.tab==='pl')  url = `/api/v1/companies/${this._cid}/reports/pl?from=${this.from}&to=${this.to}`;
                if (this.tab==='bs')  url = `/api/v1/companies/${this._cid}/reports/balance-sheet?as_of=${this.asOf}`;
                if (this.tab==='cf')  url = `/api/v1/companies/${this._cid}/reports/cash-flow?from=${this.from}&to=${this.to}`;
                if (this.tab==='ar')  url = `/api/v1/companies/${this._cid}/reports/aged-receivables`;
                if (this.tab==='ap')  url = `/api/v1/companies/${this._cid}/reports/aged-payables`;
                const res = await fetch(url,{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Erreur');
                this.result = data;
            } catch(e) { this.error = e.message; }
            finally { this.loading = false; }
        },
        fmtXaf(v) { if (v===null||v===undefined) return '— XAF'; return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:0})+' XAF'; },
    };
}

function recurringPanel() {
    return {
        items: [],
        showForm: false,
        saving: false,
        running: false,
        formError: '',
        runMsg: '',
        form: { name:'', frequency:'MONTHLY', amount_xaf:'', next_run_date:'', debit_account:'', credit_account:'', memo:'', end_date:'' },
        _cid: null,

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await (await fetch('/api/v1/auth/me',{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}})).json();
            this._cid = me.company?.id;
            await this.load();
        },
        async load() {
            if (!this._cid) return;
            const token = localStorage.getItem('opes_token');
            const res = await fetch(`/api/v1/companies/${this._cid}/recurring`,{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            const data = await res.json();
            this.items = Array.isArray(data) ? data : (data.data ?? []);
        },
        async save() {
            this.saving=true; this.formError='';
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/companies/${this._cid}/recurring`,{
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify({...this.form, amount_xaf: Number(this.form.amount_xaf)}),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.form = { name:'',frequency:'MONTHLY',amount_xaf:'',next_run_date:'',debit_account:'',credit_account:'',memo:'',end_date:'' };
                this.showForm = false;
                await this.load();
            } catch(e) { this.formError = e.message; }
            finally { this.saving = false; }
        },
        async runAll() {
            this.running=true; this.runMsg='';
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/companies/${this._cid}/recurring/run-now`,{
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Accept':'application/json'},
                });
                const data = await res.json();
                this.runMsg = data.message || (document.documentElement.lang==='fr'?'Transactions exécutées.':'Transactions processed.');
                await this.load();
            } catch(e) {}
            finally { this.running = false; }
        },
        fmtXaf(v) { return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:0})+' XAF'; },
    };
}

function payrollPanel() {
    return {
        tab: 'employees',
        employees: [],
        periods: [],
        showEmpForm: false,
        empSaving: false,
        empError: '',
        empForm: { name:'', position:'', gross_salary_xaf:'', hire_date:'', cnps_number:'' },
        periodForm: { period_month: new Date().getMonth()+1, period_year: new Date().getFullYear() },
        periodCalc: false,
        periodError: '',
        _cid: null,

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await (await fetch('/api/v1/auth/me',{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}})).json();
            this._cid = me.company?.id;
            await this.loadEmployees();
            await this.loadPeriods();
        },
        async loadEmployees() {
            if (!this._cid) return;
            const token = localStorage.getItem('opes_token');
            const res = await fetch(`/api/v1/companies/${this._cid}/payroll/employees`,{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            const data = await res.json();
            this.employees = Array.isArray(data) ? data : (data.data ?? []);
        },
        async saveEmployee() {
            this.empSaving=true; this.empError='';
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/companies/${this._cid}/payroll/employees`,{
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify({...this.empForm, gross_salary_xaf: Number(this.empForm.gross_salary_xaf)}),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                this.empForm = { name:'',position:'',gross_salary_xaf:'',hire_date:'',cnps_number:'' };
                this.showEmpForm = false;
                await this.loadEmployees();
            } catch(e) { this.empError = e.message; }
            finally { this.empSaving = false; }
        },
        async loadPeriods() {
            if (!this._cid) return;
            const token = localStorage.getItem('opes_token');
            const res = await fetch(`/api/v1/companies/${this._cid}/payroll/periods`,{headers:{'Authorization':'Bearer '+token,'Accept':'application/json'}});
            const data = await res.json();
            this.periods = Array.isArray(data) ? data : (data.data ?? []);
        },
        async calculatePeriod() {
            this.periodCalc=true; this.periodError='';
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/companies/${this._cid}/payroll/periods`,{
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Content-Type':'application/json','Accept':'application/json'},
                    body: JSON.stringify({ period_month: Number(this.periodForm.period_month), period_year: Number(this.periodForm.period_year) }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || Object.values(data.errors??{}).flat().join(' | '));
                await this.loadPeriods();
            } catch(e) { this.periodError = e.message; }
            finally { this.periodCalc = false; }
        },
        async postPeriod(period) {
            try {
                const token = localStorage.getItem('opes_token');
                const res = await fetch(`/api/v1/companies/${this._cid}/payroll/periods/${period.id}/post`,{
                    method:'POST',
                    headers:{'Authorization':'Bearer '+token,'Accept':'application/json'},
                });
                if (!res.ok) { const d=await res.json(); throw new Error(d.message||'Post failed'); }
                await this.loadPeriods();
            } catch(e) { this.periodError = e.message; }
        },
        fmtXaf(v) { if (v===null||v===undefined) return '—'; return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:0})+' XAF'; },
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

function supplierInvoicesPanel() {
    return {
        invoices: [], suppliers: [], loading: true, _cid: null,
        showForm: false, submitting: false, formError: '',
        form: { supplier_id:'', invoice_number:'', supplier_ref:'', invoice_date:'', due_date:'', amount_ht:0, tva_amount:0, expense_account:'601100', notes:'' },
        fmtXaf(v) { if(v===null||v===undefined)return'—'; return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:2})+' XAF'; },
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}}).then(r=>r.json());
            this._cid = me.company?.id; if(!this._cid) return;
            const [inv, sup] = await Promise.all([
                fetch(`/api/v1/companies/${this._cid}/supplier-invoices`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
                fetch(`/api/v1/companies/${this._cid}/suppliers`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
            ]);
            this.invoices = inv.data ?? inv;
            this.suppliers = sup.data ?? sup;
            this.loading = false;
        },
        calcTva() { this.form.tva_amount = Math.round(this.form.amount_ht * 0.175); },
        async submitInvoice() {
            this.submitting = true; this.formError = '';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/supplier-invoices`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify(this.form)
            });
            const d = await r.json();
            if (r.ok) { this.invoices.unshift(d); this.showForm=false; this.form={supplier_id:'',invoice_number:'',supplier_ref:'',invoice_date:'',due_date:'',amount_ht:0,tva_amount:0,expense_account:'601100',notes:''}; }
            else { this.formError = extractError(d); }
            this.submitting = false;
        },
        async payInvoice(inv) {
            const acct = prompt('Payment account code (e.g. 521100):'); if(!acct) return;
            const ref = prompt('Payment reference:'); if(!ref) return;
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/supplier-invoices/${inv.id}/pay`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({payment_account:acct, payment_ref:ref})
            });
            if (r.ok) { const d = await r.json(); Object.assign(inv, d); }
        },
    };
}

function fixedAssetsPanel() {
    return {
        assets: [], loading: true, _cid: null,
        showForm: false, submitting: false, formError: '', depMsg: '',
        form: { name:'', category:'', syscohada_account_code:'', acquisition_date:'', acquisition_cost:0, useful_life_months:60, residual_value:0, credit_account:'401100' },
        fmtXaf(v) { if(v===null||v===undefined)return'—'; return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:2})+' XAF'; },
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}}).then(r=>r.json());
            this._cid = me.company?.id; if(!this._cid) return;
            const d = await fetch(`/api/v1/companies/${this._cid}/fixed-assets`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this.assets = Array.isArray(d) ? d : (d.data ?? []);
            this.loading = false;
        },
        async addAsset() {
            this.submitting=true; this.formError='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/fixed-assets`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify(this.form)
            });
            const d = await r.json();
            if(r.ok) { this.assets.unshift(d); this.showForm=false; }
            else { this.formError = extractError(d); }
            this.submitting=false;
        },
        async runDepreciation() {
            const token = localStorage.getItem('opes_token');
            const now = new Date();
            const r = await fetch(`/api/v1/companies/${this._cid}/fixed-assets/run-depreciation`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({month: now.getMonth()+1, year: now.getFullYear()})
            });
            const d = await r.json();
            this.depMsg = `${d.processed} immobilisation(s) amortie(s) pour ${d.period}.`;
            await this.init();
        },
        async disposeAsset(asset) {
            const proceeds = parseFloat(prompt('Proceeds from disposal (0 if none):')); if(isNaN(proceeds)) return;
            const acct = prompt('Receipt account (e.g. 521100):'); if(!acct) return;
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/fixed-assets/${asset.id}/dispose`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({proceeds, receipt_account: acct})
            });
            if(r.ok) { await this.init(); }
        },
    };
}

function reconciliationPanel() {
    return {
        sessions: [], loading: true, _cid: null, showForm: false, submitting: false,
        form: { bank_account_code:'521100', statement_date:'', statement_balance:0 },
        fmtXaf(v) { if(v===null||v===undefined)return'—'; return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:2})+' XAF'; },
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}}).then(r=>r.json());
            this._cid = me.company?.id; if(!this._cid) return;
            const d = await fetch(`/api/v1/companies/${this._cid}/reconciliation`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this.sessions = d.data ?? d;
            this.loading = false;
        },
        async submitSession() {
            this.submitting=true;
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/reconciliation`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({...this.form, lines:[]})
            });
            const d = await r.json();
            if(r.ok) { this.sessions.unshift(d); this.showForm=false; }
            this.submitting=false;
        },
    };
}

function budgetPanel() {
    return {
        budgets: [], loading: true, _cid: null, showForm: false, submitting: false,
        selectedBudget: null, variance: null,
        form: { name:'', fiscal_year: new Date().getFullYear() },
        fmtXaf(v) { if(v===null||v===undefined)return'—'; return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:2})+' XAF'; },
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}}).then(r=>r.json());
            this._cid = me.company?.id; if(!this._cid) return;
            const d = await fetch(`/api/v1/companies/${this._cid}/budgets`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this.budgets = d.data ?? d;
            this.loading = false;
        },
        async createBudget() {
            this.submitting=true;
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/budgets`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({...this.form, lines:[]})
            });
            const d = await r.json();
            if(r.ok) { this.budgets.unshift(d); this.showForm=false; }
            this.submitting=false;
        },
        async loadVariance(budget) {
            this.selectedBudget = budget; this.variance = null;
            const token = localStorage.getItem('opes_token');
            const d = await fetch(`/api/v1/companies/${this._cid}/budgets/${budget.id}/variance`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this.variance = d;
        },
    };
}

function dsfExportPanel() {
    return {
        _cid: null,
        dsfYear: new Date().getFullYear()-1, dsfLoading: false, dsfData: null,
        tvaMonth: new Date().getMonth()+1, tvaYear: new Date().getFullYear(), tvaLoading: false, tvaData: null,
        fmtXaf(v) { if(v===null||v===undefined)return'—'; return Number(v).toLocaleString('fr-CM',{minimumFractionDigits:2})+' XAF'; },
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}}).then(r=>r.json());
            this._cid = me.company?.id;
        },
        async generateDsf() {
            this.dsfLoading=true; this.dsfData=null;
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/exports/dsf`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({fiscal_year: this.dsfYear})
            });
            this.dsfData = await r.json();
            this.dsfLoading=false;
        },
        downloadDsf() {
            const blob = new Blob([JSON.stringify(this.dsfData, null, 2)], {type:'application/json'});
            const a = document.createElement('a'); a.href = URL.createObjectURL(blob);
            a.download = `DSF_${this.dsfYear}.json`; a.click();
        },
        async generateTva() {
            this.tvaLoading=true; this.tvaData=null;
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/exports/tva-monthly`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({month: this.tvaMonth, year: this.tvaYear})
            });
            this.tvaData = await r.json();
            this.tvaLoading=false;
        },
    };
}

function auditLogPanel() {
    return {
        logs: [], loading: true, _cid: null,
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}}).then(r=>r.json());
            this._cid = me.company?.id;
            await this.load();
        },
        async load() {
            this.loading=true;
            if(!this._cid) return;
            const token = localStorage.getItem('opes_token');
            const d = await fetch(`/api/v1/companies/${this._cid}/audit-log`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this.logs = d.data ?? d;
            this.loading=false;
        },
    };
}

function fiscalYearPanel() {
    return {
        _cid: null,
        closeYear: new Date().getFullYear()-1, closing: false, closeMsg: '', closeError: false,
        obYear: new Date().getFullYear(), obJson: '', obLoading: false, obMsg: '', obError: false,
        exportFrom: new Date().getFullYear() + '-01-01',
        exportTo: new Date().getFullYear() + '-12-31',
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}}).then(r=>r.json());
            this._cid = me.company?.id;
        },
        async closeFiscalYear() {
            if (!confirm(`Clôturer l'exercice ${this.closeYear} ? Cette opération est irréversible.`)) return;
            this.closing=true; this.closeMsg=''; this.closeError=false;
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/fiscal-year/close`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({fiscal_year: this.closeYear})
            });
            const d = await r.json();
            this.closeMsg = d.message ?? (r.ok ? 'Clôturé avec succès.' : 'Erreur.');
            this.closeError = !r.ok;
            this.closing=false;
        },
        async importOpeningBalances() {
            this.obLoading=true; this.obMsg=''; this.obError=false;
            let balances;
            try { balances = JSON.parse(this.obJson); } catch(e) { this.obMsg='JSON invalide.'; this.obError=true; this.obLoading=false; return; }
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/fiscal-year/opening-balances`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({fiscal_year: this.obYear, balances})
            });
            const d = await r.json();
            this.obMsg = d.message ?? (r.ok ? 'Importé.' : JSON.stringify(d));
            this.obError = !r.ok;
            this.obLoading=false;
        },
        downloadCsv(endpoint) {
            const token = localStorage.getItem('opes_token');
            const url = `/api/v1/companies/${this._cid}/exports/${endpoint}?from=${this.exportFrom}&to=${this.exportTo}`;
            // Fetch with auth and trigger download
            fetch(url, {headers:{Authorization:'Bearer '+token}})
                .then(r => r.blob())
                .then(blob => {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = endpoint + '.csv';
                    a.click();
                });
        },
    };
}

function chartOfAccountsPanel() {
    return {
        _cid: null, accounts: [], search: '', loading: true,
        showForm: false, submitting: false, formError: '',
        form: { code:'', label:'', class_digit:'' },
        get filtered() {
            const q = this.search.toLowerCase();
            if (!q) return this.accounts;
            return this.accounts.filter(a => a.code.includes(q) || a.label.toLowerCase().includes(q));
        },
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token,Accept:'application/json'}}).then(r=>r.json());
            this._cid = me.company?.id; if(!this._cid) return;
            const d = await fetch(`/api/v1/companies/${this._cid}/accounts`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this.accounts = Array.isArray(d) ? d : (d.data ?? []);
            this.loading = false;
        },
        async addAccount() {
            this.submitting=true; this.formError='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/accounts`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify(this.form)
            });
            const d = await r.json();
            if(r.ok) {
                this.accounts.push(d);
                this.accounts.sort((a,b) => a.code.localeCompare(b.code));
                this.showForm=false;
                this.form={code:'',label:'',class_digit:''};
            } else { this.formError = extractError(d); }
            this.submitting=false;
        },
    };
}

function quotationsPanel() {
    return {
        _cid: null, quotations: [], customers: [], showForm: false, saving: false, formError: '',
        form: { customer_id:'', quotation_date: new Date().toISOString().slice(0,10), valid_until:'', notes:'', lines:[{description:'',quantity:1,unit_price_ht:0}] },
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this._cid = me.company?.id;
            const [q, c] = await Promise.all([
                fetch(`/api/v1/companies/${this._cid}/quotations`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
                fetch(`/api/v1/companies/${this._cid}/customers`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
            ]);
            this.quotations = q.data ?? q;
            this.customers  = c.data ?? c;
        },
        async submitQuotation() {
            this.saving=true; this.formError='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/quotations`,{method:'POST',headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify(this.form)});
            const d = await r.json();
            if(r.ok) { this.quotations.unshift(d); this.showForm=false; this.form={customer_id:'',quotation_date:new Date().toISOString().slice(0,10),valid_until:'',notes:'',lines:[{description:'',quantity:1,unit_price_ht:0}]}; }
            else { this.formError = extractError(d); }
            this.saving=false;
        },
        async markSent(q) {
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/quotations/${q.id}/status`,{method:'PUT',headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify({status:'SENT'})});
            if(r.ok) { const d=await r.json(); const i=this.quotations.findIndex(x=>x.id===q.id); if(i>-1) this.quotations[i]=d; }
        },
        async convertQuotation(q) {
            const invDate = prompt(this.lang==='FR'?'Date de facture (YYYY-MM-DD):':'Invoice date (YYYY-MM-DD):', new Date().toISOString().slice(0,10));
            if(!invDate) return;
            const dueDate = prompt(this.lang==='FR'?'Échéance (YYYY-MM-DD):':'Due date (YYYY-MM-DD):', new Date(Date.now()+30*86400000).toISOString().slice(0,10));
            if(!dueDate) return;
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/quotations/${q.id}/convert`,{method:'POST',headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify({invoice_date:invDate,due_date:dueDate})});
            if(r.ok) { const d=await r.json(); const i=this.quotations.findIndex(x=>x.id===q.id); if(i>-1) this.quotations[i]=d.quotation; alert('Facture '+d.invoice.invoice_number+' créée.'); }
        },
    };
}

function purchaseOrdersPanel() {
    return {
        _cid: null, orders: [], suppliers: [], showForm: false, saving: false, poError: '',
        form: { supplier_id:'', order_date: new Date().toISOString().slice(0,10), expected_delivery_date:'', notes:'', lines:[{description:'',quantity:1,unit_price_ht:0}] },
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this._cid = me.company?.id;
            const [po, s] = await Promise.all([
                fetch(`/api/v1/companies/${this._cid}/purchase-orders`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
                fetch(`/api/v1/companies/${this._cid}/suppliers`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
            ]);
            this.orders    = po.data ?? po;
            this.suppliers = s.data ?? s;
        },
        async submitPO() {
            this.saving=true; this.poError='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/purchase-orders`,{method:'POST',headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify(this.form)});
            const d = await r.json();
            if(r.ok) { this.orders.unshift(d); this.showForm=false; this.form={supplier_id:'',order_date:new Date().toISOString().slice(0,10),expected_delivery_date:'',notes:'',lines:[{description:'',quantity:1,unit_price_ht:0}]}; }
            else { this.poError = extractError(d); }
            this.saving=false;
        },
    };
}

function creditNotesPanel() {
    return {
        _cid: null, customers: [], suppliers: [],
        cnForm: { customer_id:'', credit_note_date: new Date().toISOString().slice(0,10), amount_ht:0, reason:'' },
        snForm: { supplier_id:'', credit_note_date: new Date().toISOString().slice(0,10), amount_ht:0, reason:'' },
        cnSaving: false, snSaving: false, cnError:'', snError:'', cnSuccess:'', snSuccess:'',
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this._cid = me.company?.id;
            const [c, s] = await Promise.all([
                fetch(`/api/v1/companies/${this._cid}/customers`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
                fetch(`/api/v1/companies/${this._cid}/suppliers`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
            ]);
            this.customers = c.data ?? c;
            this.suppliers = s.data ?? s;
        },
        async submitCustomerCN() {
            this.cnSaving=true; this.cnError=''; this.cnSuccess='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/customers/${this.cnForm.customer_id}/credit-notes`,{method:'POST',headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify(this.cnForm)});
            const d = await r.json();
            if(r.ok) { this.cnSuccess = 'Avoir '+d.credit_note_number+' créé.'; this.cnForm={customer_id:'',credit_note_date:new Date().toISOString().slice(0,10),amount_ht:0,reason:''}; }
            else { this.cnError = extractError(d); }
            this.cnSaving=false;
        },
        async submitSupplierCN() {
            this.snSaving=true; this.snError=''; this.snSuccess='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/suppliers/${this.snForm.supplier_id}/credit-notes`,{method:'POST',headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify(this.snForm)});
            const d = await r.json();
            if(r.ok) { this.snSuccess = 'Avoir '+d.credit_note_number+' enregistré.'; this.snForm={supplier_id:'',credit_note_date:new Date().toISOString().slice(0,10),amount_ht:0,reason:''}; }
            else { this.snError = extractError(d); }
            this.snSaving=false;
        },
    };
}

function patentePanel() {
    return {
        _cid: null, records: [], showForm: false, saving: false, formError: '',
        form: { tax_year: new Date().getFullYear(), patente_number:'', amount_due_xaf:0, due_date:'', notes:'' },
        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me',{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this._cid = me.company?.id;
            const d = await fetch(`/api/v1/companies/${this._cid}/patente`,{headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this.records = d;
        },
        async submitPatente() {
            this.saving=true; this.formError='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/patente`,{method:'POST',headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify(this.form)});
            const d = await r.json();
            if(r.ok) { this.records.unshift(d); this.showForm=false; this.form={tax_year:new Date().getFullYear(),patente_number:'',amount_due_xaf:0,due_date:'',notes:''}; }
            else { this.formError = extractError(d); }
            this.saving=false;
        },
        async payPatente(p) {
            const amount  = prompt(this.lang==='FR'?'Montant à payer (XAF):':'Amount to pay (XAF):');
            if(!amount || isNaN(amount)) return;
            const date    = prompt(this.lang==='FR'?'Date de paiement (YYYY-MM-DD):':'Payment date (YYYY-MM-DD):', new Date().toISOString().slice(0,10));
            if(!date) return;
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/patente/${p.id}/pay`,{method:'POST',headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},body:JSON.stringify({amount_paid_xaf:Number(amount),paid_date:date})});
            if(r.ok) { const d=await r.json(); const i=this.records.findIndex(x=>x.id===p.id); if(i>-1) this.records[i]=d; }
        },
    };
}

function stockPanel() {
    return {
        _cid: null,
        valuation: [],
        totalValue: 0,
        ledger: null,
        showForm: false,
        saving: false,
        formError: '',
        form: { product_code:'', product_name:'', account_code:'310000', movement_type:'IN', quantity:1, unit_cost_xaf:0, movement_date: new Date().toISOString().slice(0,10), reference:'', post_to_gl:true },

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me', {headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this._cid = me.company?.id;
            await this.loadValuation();
        },

        async loadValuation() {
            const token = localStorage.getItem('opes_token');
            const d = await fetch(`/api/v1/companies/${this._cid}/stock/valuation`, {headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this.valuation   = d.items ?? [];
            this.totalValue  = d.total_value_xaf ?? 0;
        },

        async loadLedger(code) {
            const token = localStorage.getItem('opes_token');
            this.ledger = await fetch(`/api/v1/companies/${this._cid}/stock/ledger?product_code=${encodeURIComponent(code)}`, {headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
        },

        async submitMovement() {
            this.saving=true; this.formError='';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/stock`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify(this.form)
            });
            const d = await r.json();
            if(r.ok) {
                this.showForm=false;
                this.form={ product_code:'', product_name:'', account_code:'310000', movement_type:'IN', quantity:1, unit_cost_xaf:0, movement_date:new Date().toISOString().slice(0,10), reference:'', post_to_gl:true };
                await this.loadValuation();
                if(this.ledger) await this.loadLedger(d.product_code);
            } else { this.formError = extractError(d); }
            this.saving=false;
        },
    };
}

function deliveryNotesPanel() {
    return {
        _cid: null, items: [], loading: false, showForm: false, saving: false, err: '',
        customers: [], suppliers: [],
        filterType: '', filterStatus: '',
        form: {
            dn_type:'OUT', delivery_date: new Date().toISOString().slice(0,10),
            customer_id:'', supplier_id:'', delivery_address:'', notes:'',
            lines: [{ description:'', product_code:'', quantity:1, unit:'' }],
        },

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me', {headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this._cid = me.company?.id;
            const [cu, su] = await Promise.all([
                fetch(`/api/v1/companies/${this._cid}/customers?per_page=200`, {headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
                fetch(`/api/v1/companies/${this._cid}/suppliers?per_page=200`, {headers:{Authorization:'Bearer '+token}}).then(r=>r.json()),
            ]);
            this.customers = cu.data ?? cu ?? [];
            this.suppliers = su.data ?? su ?? [];
            await this.load();
        },

        addLine() { this.form.lines.push({ description:'', product_code:'', quantity:1, unit:'' }); },
        removeLine(i) { if(this.form.lines.length > 1) this.form.lines.splice(i,1); },

        async load() {
            this.loading = true;
            const token = localStorage.getItem('opes_token');
            const params = new URLSearchParams();
            if(this.filterType) params.append('dn_type', this.filterType);
            if(this.filterStatus) params.append('status', this.filterStatus);
            const d = await fetch(`/api/v1/companies/${this._cid}/delivery-notes?${params}`, {headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this.items = d.data ?? [];
            this.loading = false;
        },

        async save() {
            this.err = '';
            if(!this.form.delivery_date) { this.err='Date de livraison obligatoire.'; return; }
            if(this.form.dn_type==='OUT' && !this.form.customer_id) { this.err='Client obligatoire pour une expédition.'; return; }
            if(this.form.dn_type==='IN'  && !this.form.supplier_id) { this.err='Fournisseur obligatoire pour une réception.'; return; }
            const badLines = this.form.lines.filter(l=>!l.description||Number(l.quantity)<=0);
            if(badLines.length) { this.err='Chaque ligne doit avoir une désignation et une quantité > 0.'; return; }

            this.saving = true;
            const token = localStorage.getItem('opes_token');
            const payload = { ...this.form, lines: this.form.lines.map(l=>({...l, quantity:Number(l.quantity)})) };
            if(payload.dn_type==='OUT') delete payload.supplier_id;
            else delete payload.customer_id;
            const r = await fetch(`/api/v1/companies/${this._cid}/delivery-notes`, {
                method:'POST', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify(payload)
            });
            const d = await r.json();
            if(r.ok) {
                this.showForm=false;
                this.form={ dn_type:'OUT', delivery_date:new Date().toISOString().slice(0,10), customer_id:'', supplier_id:'', delivery_address:'', notes:'', lines:[{description:'',product_code:'',quantity:1,unit:''}] };
                await this.load();
            } else { this.err = extractError(d); }
            this.saving = false;
        },

        async markStatus(dn, status) {
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/delivery-notes/${dn.id}/status`, {
                method:'PUT', headers:{'Content-Type':'application/json',Authorization:'Bearer '+token},
                body: JSON.stringify({status})
            });
            if(r.ok) await this.load();
        },
    };
}

function cashflowPanel() {
    return {
        _cid: null, data: null, loading: false, err: '',

        async init() {
            const token = localStorage.getItem('opes_token');
            const me = await fetch('/api/v1/auth/me', {headers:{Authorization:'Bearer '+token}}).then(r=>r.json());
            this._cid = me.company?.id;
            await this.load();
        },

        async load() {
            this.loading = true; this.err = '';
            const token = localStorage.getItem('opes_token');
            const r = await fetch(`/api/v1/companies/${this._cid}/cashflow/projection`, {headers:{Authorization:'Bearer '+token}});
            if(r.ok) { this.data = await r.json(); }
            else { const d = await r.json(); this.err = d.message ?? 'Erreur'; }
            this.loading = false;
        },

        fmtXaf(v) { return new Intl.NumberFormat('fr-CM',{style:'currency',currency:'XAF',maximumFractionDigits:0}).format(v??0); },
    };
}
</script>
</body>
</html>
