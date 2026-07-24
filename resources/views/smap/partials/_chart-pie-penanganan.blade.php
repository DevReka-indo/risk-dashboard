<!-- Container Chart & Tabel Progres Penanganan Risiko SMAP -->
<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">

    <!-- Header & Bagian Chart -->
    <h3 class="mb-4 text-base font-bold text-slate-900">Progres Penanganan Risiko SMAP</h3>
    <div class="h-64">
        <canvas id="smapChartProgresUnique"></canvas>
    </div>

    <!-- Bagian Tabel Berdasarkan Unit Kerja -->
    <div class="mt-8 overflow-hidden rounded-md border border-slate-200">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="bg-indigo-600 text-white border-b border-indigo-700">
                <tr>
                    <th class="px-4 py-3 font-semibold whitespace-nowrap">Unit Kerja</th>
                    <th class="px-4 py-3 font-semibold text-center w-28 whitespace-nowrap">Belum</th>
                    <th class="px-4 py-3 font-semibold text-center w-28 whitespace-nowrap">Proses</th>
                    <th class="px-4 py-3 font-semibold text-center w-28 whitespace-nowrap">Sudah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($smapUnitTable ?? [] as $row)
                    <tr class="bg-white hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 border-b border-slate-200 text-slate-700 font-medium">
                            {{ $row->nama_unit ?? $row['nama_unit'] ?? '-' }}
                        </td>
                        <td class="px-4 py-3 border-b border-slate-200 text-center text-slate-700 font-medium">
                            {{ $row->progress_belum ?? $row['progress_belum'] ?? 0 }}
                        </td>
                        <td class="px-4 py-3 border-b border-slate-200 text-center text-slate-700 font-medium">
                            {{ $row->progress_proses ?? $row['progress_proses'] ?? 0 }}
                        </td>
                        <td class="px-4 py-3 border-b border-slate-200 text-center text-slate-700 font-medium">
                            {{ $row->progress_sudah ?? $row['progress_sudah'] ?? 0 }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500 border-b border-slate-200">
                            Belum ada data progres per unit kerja pada periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            const canvasElement = document.getElementById('smapChartProgresUnique');
            if (!canvasElement) return;

            // Membaca data pie dari controller
            const pieData = {!! json_encode($smapPieData ?? null) !!};

            // Ambil array nilai [belum, proses, sudah]
            const chartValues = pieData && pieData.progres && pieData.progres.off
                ? pieData.progres.off
                : [0, 0, 0];

            // Hapus instance lama jika ada
            const existingChart = Chart.getChart(canvasElement);
            if (existingChart) {
                existingChart.destroy();
            }

            const ctx = canvasElement.getContext('2d');

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Belum', 'Proses', 'Sudah'],
                    datasets: [{
                        data: chartValues,
                        backgroundColor: [
                            '#fcd34d', // Kuning (Belum)
                            '#a3e635', // Hijau (Proses)
                            '#93c5fd'  // Biru (Sudah)
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: { weight: 'bold' }
                            }
                        }
                    }
                }
            });
        }, 200);
    });
</script>
