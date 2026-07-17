@php
    $maxTotal = max((int) $items->max('total'), 1);
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
        @foreach ($items as $item)
            @php
                $percentage = ((int) $item['total'] / $maxTotal) * 100;
            @endphp

            <div>
                <div class="mb-2 flex items-center justify-between gap-3">
                    <div class="text-sm font-semibold text-slate-700">
                        {{ $item['label'] }}
                    </div>
                    <div class="text-sm font-bold text-slate-900">
                        {{ $item['total'] }}
                    </div>
                </div>

                <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                    <div
                        class="h-full rounded-full bg-slate-900"
                        style="width: {{ $percentage }}%">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
