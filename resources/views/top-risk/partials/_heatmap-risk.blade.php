@php
$probLabels = [
    5 => 'Hampir Pasti Terjadi',
    4 => 'Sangat Mungkin Terjadi',
    3 => 'Bisa Terjadi',
    2 => 'Jarang Terjadi',
    1 => 'Sangat Jarang Terjadi',
];

$dampakLabels = [
    1 => 'Sangat rendah',
    2 => 'Rendah',
    3 => 'Moderat',
    4 => 'Tinggi',
    5 => 'Sangat tinggi',
];

// Matrix Mapping sesuai gambar
$getCellValue = function(int $p, int $d): int {
    $matrix = [
        5 => [7, 12, 17, 21, 25],
        4 => [4,  9, 14, 19, 24],
        3 => [3,  8, 13, 18, 23],
        2 => [2,  6, 11, 16, 22],
        1 => [1,  5, 10, 15, 20],
    ];
    return $matrix[$p][$d - 1] ?? ($p * $d);
};

// Map Level Name berdasarkan Nilai
$getLevelName = function(int $v): string {
    if (in_array($v, [20, 21, 22, 23, 24, 25])) return 'High';
    if (in_array($v, [16, 17, 18, 19]))          return 'Moderate to High';
    if (in_array($v, [12, 13, 14, 15]))          return 'Moderate';
    if (in_array($v, [6, 7, 8, 9, 10, 11]))      return 'Low to Moderate';
    return 'Low';
};

// Styling warna latar & teks sel (Selalu cerah & kontras)
$cellStyle = function(int $v): string {
    if (in_array($v, [20, 21, 22, 23, 24, 25])) return 'background-color:#ff0000 !important; color:#ffffff !important;'; // Red
    if (in_array($v, [16, 17, 18, 19]))          return 'background-color:#ff9900 !important; color:#ffffff !important;'; // Orange
    if (in_array($v, [12, 13, 14, 15]))          return 'background-color:#ffff00 !important; color:#000000 !important;'; // Yellow Bright
    if (in_array($v, [6, 7, 8, 9, 10, 11]))      return 'background-color:#92d050 !important; color:#000000 !important;'; // Light Green
    return                                             'background-color:#107c41 !important; color:#ffffff !important;'; // Dark Green
};

// Kumpulkan risiko per nilai dari $heatmap
$riskByValue = [];
if (!empty($heatmap['risks'])) {
    foreach ($heatmap['risks'] as $r) {
        $v = (int)($r['value'] ?? 0);
        if ($v > 0) $riskByValue[$v][] = $r['code'];
    }
}
if (empty($riskByValue) && !empty($heatmap['rows'])) {
    foreach ($heatmap['rows'] as $row) {
        foreach ($row as $cell) {
            $v = (int)($cell['value'] ?? 0);
            foreach (($cell['risks'] ?? []) as $r) {
                if ($v > 0) $riskByValue[$v][] = $r['code'];
            }
        }
    }
}
@endphp

