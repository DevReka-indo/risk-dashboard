<div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h3 class="text-sm font-bold text-slate-900">
                Daftar Risiko SMAP
            </h3>
            <p class="text-xs text-slate-500">
                {{ $period['label'] ?? 'Periode Saat Ini' }}
            </p>
        </div>
        <span class="text-xs text-slate-400">
            Total: {{ count($items) }}
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-100">
            <thead>
                <tr>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-slate-500">NO</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500">UNIT KERJA</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500">RISK EVENT</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500">KATEGORI</th>
                    <th class="px-3 py-2 text-left text-xs font-semibold text-slate-500">LEVEL</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-slate-500">VALUE</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-slate-500">INHERENT</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-slate-500">TREND</th>
                    <th class="px-3 py-2 text-center text-xs font-semibold text-slate-500">STATUS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($items as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-3 py-2 text-center text-sm text-slate-600">{{ $item['number'] ?? $loop->iteration }}</td>
                        <td class="px-3 py-2 text-sm text-slate-600">{{ $item['unit'] ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm font-medium text-slate-900">{{ $item['risk_name'] ?? '-' }}</td>
                        <td class="px-3 py-2 text-sm text-slate-600">{{ $item['kategori'] ?? '-' }}</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $item['level_color'] ?? 'bg-slate-100 text-slate-700' }}">
                                {{ $item['level'] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center text-sm font-semibold text-slate-700">{{ $item['value'] ?? '-' }}</td>
                        <td class="px-3 py-2 text-center text-sm font-semibold text-slate-700">{{ $item['inherent'] ?? '-' }}</td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold {{ $item['trend_color'] ?? 'text-slate-600' }}">
                                {{ $item['trend_icon'] ?? '' }} {{ $item['trend'] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-center">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold {{ $item['status'] ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $item['status'] ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-3 py-8 text-center text-sm text-slate-400">
                            Belum ada data risiko.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>