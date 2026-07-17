<div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4">
        <h3 class="text-base font-semibold uppercase tracking-wider text-slate-800 text-center">
            Nilai Top Risk
        </h3>
    </div>

    <!-- Canvas Container -->
    <div class="relative w-full" style="height: 400px;">
        <canvas id="nilaiTopRiskChart"></canvas>
    </div>
</div>

<!-- Pastikan Script Chart.js sudah diload di master layout Anda. Jika belum, uncomment script di bawah -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rawData = @json($nilaiTopRisk);

        // Jika data kosong, Anda bisa memilih untuk tidak merender chart
        if (!rawData || rawData.length === 0) return;

        const labels = rawData.map(item => item.nama_peristiwa_risiko);
        const dataValues = rawData.map(item => item.nilai);

        // Default warna merah dan kuning, mengikuti format 3D/warna gambar Anda sebelumnya
        const bgColors = rawData.map(item => item.kode_warna || '#ef4444');

        const ctx = document.getElementById('nilaiTopRiskChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Risiko',
                    data: dataValues,
                    backgroundColor: bgColors,
                    borderColor: '#334155', // Garis tepi gelap seperti slate
                    borderWidth: 1.5,
                    barThickness: 24, // Ketebalan batang bisa disesuaikan
                }]
            },
            options: {
                indexAxis: 'y', // Mengubah menjadi bar horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return ' Nilai: ' + context.parsed.x;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        min: 0,
                        max: 25,
                        ticks: {
                            stepSize: 5,
                            color: '#64748b',
                            font: { size: 12, weight: '500' }
                        },
                        grid: {
                            color: '#e2e8f0',
                            drawBorder: false,
                        }
                    },
                    y: {
                        ticks: {
                            color: '#475569',
                            font: { size: 12 },
                            autoSkip: false // Agar nama risiko yang panjang tidak hilang
                        },
                        grid: {
                            display: false,
                            drawBorder: false,
                        }
                    }
                }
            }
        });
    });
</script>
