@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/charts.js'])
        @endif
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
        <style>
            .ts-wrapper.form-input,.ts-wrapper .ts-control{border:none!important;box-shadow:none!important;padding:0!important;min-height:unset!important;background:transparent!important;}
            .ts-wrapper{width:100%;}
            .ts-wrapper .ts-control{display:flex;align-items:center;flex-wrap:wrap;gap:2px;min-height:2.25rem;padding:0 0.625rem!important;background:#fff!important;border:1px solid var(--color-surface-300,#e2e8f0)!important;border-radius:0.5rem!important;font-size:.875rem;}
            .ts-wrapper.focus .ts-control{border-color:rgba(99,102,241,0.5)!important;box-shadow:0 0 0 2px rgba(99,102,241,0.15)!important;}
            .ts-dropdown{border-radius:.5rem;border:1px solid var(--color-surface-200,#e2e8f0);box-shadow:0 4px 16px rgba(0,0,0,.08);font-size:.875rem;z-index:9999;}
            .ts-dropdown .option{padding:.4rem .75rem;}
            .ts-dropdown .option.active{background:rgba(99,102,241,0.08);color:rgba(99,102,241,0.9);}
        </style>
    </head>
    <body class="app-cosmos">
        <div class="app-shell">
            <x-app.sidebar />

            <div class="app-main">
                <x-app.topbar :title="$title" />

                <main class="page-section">
                    <x-ui.flash-message />
                    {{ $slot }}
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('[data-tom-select]').forEach(function(el) {
                    new TomSelect(el, {
                        allowEmptyOption: true,
                        maxOptions: 1000,
                        searchField: ['text'],
                        placeholder: el.dataset.placeholder || 'Search...',
                    });
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