{{-- Main Container Kertas Putih (Dikunci agar Dark Mode tidak mengubah warna dalam tabel) --}}
<div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white p-6 shadow-sm text-slate-900">

    <div class="overflow-x-auto pb-4">
        <div style="min-width:850px; display:flex; gap:20px; align-items:flex-start;">

            {{-- ── LEGENDA KIRI LUAR ── --}}
            <div style="width:130px; display:flex; flex-direction:column; border:1px solid #000000; border-radius:2px; overflow:hidden; flex-shrink:0; font-family:sans-serif;">
                <div style="background-color:#ff0000 !important; color:#ffffff !important; padding:6px 10px; font-size:11px; font-weight:700;">High</div>
                <div style="background-color:#ff9900 !important; color:#ffffff !important; padding:6px 10px; font-size:11px; font-weight:700;">Moderate to High</div>
                <div style="background-color:#ffff00 !important; color:#000000 !important; padding:6px 10px; font-size:11px; font-weight:700;">Moderate</div>
                <div style="background-color:#92d050 !important; color:#000000 !important; padding:6px 10px; font-size:11px; font-weight:700;">Low to Moderate</div>
                <div style="background-color:#107c41 !important; color:#ffffff !important; padding:6px 10px; font-size:11px; font-weight:700;">Low</div>
            </div>

            {{-- ── CONTAINER UTAMA MATRIKS (Borders Dipaksa Hitam & BG Putih) ── --}}
            <div style="flex:1; border:1.5px solid #000000 !important; padding:2px; background-color:#ffffff !important;">

                <table style="width:100%; border-collapse:collapse; text-align:center; table-layout:fixed; font-family:sans-serif; background-color:#ffffff !important;">
                    <tbody>
                        @foreach([5, 4, 3, 2, 1] as $index => $prob)
                            <tr>
                                {{-- Label Vertikal "PROBABILITAS" --}}
                                @if($index === 0)
                                    <td rowspan="5" style="width:32px; border:1px solid #000000 !important; background-color:#ffffff !important; vertical-align:middle; text-align:center; padding:0;">
                                        <div style="writing-mode:vertical-rl; transform:rotate(180deg); font-size:12px; font-weight:800; letter-spacing:0.15em; color:#000000 !important; text-transform:uppercase; white-space:nowrap; margin:0 auto;">
                                            PROBABILITAS
                                        </div>
                                    </td>
                                @endif

                                {{-- Label Sumbu Probabilitas (Kiri) --}}
                                <td style="width:130px; border:1px solid #000000 !important; background-color:#ffffff !important; padding:8px 4px; font-size:11px; font-weight:600; color:#000000 !important; vertical-align:middle;">
                                    <div style="color:#000000 !important;">{{ $probLabels[$prob] }}</div>
                                    <div style="font-weight:700; margin-top:2px; color:#000000 !important;">{{ $prob }}</div>
                                </td>

                                {{-- 5 Kolom Sel Matrix --}}
                                @foreach([1, 2, 3, 4, 5] as $dampak)
                                    @php
                                        $nilai = $getCellValue($prob, $dampak);
                                        $level = $getLevelName($nilai);
                                        $style = $cellStyle($nilai);
                                        $codes = $riskByValue[$nilai] ?? [];
                                    @endphp
                                    <td style="{{ $style }} border:1px solid #000000 !important; height:68px; vertical-align:middle; padding:4px; position:relative;">
                                        <div style="font-size:11px; font-weight:600; line-height:1.1; margin-bottom:2px;">
                                            {{ $level }}
                                        </div>
                                        <div style="font-size:16px; font-weight:800; line-height:1;">
                                            {{ $nilai }}
                                        </div>

                                        {{-- Kode Risiko --}}
                                        @if(count($codes))
                                            <div style="margin-top:4px; display:flex; flex-wrap:wrap; gap:2px; justify-content:center;">
                                                @foreach($codes as $code)
                                                    <span style="background:rgba(0,0,0,0.35); color:#ffffff !important; border-radius:2px; padding:0px 3px; font-size:9px; font-weight:700;">
                                                        {{ $code }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach

                        {{-- Baris Label Dampak (Sangat rendah, Rendah, dst) --}}
                        <tr>
                            <td colspan="2" style="border:none !important; background-color:#ffffff !important;"></td>
                            @foreach([1, 2, 3, 4, 5] as $dNum)
                                <td style="border:1px solid #000000 !important; background-color:#ffffff !important; padding:6px 2px; font-size:11px; font-weight:600; color:#000000 !important; vertical-align:middle;">
                                    <div style="color:#000000 !important;">{{ $dampakLabels[$dNum] }}</div>
                                    <div style="font-weight:700; margin-top:1px; color:#000000 !important;">{{ $dNum }}</div>
                                </td>
                            @endforeach
                        </tr>

                        {{-- Baris Sumbu DAMPAK (Footer) --}}
                        <tr>
                            <td colspan="2" style="border:none !important; background-color:#ffffff !important;"></td>
                            <td colspan="5" style="border:1px solid #000000 !important; background-color:#ffffff !important; padding:6px; font-size:12px; font-weight:800; letter-spacing:0.15em; color:#000000 !important; text-transform:uppercase;">
                                DAMPAK
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>

        </div>
    </div>

    {{-- ── KETERANGAN RISIKO (Mengikuti Dark Mode Sesuai Kartu Luar) ── --}}
    @if(!empty($heatmap['risks']))
    <div class="mt-6 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 p-5">
        <h3 class="mb-3 text-sm font-bold text-slate-900 dark:text-white">Keterangan Risiko</h3>
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($heatmap['risks'] as $risk)
                <div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $risk['code'] }}</span>
                            <p class="mt-1 text-sm leading-5 text-slate-600 dark:text-slate-400 line-clamp-2">{{ $risk['risk_name'] }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="inline-flex rounded-full bg-indigo-50 dark:bg-indigo-900/40 px-3 py-1 text-xs font-bold text-indigo-700 dark:text-indigo-300">
                                Nilai {{ $risk['value'] }}
                            </span>
                            <div class="mt-1 text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $risk['level'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>