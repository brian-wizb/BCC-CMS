@props(['title' => config('app.name')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script>
            (() => {
                const sidebarCollapsed = window.localStorage.getItem('bcc-sidebar-collapsed') === 'true';
                document.documentElement.dataset.sidebarCollapsed = sidebarCollapsed ? 'true' : 'false';
                document.documentElement.dataset.sidebarOpen = 'false';

                const observer = new MutationObserver(() => {
                    const isCollapsed = document.documentElement.dataset.sidebarCollapsed === 'true';
                    window.localStorage.setItem('bcc-sidebar-collapsed', isCollapsed ? 'true' : 'false');
                });

                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['data-sidebar-collapsed'],
                });
            })();
        </script>
        <title>{{ $title }}</title>
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/charts.js'])
        @endif
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.min.css">
        <style>
            .ts-wrapper { width: 100%; }
            .ts-wrapper.single .ts-control,
            .ts-wrapper.multi .ts-control {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                min-height: 2.875rem;
                padding: 0.625rem 0.9rem;
                border-radius: 0.75rem;
                border: 1px solid rgba(168, 204, 255, 0.14);
                background: linear-gradient(180deg, rgba(8, 22, 49, 0.64), rgba(8, 21, 46, 0.48));
                backdrop-filter: blur(calc(var(--glass-blur) - 12px));
                -webkit-backdrop-filter: blur(calc(var(--glass-blur) - 12px));
                color: var(--color-ink-950);
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
            }
            .ts-wrapper.single .ts-control input,
            .ts-wrapper.multi .ts-control input,
            .ts-wrapper .item,
            .ts-wrapper .ts-control > input::placeholder {
                color: var(--color-ink-950);
            }
            .ts-wrapper.single .ts-control input,
            .ts-wrapper.multi .ts-control input {
                background: transparent !important;
                color: var(--color-ink-950) !important;
                -webkit-text-fill-color: var(--color-ink-950) !important;
                caret-color: var(--color-ink-950);
                opacity: 1 !important;
            }
            .ts-wrapper .ts-control > input::placeholder {
                color: var(--input-placeholder);
            }
            .ts-wrapper.focus .ts-control {
                border-color: var(--color-brand-500);
                box-shadow: 0 0 0 2px rgba(36, 184, 255, 0.34);
            }
            .ts-wrapper .ts-control .clear-button {
                color: var(--text-muted);
            }
            .ts-dropdown {
                margin-top: 0.35rem;
                border-radius: 0.75rem;
                border: 1px solid rgba(168, 204, 255, 0.22);
                background: linear-gradient(180deg, rgba(12, 28, 58, 0.97), rgba(9, 22, 47, 0.95));
                backdrop-filter: blur(calc(var(--glass-blur) - 10px));
                -webkit-backdrop-filter: blur(calc(var(--glass-blur) - 10px));
                color: var(--color-ink-950);
                box-shadow: 0 14px 28px rgba(0, 0, 0, 0.26);
                font-size: 0.875rem;
                z-index: 9999;
            }
            .ts-dropdown .option,
            .ts-dropdown .create {
                padding: 0.5rem 0.8rem;
            }
            .ts-dropdown .option.active,
            .ts-dropdown .option:hover {
                background: rgba(36, 184, 255, 0.16);
                color: var(--color-ink-950);
            }
            [data-theme='light'] .ts-wrapper.single .ts-control,
            [data-theme='light'] .ts-wrapper.multi .ts-control {
                border-color: rgba(117, 187, 178, 0.38);
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.82), rgba(236, 249, 246, 0.72));
                box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.92);
            }
            [data-theme='light'] .ts-dropdown {
                border-color: rgba(117, 187, 178, 0.34);
                background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(239, 250, 247, 0.95));
                box-shadow: 0 14px 30px rgba(24, 95, 89, 0.16);
            }
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
                var searchKeys = ['member', 'visitor', 'leader', 'family'];

                var shouldAutoEnhance = function(el) {
                    if (!el || el.tagName !== 'SELECT') {
                        return false;
                    }
                    if (el.hasAttribute('data-no-tom-select') || el.tomselect) {
                        return false;
                    }

                    var haystack = ((el.name || '') + ' ' + (el.id || '')).toLowerCase();
                    return searchKeys.some(function(key) {
                        return haystack.includes(key);
                    });
                };

                var defaultPlaceholder = function(el) {
                    if (el.dataset.placeholder) {
                        return el.dataset.placeholder;
                    }

                    var emptyOption = Array.from(el.options || []).find(function(opt) {
                        return (opt.value || '') === '';
                    });

                    return emptyOption ? emptyOption.text.trim() : 'Search...';
                };

                var targets = Array.from(document.querySelectorAll('select[data-tom-select], select')).filter(function(el) {
                    return el.hasAttribute('data-tom-select') || shouldAutoEnhance(el);
                });

                targets.forEach(function(el) {
                    if (el.tomselect) {
                        return;
                    }

                    new TomSelect(el, {
                        allowEmptyOption: true,
                        maxOptions: 1000,
                        searchField: ['text'],
                        placeholder: defaultPlaceholder(el),
                    });
                });
            });
        </script>
        @stack('scripts')
    </body>
</html>
