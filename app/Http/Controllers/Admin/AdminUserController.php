<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /** Roles a platform admin may assign to a tenant user (never SUPER_ADMIN). */
    private const TENANT_ROLES = ['OWNER', 'ACCOUNTANT', 'CLERK', 'AUDITOR'];

    /** POST /admin/companies/{company}/users — create a user inside a company. */
    public function store(Request $request, Company $company)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role'     => ['required', Rule::in(self::TENANT_ROLES)],
        ]);

        User::create([
            'company_id' => $company->id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'password'   => $data['password'], // 'hashed' cast
            'role'       => $data['role'],
        ]);

        return back()->with('success', "Utilisateur créé : {$data['email']}");
    }

    /** POST /admin/users/{user} — edit name/email/role. */
    public function update(Request $request, User $user)
    {
        $this->guardNonAdmin($user);

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'  => ['required', Rule::in(self::TENANT_ROLES)],
        ]);

        $user->update($data);

        return back()->with('success', "Utilisateur mis à jour : {$user->email}");
    }

    /** POST /admin/users/{user}/toggle — disable or re-enable a tenant account. */
    public function toggleDisabled(Request $request, User $user)
    {
        $this->guardNonAdmin($user);

        if ($user->disabled_at) {
            $user->update(['disabled_at' => null]);
            $msg = "Compte réactivé : {$user->email}";
        } else {
            $user->update(['disabled_at' => now()]);
            $user->tokens()->delete(); // kill any live sessions immediately
            $msg = "Compte désactivé : {$user->email}";
        }

        return back()->with('success', $msg);
    }

    /** DELETE /admin/users/{user} — remove a tenant user. */
    public function destroy(Request $request, User $user)
    {
        $this->guardNonAdmin($user);

        $email = $user->email;
        $user->tokens()->delete();
        $user->delete();

        return back()->with('success', "Utilisateur supprimé : {$email}");
    }

    /** Platform admins are managed via PlatformAdminController, never here. */
    private function guardNonAdmin(User $user): void
    {
        if ($user->role === 'SUPER_ADMIN') {
            abort(403, 'Platform admins are managed under Administrators.');
        }
    }
}
