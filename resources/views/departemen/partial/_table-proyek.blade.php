<div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
    <!-- text-sm diletakkan di table agar ukurannya selaras semua -->
    <table class="w-full text-center text-sm border-collapse">
        
        <!-- Header: Warna teks dilembutkan menjadi text-slate-600 -->
        <thead class="bg-indigo-600 text-white border-b border-indigo-700">
            <tr>
                <th rowspan="2" class="w-1/3 px-4 py-3 font-semibold border-r border-slate-200 align-middle">
                    Level
                </th>
                <th colspan="2" class="w-2/3 px-4 py-3 font-semibold border-b border-slate-200">
                    Current Risk ({{ $periodDisplay ?? 'Semua Triwulan' }})
                </th>
            </tr>
            <tr>
                <!-- Sub-header dilembutkan warnanya -->
                <th class="w-1/3 px-4 py-2 font-medium border-r border-slate-200">Proyek</th>
                <th class="w-1/3 px-4 py-2 font-medium">Non-Proyek</th>
            </tr>
        </thead>
        
        <!-- Body: font-bold dihapus, diganti font-medium agar tidak terlalu berat -->
        <tbody class="text-slate-700">
            @if(isset($matrixTypeData) && count($matrixTypeData) > 0)
                @foreach ($matrixTypeData as $data)
                    <tr class="hover:bg-slate-50/70 transition-colors">
                        
                        <!-- 
                            Warna dilembutkan menggunakan palet pastel khas Tailwind 
                            (Background muda, Teks gelap)
                        -->
                        <td class="text-left font-medium px-4 py-2.5 border-b border-r border-slate-200"
                            style="
                                @if($data['level_name'] == 'High') background-color: #fee2e2; color: #991b1b;
                                @elseif($data['level_name'] == 'Moderate to High') background-color: #ffedd5; color: #9a3412;
                                @elseif($data['level_name'] == 'Moderate') background-color: #fef3c7; color: #92400e;
                                @elseif($data['level_name'] == 'Low to Moderate') background-color: #dcfce7; color: #166534;
                                @elseif($data['level_name'] == 'Low') background-color: #d1fae5; color: #065f46;
                                @else background-color: #f1f5f9; color: #334155; @endif
                            ">
                            {{ $data['level_name'] }}
                        </td>
                        
                        <!-- Kolom Angka -->
                        <td class="px-4 py-2.5 border-b border-r border-slate-200 font-medium">
                            {{ $data['proyek'] }}
                        </td>
                        <td class="px-4 py-2.5 border-b border-slate-200 font-medium">
                            {{ $data['non_proyek'] }}
                        </td>
                        
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="p-6 text-center text-slate-400 border-b border-slate-200">
                        Data tidak tersedia.
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>