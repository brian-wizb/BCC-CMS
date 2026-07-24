<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/bcc-logo.png') }}?v=20260724">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/bcc-logo.png') }}?v=20260724">
    <script>
        (() => {
            const themeStorageKey = 'bcc-theme';
            const sidebarStorageKey = 'bcc-sidebar-collapsed';
            const availableThemes = ['light', 'dark', 'solarized', 'forest'];
            const defaultTheme = 'dark';

            const storedTheme = window.localStorage.getItem(themeStorageKey);
            const sidebarCollapsed = window.localStorage.getItem(sidebarStorageKey) === 'true';
            const theme = availableThemes.includes(storedTheme) ? storedTheme : defaultTheme;

            document.documentElement.dataset.theme = theme;
            document.documentElement.dataset.sidebarCollapsed = sidebarCollapsed ? 'true' : 'false';
            document.documentElement.dataset.sidebarOpen = 'false';
            document.documentElement.style.colorScheme = theme === 'light' ? 'light' : 'dark';
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
                <header class="print-document-header" aria-hidden="true">
                    <img src="{{ asset('images/bcc-logo.png') }}" alt="" class="print-document-logo">
                    <div>
                        <p class="print-document-church">Bethel City Church</p>
                        <h1 class="print-document-title">{{ $resolvedTitle }}</h1>
                        <p class="print-document-meta">
                            BCC Management Platform · Printed {{ now(config('app.timezone'))->format('d M Y, H:i') }}
                        </p>
                    </div>
                </header>
                <x-ui.flash-message />
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
