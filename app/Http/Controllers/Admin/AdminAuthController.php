<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Constant-ish failure path: wrong creds OR not a platform admin.
        if (! $user
            || ! Hash::check($credentials['password'], $user->password)
            || $user->role !== 'SUPER_ADMIN') {
            return back()
                ->withErrors(['email' => 'Invalid credentials or insufficient role.'])
                ->withInput($request->only('email'));
        }

        // Second factor on the most privileged surface. If the admin enrolled in
        // 2FA (via the regular app), a valid TOTP/recovery code is mandatory.
        if ($user->hasTwoFactorEnabled()) {
            $code = trim((string) $request->input('code', ''));
            if ($code === '') {
                return back()
                    ->withErrors(['code' => 'Code 2FA requis pour ce compte.'])
                    ->with('twofa', true)
                    ->withInput($request->only('email'));
            }
            if (! $user->verifyTwoFactorCode($code)) {
                return back()
                    ->withErrors(['code' => 'Code 2FA invalide.'])
                    ->with('twofa', true)
                    ->withInput($request->only('email'));
            }
        }

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('admin_last_activity', now()->timestamp);
        $user->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->save();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
