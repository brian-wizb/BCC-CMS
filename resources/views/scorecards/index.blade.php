<x-layouts.app title="Scorecards">

    {{-- Page header --}}
    <div class="mb-6 flex items-center gap-3">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(36,184,255,0.12);">
            <i class="fas fa-clipboard-list text-lg" style="color:rgba(36,184,255,0.9);"></i>
        </span>
        <div>
            <h1 class="text-2xl font-bold text-[var(--color-ink-950)]">Scorecards</h1>
            <p class="text-xs text-slate-500">Performance overview by zone and department</p>
        </div>
    </div>

    <section class="grid gap-6 md:grid-cols-2">

        <a href="{{ route('scorecards.zones') }}" class="surface-card p-6 block group transition hover:-translate-y-0.5">
            <div class="mb-4 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(52,211,153,0.14);">
                    <i class="fas fa-map-marked-alt text-base" style="color:rgba(52,211,153,0.9);"></i>
                </span>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Zone Scorecards</h3>
            </div>
            <p class="text-sm text-slate-500">Rank zones by activity indicators and attendance footprint.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(52,211,153,0.8);">
                View scorecard <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </a>

        <a href="{{ route('scorecards.departments') }}" class="surface-card p-6 block group transition hover:-translate-y-0.5">
            <div class="mb-4 flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(244,193,93,0.14);">
                    <i class="fas fa-sitemap text-base" style="color:rgba(244,193,93,0.9);"></i>
                </span>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Department Scorecards</h3>
            </div>
            <p class="text-sm text-slate-500">Track department participation and completed volunteer output.</p>
            <p class="mt-4 flex items-center gap-1.5 text-xs font-semibold" style="color:rgba(244,193,93,0.8);">
                View scorecard <i class="fas fa-arrow-right text-[10px]"></i>
            </p>
        </a>

    </section>
</x-layouts.app>
