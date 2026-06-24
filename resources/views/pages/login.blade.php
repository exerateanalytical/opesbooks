<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opes Books — Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                radial-gradient(ellipse 60% 40% at 10% 15%, rgba(245,158,11,0.09) 0%, transparent 60%),
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
            border-color: rgba(245,158,11,0.6);
            box-shadow: 0 0 0 3px rgba(245,158,11,0.12);
        }
        select.glass-input option { background: #0a192f; }
        .glass-btn-amber {
            background: linear-gradient(135deg, rgba(245,158,11,0.95) 0%, rgba(217,119,6,0.95) 100%);
            border: 1px solid rgba(245,158,11,0.5);
            box-shadow: 0 4px 20px rgba(245,158,11,0.3), 0 1px 0 rgba(255,255,255,0.2) inset;
            color: #0a192f;
            font-weight: 900;
            transition: all 0.2s;
        }
        .glass-btn-amber:hover { box-shadow: 0 6px 28px rgba(245,158,11,0.4), 0 1px 0 rgba(255,255,255,0.25) inset; }
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
                 style="background:linear-gradient(145deg,rgba(245,158,11,0.2),rgba(245,158,11,0.08));border:1px solid rgba(245,158,11,0.3);box-shadow:0 0 40px rgba(245,158,11,0.15)">
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
                <button @click="tab = 'login'"
                        class="flex-1 py-4 text-xs font-black uppercase tracking-wider transition-all"
                        :style="tab === 'login'
                            ? 'color:rgb(245,158,11);border-bottom:2px solid rgb(245,158,11)'
                            : 'color:rgba(148,163,184,0.7)'">
                    <span x-show="lang === 'FR'">Connexion</span>
                    <span x-show="lang === 'EN'" x-cloak>Login</span>
                </button>
                <button @click="tab = 'register'"
                        class="flex-1 py-4 text-xs font-black uppercase tracking-wider transition-all"
                        :style="tab === 'register'
                            ? 'color:rgb(245,158,11);border-bottom:2px solid rgb(245,158,11)'
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
                                @click="loginForm.email='owner@demo.cm'; loginForm.password='demo1234'"
                                class="px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider text-left transition-all"
                                style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2)">
                            <div class="text-amber-400">Owner</div>
                            <div class="text-slate-400 font-medium normal-case tracking-normal mt-0.5">owner@demo.cm</div>
                        </button>
                        <button type="button"
                                @click="loginForm.email='accountant@demo.cm'; loginForm.password='demo1234'"
                                class="px-3 py-2 rounded-xl text-[10px] font-black uppercase tracking-wider text-left transition-all"
                                style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2)">
                            <div class="text-emerald-400">Accountant</div>
                            <div class="text-slate-400 font-medium normal-case tracking-normal mt-0.5">accountant@demo.cm</div>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-600 text-center mt-1.5">
                        <span x-show="lang === 'FR'">Mot de passe démo : <span class="text-slate-500 font-mono">demo1234</span></span>
                        <span x-show="lang === 'EN'" x-cloak>Demo password: <span class="text-slate-500 font-mono">demo1234</span></span>
                    </p>
                </div>

                {{-- LOGIN FORM --}}
                <form x-show="tab === 'login'" @submit.prevent="doLogin" class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            <span x-show="lang === 'FR'">Adresse Email</span>
                            <span x-show="lang === 'EN'" x-cloak>Email Address</span>
                        </label>
                        <input type="email" x-model="loginForm.email" required
                               class="glass-input" placeholder="owner@entreprise.cm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">
                            <span x-show="lang === 'FR'">Mot de Passe</span>
                            <span x-show="lang === 'EN'" x-cloak>Password</span>
                        </label>
                        <input type="password" x-model="loginForm.password" required
                               class="glass-input" placeholder="••••••••">
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
            <button @click="lang = lang === 'FR' ? 'EN' : 'FR'"
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
