<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="mb-6">
        <h2 class="text-base font-bold text-slate-900">Grafik Kategori Risiko</h2>
        <p class="mt-1 text-sm text-slate-500">Distribusi total jumlah risiko yang terdaftar berdasarkan kategori SMAP.</p>
    </div>

    <div class="relative w-full min-h-[350px]">
        <canvas id="chartKategoriBar"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let existingChart = Chart.getChart("chartKategoriBar");
        if (existingChart != undefined) {
            existingChart.destroy();
        }

        const ctxCat = document.getElementById('chartKategoriBar').getContext('2d');

        // Mapping data dari koleksi $categoryDistribution ke format Chart.js
        const categoryDataRaw = {!! json_encode($items) !!};
        const catLabels = categoryDataRaw.map(item => item.label);
        const catData = categoryDataRaw.map(item => item.total);

        new Chart(ctxCat, {
            type: 'bar',
            data: {
                labels: catLabels, // Sumbu Y (Kiri): Nama Kategori
                datasets: [{
                    label: 'Jumlah Risiko',
                    data: catData, // Sumbu X (Bawah): Angka Jumlah
                    backgroundColor: '#10b981', // Hijau emerald solid
                    borderRadius: 6,
                    borderSkipped: false,
                    barThickness: 16,
                }]
            },
            options: {
                indexAxis: 'y', // Membuat grafik menyamping horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Plus Jakarta Sans, sans-serif', weight: '700' },
                        bodyFont: { family: 'Plus Jakarta Sans, sans-serif' }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            color: '#64748b',
                            font: { family: 'Plus Jakarta Sans, sans-serif', size: 11 }
                        },
                        grid: {
                            color: '#f1f5f9',
                            drawBorder: false
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            autoSkip: false, // Menjamin semua nama kategori terlihat
                            color: '#334155',
                            font: { family: 'Plus Jakarta Sans, sans-serif', size: 11, weight: '500' }
                        }
                    }
                }
            }
        });
    });
</script>
