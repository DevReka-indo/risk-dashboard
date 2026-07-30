@php
    // Pastikan $items adalah array
    $items = $items ?? [];
    
    if ($items instanceof \Illuminate\Support\Collection) {
        $items = $items->toArray();
    }
    
    // Hitung total keseluruhan untuk kalkulasi persentase
    $sumTotal = 0;
    foreach ($items as $item) {
        $sumTotal += (int) ($item['total'] ?? 0);
    }
@endphp

<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5">
        <div class="flex items-center gap-2">
            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-sm font-bold text-slate-800">Distribusi Level Risiko</h2>
                <p class="text-xs text-slate-500">Jumlah monitoring berdasarkan level risiko</p>
            </div>
        </div>
    </div>

    <div class="space-y-4">
        @forelse ($items as $item)
            @php
                $total = (int) ($item['total'] ?? 0);
                $percentage = $sumTotal > 0 ? round(($total / $sumTotal) * 100, 1) : 0;
                $label = $item['label'] ?? 'Tidak Diketahui';
            @endphp

            <div>
                <div class="mb-2 flex items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-700">
                        {{ $label }}
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-400">({{ $percentage }}%)</span>
                        <span class="text-sm font-bold text-slate-900">{{ $total }}</span>
                    </div>
                </div>

                <!-- Progress Bar Container (Warna Biru #2563eb / Tailwind blue-600) -->
                <div class="h-3.5 w-full overflow-hidden rounded-full bg-slate-100">
                    <div
                        class="h-full rounded-full transition-all duration-500 ease-out"
                        style="width: {{ $percentage }}%; background-color: #2563eb; min-width: {{ $total > 0 ? '8px' : '0' }};">
                    </div>
                </div>
            </div>
        @empty
            <div class="py-8 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">
                    <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-slate-600">Belum Ada Data Level Risiko</p>
                <p class="text-xs text-slate-400">Data akan muncul setelah ada monitoring risiko</p>
            </div>
        @endforelse
    </div>
</div>