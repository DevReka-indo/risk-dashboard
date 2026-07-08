<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-lg font-bold text-slate-900">
            {{ $title ?? 'Manajemen Kategori Risiko' }}
        </h1>
        <p class="text-sm text-slate-500">
            {{ $subtitle ?? 'Kelola kategori untuk pemisahan data SMAP dan Departemen.' }}
        </p>
    </div>
    <div>
        <a
            href="{{ $buttonRoute ?? route('kategori-risiko.create') }}"
            class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg transition hover:bg-indigo-500">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            {{ $buttonText ?? 'Tambah Kategori' }}
        </a>
    </div>
</div>