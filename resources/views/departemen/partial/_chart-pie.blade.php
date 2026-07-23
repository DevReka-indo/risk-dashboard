<div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm space-y-6">

    {{-- TOP BAR: JUDUL SECTION --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
            <h3 class="text-base font-semibold text-slate-800">Visualisasi Peta Risiko</h3>
            <p class="text-sm text-slate-500 mt-0.5">Analisis komparatif sebaran tingkat risiko inherent, current, dan target.</p>
        </div>
    </div>

    {{-- LAYOUT 3 CANVA PIE CHART BERJEJER (CARDLESS / TANPA KOTAK BORDER) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 py-4">

        {{-- Pie 1: Inherent --}}
        <div class="flex flex-col items-center">
            <h4 class="text-sm font-semibold text-slate-700 mb-4 w-full text-center flex flex-col items-center justify-center gap-1.5">
                Risiko Inherent
            </h4>
            <div class="relative w-full max-w-[200px] h-[200px] flex justify-center">
                <canvas id="chartInherent"></canvas>
            </div>
        </div>

        {{-- Pie 2: Current --}}
        <div class="flex flex-col items-center">
            <h4 class="text-sm font-semibold text-slate-700 mb-4 w-full text-center flex flex-col items-center justify-center gap-1.5">
                Risiko Current
            </h4>
            <div class="relative w-full max-w-[200px] h-[200px] flex justify-center">
                <canvas id="chartCurrent"></canvas>
            </div>
        </div>

        {{-- Pie 3: Target --}}
        <div class="flex flex-col items-center">
            <h4 class="text-sm font-semibold text-slate-700 mb-4 w-full text-center flex flex-col items-center justify-center gap-1.5">
                Risiko Target
            </h4>
            <div class="relative w-full max-w-[200px] h-[200px] flex justify-center">
                <canvas id="chartTarget"></canvas>
            </div>
        </div>

    </div>

    {{-- KETERANGAN WARNA LEGENDA UTAMA (HTML DI BAWAH GRAFIK) --}}
    <div class="pt-2 border-t border-slate-100 flex flex-wrap justify-center items-center gap-x-6 gap-y-3 text-xs font-medium text-slate-600">
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#03B050] shadow-sm"></span> Low</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#91D050] shadow-sm"></span> Low to Moderate</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#FFFF00] border border-slate-200 shadow-sm"></span> Moderate</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#FFC000] shadow-sm"></span> Moderate to High</div>
        <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-[#FF0100] shadow-sm"></span> High</div>
    </div>
</div>

{{-- Memanggil library Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        function initPieChart(canvasId, data) {
            const ctx = document.getElementById(canvasId);
            if (!ctx) return;

            // Bersihkan instance lama agar tidak bentrok jika halaman dirender ulang (Livewire/AJAX)
            if (Chart.getChart(ctx)) Chart.getChart(ctx).destroy();

            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['High', 'Moderate to High', 'Moderate', 'Low to Moderate', 'Low'],
                    datasets: [{
                        data: data,
                        // Warna disesuaikan dengan urutan label dari High (Merah) ke Low (Hijau Gelap)
                        backgroundColor: [
                            '#FF0100', // High 
                            '#FFC000', // Moderate to High 
                            '#FFFF00', // Moderate 
                            '#91D050', // Low to Moderate 
                            '#03B050'  // Low 
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff' // Memberikan garis batas putih bersih antar potongan pie
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Mematikan legenda bawaan Chart.js karena kita pakai legenda HTML
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return ` ${label}: ${value} (${pct}%)`;
                                }
                            }
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