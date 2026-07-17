<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex flex-col items-center">
        <h3 class="text-center font-bold text-slate-800 mb-4">Risiko Inherent</h3>
        <div class="relative w-full h-56 md:h-64 flex justify-center">
            <canvas id="chartInherent"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex flex-col items-center">
        <h3 class="text-center font-bold text-slate-800 mb-4">Risiko Current</h3>
        <div class="relative w-full h-56 md:h-64 flex justify-center">
            <canvas id="chartCurrent"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg border border-slate-200 shadow-sm flex flex-col items-center">
        <h3 class="text-center font-bold text-slate-800 mb-4">Risiko Target</h3>
        <div class="relative w-full h-56 md:h-64 flex justify-center">
            <canvas id="chartTarget"></canvas>
        </div>
    </div>
</div>

{{-- Memanggil library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        function initPieChart(canvasId, data) {
            const ctx = document.getElementById(canvasId);

            if (!ctx) return;

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['High', 'Moderate to High', 'Moderate', 'Low to Moderate', 'Low'],
                    datasets: [{
                        // Menggunakan data dinamis dari parameter fungsi
                        data: data,
                        backgroundColor: [
                            '#FF0000', // High (Merah)
                            '#F28C28', // Moderate to High (Oranye)
                            '#FFD700', // Moderate (Kuning)
                            '#90EE90', // Low to Moderate (Hijau Muda)
                            '#228B22'  // Low (Hijau Tua)
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } }
                        }
                    }
                }
            });
        }

        // Variabel dari Controller ditangkap di sini menggunakan json_encode murni
        const inherentData = {!! json_encode($inherentData ?? [0, 0, 0, 0, 0]) !!};
        const currentData  = {!! json_encode($currentData ?? [0, 0, 0, 0, 0]) !!};
        const targetData   = {!! json_encode($targetData ?? [0, 0, 0, 0, 0]) !!};

        // Jalankan render chart
        initPieChart('chartInherent', inherentData);
        initPieChart('chartCurrent', currentData);
        initPieChart('chartTarget', targetData);
    });
</script>
