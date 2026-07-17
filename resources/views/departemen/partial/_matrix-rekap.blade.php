<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Komposisi Risk Owner atas Distribusi Level Risiko</h2>
        <p class="mt-1 text-sm text-slate-500">Jumlah event risiko per departemen berdasarkan tingkat level risiko.</p>
    </div>

    <div class="relative w-full min-h-[300px] flex items-center justify-center">
        <canvas id="chartKomposisiRiskOwner"></canvas>

        <div id="emptyMatrixMessage" class="hidden text-center text-slate-400 font-medium">
            <svg class="mx-auto h-12 w-12 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Belum ada data distribusi risiko untuk periode ini.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const labelsData = {!! json_encode($labels ?? []) !!};
        const datasets = {!! json_encode($chartDatasets ?? []) !!};

        const canvas = document.getElementById('chartKomposisiRiskOwner');
        const emptyMessage = document.getElementById('emptyMatrixMessage');

        // Cek apakah ada angka lebih besar dari 0 di dalam datasets
        const hasData = datasets.length > 0 && datasets.some(ds => ds.data.some(val => val > 0));

        // Jika tidak ada data, sembunyikan canvas dan tampilkan pesan
        if (!hasData || labelsData.length === 0) {
            canvas.style.display = 'none';
            emptyMessage.classList.remove('hidden');
            return;
        }

        // Hapus chart lama jika reload
        let existingChart = Chart.getChart("chartKomposisiRiskOwner");
        if (existingChart) existingChart.destroy();

        const ctxStacked = canvas.getContext('2d');

        // Buat tinggi chart menyesuaikan banyaknya departemen agar tidak berdempetan
        const chartHeight = Math.max(300, labelsData.length * 55);
        canvas.parentNode.style.height = chartHeight + 'px';

        new Chart(ctxStacked, {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: datasets
            },
            options: {
                indexAxis: 'y', // Ubah jadi horizontal
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { left: 20, right: 20, top: 10, bottom: 10 } },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, boxWidth: 10, font: { size: 12 } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { size: 13 },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            // Sembunyikan label di tooltip jika nilainya 0
                            label: function(context) {
                                if (context.parsed.x === 0) return null;
                                return ` ${context.dataset.label}: ${context.parsed.x}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        stacked: true,
                        grid: { display: false },
                        ticks: { autoSkip: false, font: { size: 12, weight: '500' }, padding: 12 }
                    }
                }
            }
        });
    });
</script>
