<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return Auth::user()?->hasPermission('dashboard.read')
                ? redirect()->route('dashboard.index')
                : redirect()->route('attendance.scan');
        }

        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();
        $user = User::query()->where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['username' => 'The provided credentials are invalid.'])
                ->onlyInput('username');
        }

        if (! $user->isActive()) {
            return back()
                ->withErrors(['username' => 'This account is inactive. Contact the system administrator.'])
                ->onlyInput('username');
        }

        Auth::login($user, (bool) ($credentials['remember'] ?? false));
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();

        $this->auditLogger->log(
            request: $request,
            action: 'auth.login',
            entityType: 'user',
            entityId: $user->id,
            after: ['username' => $user->username],
            user: $user,
        );

        return redirect()->intended(
            Auth::user()?->hasPermission('dashboard.read')
                ? route('dashboard.index')
                : route('attendance.scan')
        );
    }

    public function destroy(): RedirectResponse
    {
        $user = request()->user();

        if ($user) {
            $this->auditLogger->log(
                request: request(),
                action: 'auth.logout',
                entityType: 'user',
                entityId: $user->id,
                after: ['username' => $user->username],
                user: $user,
            );
        }

        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
}
