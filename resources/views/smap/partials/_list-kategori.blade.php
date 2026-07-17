<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <h2 class="text-lg font-bold text-slate-900 mb-6">Grafik Kategori Risiko (SMAP)</h2>

    <div class="relative w-full min-h-[350px]">
        <canvas id="chartKategoriBar"></canvas>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Hancurkan chart lama biar gak nyangkut di memori browser
        let existingChart = Chart.getChart("chartKategoriBar");
        if (existingChart != undefined) {
            existingChart.destroy();
        }

        const ctxCat = document.getElementById('chartKategoriBar').getContext('2d');

        new Chart(ctxCat, {
            type: 'bar',
            data: {
                labels: {!! json_encode($catLabels) !!}, // SUMBU Y (Kiri): Nama Kategori
                datasets: [{
                    label: 'Jumlah Risiko',
                    data: {!! json_encode($catData) !!}, // SUMBU X (Bawah): Angka Jumlah
                    backgroundColor: '#10b981', // Warna hijau emerald (biar beda dengan warna departemen)
                    borderRadius: 6,
                    barThickness: 16, // Ketebalan batang bar dibuat proporsional
                }]
            },
            options: {
                indexAxis: 'y', // 🔥 INI KUNCINYA: Memutar grafik jadi menyamping (Y = Nama, X = Angka)
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { // Angka di bawah
                        beginAtZero: true,
                        ticks: { stepSize: 1 },
                        grid: { drawBorder: false }
                    },
                    y: { // Teks nama kategori di kiri
                        grid: { display: false },
                        ticks: {
                            autoSkip: false // Paksa semua nama kategori SMAP muncul tanpa ada yang skip
                        }
                    }
                }
            }
        });
    });
</script>
