@extends('admin.layout')

@section('title', 'Users')

@section('content')
<div class="mb-8 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-black text-white uppercase tracking-wide">Tenant Users</h1>
        <p class="text-slate-500 text-xs mt-1">All non-admin users across all companies</p>
    </div>
    <span class="text-[10px] font-mono font-black px-3 py-1 rounded-full bg-amber-500/15 text-amber-300 border border-amber-500/30">
        {{ $users->total() }} users
    </span>
</div>

<!-- Search / filter -->
<form method="GET" class="mb-5 flex flex-wrap gap-3">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…"
           class="flex-1 min-w-[200px] bg-[#151F2E] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-amber-500">
    <select name="role" class="bg-[#151F2E] border border-[#253347] rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-amber-500">
        <option value="">All roles</option>
        @foreach(['OWNER','ACCOUNTANT','CLERK','AUDITOR'] as $r)
            <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ $r }}</option>
        @endforeach
    </select>
    <button class="px-5 py-2.5 rounded-xl text-sm font-black uppercase tracking-widest text-slate-900 bg-amber-400 hover:bg-amber-300 transition-all">Search</button>
    @if(request('search') || request('role'))
        <a href="{{ route('admin.users') }}" class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-400 hover:text-white border border-[#253347]">Clear</a>
    @endif
</form>

<!-- Impersonate result modal (shown via JS) -->
<div id="impersonate-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm">
    <div class="bg-[#151F2E] border border-[#334155] rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl">
        <h3 class="text-sm font-black text-white uppercase tracking-wider mb-4">Impersonation Token</h3>
        <p class="text-slate-400 text-xs mb-3">Use this Bearer token in the Authorization header to act as the user for 1 hour:</p>
        <code id="impersonate-token" class="block bg-[#1C2A3A] rounded-xl px-4 py-3 text-amber-300 text-[10px] font-mono break-all mb-5"></code>
        <div id="impersonate-user" class="text-slate-500 text-[10px] mb-4"></div>
        <button onclick="document.getElementById('impersonate-modal').classList.add('hidden')"
                class="w-full py-2 rounded-xl text-xs font-black uppercase tracking-widest bg-[#1C2A3A] hover:bg-slate-700 text-slate-300 border border-[#334155] transition-all">
            Close
        </button>
    </div>
</div>

<div class="bg-[#151F2E] border border-[#253347] rounded-2xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[9px] font-black uppercase tracking-widest text-slate-500 border-b border-[#253347] bg-[#0B1120]/50">
                    <th class="py-3 px-6">Name</th>
                    <th class="py-3 px-4">Email</th>
                    <th class="py-3 px-4">Role</th>
                    <th class="py-3 px-4">Company</th>
                    <th class="py-3 px-4">Joined</th>
                    <th class="py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody class="text-xs font-medium divide-y divide-slate-800/60">
                @forelse($users as $user)
                    <tr class="hover:bg-[#1C2A3A]/40 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-white">
                            {{ $user->name }}
                            @if($user->disabled_at)<span class="ml-1.5 text-[9px] font-black uppercase text-red-400">Disabled</span>@endif
                        </td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $user->email }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase
                                {{ $user->role === 'OWNER' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' :
                                   ($user->role === 'ACCOUNTANT' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' :
                                   'bg-slate-500/20 text-slate-300 border border-slate-500/30') }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $user->company?->name ?? '—' }}</td>
                        <td class="py-3.5 px-4 text-slate-500 font-mono text-[10px]">
                            {{ $user->created_at->format('Y-m-d') }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('admin.impersonate', $user) }}"
                                      onsubmit="handleImpersonate(event, this)">
                                    @csrf
                                    <button type="submit"
                                            class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/30 transition-all">
                                        Impersonate
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}">@csrf
                                    <button class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide border transition-all
                                        {{ $user->disabled_at ? 'bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border-amber-500/30' }}">
                                        {{ $user->disabled_at ? 'Enable' : 'Disable' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Supprimer {{ $user->email }} ?')">@csrf @method('DELETE')
                                    <button class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-red-500/15 hover:bg-red-500/25 text-red-300 border border-red-500/30 transition-all">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-500 text-sm">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-[#253347]">
            {{ $users->links() }}
        </div>
    @endif
</div>

<script>
async function handleImpersonate(e, form) {
    e.preventDefault();
    const resp = await fetch(form.action, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': form.querySelector('[name=_token]').value, 'Accept': 'application/json' }
    });
    const data = await resp.json();
    if (data.token) {
        // Enter the tenant app as this user, with an impersonation banner.
        localStorage.setItem('opes_token', data.token);
        localStorage.setItem('opes_user', JSON.stringify(data.user));
        localStorage.setItem('opes_impersonation', JSON.stringify({ name: data.company_name || '', role: data.user.role }));
        window.open('/app', '_blank');
        // Also surface the raw token for API testing.
        document.getElementById('impersonate-token').textContent = data.token;
        document.getElementById('impersonate-user').textContent =
            `User: ${data.user.name} (${data.user.email}) — Role: ${data.user.role}`;
        document.getElementById('impersonate-modal').classList.remove('hidden');
    } else {
        alert('Failed to generate impersonation token.');
    }
}
</script>
@endsection
