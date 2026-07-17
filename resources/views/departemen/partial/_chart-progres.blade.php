<!-- Container Chart -->
<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
    <h3 class="mb-4 text-base font-bold text-slate-900">Progres Penanganan Risiko</h3>
    <div class="h-64">
        <canvas id="chartProgres"></canvas>
    </div>
</div>

<!-- Script Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delay 200ms untuk memastikan container tab sudah terbuka/ter-render
        setTimeout(function() {
            const canvasElement = document.getElementById('chartProgres');
            if (!canvasElement) return;

            const ctx = canvasElement.getContext('2d');
            const progresData = {!! json_encode($progresData ?? [0, 0, 0]) !!};

            // Debug: Cek di Console F12 apakah data masuk sebagai [x, y, z]
            console.log("Data grafik:", progresData);

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Belum', 'Proses', 'Sudah'],
                    datasets: [{
                        data: progresData,
                        backgroundColor: [
                            '#fcd34d', // Kuning (Belum)
                            '#a3e635', // Hijau Muda (Proses)
                            '#93c5fd'  // Biru (Sudah)
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // Penting agar mengikuti h-64
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { font: { weight: 'bold' } }
                        }
                    }
                }
            });
        }, 200);
    });
</script>
