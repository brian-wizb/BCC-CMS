@php
    $navigation = collect(config('permissions.navigation', []))->filter(function (array $item) {
        $permission = $item['permission'] ?? null;

        return ! $permission || auth()->user()?->hasPermission($permission);
    });

    $role = auth()->user()?->primaryRole();
    $roleName = is_object($role) ? ($role->name ?? 'Church Team') : ($role ?: 'Church Team');
    $roleKey = is_object($role) ? ($role->key ?? null) : null;

    $iconPaths = [
        'home' => '<path d="M3 10.5 12 3l9 7.5"></path><path d="M5.5 9.5V20h13V9.5"></path>',
        'chart' => '<path d="M4 19h16"></path><path d="M7 16V9"></path><path d="M12 16V5"></path><path d="M17 16v-7"></path>',
        'users' => '<path d="M16 19v-1a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v1"></path><circle cx="10" cy="8" r="3"></circle><path d="M20 19v-1a3 3 0 0 0-2-2.82"></path><path d="M16 5.2a3 3 0 0 1 0 5.6"></path>',
        'building' => '<path d="M4 20V8l8-4 8 4v12"></path><path d="M9 20v-5h6v5"></path><path d="M8 10h.01"></path><path d="M16 10h.01"></path><path d="M8 13h.01"></path><path d="M16 13h.01"></path>',
        'layers' => '<path d="m12 3 9 4.5-9 4.5-9-4.5L12 3Z"></path><path d="m3 12 9 4.5 9-4.5"></path><path d="m3 16.5 9 4.5 9-4.5"></path>',
        'heart' => '<path d="m12 20-1.1-1C5.14 13.82 2 10.97 2 7.5A4.5 4.5 0 0 1 6.5 3C8.24 3 9.91 3.81 11 5.09 12.09 3.81 13.76 3 15.5 3A4.5 4.5 0 0 1 20 7.5c0 3.47-3.14 6.32-8.9 11.5L12 20Z"></path>',
        'bell' => '<path d="M6 8a6 6 0 1 1 12 0c0 7 3 8 3 8H3s3-1 3-8"></path><path d="M10 20a2 2 0 0 0 4 0"></path>',
        'calendar' => '<rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4"></path><path d="M8 3v4"></path><path d="M3 10h18"></path>',
        'megaphone' => '<path d="M3 11.5v1a2.5 2.5 0 0 0 2.5 2.5H7l2 4h2l-1.5-4H12l7-4V7l-7-4H5.5A2.5 2.5 0 0 0 3 5.5v1"></path>',
        'wallet' => '<path d="M3 7.5A2.5 2.5 0 0 1 5.5 5H19a2 2 0 0 1 2 2v1H5.5A2.5 2.5 0 0 0 3 10.5v6A2.5 2.5 0 0 0 5.5 19H21v-8h-4.5A2.5 2.5 0 0 1 14 8.5v-1"></path><circle cx="16.5" cy="13.5" r=".5" fill="currentColor" stroke="none"></circle>',
        'gift' => '<rect x="3" y="8" width="18" height="13" rx="2"></rect><path d="M12 8v13"></path><path d="M3 12h18"></path><path d="M12 8H7.5a2.5 2.5 0 1 1 0-5C10 3 12 8 12 8Z"></path><path d="M12 8h4.5a2.5 2.5 0 1 0 0-5C14 3 12 8 12 8Z"></path>',
        'folder' => '<path d="M3 19V7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"></path>',
        'check' => '<path d="M20 6 9 17l-5-5"></path>',
        'shield' => '<path d="M12 3 5 6v6c0 4.5 2.9 7.9 7 9 4.1-1.1 7-4.5 7-9V6l-7-3Z"></path>',
        'spark' => '<path d="m12 3 1.9 4.8L19 9.7l-4 3.3 1.2 5-4.2-2.6L7.8 18l1.2-5-4-3.3 5.1-1.9L12 3Z"></path>',
    ];

    $sectionIconMap = [
        'Core' => 'home',
        'People' => 'users',
        'Ministry' => 'heart',
        'Finance' => 'wallet',
        'Operations' => 'layers',
    ];

    $moduleIconMap = [
        'Dashboard' => 'home',
        'Reports' => 'folder',
        'Users' => 'users',
        'Members' => 'users',
        'Departments' => 'building',
        'Zones' => 'layers',
        'Groups' => 'users',
        'Visitors' => 'users',
        'Children Ministry' => 'users',
        'Discipleship' => 'heart',
        'Follow-Up' => 'check',
        'Leaders' => 'shield',
        'Attendance' => 'check',
        'Alerts' => 'bell',
        'Communications' => 'megaphone',
        'Expenditures' => 'wallet',
        'Department Income' => 'chart',
        'Department Expenses' => 'chart',
        'Income Types' => 'bookmark',
        'Income Records' => 'chart',
        'Givings' => 'gift',
        'Campaigns' => 'megaphone',
        'Pledges' => 'gift',
        'Missed Pledges' => 'bell',
        'Pledge Payments' => 'wallet',
        'Role Permissions' => 'shield',
        'Audit Log' => 'folder',
    ];

    $renderIcon = function (string $key) use ($iconPaths) {
        $paths = $iconPaths[$key] ?? $iconPaths['layers'];

        return new \Illuminate\Support\HtmlString(
            '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.85" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>'
        );
    };

    $sectionMap = [
        'Core' => ['Dashboard', 'Reports'],
        'People' => ['Users', 'Members', 'Departments', 'Zones', 'Groups', 'Visitors', 'Children Ministry', 'Follow-Up', 'Leaders'],
        'Ministry' => ['Discipleship', 'Attendance', 'Alerts', 'Communications'],
        'Finance' => ['Expenditures', 'Department Income', 'Department Expenses', 'Income Records', 'Income Types', 'Givings', 'Campaigns', 'Pledges', 'Missed Pledges', 'Pledge Payments'],
    ];

    if ($roleKey === 'chief_usher') {
        $financeLabels = collect($sectionMap['Finance'] ?? [])->all();
        $navigation = $navigation->reject(fn ($item) => in_array($item['label'], $financeLabels, true))->values();
        unset($sectionMap['Finance']);
    }

    $assignedLabels = collect($sectionMap)->flatten()->all();
    $groupedNavigation = collect($sectionMap)->mapWithKeys(function ($labels, $section) use ($navigation) {
        return [
            $section => $navigation->filter(fn ($item) => in_array($item['label'], $labels, true))->values(),
        ];
    });

    $groupedNavigation['Operations'] = $navigation->reject(fn ($item) => in_array($item['label'], $assignedLabels, true))->values();
