<!-- Container Chart & Tabel -->
<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">

    <!-- Bagian Chart -->
    <h3 class="mb-4 text-base font-bold text-slate-900">Progres Penanganan Risiko</h3>
    <div class="h-64">
        <canvas id="chartProgres"></canvas>
    </div>

<!-- Bagian Tabel Berdasarkan Unit Kerja -->
    <div class="mt-8 overflow-hidden rounded-md border border-slate-200">
        <table class="w-full text-sm text-left border-collapse">

            <!-- BAGIAN THEAD DIPERBARUI AGAR SELARAS DENGAN TEMA SLATE -->
            <thead class="bg-indigo-600 text-white border-b border-indigo-700">
                <tr>
                    <th class="px-4 py-3 font-semibold whitespace-nowrap">Unit Kerja</th>
                    <th class="px-4 py-3 font-semibold text-center w-28 whitespace-nowrap">Belum</th>
                    <th class="px-4 py-3 font-semibold text-center w-28 whitespace-nowrap">Proses</th>
                    <th class="px-4 py-3 font-semibold text-center w-28 whitespace-nowrap">Sudah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($progresPerUnit ?? [] as $item)
                    <tr class="bg-white hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3 border-b text-slate-700">
                            {{ $item['nama_unit'] }}
                        </td>
                        <td class="px-4 py-3 border-b text-center text-slate-700 font-medium">
                            {{ $item['belum'] }}
                        </td>
                        <td class="px-4 py-3 border-b text-center text-slate-700 font-medium">
                            {{ $item['proses'] }}
                        </td>
                        <td class="px-4 py-3 border-b text-center text-slate-700 font-medium">
                            {{ $item['sudah'] }}
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

<!-- Script Chart.js (Tetap sama seperti milikmu) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const canvasElement = document.getElementById('chartProgres');
            if (!canvasElement) return;

            const ctx = canvasElement.getContext('2d');
            const progresData = {!! json_encode($progresData ?? [0, 0, 0]) !!};

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Belum', 'Proses', 'Sudah'],
                    datasets: [{
                        data: progresData,
                        backgroundColor: [
                            '#fcd34d', // Kuning
                            '#a3e635', // Hijau
                            '#93c5fd'  // Biru
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom', // Disesuaikan sedikit agar tabel di bawahnya tidak terlalu sesak
                            labels: { font: { weight: 'bold' } }
                        }
                    }
                }
            });
        }, 200);
    });
</script>
