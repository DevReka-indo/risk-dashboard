<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm space-y-4">

    {{-- TOP BAR --}}
    <div class="border-b border-slate-100 pb-3">
        <h3 class="text-base font-bold text-slate-900">Progres Penanganan Risiko SMAP</h3>
        <p class="text-xs text-slate-500 mt-0.5">Visualisasi status penanganan risiko pada kuartal berjalan.</p>
    </div>

    {{-- AREA CANVAS CHART --}}
    <div class="flex flex-col items-center p-6 bg-indigo-50/30 rounded-lg border border-indigo-100/50 max-w-sm mx-auto">
        <h4 class="text-xs font-black text-indigo-950 bg-white shadow-sm border border-indigo-100 px-3 py-1.5 rounded-xl mb-4 w-full text-center uppercase tracking-wider flex justify-center items-center gap-2">
            Status Penanganan Risiko
            <span class="text-[9px] px-1.5 py-0.5 rounded font-extrabold text-white bg-indigo-500">CURRENT TW</span>
        </h4>
        <div class="relative w-full h-[220px]">
            <canvas id="smapChartProgresUnique"></canvas>
        </div>
    </div>

    {{-- TABEL PROGRES PER UNIT KERJA (Sesuai Gambar Client) --}}
    <div class="mt-6 overflow-x-auto">
        <table class="w-full text-center text-xs border-collapse border border-slate-900">
            <thead class="bg-blue-600 text-white font-bold" style="background-color: #2B72B9 !important; color: #ffffff !important;">
                <tr>
                    <th class="border border-slate-900 py-2.5 px-3 text-left w-2/5 font-bold text-white">Unit kerja</th>
                    <th class="border border-slate-900 py-2.5 px-3 w-1/5 font-bold text-white">Belum</th>
                    <th class="border border-slate-900 py-2.5 px-3 w-1/5 font-bold text-white">Proses</th>
                    <th class="border border-slate-900 py-2.5 px-3 w-1/5 font-bold text-white">Sudah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-900 font-semibold text-slate-900">
                {{-- Modifikasi variabel $smapUnitTable sesuai variabel yang kamu pass dari Controller --}}
                @forelse($smapUnitTable ?? [] as $row)
                    <tr>
                        <td class="border border-slate-900 py-2 px-3 text-left font-medium">{{ $row->nama_unit }}</td>
                        <td class="border border-slate-900 py-2 px-3">{{ $row->progress_belum > 0 ? $row->progress_belum : '' }}</td>
                        <td class="border border-slate-900 py-2 px-3">{{ $row->progress_proses > 0 ? $row->progress_proses : '' }}</td>
                        <td class="border border-slate-900 py-2 px-3">{{ $row->progress_sudah > 0 ? $row->progress_sudah : '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="border border-slate-900 py-3 text-slate-400 italic font-normal">
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
                            font: { size: 10, weight: '600' }
                        }
                    }
                }
            }
        });
    });
</script>
