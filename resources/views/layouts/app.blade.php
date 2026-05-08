<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (() => {
            const themeStorageKey = 'bcc-theme';
            const sidebarStorageKey = 'bcc-sidebar-collapsed';
            const storedTheme = window.localStorage.getItem(themeStorageKey);
            const sidebarCollapsed = window.localStorage.getItem(sidebarStorageKey) === 'true';
            const theme = storedTheme === 'light' || storedTheme === 'dark'
                ? storedTheme
                : (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

            document.documentElement.dataset.theme = theme;
            document.documentElement.dataset.sidebarCollapsed = sidebarCollapsed ? 'true' : 'false';
            document.documentElement.dataset.sidebarOpen = 'false';
            document.documentElement.style.colorScheme = theme;
        })();
    </script>
    @php
        $routeName = request()->route()?->getName();
        $routeTitle = $routeName
            ? \Illuminate\Support\Str::of($routeName)->beforeLast('.')->replace(['-', '.'], ' ')->headline()->value()
            : null;
        $resolvedTitle = $title ?? trim($__env->yieldContent('title')) ?: $routeTitle ?: config('app.name', 'BCC CMS');
    @endphp
    <title>{{ $resolvedTitle }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/charts.js'])
    @endif
</head>
<body class="app-cosmos">
    <div class="app-shell">
        <x-app.sidebar />
        <button type="button" class="sidebar-backdrop" data-sidebar-backdrop aria-label="Close navigation"></button>

        <div class="app-main">
            <x-app.topbar :title="$resolvedTitle" />

            <main class="page-section">
                <x-ui.flash-message />
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