@endphp

<aside class="app-sidebar" data-sidebar>
    <div class="app-sidebar-inner">
        <div class="app-sidebar-head">
            <div class="brand-panel sidebar-brand-panel">
                <div class="sidebar-brand-row">
                    <img src="{{ asset('images/bcc-logo.png') }}" alt="BCC Logo" class="sidebar-brand-logo">
                    <button type="button" class="sidebar-collapse-btn" data-sidebar-toggle aria-label="Toggle navigation">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 6l-6 6 6 6"></path>
                        </svg>
                    </button>
                </div>
                <div class="sidebar-brand-copy">
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-[var(--brand-title)]">BCC Management System</p>
                    <p class="mt-2 text-xs leading-5 text-[var(--brand-copy)]">Operational platform for church governance, finance, and ministry execution.</p>
                </div>
            </div>

            <div class="glass-pane-soft sidebar-role-card">
                <p class="sidebar-role-kicker">Workspace</p>
                <p class="sidebar-role-title">{{ $roleName }}</p>
                <div class="sidebar-role-meta">
                    <span>{{ $navigation->count() }} modules</span>
                    <span class="sidebar-role-divider"></span>
                    <span>{{ $groupedNavigation->filter(fn ($items) => $items->isNotEmpty())->count() }} hubs</span>
                </div>
            </div>
        </div>

        <div class="sidebar-scroll">
            @foreach ($groupedNavigation as $section => $items)
                @continue($items->isEmpty())
                @php
                    $sectionId = \Illuminate\Support\Str::slug($section);
                    $sectionActive = $items->contains(function ($item) {
                        $routePattern = str_replace('.index', '.*', $item['route']);

                        return request()->routeIs($routePattern);
                    });
                    $defaultOpen = $sectionActive || $loop->first;
                    $sectionIcon = $sectionIconMap[$section] ?? 'layers';
                @endphp
                <section
                    class="sidebar-section"
                    data-nav-section
                    data-section-id="{{ $sectionId }}"
                    data-default-open="{{ $defaultOpen ? 'true' : 'false' }}"
                    data-active-section="{{ $sectionActive ? 'true' : 'false' }}"
                >
                    <button type="button" class="sidebar-section-toggle" data-section-toggle aria-expanded="{{ $defaultOpen ? 'true' : 'false' }}">
                        <span class="sidebar-section-meta">
                            <span class="sidebar-section-icon">{!! $renderIcon($sectionIcon) !!}</span>
                            <span class="sidebar-section-copy">
                                <span class="sidebar-section-title">{{ $section }}</span>
                                <span class="sidebar-section-count">{{ $items->count() }} {{ \Illuminate\Support\Str::plural('item', $items->count()) }}</span>
                            </span>
                        </span>
                        <span class="sidebar-section-chevron" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 8 4 4 4-4"></path>
                            </svg>
                        </span>
                    </button>

                    <div class="sidebar-section-panel" data-section-panel>
                        <nav class="sidebar-nav-list">
                            @foreach ($items as $item)
                                @php
                                    $routePattern = str_replace('.index', '.*', $item['route']);
                                    $itemIcon = $moduleIconMap[$item['label']] ?? $sectionIcon;
                                @endphp
                                <a
                                    href="{{ route($item['route']) }}"
                                    aria-label="{{ $item['label'] }}"
                                    data-tooltip="{{ $item['label'] }}"
                                    data-sidebar-close
                                    style="--stagger-index: {{ ($loop->parent->index * 10) + $loop->index }};"
                                    @class([
                                        'nav-link',
                                        'nav-link-active' => request()->routeIs($routePattern),
                                    ])
                                >
                                    <span class="nav-link-icon">{!! $renderIcon($itemIcon) !!}</span>
                                    <span class="nav-link-copy">
                                        <span class="nav-link-label">{{ $item['label'] }}</span>
                                        <span class="nav-link-meta">{{ $section }} workspace</span>
                                    </span>
                                    <span class="nav-link-indicator" aria-hidden="true"></span>
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</aside>
