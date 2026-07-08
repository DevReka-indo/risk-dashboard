<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5">
        <h2 class="text-base font-bold text-slate-900">
            Jumlah Status Risiko
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Status monitoring pada periode terpilih.
        </p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        @foreach ($items as $item)
            <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                <div class="text-sm font-medium text-slate-500">
                    {{ $item['label'] }}
                </div>
                <div class="mt-3 text-3xl font-bold text-slate-900">
                    {{ $item['total'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>
