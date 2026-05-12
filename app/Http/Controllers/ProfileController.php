<?php

namespace App\Http\Controllers;

use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger)
    {
    }

    public function index(): View
    {
        return view('profile.index', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'full_name' => ['nullable', 'string', 'max:255'],
            'email'     => ['nullable', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id)->withoutTrashed()],
            'phone'     => ['nullable', 'string', 'max:30'],
        ]);

        $before = [
            'full_name' => $user->full_name,
            'email'     => $user->email,
            'phone'     => $user->phone,
        ];

        $user->fill($data)->save();

        $this->auditLogger->log(
            request: $request,
            action: 'profile.update',
            entityType: 'system_user',
            entityId: $user->id,
            before: $before,
            after: $data,
        );

        return back()->with('status', 'Profile updated successfully.');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'      => ['required', 'string', 'current_password'],
            'password'              => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'password_confirmation' => ['required', 'string'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        $this->auditLogger->log(
            request: $request,
            action: 'profile.password_changed',
            entityType: 'system_user',
            entityId: $user->id,
        );

        return back()->with('status', 'Password changed successfully.');
    }
}
