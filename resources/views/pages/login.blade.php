<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opes Books — Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { amber: {
            300:'#E3B420', 400:'#C99B0E', 500:'#B5890C', 600:'#A07C08', 700:'#866709'
        } } } } };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Helvetica Neue', sans-serif;
            background: radial-gradient(ellipse 120% 80% at 20% -5%, #1a2d4f 0%, #0a192f 35%, #050d1a 65%, #0f0a1e 100%);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(ellipse 60% 40% at 10% 15%, rgba(201,155,14,0.09) 0%, transparent 60%),
                radial-gradient(ellipse 50% 35% at 90% 80%, rgba(16,185,129,0.07) 0%, transparent 55%);
        }
        .glass-card {
            background: linear-gradient(145deg, rgba(255,255,255,0.10) 0%, rgba(255,255,255,0.04) 100%);
            backdrop-filter: blur(32px) saturate(200%);
            -webkit-backdrop-filter: blur(32px) saturate(200%);
            border: 1px solid rgba(255,255,255,0.14);
            border-top-color: rgba(255,255,255,0.24);
            box-shadow: 0 8px 48px rgba(0,0,0,0.6), 0 1px 0 rgba(255,255,255,0.14) inset;
        }
        .glass-input {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.14);
            color: #f1f5f9;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
            border-radius: 0.75rem;
            padding: 0.625rem 1rem;
            font-size: 0.875rem;
        }
        .glass-input::placeholder { color: rgba(148,163,184,0.55); }
        .glass-input:focus {
            outline: none;
            border-color: rgba(201,155,14,0.6);
            box-shadow: 0 0 0 3px rgba(201,155,14,0.12);
        }
        select.glass-input option { background: #0a192f; }
        .glass-btn-amber {
            background: linear-gradient(135deg, rgba(201,155,14,0.95) 0%, rgba(160,124,8,0.95) 100%);
            border: 1px solid rgba(201,155,14,0.5);
            box-shadow: 0 4px 20px rgba(201,155,14,0.3), 0 1px 0 rgba(255,255,255,0.2) inset;
            color: #0a192f;
            font-weight: 900;
            transition: all 0.2s;
        }
        .glass-btn-amber:hover { box-shadow: 0 6px 28px rgba(201,155,14,0.4), 0 1px 0 rgba(255,255,255,0.25) inset; }
        .glass-btn-amber:active { transform: scale(0.98); }
        .glass-btn-dark {
            background: linear-gradient(135deg, rgba(30,58,100,0.9) 0%, rgba(15,30,58,0.95) 100%);
            border: 1px solid rgba(255,255,255,0.14);
            box-shadow: 0 4px 16px rgba(0,0,0,0.5), 0 1px 0 rgba(255,255,255,0.1) inset;
            color: white;
            font-weight: 900;
            transition: all 0.2s;
        }
        .glass-btn-dark:hover { border-color: rgba(255,255,255,0.22); }
        .glass-btn-dark:active { transform: scale(0.98); }
        @keyframes float-in { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
        .float-in { animation: float-in 0.4s cubic-bezier(0.34,1.56,0.64,1) both; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 99px; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative" x-data="loginApp()" x-cloak>

    <div class="w-full max-w-md relative z-10">

        {{-- Brand --}}
        <div class="text-center mb-8 float-in">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl mb-4"
                 style="background:linear-gradient(145deg,rgba(201,155,14,0.2),rgba(201,155,14,0.08));border:1px solid rgba(201,155,14,0.3);box-shadow:0 0 40px rgba(201,155,14,0.15)">
                <span class="text-amber-400 font-black text-lg tracking-widest">OB</span>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">
                OPES<span class="text-amber-400">BOOKS</span>
            </h1>
            <p class="text-slate-400 text-xs mt-2 font-medium uppercase tracking-widest">
                <span x-show="lang === 'FR'">Votre Bouclier Fiscal Camerounais</span>
                <span x-show="lang === 'EN'" x-cloak>Your Cameroonian Tax Shield</span>
            </p>
        </div>

        {{-- Glass card --}}
        <div class="glass-card rounded-3xl overflow-hidden float-in" style="animation-delay:0.05s">

            {{-- Tab header --}}
            <div class="flex border-b" style="border-color:rgba(255,255,255,0.08)">
                <button @click="tab = 'login'; error=''"
                        class="flex-1 py-4 text-xs font-black uppercase tracking-wider transition-all"
                        :style="tab === 'login'
                            ? 'color:rgb(201,155,14);border-bottom:2px solid rgb(201,155,14)'
                            : 'color:rgba(148,163,184,0.7)'">
                    <span x-show="lang === 'FR'">Connexion</span>
                    <span x-show="lang === 'EN'" x-cloak>Login</span>
                </button>
                <button @click="tab = 'register'; error=''"
                        class="flex-1 py-4 text-xs font-black uppercase tracking-wider transition-all"
                        :style="tab === 'register'
                            ? 'color:rgb(201,155,14);border-bottom:2px solid rgb(201,155,14)'
                            : 'color:rgba(148,163,184,0.7)'">
                    <span x-show="lang === 'FR'">Créer un Compte</span>
                    <span x-show="lang === 'EN'" x-cloak>Register</span>
                </button>
            </div>

            <div class="p-6">

                {{-- Error --}}
                <div x-show="error" class="mb-4 px-4 py-3 rounded-xl text-sm font-bold"
                     style="background:rgba(244,63,94,0.12);border:1px solid rgba(244,63,94,0.28);color:rgb(252,165,165)"
                     x-text="error"></div>

                {{-- Demo login shortcuts --}}
                <div x-show="tab === 'login'" class="mb-4">
                    <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 text-center">
                        <span x-show="lang === 'FR'">Accès Démo Rapide</span>
                        <span x-show="lang === 'EN'" x-cloak>Quick Demo Access</span>
                    </p>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button"
                                @click="loginForm.email='owner@testco.cm'; loginForm.password='password123'"
                                class="px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider text-left transition-all"
                                style="background:rgba(201,155,14,0.08);border:1px solid rgba(201,155,14,0.2)">
                            <div class="text-amber-400 flex items-center gap-1.5">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7z"/><path d="M5 20h14"/></svg>
                                Owner
                            </div>
                            <div class="text-slate-400 font-medium normal-case tracking-normal mt-0.5">owner@testco.cm</div>
                        </button>
                        <button type="button"
                                @click="loginForm.email='accountant@testco.cm'; loginForm.password='password123'"
                                class="px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider text-left transition-all"
                                style="background:rgba(99,102,241,0.08);border:1px solid rgba(99,102,241,0.2)">
                            <div class="text-indigo-400 flex items-center gap-1.5">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                                Accountant
                            </div>
                            <div class="text-slate-400 font-medium normal-case tracking-normal mt-0.5">accountant@testco.cm</div>
                        </button>
                        <button type="button"
                                @click="loginForm.email='clerk@testco.cm'; loginForm.password='password123'"
                                class="px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider text-left transition-all"
                                style="background:rgba(100,116,139,0.08);border:1px solid rgba(100,116,139,0.2)">
                            <div class="text-slate-400 flex items-center gap-1.5">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                Clerk
                            </div>
                            <div class="text-slate-400 font-medium normal-case tracking-normal mt-0.5">clerk@testco.cm</div>
                        </button>
                        <button type="button"
                                @click="loginForm.email='admin@opesbooks.cm'; loginForm.password='yourpassword'"
                                class="px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider text-left transition-all"
                                style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2)">
                            <div class="text-red-400 flex items-center gap-1.5">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                                Super Admin
                            </div>
                            <div class="text-slate-400 font-medium normal-case tracking-normal mt-0.5">admin@opesbooks.cm</div>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-600 text-center mt-1.5">
                        <span x-show="lang === 'FR'">Cliquez un rôle pour remplir automatiquement</span>
                        <span x-show="lang === 'EN'" x-cloak>Click a role to auto-fill credentials</span>
                    </p>
                </div>

                {{-- LOGIN FORM --}}
                <form x-show="tab === 'login'" @submit.prevent="doLogin" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            <span x-show="lang === 'FR'">Adresse Email</span>
                            <span x-show="lang === 'EN'" x-cloak>Email Address</span>
                        </label>
                        <input type="email" x-model="loginForm.email" required autocomplete="email"
                               class="glass-input" placeholder="owner@entreprise.cm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            <span x-show="lang === 'FR'">Mot de Passe</span>
                            <span x-show="lang === 'EN'" x-cloak>Password</span>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" x-model="loginForm.password" required
                                   autocomplete="current-password"
                                   class="glass-input pr-10" placeholder="••••••••">
                            <button type="button" @click="showPassword = !showPassword" tabindex="-1"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-amber-400 transition-colors"
                                    :aria-label="showPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe'">
                                <svg x-show="!showPassword" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg x-show="showPassword" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                            </button>
                        </div>
                        <div class="text-right mt-1.5">
                            <button type="button" @click="showForgot = !showForgot; forgotMsg=''; forgotErr=''"
                                    class="text-[10px] text-slate-500 hover:text-amber-400 transition-colors font-bold uppercase tracking-wider">
                                <span x-show="lang === 'FR'">Mot de passe oublié ?</span>
                                <span x-show="lang === 'EN'" x-cloak>Forgot password?</span>
                            </button>
                        </div>
                        <div x-show="showForgot" x-cloak class="mt-3 p-3 rounded-xl" style="background:rgba(255,255,255,0.04);border:1px solid rgba(255,255,255,0.1)">
                            <p class="text-[10px] text-slate-400 mb-2">
                                <span x-show="lang === 'FR'">Entrez votre email pour recevoir un lien de réinitialisation.</span>
                                <span x-show="lang === 'EN'" x-cloak>Enter your email to receive a reset link.</span>
                            </p>
                            <div class="flex gap-2">
                                <input type="email" x-model="forgotEmail" autocomplete="email"
                                       class="glass-input text-xs" placeholder="email@entreprise.cm">
                                <button type="button" @click="doForgot" :disabled="forgotLoading"
                                        class="glass-btn-amber px-4 rounded-xl text-[10px] uppercase tracking-wider whitespace-nowrap disabled:opacity-40">
                                    <span x-show="!forgotLoading">
                                        <span x-show="lang === 'FR'">Envoyer</span><span x-show="lang === 'EN'" x-cloak>Send</span>
                                    </span>
                                    <span x-show="forgotLoading" x-cloak>…</span>
                                </button>
                            </div>
                            <p x-show="forgotMsg" x-cloak class="text-[10px] text-emerald-400 mt-2 font-bold" x-text="forgotMsg"></p>
                            <p x-show="forgotErr" x-cloak class="text-[10px] text-red-400 mt-2 font-bold" x-text="forgotErr"></p>
                        </div>
                    </div>
                    <button type="submit" :disabled="loading"
                            class="w-full glass-btn-dark py-3 rounded-xl uppercase tracking-widest text-xs disabled:opacity-40">
                        <span x-show="!loading">
                            <span x-show="lang === 'FR'">Se Connecter</span>
                            <span x-show="lang === 'EN'" x-cloak>Sign In</span>
                        </span>
                        <span x-show="loading" x-cloak>…</span>
                    </button>
                </form>

                {{-- REGISTER FORM --}}
                <form x-show="tab === 'register'" x-cloak @submit.prevent="doRegister" class="space-y-3">
                    <p class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-3">
                        <span x-show="lang === 'FR'">Informations Entreprise</span>
                        <span x-show="lang === 'EN'" x-cloak>Company Details</span>
                    </p>

                    <input type="text" x-model="regForm.company_name" required
                           class="glass-input" placeholder="Nom de l'entreprise">
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

                    <div class="border-t pt-3" style="border-color:rgba(255,255,255,0.08)">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                            <span x-show="lang === 'FR'">Votre Compte Administrateur</span>
                            <span x-show="lang === 'EN'" x-cloak>Owner Account</span>
                        </p>
                        <div class="space-y-2.5">
                            <input type="text" x-model="regForm.name" required
                                   class="glass-input" placeholder="Votre nom complet">
                            <input type="email" x-model="regForm.email" required
                                   class="glass-input" placeholder="Email">
                            <div class="grid grid-cols-2 gap-2.5">
                                <input type="password" x-model="regForm.password" required
                                       class="glass-input" placeholder="Mot de passe">
                                <input type="password" x-model="regForm.password_confirmation" required
                                       class="glass-input" placeholder="Confirmer">
                            </div>
                        </div>
                    </div>

                    <button type="submit" :disabled="loading"
                            class="w-full glass-btn-amber py-3 rounded-xl uppercase tracking-widest text-xs disabled:opacity-40">
                        <span x-show="!loading">
                            <span x-show="lang === 'FR'">Créer Mon Compte</span>
                            <span x-show="lang === 'EN'" x-cloak>Create Account</span>
                        </span>
                        <span x-show="loading" x-cloak>…</span>
                    </button>
                </form>

            </div>
        </div>

        {{-- Lang toggle --}}
        <div class="text-center mt-5">
            <button @click="lang = lang === 'FR' ? 'EN' : 'FR'; localStorage.setItem('opes_lang', lang)"
                    class="text-slate-500 hover:text-amber-400 text-[10px] font-black uppercase tracking-widest transition-colors">
                <span x-show="lang === 'FR'">Switch to English</span>
                <span x-show="lang === 'EN'" x-cloak>Passer en Français</span>
            </button>
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
            loginForm: { email: '', password: '' },
            regForm: {
                company_name: '', company_niu: '', company_rccm: '',
                company_tax_regime: '', company_tax_center: '',
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
                    if (!res.ok) throw new Error(json.message || Object.values(json.errors || {})[0]?.[0] || 'Login failed');
                    localStorage.setItem('opes_token', json.token);
                    localStorage.setItem('opes_user', JSON.stringify(json.user));
                    if (json.company) localStorage.setItem('opes_company', JSON.stringify(json.company));
                    window.location.href = '/app';
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
                    window.location.href = '/app';
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
