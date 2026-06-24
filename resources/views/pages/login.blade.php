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
    </style>
</head>
<body class="min-h-screen bg-[#0A192F] flex items-center justify-center p-4" x-data="loginApp()" x-cloak>

    <div class="w-full max-w-md">

        {{-- Brand --}}
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-white tracking-tight">
                OPES<span class="text-[#F59E0B]">BOOKS</span>
            </h1>
            <p class="text-slate-400 text-sm mt-2">
                <span x-show="lang === 'FR'">Votre Bouclier Fiscal Camerounais</span>
                <span x-show="lang === 'EN'">Your Cameroonian Tax Shield</span>
            </p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">

            {{-- Tab header --}}
            <div class="flex border-b border-slate-200">
                <button @click="tab = 'login'"
                        :class="tab === 'login' ? 'border-b-2 border-[#F59E0B] text-[#0A192F] font-black' : 'text-slate-400'"
                        class="flex-1 py-4 text-sm uppercase tracking-wider transition-colors">
                    <span x-show="lang === 'FR'">Connexion</span>
                    <span x-show="lang === 'EN'">Login</span>
                </button>
                <button @click="tab = 'register'"
                        :class="tab === 'register' ? 'border-b-2 border-[#F59E0B] text-[#0A192F] font-black' : 'text-slate-400'"
                        class="flex-1 py-4 text-sm uppercase tracking-wider transition-colors">
                    <span x-show="lang === 'FR'">Créer un Compte</span>
                    <span x-show="lang === 'EN'">Register</span>
                </button>
            </div>

            <div class="p-6">

                {{-- Error banner --}}
                <div x-show="error" class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3 text-sm text-red-700 font-medium" x-text="error"></div>

                {{-- ── LOGIN FORM ──────────────────────────────────────────────── --}}
                <form x-show="tab === 'login'" @submit.prevent="doLogin" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                            <span x-show="lang === 'FR'">Adresse Email</span>
                            <span x-show="lang === 'EN'">Email Address</span>
                        </label>
                        <input type="email" x-model="loginForm.email" required
                               class="w-full border-2 border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#F59E0B] transition-colors"
                               placeholder="owner@entreprise.cm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase mb-1">
                            <span x-show="lang === 'FR'">Mot de Passe</span>
                            <span x-show="lang === 'EN'">Password</span>
                        </label>
                        <input type="password" x-model="loginForm.password" required
                               class="w-full border-2 border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-[#F59E0B] transition-colors">
                    </div>
                    <button type="submit" :disabled="loading"
                            class="w-full bg-[#0A192F] hover:bg-slate-800 text-white font-black py-3 rounded-lg uppercase tracking-wide transition-colors disabled:opacity-50">
                        <span x-show="!loading">
                            <span x-show="lang === 'FR'">Se Connecter</span>
                            <span x-show="lang === 'EN'">Sign In</span>
                        </span>
                        <span x-show="loading">...</span>
                    </button>
                </form>

                {{-- ── REGISTER FORM ───────────────────────────────────────────── --}}
                <form x-show="tab === 'register'" @submit.prevent="doRegister" class="space-y-3">
                    <p class="text-xs font-bold text-[#F59E0B] uppercase tracking-wider mb-2">
                        <span x-show="lang === 'FR'">Informations Entreprise</span>
                        <span x-show="lang === 'EN'">Company Details</span>
                    </p>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <input type="text" x-model="regForm.company_name" required placeholder="Nom de l'entreprise"
                                   class="w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B]">
                        </div>
                        <div>
                            <input type="text" x-model="regForm.company_niu" required placeholder="NIU (ex: M08200001)"
                                   class="w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B]">
                        </div>
                        <div>
                            <input type="text" x-model="regForm.company_rccm" required placeholder="RCCM"
                                   class="w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B]">
                        </div>
                        <div>
                            <select x-model="regForm.company_tax_regime" required
                                    class="w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B]">
                                <option value="">Régime fiscal...</option>
                                <option value="REEL">REEL</option>
                                <option value="SIMPLIFIE">SIMPLIFIÉ</option>
                                <option value="LIBERATOIRE">LIBÉRATOIRE</option>
                            </select>
                        </div>
                        <div>
                            <input type="text" x-model="regForm.company_tax_center" required placeholder="Centre fiscal (ex: CIME Douala I)"
                                   class="w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B]">
                        </div>
                    </div>

                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider pt-2">
                        <span x-show="lang === 'FR'">Votre Compte Administrateur</span>
                        <span x-show="lang === 'EN'">Owner Account</span>
                    </p>

                    <input type="text" x-model="regForm.name" required placeholder="Votre nom complet"
                           class="w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B]">
                    <input type="email" x-model="regForm.email" required placeholder="Email"
                           class="w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B]">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="password" x-model="regForm.password" required placeholder="Mot de passe"
                               class="w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B]">
                        <input type="password" x-model="regForm.password_confirmation" required placeholder="Confirmer"
                               class="w-full border-2 border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#F59E0B]">
                    </div>

                    <button type="submit" :disabled="loading"
                            class="w-full bg-[#F59E0B] hover:bg-amber-400 text-[#0A192F] font-black py-3 rounded-lg uppercase tracking-wide transition-colors disabled:opacity-50">
                        <span x-show="!loading">
                            <span x-show="lang === 'FR'">Créer Mon Compte</span>
                            <span x-show="lang === 'EN'">Create Account</span>
                        </span>
                        <span x-show="loading">...</span>
                    </button>
                </form>

            </div>
        </div>

        {{-- Lang toggle --}}
        <div class="text-center mt-4">
            <button @click="lang = lang === 'FR' ? 'EN' : 'FR'"
                    class="text-slate-400 hover:text-[#F59E0B] text-xs font-bold uppercase tracking-wider transition-colors">
                <span x-show="lang === 'FR'">Switch to English</span>
                <span x-show="lang === 'EN'">Passer en Français</span>
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
