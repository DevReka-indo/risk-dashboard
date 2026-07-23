<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm space-y-4">

    {{-- TOP BAR --}}
    <div class="border-b border-slate-100 pb-3">
        <h3 class="text-base font-bold text-slate-900">Progres Penanganan Risiko SMAP</h3>
        <p class="text-xs text-slate-500 mt-0.5">Visualisasi status penanganan risiko pada kuartal berjalan.</p>
    </div>

    {{-- AREA CANVAS CHART --}}
    <div class="flex flex-col items-center p-6 bg-indigo-50/30 rounded-lg border border-indigo-100/50 max-w-sm mx-auto">
        <!-- UI Label dilembutkan: font-black -> font-bold, warna badge disesuaikan ke pastel -->
        <h4 class="text-xs font-bold text-slate-700 bg-white shadow-sm border border-slate-200 px-3 py-1.5 rounded-xl mb-4 w-full text-center uppercase tracking-wider flex justify-center items-center gap-2">
            Status Penanganan Risiko
            <span class="text-[9px] px-1.5 py-0.5 rounded font-bold text-indigo-700 bg-indigo-100">CURRENT TW</span>
        </h4>
        <div class="relative w-full h-[220px]">
            <canvas id="smapChartProgresUnique"></canvas>
        </div>
    </div>

    {{-- TABEL PROGRES PER UNIT KERJA --}}
    <!-- Wrapper div ditambahkan border rounded agar tabel terlihat rapi di dalam kotak -->
    <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 shadow-sm">
        <!-- text-xs diubah ke text-sm agar seragam ukurannya dengan tabel lain -->
        <table class="w-full text-center text-sm border-collapse">
            
            <!-- Thead: Inline style biru dihapus, diganti bg-slate-50 yang lembut -->
            <thead class="bg-indigo-600 text-white border-b border-indigo-700">
                <tr>
                    <th class="py-3 px-4 text-left w-2/5 font-semibold">Unit kerja</th>
                    <th class="py-3 px-4 w-1/5 font-semibold">Belum</th>
                    <th class="py-3 px-4 w-1/5 font-semibold">Proses</th>
                    <th class="py-3 px-4 w-1/5 font-semibold">Sudah</th>
                </tr>
            </thead>
            
            <!-- Tbody: Border hitam (slate-900) dihapus, diganti text-slate-700 dan font-medium -->
            <tbody class="text-slate-700">
                {{-- Modifikasi variabel $smapUnitTable sesuai variabel yang kamu pass dari Controller --}}
                @forelse($smapUnitTable ?? [] as $row)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        <td class="border-b border-r border-slate-200 py-2.5 px-4 text-left font-medium">{{ $row->nama_unit }}</td>
                        <td class="border-b border-r border-slate-200 py-2.5 px-4 font-medium">{{ $row->progress_belum > 0 ? $row->progress_belum : '' }}</td>
                        <td class="border-b border-r border-slate-200 py-2.5 px-4 font-medium">{{ $row->progress_proses > 0 ? $row->progress_proses : '' }}</td>
                        <td class="border-b border-slate-200 py-2.5 px-4 font-medium">{{ $row->progress_sudah > 0 ? $row->progress_sudah : '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border-b border-slate-200 py-6 text-slate-400 text-center font-normal">
                            Data unit kerja belum tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Mengambil data dari controller
        const pieData = {!! json_encode($smapPieData ?? null) !!};

        if (!pieData || !pieData.progres) return;

        const ctxProgres = document.getElementById('smapChartProgresUnique');
        if (!ctxProgres) return;

        // Reset instance Chart.js jika sebelumnya sudah ada
        if (Chart.getChart(ctxProgres)) {
            Chart.getChart(ctxProgres).destroy();
        }

        // Warna Chart: Belum Dimulai (Merah), Sedang Berjalan (Amber/Kuning), Selesai (Emerald/Hijau)
        const colorsProgres = ['#ef4444', '#f59e0b', '#10b981'];

        // Inisialisasi Chart murni menggunakan data '.off' (Kuartal Berjalan saja)
        new Chart(ctxProgres.getContext('2d'), {
            type: 'pie',
            data: {
                labels: pieData.progres.labels,
                datasets: [{
                    data: pieData.progres.off, // Mengunci data hanya pada TW berjalan
                    backgroundColor: colorsProgres,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10, weight: '500' } // Menurunkan sedikit ketebalan font legenda grafik
                        }
                    }
                }
            }
        });
    });
</script>