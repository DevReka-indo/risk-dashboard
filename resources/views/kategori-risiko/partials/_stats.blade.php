<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Total Kategori
                </p>
                <p class="mt-2 text-2xl font-bold text-slate-900">
                    {{ $total ?? 0 }}
                </p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-indigo-50 text-indigo-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Kategori SMAP
                </p>
                <p class="mt-2 text-2xl font-bold text-slate-900">
                    {{ $smapCount ?? 0 }}
                </p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-purple-50 text-purple-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h15.75M10.5 16.5 7.5 13.5M7.5 10.5 10.5 7.5M16.5 16.5 19.5 13.5M16.5 10.5 13.5 7.5" />
                </svg>
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                    Kategori Departemen
                </p>
                <p class="mt-2 text-2xl font-bold text-slate-900">
                    {{ $departemenCount ?? 0 }}
                </p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-3xl bg-blue-50 text-blue-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h15.75M10.5 16.5 7.5 13.5M7.5 10.5 10.5 7.5M16.5 16.5 19.5 13.5M16.5 10.5 13.5 7.5" />
                </svg>
            </div>
        </div>
    </div>
</div>