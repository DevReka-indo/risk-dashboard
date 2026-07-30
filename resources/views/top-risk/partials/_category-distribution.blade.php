@php
    $items = $items ?? [];
    
    // Konversi Collection ke array jika diperlukan
    if ($items instanceof \Illuminate\Support\Collection) {
        $items = $items->toArray();
    }
    
    // Hitung total keseluruhan untuk kalkulasi persentase yang akurat
    $sumTotal = 0;
    foreach ($items as $item) {
        $sumTotal += (int) ($item['total'] ?? 0);
    }
@endphp

<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-5">
        <h2 class="text-base font-bold text-slate-900">
            Jumlah Kategori Risiko
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Distribusi risiko berdasarkan kategori.
        </p>
    </div>

    <div class="space-y-4">
        @forelse ($items as $item)
            @php
                $total = (int) ($item['total'] ?? 0);
                // Hitung persentase terhadap total keseluruhan data
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
                <p class="text-sm font-medium text-slate-600">Belum Ada Data Kategori Risiko</p>
                <p class="text-xs text-slate-400">Data akan muncul setelah ada monitoring risiko</p>
            </div>
        @endforelse
    </div>
</div>