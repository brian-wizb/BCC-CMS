<x-layouts.app title="Follow-Up">
    <section class="space-y-6">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">
                <i class="fa-solid fa-clipboard-list mr-1"></i> Visitors + Follow-up
            </p>
            <h3 class="mt-2 text-2xl font-semibold text-[var(--color-ink-950)]">
                <i class="fa-solid fa-arrows-to-circle mr-2" style="color:#7c3aed;"></i> Follow-up Centre
            </h3>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <a href="{{ route('follow-up.pipeline') }}"
               class="surface-card block rounded-2xl p-6 hover:bg-[var(--color-surface-50)] transition-colors">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl"
                     style="background:#f3f0ff;">
                    <i class="fa-solid fa-sitemap text-xl" style="color:#7c3aed;"></i>
                </div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Follow-up</p>
                <h4 class="mt-1 text-xl font-semibold text-[var(--color-ink-950)]">Pipeline Board</h4>
                <p class="mt-2 text-sm text-slate-500">
                    Track visitors through all 6 conversion stages. See members with active tasks.
                </p>
                <p class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-purple-600">
                    Open pipeline <i class="fa-solid fa-arrow-right"></i>
                </p>
            </a>

            <a href="{{ route('follow-up.tasks') }}"
               class="surface-card block rounded-2xl p-6 hover:bg-[var(--color-surface-50)] transition-colors">
                <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-2xl"
                     style="background:#ecfdf5;">
                    <i class="fa-solid fa-list-check text-xl" style="color:#16a34a;"></i>
                </div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Follow-up</p>
                <h4 class="mt-1 text-xl font-semibold text-[var(--color-ink-950)]">Tasks</h4>
                <p class="mt-2 text-sm text-slate-500">
                    Create and complete calls, visits, counseling, and prayer assignments. Assign to leaders.
                </p>
                <p class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-emerald-600">
                    Manage tasks <i class="fa-solid fa-arrow-right"></i>
                </p>
            </a>
        </div>
    </section>
</x-layouts.app>
