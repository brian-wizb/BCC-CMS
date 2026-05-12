<x-layouts.app title="Access Denied">
    <div class="flex min-h-[60vh] flex-col items-center justify-center text-center px-4">
        <div class="surface-card max-w-md w-full p-10 rounded-2xl shadow-sm">
            <div class="mb-6 flex justify-center">
                <span class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-50 text-red-400">
                    <i class="fa-solid fa-lock text-4xl"></i>
                </span>
            </div>

            <h1 class="text-2xl font-bold text-[var(--color-ink-950)] mb-2">Access Denied</h1>
            <p class="text-sm text-slate-500 mb-1">You do not have permission to view this page.</p>
            <p class="text-sm text-slate-400 mb-8">
                If you believe this is a mistake, please contact your system administrator.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                @if(auth()->user()?->hasPermission('dashboard.read'))
                    <a href="{{ route('dashboard.index') }}"
                       class="btn-primary inline-flex items-center gap-2 justify-center">
                        <i class="fa-solid fa-house text-sm"></i>
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('attendance.scan') }}"
                       class="btn-primary inline-flex items-center gap-2 justify-center">
                        <i class="fa-solid fa-qrcode text-sm"></i>
                        Go to Scanner
                    </a>
                @endif

                <button onclick="history.back()"
                        class="btn-secondary inline-flex items-center gap-2 justify-center">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                    Go Back
                </button>
            </div>
        </div>
    </div>
</x-layouts.app>
