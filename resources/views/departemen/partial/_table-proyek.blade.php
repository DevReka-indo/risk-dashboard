<div class="overflow-x-auto bg-white shadow-sm mt-6">
    <table class="w-full text-center text-sm border-collapse border border-slate-300">
        <thead>
            <tr>
                <th rowspan="2" style="background-color: #2f75b5;" class="rw-1/3 p-3 font-semibold text-white border border-white align-middle shadow-[inset_0_-1px_0_rgba(255,255,255,0.5)]">
                    Level
                </th>
                <th colspan="2" style="background-color: #1f3864;" class="w-2/3 p-3 font-semibold text-white border border-white">
                    Current Risk ({{ $periodDisplay ?? 'Semua Triwulan' }})
                </th>
            </tr>
            <tr>
                <th style="background-color: #2f75b5;" class="w-1/3 p-2 font-semibold text-white border border-white">Proyek</th>
                <th style="background-color: #2f75b5;" class="w-1/3 p-2 font-semibold text-white border border-white">Non-Proyek</th>
            </tr>
        </thead>
        <tbody class="font-bold text-slate-900 text-base">
            @if(isset($matrixTypeData) && count($matrixTypeData) > 0)
                @foreach ($matrixTypeData as $data)
                    <tr>
                        <td class="text-left font-bold text-white px-3 py-2 border border-white"
                            style="
                                @if($data['level_name'] == 'High') background-color: #ff0000;
                                @elseif($data['level_name'] == 'Moderate to High') background-color: #ed7d31;
                                @elseif($data['level_name'] == 'Moderate') background-color: #ffc000;
                                @elseif($data['level_name'] == 'Low to Moderate') background-color: #92d050;
                                @elseif($data['level_name'] == 'Low') background-color: #385723;
                                @else background-color: #6b7280; @endif
                            ">
                            {{ $data['level_name'] }}
                        </td>
                        <td style="background-color: #f2f2f2;" class="px-3 py-2 border border-white shadow-[inset_0_1px_0_rgba(255,255,255,1)]">
                            {{ $data['proyek'] }}
                        </td>
                        <td style="background-color: #f2f2f2;" class="px-3 py-2 border border-white shadow-[inset_0_1px_0_rgba(255,255,255,1)]">
                            {{ $data['non_proyek'] }}
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="3" class="p-4 text-gray-500">Data tidak tersedia.</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
