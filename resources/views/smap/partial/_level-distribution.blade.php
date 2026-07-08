<div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4">
        <h3 class="text-sm font-bold text-slate-900">
            Distribusi Level Risiko
        </h3>
        <p class="text-xs text-slate-500">
            Sebaran risiko SMAP berdasarkan level.
        </p>
    </div>

    <div class="space-y-3">
        @forelse ($items as $item)
            <div class="flex items-center gap-3">
                <span class="inline-flex min-w-[130px] rounded-full px-3 py-1 text-xs font-semibold {{ $item['color'] ?? 'bg-slate-100 text-slate-700' }}">
                    {{ $item['label'] ?? '-' }}
                </span>
                <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                    @php
                        $totalItems = $items->sum('total') ?: 1;
                        $percentage = ($item['total'] / $totalItems) * 100;
                    @endphp
                    <div class="h-full rounded-full {{ $item['color'] ?? 'bg-slate-400' }} transition-all duration-500" style="width: {{ $percentage }}%"></div>
                </div>
                <span class="text-sm font-semibold text-slate-700 min-w-[40px] text-right">
                    {{ $item['total'] }}
                </span>
            </div>
        @empty
            <p class="text-sm text-slate-400">Belum ada data level risiko.</p>
        @endforelse
    </div>
</div>