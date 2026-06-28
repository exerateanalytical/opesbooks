@extends('admin.layout')

@section('title', 'My Profile')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-black text-white uppercase tracking-wide">My Profile</h1>
    <p class="text-slate-500 text-xs mt-1">Manage your own platform-admin account</p>
</div>

<div class="grid lg:grid-cols-2 gap-6 max-w-4xl">
    {{-- Profile details --}}
    <form method="POST" action="{{ route('admin.profile.update') }}"
          class="bg-[#151F2E] border border-[#253347] rounded-2xl p-6 space-y-4">
        @csrf
        <div>
            <h2 class="text-sm font-black text-white uppercase tracking-wide">Account details</h2>
            <p class="text-xs text-slate-500 mt-0.5">Role: <span class="text-amber-300 font-bold">SUPER_ADMIN</span></p>
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-slate-400">Name</label>
            <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                   class="w-full bg-[#1C2A3A] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-slate-400">Email</label>
            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                   class="w-full bg-[#1C2A3A] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
        </div>
        <button type="submit"
                class="px-5 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all">
            Save profile
        </button>
    </form>

    {{-- Password change --}}
    <form method="POST" action="{{ route('admin.profile.password') }}"
          class="bg-[#151F2E] border border-[#253347] rounded-2xl p-6 space-y-4">
        @csrf
        <div>
            <h2 class="text-sm font-black text-white uppercase tracking-wide">Change password</h2>
            <p class="text-xs text-slate-500 mt-0.5">Requires your current password</p>
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-slate-400">Current password</label>
            <input type="password" name="current_password" required
                   class="w-full bg-[#1C2A3A] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
            @error('current_password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-slate-400">New password</label>
            <input type="password" name="password" required
                   class="w-full bg-[#1C2A3A] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
            @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-[10px] font-black uppercase tracking-widest mb-1.5 text-slate-400">Confirm new password</label>
            <input type="password" name="password_confirmation" required
                   class="w-full bg-[#1C2A3A] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white focus:border-amber-500 outline-none">
        </div>
        <button type="submit"
                class="px-5 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-white bg-[#1C2A3A] border border-[#334155] hover:border-amber-500 transition-all">
            Update password
        </button>
    </form>
</div>
@endsection
