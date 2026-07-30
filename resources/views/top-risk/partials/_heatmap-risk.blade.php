@php
$probLabels = [
    5 => 'Hampir Pasti Terjadi',
    4 => 'Sangat Mungkin Terjadi',
    3 => 'Bisa Terjadi',
    2 => 'Jarang Terjadi',
    1 => 'Sangat Jarang Terjadi',
];
$dampakLabels = [
    1 => ['label' => 'Sangat rendah', 'num' => 1],
    2 => ['label' => 'Rendah',        'num' => 2],
    3 => ['label' => 'Moderat',       'num' => 3],
    4 => ['label' => 'Tinggi',        'num' => 4],
    5 => ['label' => 'Sangat tinggi', 'num' => 5],
];

$cellStyle = function(int $v): string {
    if ($v >= 20) return 'background:#ef4444;color:#fff;';
    if ($v >= 15) return 'background:#f97316;color:#fff;';
    if ($v >= 10) return 'background:#eab308;color:#fff;';
    if ($v >= 5)  return 'background:#84cc16;color:#fff;';
    return               'background:#22c55e;color:#fff;';
};

$levelName = function(int $v): string {
    if ($v >= 20) return 'High';
    if ($v >= 15) return 'Moderate to High';
    if ($v >= 10) return 'Moderate';
    if ($v >= 5)  return 'Low to Moderate';
    return               'Low';
};

// Kumpulkan risiko per nilai dari $heatmap
$riskByValue = [];
if (!empty($heatmap['risks'])) {
    foreach ($heatmap['risks'] as $r) {
        $v = (int)($r['value'] ?? 0);
        if ($v > 0) $riskByValue[$v][] = $r['code'];
    }
}
// Fallback: ambil dari rows lama jika risks kosong
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

<div class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-6 shadow-sm">

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-base font-bold text-slate-900 dark:text-white">Heatmap Risiko</h2>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Pemetaan risiko berdasarkan Probabilitas × Dampak pada periode terpilih.
        </p>
    </div>

    <div class="overflow-x-auto pb-2">
        <div style="min-width:700px; display:flex; gap:14px; align-items:flex-start;">

            {{-- ── LEGENDA VERTIKAL ── --}}
            <div style="display:flex; flex-direction:column; gap:3px; margin-top:12px; flex-shrink:0;">
                <div style="background:#ef4444; color:#fff; border-radius:4px; padding:7px 10px; font-size:11px; font-weight:700;">High</div>
                <div style="background:#f97316; color:#fff; border-radius:4px; padding:7px 10px; font-size:11px; font-weight:700;">Moderate to High</div>
                <div style="background:#eab308; color:#fff; border-radius:4px; padding:7px 10px; font-size:11px; font-weight:700;">Moderate</div>
                <div style="background:#84cc16; color:#fff; border-radius:4px; padding:7px 10px; font-size:11px; font-weight:700;">Low to Moderate</div>
                <div style="background:#22c55e; color:#fff; border-radius:4px; padding:7px 10px; font-size:11px; font-weight:700;">Low</div>
            </div>

            {{-- ── MATRIKS + LABEL SUMBU ── --}}
            <div style="flex:1;">
                <div style="display:flex; align-items:stretch;">

                    {{-- Label PROBABILITAS vertikal --}}
                    <div style="writing-mode:vertical-rl; transform:rotate(180deg); font-size:11px; font-weight:700; color:#64748b; letter-spacing:0.1em; text-transform:uppercase; padding:0 6px 38px 0; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        PROBABILITAS
                    </div>

                    {{-- Kolom kiri label prob + grid 5×5 --}}
                    <div style="flex:1;">

                        {{-- Baris 1–5 (prob 5 → 1) --}}
                        @foreach ([5,4,3,2,1] as $prob)
                        <div style="display:grid; grid-template-columns:115px repeat(5,1fr); gap:1px; margin-bottom:1px;">

                            {{-- Label probabilitas --}}
                            <div style="background:#f1f5f9; border:1px solid #e2e8f0; border-radius:2px; padding:6px 8px; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
                                <span style="font-size:10px; font-weight:600; color:#64748b; line-height:1.3;">{{ $probLabels[$prob] }}</span>
                                <span style="font-size:12px; font-weight:800; color:#1e293b; margin-top:2px;">{{ $prob }}</span>
                            </div>

                            {{-- 5 sel dampak --}}
                            @foreach ([1,2,3,4,5] as $dampak)
                                @php
                                    $nilai  = $prob * $dampak;
                                    $style  = $cellStyle($nilai);
                                    $level  = $levelName($nilai);
                                    $codes  = $riskByValue[$nilai] ?? [];
                                @endphp
                                <div style="{{ $style }} border-radius:6px; padding:8px 5px; min-height:px; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; text-align:center; box-shadow:inset 0 0 0 1px rgba(0,0,0,0.08);">
                                    <div style="font-size:9px; font-weight:600; line-height:1.2; opacity:0.88;">{{ $level }}</div>
                                    <div style="font-size:17px; font-weight:900; line-height:1;">{{ $nilai }}</div>
                                    @if(count($codes))
                                        <div style="display:flex; flex-wrap:wrap; gap:2px; justify-content:center;">
                                            @foreach($codes as $code)
                                                <span style="background:rgba(255,255,255,0.28); border-radius:20px; padding:1px 5px; font-size:9px; font-weight:700;">{{ $code }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        @endforeach

                        {{-- Label bawah: nama dampak --}}
                        <div style="display:grid; grid-template-columns:115px repeat(5,1fr); gap:3px; margin-top:4px;">
                            <div></div>
                            @foreach($dampakLabels as $info)
                                <div style="text-align:center; padding:5px 4px;">
                                    <div style="font-size:10px; color:#64748b; font-weight:500; line-height:1.3;">{{ $info['label'] }}</div>
                                    <div style="font-size:11px; font-weight:800; color:#1e293b;">{{ $info['num'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Label sumbu DAMPAK --}}
                        <div style="display:grid; grid-template-columns:115px 1fr; margin-top:2px;">
                            <div></div>
                            <div style="text-align:center; font-size:11px; font-weight:700; color:#64748b; letter-spacing:0.1em; text-transform:uppercase; padding:4px 0; border-top:2px solid #e2e8f0;">
                                DAMPAK
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── KETERANGAN RISIKO ── --}}
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
