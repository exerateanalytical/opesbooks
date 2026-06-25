<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpesBooks — Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-200 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-[11px] font-black text-amber-400">OB</div>
                <span class="text-white font-black text-lg tracking-widest">OPES<span class="text-amber-400">ADMIN</span></span>
            </div>
            <p class="text-slate-500 text-xs uppercase tracking-widest font-bold">Platform Console</p>
        </div>

        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl">
            <h1 class="text-lg font-black text-white mb-6 text-center">Sign In</h1>

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-500/15 border border-red-500/30 text-red-300 text-sm font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-amber-500/60 focus:ring-2 focus:ring-amber-500/10 transition-all"
                           placeholder="admin@opesbooks.cm">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Password</label>
                    <input type="password" name="password" required
                           class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2.5 text-sm text-white placeholder-slate-600 focus:outline-none focus:border-amber-500/60 focus:ring-2 focus:ring-amber-500/10 transition-all"
                           placeholder="••••••••">
                </div>
                <button type="submit"
                        class="w-full mt-2 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 transition-all
                               bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 shadow-lg shadow-amber-500/20">
                    Access Admin Panel
                </button>
            </form>
        </div>

        <p class="text-center text-slate-600 text-[10px] mt-6 uppercase tracking-widest">
            Restricted to SUPER_ADMIN accounts only
        </p>
    </div>

</body>
</html>
