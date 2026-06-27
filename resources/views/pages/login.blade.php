<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opes Books — Connexion</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Helvetica Neue', sans-serif;
            background: #0B1120;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        .glass-input {
            background: #1C2A3A;
            border: 1px solid #253347;
            color: #f1f5f9;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
            border-radius: 0.75rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
        }
        .glass-input::placeholder { color: #64748B; }
        .glass-input:focus {
            outline: none;
            border-color: rgba(245,158,11,0.7);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
        }
        select.glass-input option { background: #151F2E; }
        .btn-amber {
            background: #F59E0B;
            border: none;
            color: #0F172A;
            font-weight: 900;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-amber:hover { background: #D97706; box-shadow: 0 4px 18px rgba(245,158,11,0.35); }
        .btn-amber:active { transform: scale(0.98); }
        .btn-dark {
            background: #1C2A3A;
            border: 1px solid #253347;
            color: white;
            font-weight: 700;
            transition: all 0.2s;
            cursor: pointer;
        }
        .btn-dark:hover { border-color: rgba(255,255,255,0.22); }
        .btn-dark:active { transform: scale(0.98); }
        .demo-btn {
            background: transparent;
            border: 1px solid #253347;
            color: #94a3b8;
            transition: all 0.18s;
            cursor: pointer;
            text-align: left;
            border-radius: 0.75rem;
            padding: 0.5rem 0.75rem;
        }
        .demo-btn:hover { border-color: rgba(245,158,11,0.4); background: rgba(245,158,11,0.05); }
        @keyframes fade-up { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
        .fade-up { animation: fade-up 0.45s cubic-bezier(0.34,1.4,0.64,1) both; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 99px; }

        /* Right panel gradient orbs */
        .orb-1 {
            position: absolute; width: 340px; height: 340px; border-radius: 50%;
            background: radial-gradient(circle, rgba(245,158,11,0.14) 0%, transparent 70%);
            top: -80px; right: -60px; pointer-events: none;
        }
        .orb-2 {
            position: absolute; width: 260px; height: 260px; border-radius: 50%;
            background: radial-gradient(circle, rgba(16,185,129,0.10) 0%, transparent 70%);
            bottom: 40px; left: -40px; pointer-events: none;
        }

        /* Feature card */
        .feat-card {
            background: rgba(21,31,46,0.7);
            border: 1px solid rgba(37,51,71,0.8);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 0.875rem;
        }
        .feat-icon {
            width: 38px; height: 38px; border-radius: 0.625rem; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }

        /* Stat badge */
        .stat-badge {
            background: rgba(21,31,46,0.8);
            border: 1px solid #253347;
            border-radius: 0.875rem;
            padding: 0.75rem 1rem;
            text-align: center;
        }

        /* Layout */
        .split-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }
        @media (max-width: 900px) {
            .split-layout { grid-template-columns: 1fr; }
            .right-panel { display: none; }
        }
    </style>
</head>
<body x-data="loginApp()" x-cloak>

<div class="split-layout">

    {{-- ═══════════════════════════════════ LEFT — FORM ═══════════════════════════════════ --}}
    <div class="flex flex-col justify-center min-h-screen overflow-y-auto py-10 px-8 md:px-12 lg:px-16 relative"
         style="background:#0B1120;border-right:1px solid #151F2E">

        {{-- Top bar --}}
        <div class="flex items-center justify-between mb-10 fade-up">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl"
                     style="background:linear-gradient(145deg,rgba(245,158,11,0.25),rgba(245,158,11,0.08));border:1px solid rgba(245,158,11,0.3)">
                    <span class="text-amber-400 font-black text-xs tracking-widest">OB</span>
                </div>
                <span class="text-white font-black text-base tracking-tight">OPES<span class="text-amber-400">BOOKS</span></span>
            </div>
            <button @click="lang = lang==='FR'?'EN':'FR'; localStorage.setItem('opes_lang',lang)"
                    class="text-[10px] font-black uppercase tracking-widest transition-colors"
                    style="color:#64748b;background:none;border:none;cursor:pointer"
                    onmouseover="this.style.color='#F59E0B'" onmouseout="this.style.color='#64748b'">
                <span x-show="lang==='FR'">EN</span>
                <span x-show="lang==='EN'" x-cloak>FR</span>
            </button>
        </div>

        {{-- Heading --}}
        <div class="mb-7 fade-up" style="animation-delay:0.04s">
            <h1 class="text-2xl font-black text-white tracking-tight mb-1">
                <span x-show="tab==='login'">
                    <span x-show="lang==='FR'">Bienvenue</span>
                    <span x-show="lang==='EN'" x-cloak>Welcome back</span>
                </span>
                <span x-show="tab==='register'" x-cloak>
                    <span x-show="lang==='FR'">Créer un compte</span>
                    <span x-show="lang==='EN'" x-cloak>Create account</span>
                </span>
            </h1>
            <p class="text-sm" style="color:#64748b">
                <span x-show="tab==='login'">
                    <span x-show="lang==='FR'">Connectez-vous à votre espace comptable.</span>
                    <span x-show="lang==='EN'" x-cloak>Sign in to your accounting workspace.</span>
                </span>
                <span x-show="tab==='register'" x-cloak>
                    <span x-show="lang==='FR'">Votre bouclier fiscal en 2 minutes.</span>
                    <span x-show="lang==='EN'" x-cloak>Your tax shield set up in 2 minutes.</span>
                </span>
            </p>
        </div>

        {{-- Tab switcher --}}
        <div class="flex gap-1 p-1 rounded-xl mb-6 fade-up" style="background:#151F2E;animation-delay:0.07s">
            <button @click="tab='login';error=''"
                    class="flex-1 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all"
                    :style="tab==='login'
                        ? 'background:#1C2A3A;color:#F59E0B;box-shadow:0 1px 4px rgba(0,0,0,0.3)'
                        : 'color:#64748b;background:transparent'">
                <span x-show="lang==='FR'">Connexion</span>
                <span x-show="lang==='EN'" x-cloak>Sign in</span>
            </button>
            <button @click="tab='register';error=''"
                    class="flex-1 py-2 rounded-lg text-xs font-black uppercase tracking-wider transition-all"
                    :style="tab==='register'
                        ? 'background:#1C2A3A;color:#F59E0B;box-shadow:0 1px 4px rgba(0,0,0,0.3)'
                        : 'color:#64748b;background:transparent'">
                <span x-show="lang==='FR'">Inscription</span>
                <span x-show="lang==='EN'" x-cloak>Register</span>
            </button>
        </div>

        {{-- Error banner --}}
        <div x-show="error" class="mb-4 px-4 py-3 rounded-xl text-sm font-bold fade-up"
             style="background:rgba(244,63,94,0.12);border:1px solid rgba(244,63,94,0.28);color:rgb(252,165,165)"
             x-text="error"></div>

        {{-- ─── LOGIN FORM ─── --}}
        <div x-show="tab==='login'" class="fade-up" style="animation-delay:0.1s">

            {{-- Demo quick-access --}}
            <div class="mb-5">
                <p class="text-[10px] font-black uppercase tracking-widest mb-2.5" style="color:#475569">
                    <span x-show="lang==='FR'">Accès démo rapide</span>
                    <span x-show="lang==='EN'" x-cloak>Quick demo access</span>
                </p>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                            @click="loginForm.email='owner@demo.cm'; loginForm.password='demo1234'"
                            class="demo-btn">
                        <div class="flex items-center gap-1.5 mb-0.5" style="color:#F59E0B">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7z"/><path d="M5 20h14"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-wider">Owner</span>
                        </div>
                        <div class="text-[10px]" style="color:#475569">owner@demo.cm</div>
                    </button>
                    <button type="button"
                            @click="loginForm.email='accountant@demo.cm'; loginForm.password='demo1234'"
                            class="demo-btn">
                        <div class="flex items-center gap-1.5 mb-0.5" style="color:#818cf8">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-wider">Accountant</span>
                        </div>
                        <div class="text-[10px]" style="color:#475569">accountant@demo.cm</div>
                    </button>
                    <button type="button"
                            @click="loginForm.email='junior@demo.cm'; loginForm.password='demo1234'"
                            class="demo-btn">
                        <div class="flex items-center gap-1.5 mb-0.5" style="color:#94a3b8">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-wider">Junior Accountant</span>
                        </div>
                        <div class="text-[10px]" style="color:#475569">junior@demo.cm</div>
                    </button>
                    <button type="button"
                            @click="loginForm.email='cabinet@demo.cm'; loginForm.password='demo1234'"
                            class="demo-btn">
                        <div class="flex items-center gap-1.5 mb-0.5" style="color:#f87171">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span class="text-[10px] font-black uppercase tracking-wider">Cabinet Partner</span>
                        </div>
                        <div class="text-[10px]" style="color:#475569">cabinet@demo.cm</div>
                    </button>
                </div>

                {{-- Cabinet demo — full-width featured --}}
                <button type="button"
                        @click="loginForm.email='cabinet@demo.cm'; loginForm.password='demo1234'"
                        class="w-full mt-2 text-left rounded-xl px-4 py-3 transition-all"
                        style="background:rgba(245,158,11,0.07);border:1px solid rgba(245,158,11,0.28);cursor:pointer">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2" style="color:#F59E0B">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <span class="text-[11px] font-black uppercase tracking-wider">Cabinet Comptable — Demo Enterprise</span>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-wider px-2 py-0.5 rounded"
                              style="background:rgba(245,158,11,0.2);color:#F59E0B">NOUVEAU</span>
                    </div>
                    <div class="text-[10px] mt-1" style="color:#475569">cabinet@demo.cm · demo1234 · Gère 4 sociétés clientes</div>
                </button>

                <p class="text-[10px] mt-2 text-center" style="color:#334155">
                    <span x-show="lang==='FR'">Cliquez un rôle pour remplir automatiquement</span>
                    <span x-show="lang==='EN'" x-cloak>Click a role to auto-fill credentials</span>
                </p>
            </div>

            <form @submit.prevent="doLogin" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5" style="color:#64748b">
                        <span x-show="lang==='FR'">Adresse Email</span>
                        <span x-show="lang==='EN'" x-cloak>Email Address</span>
                    </label>
                    <input type="email" x-model="loginForm.email" required autocomplete="email"
                           class="glass-input" placeholder="owner@entreprise.cm">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5" style="color:#64748b">
                        <span x-show="lang==='FR'">Mot de Passe</span>
                        <span x-show="lang==='EN'" x-cloak>Password</span>
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" x-model="loginForm.password" required
                               autocomplete="current-password"
                               class="glass-input pr-10" placeholder="••••••••">
                        <button type="button" @click="showPassword=!showPassword" tabindex="-1"
                                style="position:absolute;right:0.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#64748b;padding:0"
                                onmouseover="this.style.color='#F59E0B'" onmouseout="this.style.color='#64748b'">
                            <svg x-show="!showPassword" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg x-show="showPassword" x-cloak width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                        </button>
                    </div>
                    <div class="text-right mt-1">
                        <button type="button" @click="showForgot=!showForgot;forgotMsg='';forgotErr=''"
                                style="background:none;border:none;cursor:pointer;font-size:0.625rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#475569;transition:color 0.2s"
                                onmouseover="this.style.color='#F59E0B'" onmouseout="this.style.color='#475569'">
                            <span x-show="lang==='FR'">Mot de passe oublié ?</span>
                            <span x-show="lang==='EN'" x-cloak>Forgot password?</span>
                        </button>
                    </div>
                    <div x-show="showForgot" x-cloak class="mt-3 p-3 rounded-xl" style="background:#1C2A3A;border:1px solid #253347">
                        <p class="text-[10px] mb-2" style="color:#94a3b8">
                            <span x-show="lang==='FR'">Entrez votre email pour recevoir un lien de réinitialisation.</span>
                            <span x-show="lang==='EN'" x-cloak>Enter your email to receive a reset link.</span>
                        </p>
                        <div class="flex gap-2">
                            <input type="email" x-model="forgotEmail" autocomplete="email"
                                   class="glass-input text-xs" placeholder="email@entreprise.cm">
                            <button type="button" @click="doForgot" :disabled="forgotLoading"
                                    class="btn-amber px-4 rounded-xl text-[10px] uppercase tracking-wider whitespace-nowrap disabled:opacity-40">
                                <span x-show="!forgotLoading">
                                    <span x-show="lang==='FR'">Envoyer</span><span x-show="lang==='EN'" x-cloak>Send</span>
                                </span>
                                <span x-show="forgotLoading" x-cloak>…</span>
                            </button>
                        </div>
                        <p x-show="forgotMsg" x-cloak class="text-[10px] mt-2 font-bold" style="color:#34d399" x-text="forgotMsg"></p>
                        <p x-show="forgotErr" x-cloak class="text-[10px] mt-2 font-bold" style="color:#f87171" x-text="forgotErr"></p>
                    </div>
                </div>

                {{-- 2FA --}}
                <div x-show="twoFactorRequired" x-cloak>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5" style="color:#64748b">
                        <span x-show="lang==='FR'">Code d'authentification</span>
                        <span x-show="lang==='EN'" x-cloak>Authentication code</span>
                    </label>
                    <input type="text" x-model="loginForm.code" inputmode="numeric" autocomplete="one-time-code"
                           class="glass-input text-center tracking-[0.4em] font-black" placeholder="000000" maxlength="9">
                    <p class="text-[10px] mt-1.5" style="color:#475569">
                        <span x-show="lang==='FR'">Saisissez le code de votre application (ou un code de récupération).</span>
                        <span x-show="lang==='EN'" x-cloak>Enter the code from your authenticator app (or a recovery code).</span>
                    </p>
                </div>

                <button type="submit" :disabled="loading"
                        class="w-full btn-dark py-3 rounded-xl uppercase tracking-widest text-xs disabled:opacity-40">
                    <span x-show="!loading">
                        <span x-show="twoFactorRequired">
                            <span x-show="lang==='FR'">Vérifier</span><span x-show="lang==='EN'" x-cloak>Verify</span>
                        </span>
                        <span x-show="!twoFactorRequired">
                            <span x-show="lang==='FR'">Se Connecter</span><span x-show="lang==='EN'" x-cloak>Sign In</span>
                        </span>
                    </span>
                    <span x-show="loading" x-cloak>…</span>
                </button>
            </form>
        </div>

        {{-- ─── REGISTER FORM ─── --}}
        <div x-show="tab==='register'" x-cloak class="fade-up" style="animation-delay:0.1s">
            <form @submit.prevent="doRegister" class="space-y-3">
                <p class="text-[10px] font-black uppercase tracking-widest mb-1" style="color:#F59E0B">
                    <span x-show="lang==='FR'">Informations Entreprise</span>
                    <span x-show="lang==='EN'" x-cloak>Company Details</span>
                </p>
                <input type="text" x-model="regForm.company_name" required
                       class="glass-input" placeholder="Nom de l'entreprise">
                <select x-model="regForm.company_country_code" required class="glass-input">
                    <option value="CM">🇨🇲 Cameroun (XAF · TVA 19,25% · NIU)</option>
                    <option value="GA">🇬🇦 Gabon (XAF · TVA 18% · NIF)</option>
                    <option value="CG">🇨🇬 Congo (XAF · TVA 18% · NIU)</option>
                    <option value="TD">🇹🇩 Tchad (XAF · TVA 18% · NIF)</option>
                    <option value="GQ">🇬🇶 Guinée Équatoriale (XAF · TVA 15% · NIF)</option>
                    <option value="CF">🇨🇫 Centrafrique (XAF · TVA 19% · NIF)</option>
                </select>
                <div class="grid grid-cols-2 gap-2.5">
                    <input type="text" x-model="regForm.company_niu" required
                           class="glass-input" placeholder="NIU (ex: M08200001)">
                    <input type="text" x-model="regForm.company_rccm" required
                           class="glass-input" placeholder="RCCM">
                    <select x-model="regForm.company_tax_regime" required class="glass-input">
                        <option value="">Régime fiscal…</option>
                        <option value="REEL">REEL</option>
                        <option value="SIMPLIFIE">SIMPLIFIÉ</option>
                        <option value="LIBERATOIRE">LIBÉRATOIRE</option>
                    </select>
                    <input type="text" x-model="regForm.company_tax_center" required
                           class="glass-input" placeholder="Centre fiscal">
                </div>

                <div class="border-t pt-3 mt-1" style="border-color:#1C2A3A">
                    <p class="text-[10px] font-black uppercase tracking-widest mb-2" style="color:#64748b">
                        <span x-show="lang==='FR'">Votre Compte Administrateur</span>
                        <span x-show="lang==='EN'" x-cloak>Owner Account</span>
                    </p>
                    <div class="space-y-2.5">
                        <input type="text" x-model="regForm.name" required
                               class="glass-input" placeholder="Votre nom complet">
                        <input type="email" x-model="regForm.email" required
                               class="glass-input" placeholder="Email">
                        <div class="grid grid-cols-2 gap-2.5">
                            <input type="password" x-model="regForm.password" required autocomplete="new-password"
                                   class="glass-input" placeholder="Mot de passe">
                            <input type="password" x-model="regForm.password_confirmation" required autocomplete="new-password"
                                   class="glass-input" placeholder="Confirmer">
                        </div>
                        <div x-show="regForm.password.length > 0" x-cloak
                             x-data="{ get score(){ let s=0,p=regForm.password; if(p.length>=8)s++; if(/[A-Z]/.test(p))s++; if(/[0-9]/.test(p))s++; if(/[^A-Za-z0-9]/.test(p))s++; return s; }, get label(){ return ['','Très faible','Faible','Moyen','Fort'][this.score]; } }">
                            <div class="flex gap-1">
                                <template x-for="i in 4" :key="i">
                                    <div class="h-1 flex-1 rounded transition-colors"
                                         :style="score>=i ? (score<=1?'background:#ef4444':score===2?'background:#f59e0b':score===3?'background:#facc15':'background:#10b981') : 'background:#253347'"></div>
                                </template>
                            </div>
                            <p class="text-[10px] mt-1 font-bold"
                               :style="score<=1?'color:#f87171':score===2?'color:#fbbf24':score===3?'color:#facc15':'color:#34d399'"
                               x-text="label"></p>
                        </div>
                    </div>
                </div>

                <button type="submit" :disabled="loading"
                        class="w-full btn-amber py-3 rounded-xl uppercase tracking-widest text-xs disabled:opacity-40">
                    <span x-show="!loading">
                        <span x-show="lang==='FR'">Créer Mon Compte</span>
                        <span x-show="lang==='EN'" x-cloak>Create Account</span>
                    </span>
                    <span x-show="loading" x-cloak>…</span>
                </button>
            </form>
        </div>

    </div>

    {{-- ═══════════════════════════════════ RIGHT — FEATURES ═══════════════════════════════════ --}}
    <div class="right-panel relative overflow-hidden flex flex-col justify-between py-12 px-12"
         style="background:linear-gradient(160deg,#0D1929 0%,#0B1120 60%,#0A1018 100%)">

        <div class="orb-1"></div>
        <div class="orb-2"></div>

        <div class="relative z-10">

            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full mb-8"
                 style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.25)">
                <span style="width:6px;height:6px;background:#F59E0B;border-radius:50%;display:inline-block;box-shadow:0 0 8px rgba(245,158,11,0.7)"></span>
                <span class="text-[10px] font-black uppercase tracking-widest" style="color:#F59E0B">SYSCOHADA Revised · DGI Fiscalis 2026</span>
            </div>

            <h2 class="text-3xl font-black text-white mb-3 leading-tight">
                La comptabilité<br>
                <span style="color:#F59E0B">camerounaise</span><br>
                réinventée.
            </h2>
            <p class="text-sm mb-10" style="color:#64748b;max-width:340px;line-height:1.7">
                Gestion SYSCOHADA, e-facturation DGI en temps réel, TVA 19,25%, et suivi multi-sociétés — tout-en-un.
            </p>

            {{-- Feature cards --}}
            <div class="space-y-3 mb-10">
                <div class="feat-card">
                    <div class="feat-icon" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.2)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-black text-white mb-0.5">Journal & Grand Livre SYSCOHADA</div>
                        <div class="text-xs" style="color:#475569">Plan comptable 2017 intégré. Exportation DGI-Fiscalis en un clic.</div>
                    </div>
                </div>
                <div class="feat-card">
                    <div class="feat-icon" style="background:rgba(16,185,129,0.10);border:1px solid rgba(16,185,129,0.2)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-black text-white mb-0.5">E-facturation DGI temps réel</div>
                        <div class="text-xs" style="color:#475569">Synchronisation SIGIT automatique. Conformité Loi Finance 2026.</div>
                    </div>
                </div>
                <div class="feat-card">
                    <div class="feat-icon" style="background:rgba(99,102,241,0.10);border:1px solid rgba(99,102,241,0.2)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#818cf8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-black text-white mb-0.5">Portail Cabinet Comptable</div>
                        <div class="text-xs" style="color:#475569">Multi-dossiers clients, rapports consolidés, gestion d'équipe par rôle.</div>
                    </div>
                </div>
                <div class="feat-card">
                    <div class="feat-icon" style="background:rgba(251,191,36,0.10);border:1px solid rgba(251,191,36,0.2)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    </div>
                    <div>
                        <div class="text-sm font-black text-white mb-0.5">Paiement Mobile Money</div>
                        <div class="text-xs" style="color:#475569">STK push MTN MoMo & Orange Money. Abonnement sans friction.</div>
                    </div>
                </div>
            </div>

            {{-- Stats row --}}
            <div class="grid grid-cols-3 gap-3">
                <div class="stat-badge">
                    <div class="text-xl font-black" style="color:#F59E0B">19,25%</div>
                    <div class="text-[10px] uppercase tracking-wider mt-0.5" style="color:#475569">TVA TTC</div>
                </div>
                <div class="stat-badge">
                    <div class="text-xl font-black" style="color:#10b981">6</div>
                    <div class="text-[10px] uppercase tracking-wider mt-0.5" style="color:#475569">Pays CEMAC</div>
                </div>
                <div class="stat-badge">
                    <div class="text-xl font-black" style="color:#818cf8">2026</div>
                    <div class="text-[10px] uppercase tracking-wider mt-0.5" style="color:#475569">Loi Finance</div>
                </div>
            </div>
        </div>

        {{-- Footer quote --}}
        <div class="relative z-10 mt-10 pt-6" style="border-top:1px solid #151F2E">
            <p class="text-xs italic" style="color:#334155;line-height:1.6">
                "La conformité fiscale n'est plus une contrainte — c'est un avantage concurrentiel."
            </p>
            <p class="text-[10px] font-bold mt-1.5 uppercase tracking-widest" style="color:#1e3a5f">— Opesware · Douala, Cameroun</p>
        </div>
    </div>

</div>

<script>
function loginApp() {
    return {
        tab: 'login',
        lang: localStorage.getItem('opes_lang') || 'FR',
        loading: false,
        error: '',
        showPassword: false,
        showForgot: false,
        forgotEmail: '',
        forgotLoading: false,
        forgotMsg: '',
        forgotErr: '',
        twoFactorRequired: false,
        loginForm: { email: '', password: '', code: '' },
        regForm: {
            company_name: '', company_niu: '', company_rccm: '',
            company_tax_regime: '', company_tax_center: '', company_country_code: 'CM',
            name: '', email: '', password: '', password_confirmation: '',
        },
        async doLogin() {
            this.loading = true; this.error = '';
            try {
                const res = await fetch('/api/v1/auth/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(this.loginForm),
                });
                const json = await res.json();
                if (res.status === 422 && json.two_factor_required) {
                    this.twoFactorRequired = true;
                    this.error = this.loginForm.code ? (json.message || 'Code invalide.') : '';
                    return;
                }
                if (!res.ok) throw new Error(json.message || Object.values(json.errors || {})[0]?.[0] || 'Login failed');
                localStorage.setItem('opes_token', json.token);
                localStorage.setItem('opes_user', JSON.stringify(json.user));
                if (json.company) localStorage.setItem('opes_company', JSON.stringify(json.company));
                window.location.href = json.user?.role === 'SUPER_ADMIN' ? '/admin' : '/app';
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },
        async doForgot() {
            if (!this.forgotEmail) { this.forgotErr = this.lang==='FR' ? 'Email requis.' : 'Email required.'; return; }
            this.forgotLoading = true; this.forgotMsg = ''; this.forgotErr = '';
            try {
                const res = await fetch('/api/v1/auth/forgot-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ email: this.forgotEmail }),
                });
                const json = await res.json();
                if (!res.ok) throw new Error(json.message || 'Erreur');
                this.forgotMsg = this.lang==='FR' ? 'Lien de réinitialisation envoyé par email.' : 'Reset link sent to your email.';
            } catch (e) {
                this.forgotErr = e.message;
            } finally {
                this.forgotLoading = false;
            }
        },
        async doRegister() {
            this.loading = true; this.error = '';
            try {
                const res = await fetch('/api/v1/auth/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(this.regForm),
                });
                const json = await res.json();
                if (!res.ok) {
                    const msgs = json.errors ? Object.values(json.errors).flat().join(' | ') : json.message;
                    throw new Error(msgs);
                }
                localStorage.setItem('opes_token', json.token);
                localStorage.setItem('opes_user', JSON.stringify(json.user));
                if (json.company) localStorage.setItem('opes_company', JSON.stringify(json.company));
                window.location.href = '/onboarding';
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
</body>
</html>
