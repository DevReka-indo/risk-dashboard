<div x-show="activeTab === 'audit'" style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0">

    <!-- 1. KARTU STATISTIK (Sejajar Horizontal & Dinamis) -->
    <div class="flex gap-5 mb-6 w-full">
        <!-- Card 1: Total -->
        <div class="w-1/3 rounded-xl bg-white p-5 shadow-sm" style="border: 1.5px solid #5a5cfa;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Aktivitas Log</p>
                    <h3 class="mt-1 text-3xl font-bold text-slate-900">{{ number_format($totalLogs ?? 0) }}</h3>
                </div>
                <div class="rounded-lg p-3" style="background-color: #f5f5ff; color: #5a5cfa;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
        </div>

        <!-- Card 2: Hari Ini -->
        <div class="w-1/3 rounded-xl p-5 shadow-sm" style="border: 1px solid #fce7f3; background-color: #fdf2f8;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" style="color: #9d174d;">Aktivitas Hari Ini</p>
                    <h3 class="mt-1 text-3xl font-bold" style="color: #831843;">{{ number_format($todayLogs ?? 0) }}</h3>
                </div>
                <div class="rounded-lg bg-white p-3" style="border: 1px solid #fce7f3; color: #db2777;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <!-- Card 3: User Aktif -->
        <div class="w-1/3 rounded-xl p-5 shadow-sm" style="border: 1px solid #e0f2fe; background-color: #f0f9ff;">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" style="color: #075985;">User Beraktivitas</p>
                    <h3 class="mt-1 text-3xl font-bold" style="color: #0c4a6e;">{{ number_format($activeUsers ?? 0) }} <span class="text-sm font-normal">orang</span></h3>
                </div>
                <div class="rounded-lg bg-white p-3" style="border: 1px solid #e0f2fe; color: #0284c7;">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. ACTION BAR (Filter & Tombol Sejajar) -->
    <div class="flex items-center justify-between mb-6">
        <!-- Form Filter Kiri -->
        <form action="{{ route('settings.index') }}" method="GET" class="flex items-center gap-3">
            <!-- Parameter hidden berfungsi dengan baik -->
            <input type="hidden" name="tab" value="audit">

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..."
                   class="rounded-lg border-slate-300 px-4 py-2.5 text-sm shadow-sm focus:ring-[#5a5cfa] focus:border-[#5a5cfa]" style="width: 260px;">

            <input type="date" name="start_date" value="{{ request('start_date') }}"
                   class="rounded-lg border-slate-300 px-3 py-2.5 text-sm text-slate-600 shadow-sm focus:ring-[#5a5cfa] focus:border-[#5a5cfa]" style="width: 150px;">

            <span class="text-slate-400 font-medium">-</span>

            <input type="date" name="end_date" value="{{ request('end_date') }}"
                   class="rounded-lg border-slate-300 px-3 py-2.5 text-sm text-slate-600 shadow-sm focus:ring-[#5a5cfa] focus:border-[#5a5cfa]" style="width: 150px;">

            <button type="submit" class="rounded-lg px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-opacity" style="background-color: #5a5cfa;">
                Cari
            </button>
        </form>

        <!-- Kumpulan Tombol Aksi Kanan -->
        <div class="flex items-center gap-3">

            <!-- Tombol Hapus Semua History -->
            <form action="{{ route('settings.clear-audit') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua riwayat audit? Tindakan ini tidak dapat dibatalkan.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="flex items-center justify-center rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-medium text-red-600 shadow-sm hover:bg-red-50 hover:border-red-300 transition-colors">
                    <svg class="mr-2 h-4 w-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Hapus Semua
                </button>
            </form>

            <!-- Tombol Unduh Excel -->
            <a href="{{ route('settings.export', request()->query()) }}"
               class="flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition-colors">
                <svg class="mr-2 h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Unduh Excel
            </a>
        </div>
    </div>

    <!-- 3. TABEL UTAMA -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full" style="border-collapse: collapse;">
                <thead style="background-color: #5a5cfa;">
                    <tr>
                        <th scope="col" class="text-left text-xs font-semibold uppercase tracking-wider text-white" style="padding: 16px 16px 16px 24px;">WAKTU</th>
                        <th scope="col" class="text-left text-xs font-semibold uppercase tracking-wider text-white" style="padding: 16px 16px;">PENGGUNA</th>
                        <th scope="col" class="text-left text-xs font-semibold uppercase tracking-wider text-white" style="padding: 16px 16px;">MODUL</th>
                        <th scope="col" class="text-left text-xs font-semibold uppercase tracking-wider text-white" style="padding: 16px 16px;">AKTIVITAS</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @forelse ($logs ?? [] as $log)
                        @php
                            $bg = '#f3f4f6'; $text = '#374151'; $border = '#d1d5db';
                            $modulLower = strtolower($log->modul);
                            if (str_contains($modulLower, 'top risk')) {
                                $bg = '#eff6ff'; $text = '#1e40af'; $border = '#bfdbfe';
                            } elseif (str_contains($modulLower, 'monitoring')) {
                                $bg = '#fdf2f8'; $text = '#9d174d'; $border = '#fbcfe8';
                            } elseif (str_contains($modulLower, 'departemen')) {
                                $bg = '#ecfdf5'; $text = '#065f46'; $border = '#a7f3d0';
                            }
                        @endphp

                        <tr class="hover:bg-slate-50/80 transition-colors" style="border-bottom: 1px solid #e2e8f0;">
                            <td class="whitespace-nowrap text-sm text-slate-900 font-medium" style="padding: 20px 16px 20px 24px;">
                                {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y H:i') }}
                            </td>
                            <td class="whitespace-nowrap text-sm text-slate-700 font-medium" style="padding: 20px 16px;">
                                {{ $log->user->name ?? 'Sistem' }}
                            </td>
                            <td class="whitespace-nowrap text-sm" style="padding: 20px 16px;">
                                <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold" style="background-color: {{ $bg }}; color: {{ $text }}; border: 1px solid {{ $border }};">
                                    {{ $log->modul }}
                                </span>
                            </td>
                            <td class="text-sm text-slate-600" style="padding: 20px 16px; line-height: 1.5;">
                                {!! $log->aktivitas !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-slate-500 py-10">
                                Tidak ada aktivitas log yang ditemukan berdasarkan filter yang digunakan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 flex items-center justify-between" style="background-color: #f8fafc; border-top: 1px solid #e2e8f0;">
            @if(isset($logs) && $logs->count() > 0)
                <div class="w-full text-sm">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
