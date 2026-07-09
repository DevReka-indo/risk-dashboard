<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Risk Monitoring Dashboard
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Monitoring risiko internal dan external perusahaan secara terpusat.
        </p>
    </x-slot>

    <div class="space-y-6">

        {{-- Stat Cards --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-5">

            {{-- Total Risk --}}
            <a
                href="{{ route('top-risk.index') }}"
                class="block rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-slate-500">Total Risk</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">
                    {{ $stats['total_risks'] ?? 0 }}
                </h2>
            </a>

            {{-- Total Users --}}
            <a
                href="{{ route('users.index') }}"
                class="block rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-blue-600">Total Users</p>
                <h2 class="mt-2 text-3xl font-bold text-blue-700">
                    {{ $stats['total_users'] ?? 0 }}
                </h2>
            </a>

            {{-- High Risk --}}
            <a
                href="{{ route('department-risk.index', ['level_id' => $highLevelId ?? null]) }}"
                class="block rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-red-600">High Risk</p>
                <h2 class="mt-2 text-3xl font-bold text-red-700">
                    {{ $stats['high_risks'] ?? 0 }}
                </h2>
            </a>

            {{-- Aktif --}}
            <a
                href="{{ route('department-risk.index', ['status' => '1']) }}"
                class="block rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-emerald-600">Aktif</p>
                <h2 class="mt-2 text-3xl font-bold text-emerald-700">
                    {{ $stats['active_risks'] ?? 0 }}
                </h2>
            </a>

            {{-- Non-Aktif --}}
            <a
                href="{{ route('department-risk.index', ['status' => '0']) }}"
                class="block rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-amber-600">Non-Aktif</p>
                <h2 class="mt-2 text-3xl font-bold text-amber-700">
                    {{ $stats['inactive_risks'] ?? 0 }}
                </h2>
            </a>
        </div>

        {{-- Shortcut Risk Module --}}
        <div class="grid gap-6 lg:grid-cols-3">
            <a
                href="{{ route('top-risk.index') }}"
                class="rounded-2xl border border-indigo-200 bg-indigo-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm font-semibold text-indigo-600">Top Risk</p>
                <h3 class="mt-2 text-xl font-bold text-indigo-900">
                    Monitoring Top Risk
                </h3>
                <p class="mt-2 text-sm leading-6 text-indigo-700">
                    Lihat daftar Top Risk dan monitoring bulanan.
                </p>
            </a>

            <a
                href="{{ route('department-risk.index') }}"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm font-semibold text-slate-500">Department Risk</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">
                    Risiko Departemen
                </h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Kelola risk register berdasarkan departemen.
                </p>
            </a>

            <a
                href="{{ route('smap-risk.index') }}"
                class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm font-semibold text-slate-500">SMAP Risk</p>
                <h3 class="mt-2 text-xl font-bold text-slate-900">
                    Risiko SMAP
                </h3>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Kelola daftar risiko terkait SMAP.
                </p>
            </a>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">

            {{-- Risk by Category --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">
                            Risk by Category
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Rekap jumlah risiko berdasarkan kategori.
                        </p>
                    </div>

                    <a
                        href="{{ route('kategori-risiko.index') }}"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Kelola Kategori
                    </a>
                </div>

                <div class="space-y-3">
                    @forelse ($riskCategories as $category)
                        <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                            <span class="font-medium text-slate-700">
                                {{ $category['name'] }}
                            </span>

                            <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-700">
                                {{ $category['total'] }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">
                            Belum ada data kategori.
                        </p>
                    @endforelse
                </div>
            </div>

            {{-- Users by Role --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-900">
                            Users by Role
                        </h2>
                        <p class="mt-1 text-sm text-slate-500">
                            Distribusi user berdasarkan role aplikasi.
                        </p>
                    </div>

                    <a
                        href="{{ route('roles.index') }}"
                        class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                        Kelola Role
                    </a>
                </div>

                <div class="space-y-4">
                    @forelse ($roleStatistics as $role)
                        @php
                            $max = max($roleStatistics->pluck('total')->toArray()) ?: 1;
                            $percent = ((int) $role['total'] / $max) * 100;
                        @endphp

                        <div>
                            <div class="mb-2 flex justify-between gap-3">
                                <span class="font-medium text-slate-700">
                                    {{ $role['name'] }}
                                </span>

                                <span class="text-sm font-semibold text-indigo-600">
                                    {{ $role['total'] }} User
                                </span>
                            </div>

                            <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                                <div
                                    class="h-3 rounded-full bg-indigo-600"
                                    style="width: {{ $percent }}%">
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">
                            Belum ada data role.
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>