<div class="space-y-6">

    <!-- 1. STATISTIC CARDS -->
    <div class="grid gap-6 md:grid-cols-3">

        <!-- Card 1: Total Aktivitas Log -->
        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Aktivitas Log</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">
                    {{ number_format($totalLogs ?? 574) }}
                </h2>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </div>
        </div>

        <!-- Card 2: Aktivitas Hari Ini -->
        <div class="flex items-center justify-between rounded-xl border border-blue-100 bg-blue-50/50 p-5 shadow-sm">
            <div>
                <p class="text-xs font-medium text-blue-600">Aktivitas Hari Ini</p>
                <h2 class="mt-1 text-2xl font-bold text-blue-700">
                    {{ number_format($logsToday ?? 0) }}
                </h2>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-blue-600 shadow-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Card 3: User Beraktivitas -->
        <div class="flex items-center justify-between rounded-xl border border-emerald-100 bg-emerald-50/50 p-5 shadow-sm">
            <div>
                <p class="text-xs font-medium text-emerald-600">User Beraktivitas</p>
                <div class="mt-1 flex items-baseline gap-1">
                    <h2 class="text-2xl font-bold text-emerald-700">
                        {{ number_format($activeUsers ?? 4) }}
                    </h2>
                    <span class="text-xs font-medium text-emerald-600">orang</span>
                </div>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

    </div>

    <!-- 2. FILTER & ACTION BAR (Satu Baris Rapi) -->
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

        <!-- Form Filter Kiri: Sejajar Horizontal -->
        <form action="{{ request()->url() }}" method="GET" class="flex flex-wrap items-center gap-2">
            <input type="hidden" name="tab" value="audit">

            <!-- Search Field -->
            <div class="relative min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aktivitas..."
                    class="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2 pr-9 text-xs text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Start Date -->
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:outline-none shadow-sm">

            <span class="text-xs text-slate-400">-</span>

            <!-- End Date -->
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700 focus:border-indigo-500 focus:outline-none shadow-sm">

            <!-- Tombol Cari -->
            <button type="submit"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-medium text-white hover:bg-indigo-700 transition shadow-sm">
                Cari
            </button>
        </form>

        <!-- Tombol Kanan: Hapus Semua & Unduh Excel -->
        <div class="flex items-center gap-2">

            <!-- Hapus Semua -->
            <form action="{{ route('settings.clear-audit') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua log aktivitas?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-white px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 transition shadow-sm">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Semua
                </button>
            </form>

            <!-- Unduh Excel -->
            <!-- request()->query() digunakan agar filter tanggal/search ikut dikirim ke Excel -->
            <a href="{{ route('settings.export', request()->query()) }}"
                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Unduh Excel
            </a>

        </div>

    </div>

    <!-- 3. DATA TABLE (Warna Indigo Khas Aplikasi Anda) -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-indigo-600 text-white font-semibold uppercase tracking-wider">
                        <th class="px-6 py-3.5">WAKTU</th>
                        <th class="px-6 py-3.5">PENGGUNA</th>
                        <th class="px-6 py-3.5">MODUL</th>
                        <th class="px-6 py-3.5">AKTIVITAS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-600">

                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                            24 Jul 2026 08:31
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                            Admin
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center rounded-md bg-purple-50 px-2.5 py-1 text-[11px] font-semibold text-purple-700 border border-purple-100">
                                Risk SMAP
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            Menghapus data Risk SMAP (ID: 236).
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                            24 Jul 2026 08:30
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                            Admin
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center rounded-md bg-purple-50 px-2.5 py-1 text-[11px] font-semibold text-purple-700 border border-purple-100">
                                Risk SMAP
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            Menambahkan Risk SMAP baru (ID: 236).
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-slate-500">
                            24 Jul 2026 02:54
                        </td>
                        <td class="px-6 py-4 font-bold text-slate-900 whitespace-nowrap">
                            Tester Superadmin
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center rounded-md bg-purple-50 px-2.5 py-1 text-[11px] font-semibold text-purple-700 border border-purple-100">
                                Risk SMAP
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            Memperbarui data Risk SMAP (ID: 233).
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>

</div>
