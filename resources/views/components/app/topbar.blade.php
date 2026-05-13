@props(['title' => 'Dashboard'])

@php
    $user = auth()->user();
    $role = $user?->primaryRole();
    $roleName = is_object($role) ? ($role->name ?? 'Church Team') : ($role ?: 'Church Team');
    $userName = $user?->full_name ?? 'BCC User';
    $userInitials = collect(preg_split('/\s+/', trim($userName)))
        ->filter()
        ->map(fn ($segment) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($segment, 0, 1)))
        ->take(2)
        ->implode('');
@endphp

<header class="app-topbar">
    <div class="app-topbar-primary">
        <div class="topbar-heading-wrap">
            <button type="button" class="topbar-nav-toggle" data-sidebar-toggle aria-label="Toggle navigation">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round">
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h10"></path>
                </svg>
            </button>

            <div class="app-topbar-copy">
                <div class="topbar-title-row">
                    <p class="eyebrow">{{ now()->format('l, d M Y') }}</p>
                    <span class="topbar-chip">Operations Console</span>
                </div>
                <h2 class="app-topbar-title">{{ $title }}</h2>
                <p class="mt-1 text-sm text-[var(--header-copy)]">Bishop Community Church Management Platform</p>
            </div>
        </div>

        <div class="topbar-actions">
            <div class="glass-pane topbar-profile-card">
                <div class="topbar-profile-avatar">{{ $userInitials }}</div>
                <div class="topbar-profile-copy">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[var(--profile-label)]">Signed in</p>
                    <p class="mt-1 text-sm font-semibold text-[var(--color-ink-950)]">{{ $userName }}</p>
                    <div class="topbar-profile-meta">
                        <span>{{ $user?->username }}</span>
                        <span class="topbar-meta-dot"></span>
                        <span>{{ $roleName }}</span>
                    </div>
                </div>
                <div class="topbar-profile-status">
                    <x-ui.status-badge :status="$user?->status ?? 'unknown'" />
                </div>
                <a href="{{ route('profile.index') }}" title="My Profile"
                   class="ml-2 flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 hover:text-[var(--color-ink-950)] hover:bg-[var(--color-surface-200)] transition-colors">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z"/><path d="M3 21c0-4.418 4.03-8 9-8s9 3.582 9 8"/>
                    </svg>
                </a>
            </div>

            <div class="topbar-utility-stack">
                <!-- Premium Theme Picker -->
                <div class="theme-picker">
                    <button type="button" class="theme-picker-trigger" data-theme-picker-trigger
                            aria-label="Theme Select" aria-expanded="false" aria-haspopup="menu" title="Theme Select">
                        <span class="theme-picker-trigger-icon" aria-hidden="true">
                            <svg class="theme-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 3c-4.97 0-9 4.03-9 9a9 9 0 0 0 9 9c1.3 0 2.34-1.04 2.34-2.34 0-.58-.22-1.13-.61-1.55a2.2 2.2 0 0 1-.59-1.5c0-1.25 1.02-2.27 2.27-2.27h2.13A3.46 3.46 0 0 0 21 9.87C21 6.08 16.97 3 12 3Z"/>
                                <circle cx="7.75" cy="10" r="1.15" fill="currentColor" stroke="none"/>
                                <circle cx="11.2" cy="7.15" r="1.05" fill="currentColor" stroke="none"/>
                                <circle cx="15.7" cy="8.7" r="1.05" fill="currentColor" stroke="none"/>
                            </svg>
                        </span>
                        <span class="theme-picker-trigger-copy">
                            <span class="theme-picker-kicker">Theme Select</span>
                            <span class="theme-picker-label" data-theme-label>Dark</span>
                        </span>
                    </button>

                    <div class="theme-picker-menu" data-hidden="true" role="menu">
                        <div class="theme-option" data-theme="light" role="menuitem">
                            <div class="theme-option-title">Light</div>
                            <div class="theme-option-preview">Clean & bright</div>
                        </div>
                        <div class="theme-option" data-theme="dark" role="menuitem">
                            <div class="theme-option-title">Dark</div>
                            <div class="theme-option-preview">Classic dark</div>
                        </div>
                        <div class="theme-option" data-theme="solarized" role="menuitem">
                            <div class="theme-option-title">Solarized</div>
                            <div class="theme-option-preview">Warm amber & cream</div>
                        </div>
                        <div class="theme-option" data-theme="forest" role="menuitem">
                            <div class="theme-option-title">Forest</div>
                            <div class="theme-option-preview">Green & natural</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="topbar-logout-btn">
                        <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16,17 21,12 16,7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
