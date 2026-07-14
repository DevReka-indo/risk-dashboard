<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-4">

    {{-- Top Header --}}
    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Efektivitas Mitigasi Risiko</h3>
            <p class="text-xs text-slate-500 mt-0.5">Analisis hasil realisasi penanganan risiko triwulan ini.</p>
        </div>
    </div>

    {{-- Area Canvas --}}
    <div class="flex flex-col items-center p-6 bg-slate-50/50 rounded-2xl border border-slate-100 max-w-sm mx-auto">
        <h4 class="text-xs font-black text-slate-700 bg-white shadow-sm border border-slate-200 px-3 py-1.5 rounded-xl mb-4 w-full text-center uppercase tracking-wider">
            Matriks Efektivitas
        </h4>
        <div class="relative w-full h-[220px]">
            <canvas id="smapChartEfektif"></canvas>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const pieData = {!! json_encode($smapPieData ?? null) !!};

        if (!pieData || !pieData.efektif) return;

        const ctxEfektif = document.getElementById('smapChartEfektif');
        if (!ctxEfektif) return;

        // Reset instance Chart.js jika sebelumnya sudah ada
        if (Chart.getChart(ctxEfektif)) {
            Chart.getChart(ctxEfektif).destroy();
        }

        // Palette warna sesuai status (Pencatatan, Effective, Mostly Effective, Partially Effective, In-Effective, Unmeasurable)
        const colorsEfektif = ['#3b82f6', '#10b981', '#14b8a6', '#f59e0b', '#ef4444', '#64748b'];

        new Chart(ctxEfektif.getContext('2d'), {
            type: 'pie',
            data: {
                labels: pieData.efektif.labels,
                datasets: [{
                    data: pieData.efektif.off, // Mengunci langsung pada data triwulan berjalan
                    backgroundColor: colorsEfektif,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10, weight: '600' }
                        }
                    }
                }
            }
        });
    });
</script>
