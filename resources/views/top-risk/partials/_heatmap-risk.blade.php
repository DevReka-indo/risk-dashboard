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

// Styling warna latar & teks sel (WARNA MENCOLOK & KONTRAS)
$cellStyle = function(int $v): string {
    if (in_array($v, [20, 21, 22, 23, 24, 25])) return 'background-color:#ef4444 !important; color:#ffffff !important;'; // Red Vibrant
    if (in_array($v, [16, 17, 18, 19]))          return 'background-color:#f97316 !important; color:#ffffff !important;'; // Orange Vibrant
    if (in_array($v, [12, 13, 14, 15]))          return 'background-color:#facc15 !important; color:#1e293b !important;'; // Yellow Vibrant
    if (in_array($v, [6, 7, 8, 9, 10, 11]))      return 'background-color:#22c55e !important; color:#ffffff !important;'; // Light Green Vibrant
    return                                         'background-color:#10b981 !important; color:#ffffff !important;'; // Dark Green/Emerald Vibrant
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

{{-- Main Container --}}
<div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm text-slate-800">

    <div class="overflow-x-auto pb-2">
        <div style="min-width:850px; display:flex; gap:24px; align-items:stretch;">

            {{-- ── LEGENDA KIRI (MENCOLOK & SMOOTH) ── --}}
            <div style="width:160px; display:flex; flex-direction:column; justify-content:center; gap:8px; flex-shrink:0;">
                <div style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Tingkat Risiko</div>
                
                <div style="background:#ef4444; color:#ffffff; padding:9px 12px; font-size:11px; font-weight:800; border-radius:10px; box-shadow:0 2px 4px rgba(239, 68, 68, 0.2);">
                    High
                </div>
                <div style="background:#f97316; color:#ffffff; padding:9px 12px; font-size:11px; font-weight:800; border-radius:10px; box-shadow:0 2px 4px rgba(249, 115, 22, 0.2);">
                    Moderate to High
                </div>
                <div style="background:#facc15; color:#1e293b; padding:9px 12px; font-size:11px; font-weight:800; border-radius:10px; box-shadow:0 2px 4px rgba(250, 204, 21, 0.2);">
                    Moderate
                </div>
                <div style="background:#22c55e; color:#ffffff; padding:9px 12px; font-size:11px; font-weight:800; border-radius:10px; box-shadow:0 2px 4px rgba(34, 197, 94, 0.2);">
                    Low to Moderate
                </div>
                <div style="background:#10b981; color:#ffffff; padding:9px 12px; font-size:11px; font-weight:800; border-radius:10px; box-shadow:0 2px 4px rgba(16, 185, 129, 0.2);">
                    Low
                </div>
            </div>

            {{-- ── CONTAINER UTAMA MATRIKS ── --}}
            <div style="flex:1; background:#ffffff; border-radius:16px; border:1px solid #f1f5f9; padding:12px;">

                <table style="width:100%; border-collapse:separate; border-spacing:6px; text-align:center; table-layout:fixed;">
                    <tbody>
                        @foreach([5, 4, 3, 2, 1] as $index => $prob)
                            <tr>
                                {{-- Label Vertikal "PROBABILITAS" --}}
                                @if($index === 0)
                                    <td rowspan="5" style="width:36px; background:#f8fafc; border-radius:12px; vertical-align:middle; text-align:center; padding:0;">
                                        <div style="writing-mode:vertical-rl; transform:rotate(180deg); font-size:11px; font-weight:800; letter-spacing:0.2em; color:#64748b; text-transform:uppercase; white-space:nowrap; margin:0 auto;">
                                            PROBABILITAS
                                        </div>
                                    </td>
                                @endif

                                {{-- Label Sumbu Probabilitas (Kiri) --}}
                                <td style="width:140px; background:#f8fafc; border-radius:12px; padding:10px 8px; font-size:11px; vertical-align:middle; text-align:right; padding-right:12px;">
                                    <div style="color:#334155; font-weight:600; line-height:1.2;">{{ $probLabels[$prob] }}</div>
                                </td>

                                {{-- 5 Kolom Sel Matrix --}}
                                @foreach([1, 2, 3, 4, 5] as $dampak)
                                    @php
                                        $nilai = $getCellValue($prob, $dampak);
                                        $level = $getLevelName($nilai);
                                        $style = $cellStyle($nilai);
                                        $codes = $riskByValue[$nilai] ?? [];
                                    @endphp
                                    <td class="heatmap-cell" style="{{ $style }} height:72px; vertical-align:middle; padding:8px; border-radius:12px; transition:all 0.2s ease; position:relative;">
                                        <div style="font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:0.03em; opacity:0.95; margin-bottom:2px;">
                                            {{ $level }}
                                        </div>
                                        <div style="font-size:20px; font-weight:900; line-height:1;">
                                            {{ $nilai }}
                                        </div>

                                        {{-- Kode Risiko --}}
                                        @if(count($codes))
                                            <div style="margin-top:6px; display:flex; flex-wrap:wrap; gap:3px; justify-content:center;">
                                                @foreach($codes as $code)
                                                    <span style="background:rgba(0,0,0,0.22); color:#ffffff !important; border-radius:6px; padding:2px 6px; font-size:9px; font-weight:800; backdrop-filter:blur(2px);">
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
                            <td colspan="2" style="background:transparent;"></td>
                            @foreach([1, 2, 3, 4, 5] as $dNum)
                                <td style="background:#f8fafc; border-radius:12px; padding:8px 4px; font-size:11px; vertical-align:middle;">
                                    <div style="color:#334155; font-weight:600; line-height:1.2;">{{ $dampakLabels[$dNum] }}</div>
                                </td>
                            @endforeach
                        </tr>

                        {{-- Baris Sumbu DAMPAK (Footer) --}}
                        <tr>
                            <td colspan="2" style="background:transparent;"></td>
                            <td colspan="5" style="background:#f8fafc; border-radius:12px; padding:8px; font-size:11px; font-weight:800; letter-spacing:0.2em; color:#64748b; text-transform:uppercase;">
                                DAMPAK
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>

        </div>
    </div>

    {{-- ── KETERANGAN RISIKO ── --}}
    @if(!empty($heatmap['risks']))
    <div class="mt-6">
        <h3 style="font-size:11px; font-weight:800; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:12px;">Keterangan Risiko</h3>
        <div class="grid gap-3 sm:grid-cols-2">
            @foreach($heatmap['risks'] as $risk)
                <div class="rounded-xl border border-slate-200/60 bg-white p-3.5 shadow-sm transition-all hover:border-slate-300">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <span class="inline-block rounded-md bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-800">{{ $risk['code'] }}</span>
                            <p class="mt-1.5 text-sm font-medium leading-snug text-slate-700 line-clamp-2">{{ $risk['risk_name'] }}</p>
                        </div>
                        <div class="shrink-0 text-right">
                            <span class="inline-flex rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-bold text-indigo-600">
                                Nilai {{ $risk['value'] }}
                            </span>
                            <div class="mt-1 text-[11px] font-semibold text-slate-400">{{ $risk['level'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- CSS UNTUK HOVER & FIX DARK MODE TEKS --}}
<style>
    .heatmap-cell:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        z-index: 10;
    }
    .dark [style*="color:#334155"],
    .dark [style*="color: #334155"],
    .dark [style*="color:#64748b"],
    .dark [style*="color: #64748b"],
    .dark .text-slate-700,
    .dark .text-slate-800 {
        color: #ffffff !important;
    }
    .dark [style*="background:#f8fafc"],
    .dark [style*="background: #f8fafc"] {
        background-color: rgba(255, 255, 255, 0.08) !important;
    }
</style>