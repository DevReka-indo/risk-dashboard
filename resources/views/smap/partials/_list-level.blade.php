<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Distribusi Level Risiko</h2>
        <p class="mt-1 text-sm text-slate-500">Jumlah monitoring berdasarkan level risiko.</p>
    </div>

    <div class="space-y-5">
        @forelse ($dashboardData['level_distribution'] as $level)
            <div>
                <div class="mb-2 flex items-center justify-between text-sm font-semibold">
                    <span class="text-slate-700">{{ $level['name'] }}</span>
                    <span class="text-slate-900">{{ $level['count'] }}</span>
                </div>

                <div class="h-2.5 w-full overflow-hidden rounded-full bg-slate-100">
                    <div
                        class="h-full rounded-full bg-indigo-600 transition-all duration-500 ease-in-out"
                        style="width: {{ $level['percentage'] }}%">
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-sm text-slate-400 py-4">
                Tidak ada data level risiko.
            </div>
        @endforelse
    </div>
</div>
