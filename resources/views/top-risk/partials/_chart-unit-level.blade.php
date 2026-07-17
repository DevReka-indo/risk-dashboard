<div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4">
        <h3 class="text-base font-semibold uppercase tracking-wider text-slate-800 text-center">
            Komposisi Risk Owner Berdasarkan Level
        </h3>
    </div>

    <!-- Canvas Container -->
    <div class="relative w-full" style="height: 400px;">
        <canvas id="unitLevelChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil data distribusi unit-level dari backend
        const chartData = @json($unitLevelDistribution);

        if (!chartData || chartData.labels.length === 0) return;

        const ctx = document.getElementById('unitLevelChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: chartData.datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right', // Legend di sebelah kanan mengikuti referensi gambar
                        labels: {
                            font: { size: 12 },
                            color: '#475569',
                            usePointStyle: true,
                            boxWidth: 10
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        mode: 'index', // Menampilkan semua level dalam satu bar saat dihover
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        stacked: true, // WAJIB untuk membuat bar bertumpuk
                        ticks: {
                            color: '#64748b',
                            font: { size: 11 },
                            maxRotation: 45, // Miringkan teks jika nama unit terlalu panjang
                            minRotation: 45
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    },
                    y: {
                        stacked: true, // WAJIB untuk membuat bar bertumpuk
                        ticks: {
                            color: '#475569',
                            font: { size: 12 },
                            stepSize: 1 // Skala grafik lompat 1 angka (0, 1, 2, 3...)
                        },
                        grid: {
                            color: '#e2e8f0',
                            drawBorder: false
                        }
                    }
                }
            }
        });
    });
</script>
