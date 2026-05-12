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
                <button type="button" class="theme-toggle" data-theme-toggle aria-label="Switch theme">
                    <span class="theme-toggle-icon" aria-hidden="true">
                        <svg class="theme-icon theme-icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4.25"></circle>
                            <path d="M12 2.75v2.5"></path>
                            <path d="M12 18.75v2.5"></path>
                            <path d="m5.46 5.46 1.77 1.77"></path>
                            <path d="m16.77 16.77 1.77 1.77"></path>
                            <path d="M2.75 12h2.5"></path>
                            <path d="M18.75 12h2.5"></path>
                            <path d="m5.46 18.54 1.77-1.77"></path>
                            <path d="m16.77 7.23 1.77-1.77"></path>
                        </svg>
                        <svg class="theme-icon theme-icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 15.2A7.9 7.9 0 0 1 13.35 20 8 8 0 0 1 5 11.65 7.9 7.9 0 0 1 9.8 5 6.35 6.35 0 1 0 20 15.2Z"></path>
                        </svg>
                    </span>
                    <span class="theme-toggle-copy">
                        <span class="theme-toggle-kicker">Appearance</span>
                        <span class="theme-toggle-label" data-theme-label>Light mode</span>
                    </span>
                </button>

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
