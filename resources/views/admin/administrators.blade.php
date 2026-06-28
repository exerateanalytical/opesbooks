@extends('admin.layout')

@section('title', 'Administrators')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">Platform Administrators</h1>
    <p class="text-slate-500 text-xs mt-1">SUPER_ADMIN accounts with full platform access</p>
</div>

<div class="grid lg:grid-cols-3 gap-6 max-w-5xl">
    {{-- List --}}
    <div class="lg:col-span-2 bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-[10px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347]">
                    <th class="px-5 py-3">Name</th>
                    <th class="px-5 py-3">Email</th>
                    <th class="px-5 py-3">Last login</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @foreach($admins as $a)
                <tr class="hover:bg-[#1C2A3A]/30">
                    <td class="px-5 py-3 font-bold text-white">
                        {{ $a->name }}
                        @if($a->id === auth()->id())<span class="ml-2 text-[9px] font-black uppercase tracking-widest text-amber-300">You</span>@endif
                    </td>
                    <td class="px-5 py-3 text-slate-300">{{ $a->email }}</td>
                    <td class="px-5 py-3 text-slate-500 text-xs">{{ $a->last_login_at ? $a->last_login_at->format('Y-m-d H:i') : '—' }}</td>
                    <td class="px-5 py-3 text-right">
                        @if($a->id !== auth()->id() && $admins->count() > 1)
                        <form method="POST" action="{{ route('admin.administrators.revoke', $a) }}"
                              onsubmit="return confirm('Révoquer définitivement l\'accès de {{ $a->email }} ?')">
                            @csrf
                            <button class="text-[10px] font-black uppercase tracking-widest text-red-400 hover:text-red-300">Revoke</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Create --}}
    <form method="POST" action="{{ route('admin.administrators.store') }}"
          class="bg-[#151F2E] border border-[#253347] rounded-2xl p-6 space-y-4 self-start">
        @csrf
        <h2 class="text-sm font-black text-white uppercase tracking-wide">Add administrator</h2>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-slate-400">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full bg-[#1C2A3A] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-slate-400">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                   class="w-full bg-[#1C2A3A] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
            @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-slate-400">Password</label>
            <input type="password" name="password" required
                   class="w-full bg-[#1C2A3A] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
            @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-slate-400">Confirm password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full bg-[#1C2A3A] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
        </div>
        <button type="submit"
                class="w-full px-5 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all">
            Create admin
        </button>
    </form>
</div>
@endsection
