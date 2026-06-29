<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PlatformAdminController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'SUPER_ADMIN')->orderBy('name')->get();
        return view('admin.administrators', compact('admins'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Platform admins are company-less (company_id is nullable).
        User::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => $data['password'], // 'hashed' cast
            'role'       => 'SUPER_ADMIN',
            'company_id' => null,
        ]);

        return back()->with('success', 'Administrateur créé : ' . $data['email']);
    }

    public function revoke(Request $request, User $user)
    {
        if ($user->role !== 'SUPER_ADMIN') {
            abort(404);
        }
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['email' => 'Vous ne pouvez pas révoquer votre propre accès.']);
        }
        if (User::where('role', 'SUPER_ADMIN')->whereNull('disabled_at')->count() <= 1) {
            return back()->withErrors(['email' => 'Impossible de révoquer le dernier administrateur actif.']);
        }

        // Soft-revoke: disable + kill tokens, but keep the row so this admin's
        // past audit-log / announcement attribution is preserved.
        $user->tokens()->delete();
        $user->update(['disabled_at' => now()]);

        return back()->with('success', 'Accès administrateur révoqué.');
    }

    public function reinstate(Request $request, User $user)
    {
        if ($user->role !== 'SUPER_ADMIN') {
            abort(404);
        }
        $user->update(['disabled_at' => null]);

        return back()->with('success', 'Accès administrateur rétabli.');
    }
}
