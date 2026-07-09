<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-8">
    {{-- Header Konten --}}
    <div>
        <h2 class="text-base font-bold text-slate-900">
            Rekapitulasi & Persebaran Level Risiko per Departemen
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Visualisasi grafik bertumpuk beserta matriks rincian tingkat risiko pada masing-masing unit kerja periode {{ $period['label'] }}.
        </p>
    </div>

    {{-- Tampilan Grafik Stacked Bar Chart --}}
    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
        <div class="relative w-full h-[350px]">
            <canvas id="matrixStackedChart"></canvas>
        </div>
    </div>

    {{-- Tampilan Tabel Rincian Matriks --}}
    <div>
        <h3 class="text-sm font-bold text-slate-800 mb-3 px-1">Tabel Rincian Matriks</h3>
        <div class="overflow-x-auto rounded-2xl border border-slate-100">
            <table class="w-full min-w-[700px] border-collapse text-left text-sm text-slate-600">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-700">
                        <th class="px-4 py-3 w-16">No</th>
                        <th class="px-4 py-3">Departemen / Unit Kerja</th>
                        @foreach($levels as $level)
                            <th class="px-4 py-3 text-center">{{ $level->nama_level }}</th>
                        @endforeach
                        <th class="px-4 py-3 text-center bg-indigo-50/50 text-indigo-700">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($rekapUnitLevel as $index => $row)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 font-medium text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3.5 font-semibold text-slate-800">{{ $row['nama_unit'] }}</td>
                            @foreach($levels as $level)
                                <td class="px-4 py-3.5 text-center font-medium @if($row['levels'][$level->id_level] > 0) text-slate-900 font-bold @else text-slate-300 @endif">
                                    {{ $row['levels'][$level->id_level] }}
                                </td>
                            @endforeach
                            <td class="px-4 py-3.5 text-center font-bold bg-indigo-50/30 text-indigo-600">
                                {{ $row['total_unit'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($levels) + 3 }}" class="px-4 py-8 text-center text-slate-400">
                                Tidak ada data matriks untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Script Chart.js Inisialisasi Stacked Bar --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('matrixStackedChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($matrixChartLabels) !!},
                datasets: {!! json_encode($matrixChartDatasets) !!}
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: { family: 'Plus Jakarta Sans, sans-serif', size: 12, weight: '500' },
                            color: '#475569'
                        }
                    },
                    tooltip: {
                        padding: 12,
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Plus Jakarta Sans, sans-serif', weight: '700' },
                        bodyFont: { family: 'Plus Jakarta Sans, sans-serif' }
                    }
                },
                scales: {
                    x: {
                        stacked: true, // Mengaktifkan tumpukan di sumbu X
                        grid: { display: false },
                        ticks: {
                            color: '#64748b',
                            font: { family: 'Plus Jakarta Sans, sans-serif', size: 11 }
                        }
                    },
                    y: {
                        stacked: true, // Mengaktifkan tumpukan di sumbu Y
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            precision: 0,
                            color: '#64748b',
                            font: { family: 'Plus Jakarta Sans, sans-serif', size: 11 }
                        }
                    }
                }
            }
        });
    });
</script>
