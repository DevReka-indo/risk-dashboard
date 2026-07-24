<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-900">Komposisi Risk Owner atas Distribusi Level Risiko</h2>
        <p class="mt-1 text-sm text-slate-500">Jumlah event risiko per departemen berdasarkan tingkat level risiko (Hanya menampilkan departemen yang memiliki data risiko).</p>
    </div>

    <!-- Container dengan fleksibilitas empty state & dynamic height -->
    <div class="relative w-full min-h-[300px] flex items-center justify-center">
        <canvas id="chartKomposisiRiskOwner"></canvas>

        <!-- Pesan ketika data kosong -->
        <div id="emptyMatrixMessage" class="hidden text-center text-slate-400 font-medium">
            <svg class="mx-auto h-12 w-12 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Belum ada data distribusi risiko SMAP untuk periode ini.
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const labelsData = {!! json_encode($labels ?? []) !!};
        const rawDatasets = {!! json_encode($chartDatasets ?? []) !!};

        const canvas = document.getElementById('chartKomposisiRiskOwner');
        const emptyMessage = document.getElementById('emptyMatrixMessage');

        if (!canvas) return;

        // Cek apakah ada data risiko yang nilainya > 0
        const hasData = rawDatasets.length > 0 && rawDatasets.some(ds => ds.data && ds.data.some(val => val > 0));

        // Jika tidak ada data / label kosong, tampilkan empty state
        if (!hasData || labelsData.length === 0) {
            canvas.style.display = 'none';
            if (emptyMessage) emptyMessage.classList.remove('hidden');
            return;
        }

        canvas.style.display = 'block';
        if (emptyMessage) emptyMessage.classList.add('hidden');

        // Bersihkan instance lama agar tidak glitch saat berpindah tab
        let existingChart = Chart.getChart("chartKomposisiRiskOwner");
        if (existingChart) existingChart.destroy();

        // Samain persis seperti modul Departemen: Tanpa barThickness kaku!
        const cleanDatasets = rawDatasets.map(dataset => {
            return {
                ...dataset,
                borderRadius: 4,
                maxBarThickness: 40,      // Batas atas maksimal bar gagah
                categoryPercentage: 0.8,  // Memaksimalkan area kategori (80%)
                barPercentage: 0.9,       // Memaksimalkan ketebalan bar (90%)
            };
        });

        const ctxStacked = canvas.getContext('2d');

        // Set tinggi canvas menyesuaikan jumlah bar departemen (55px per bar)
        const chartHeight = Math.max(300, labelsData.length * 55);
        canvas.parentNode.style.height = chartHeight + 'px';

        new Chart(ctxStacked, {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: cleanDatasets
            },
            options: {
                indexAxis: 'y', // Horizontal Stacked Bar
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: { left: 20, right: 20, top: 10, bottom: 10 }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 10,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { size: 13 },
                        bodyFont: { size: 13 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            // Menyembunyikan item tooltip jika nilainya 0
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
                        ticks: {
                            autoSkip: false,
                            font: { size: 12, weight: '500' },
                            padding: 12
                        }
                    }
                }
            }
        });
    });
</script>
