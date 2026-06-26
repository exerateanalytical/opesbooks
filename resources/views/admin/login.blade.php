<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpesBooks — Admin Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{--c-bg:#0B1120;--c-surface:#151F2E;--c-raised:#1C2A3A;--c-border:#253347;--c-border-strong:#334155;--c-accent:#F59E0B;--c-text:#F0F4FA;--c-muted:#8B9EC0;--c-faint:#4E647E}
        *{box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:var(--c-bg);color:var(--c-text);-webkit-font-smoothing:antialiased}
        .field{width:100%;background:var(--c-raised);border:1.5px solid var(--c-border);color:var(--c-text);border-radius:0.75rem;padding:0.625rem 1rem;font-size:0.875rem;font-family:inherit;transition:border-color .15s,box-shadow .15s;outline:none}
        .field::placeholder{color:var(--c-faint)}
        .field:focus{border-color:var(--c-accent);box-shadow:0 0 0 3px rgba(245,158,11,.12)}
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 mb-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-[11px] font-black" style="background:rgba(245,158,11,0.12);border:1px solid rgba(245,158,11,0.3);color:#F59E0B">OB</div>
                <span class="text-white font-black text-lg tracking-widest">OPES<span style="color:#F59E0B">ADMIN</span></span>
            </div>
            <p class="text-xs font-bold uppercase tracking-widest" style="color:var(--c-faint)">Platform Console</p>
        </div>

        <div class="rounded-2xl p-8" style="background:var(--c-surface);border:1px solid var(--c-border);box-shadow:0 4px 24px rgba(0,0,0,0.5)">
            <h1 class="text-lg font-black text-white mb-6 text-center">Sign In</h1>

            @if($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl text-sm font-semibold" style="background:rgba(244,63,94,0.1);border:1px solid rgba(244,63,94,0.25);color:rgb(252,165,165)">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5" style="color:var(--c-muted)">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                           class="field" placeholder="admin@opesbooks.cm">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5" style="color:var(--c-muted)">Password</label>
                    <input type="password" name="password" required class="field" placeholder="••••••••">
                </div>
                <button type="submit"
                        class="w-full mt-2 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest transition-all"
                        style="background:#F59E0B;color:#0B1120;box-shadow:0 2px 8px rgba(245,158,11,0.3)">
                    Access Admin Panel
                </button>
            </form>
        </div>

        <p class="text-center text-xs mt-6 uppercase tracking-widest" style="color:var(--c-faint)">
            Restricted to SUPER_ADMIN accounts only
        </p>
    </div>

</body>
</html>
