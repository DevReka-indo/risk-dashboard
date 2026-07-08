<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Risk Monitoring Dashboard
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Monitoring risiko internal dan external perusahaan secara terpusat
        </p>
    </x-slot>

    <div class="space-y-6">

        {{-- Stat Cards --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-5">

            {{-- Total Risk --}}
            <a href="{{ route('risk-register.index') }}"
               class="block rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-slate-500">Total Risk</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total_risks'] }}</h2>
            </a>

            {{-- Total Users --}}
            <a href="{{ route('users.index') }}"
               class="block rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-blue-600">Total Users</p>
                <h2 class="mt-2 text-3xl font-bold text-blue-700">{{ $stats['total_users'] }}</h2>
            </a>

            {{-- High Risk --}}
            <a href="{{ route('risk-register.index', ['level_id' => $highLevelId]) }}"
               class="block rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-red-600">High Risk</p>
                <h2 class="mt-2 text-3xl font-bold text-red-700">{{ $stats['high_risks'] }}</h2>
            </a>

            {{-- Aktif --}}
            <a href="{{ route('risk-register.index', ['status' => '1']) }}"
               class="block rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-emerald-600">Aktif</p>
                <h2 class="mt-2 text-3xl font-bold text-emerald-700">{{ $stats['active_risks'] }}</h2>
            </a>

            {{-- Non-Aktif --}}
            <a href="{{ route('risk-register.index', ['status' => '0']) }}"
               class="block rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-amber-600">Non-Aktif</p>
                <h2 class="mt-2 text-3xl font-bold text-amber-700">{{ $stats['inactive_risks'] }}</h2>
            </a>

        </div>

        {{-- Risk by Category --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Risk by Category</h2>
<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Risk Monitoring Dashboard
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Monitoring risiko internal dan external perusahaan secara terpusat
        </p>
    </x-slot>

    <div class="space-y-6">

        {{-- Stat Cards --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-5">

            {{-- Total Risk (Sudah Diperbaiki) --}}
            <a href="{{ route('department-risk.index') }}"
               class="block rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-slate-500">Total Risk</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total_risks'] }}</h2>
            </a>

            {{-- Total Users --}}
            <a href="{{ route('users.index') }}"
               class="block rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-blue-600">Total Users</p>
                <h2 class="mt-2 text-3xl font-bold text-blue-700">{{ $stats['total_users'] }}</h2>
            </a>

            {{-- High Risk (Sudah Diperbaiki) --}}
            <a href="{{ route('department-risk.index', ['level_id' => $highLevelId]) }}"
               class="block rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-red-600">High Risk</p>
                <h2 class="mt-2 text-3xl font-bold text-red-700">{{ $stats['high_risks'] }}</h2>
            </a>

            {{-- Aktif (Sudah Diperbaiki) --}}
            <a href="{{ route('department-risk.index', ['status' => '1']) }}"
               class="block rounded-2xl border border-emerald-200 bg-emerald-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-emerald-600">Aktif</p>
                <h2 class="mt-2 text-3xl font-bold text-emerald-700">{{ $stats['active_risks'] }}</h2>
            </a>

            {{-- Non-Aktif (Sudah Diperbaiki) --}}
            <a href="{{ route('department-risk.index', ['status' => '0']) }}"
               class="block rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm transition duration-200 hover:-translate-y-1 hover:shadow-lg">
                <p class="text-sm text-amber-600">Non-Aktif</p>
                <h2 class="mt-2 text-3xl font-bold text-amber-700">{{ $stats['inactive_risks'] }}</h2>
            </a>

        </div>

        {{-- Risk by Category --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Risk by Category</h2>

            <div class="space-y-3">
                @forelse ($riskCategories as $category)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <span class="font-medium text-slate-700">{{ $category['name'] }}</span>
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-700">
                            {{ $category['total'] }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada data kategori.</p>
                @endforelse
            </div>
        </div>

        {{-- Users by Role --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Users by Role</h2>

            <div class="space-y-4">
                @foreach ($roleStatistics as $role)
                    @php
                        $max = max($roleStatistics->pluck('total')->toArray()) ?: 1;
                        $percent = ($role['total'] / $max) * 100;
                    @endphp
                    <div>
                        <div class="mb-2 flex justify-between">
                            <span class="font-medium text-slate-700">{{ $role['name'] }}</span>
                            <span class="text-sm font-semibold text-indigo-600">{{ $role['total'] }} User</span>
                        </div>
                        <div class="h-3 rounded-full bg-slate-200">
                            <div class="h-3 rounded-full bg-indigo-600" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-admin-layout>
            <div class="space-y-3">
                @forelse ($riskCategories as $category)
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <span class="font-medium text-slate-700">{{ $category['name'] }}</span>
                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-700">
                            {{ $category['total'] }}
                        </span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada data kategori.</p>
                @endforelse
            </div>
        </div>

        {{-- Users by Role --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-slate-900">Users by Role</h2>

            <div class="space-y-4">
                @foreach ($roleStatistics as $role)
                    @php
                        $max = max($roleStatistics->pluck('total')->toArray()) ?: 1;
                        $percent = ($role['total'] / $max) * 100;
                    @endphp
                    <div>
                        <div class="mb-2 flex justify-between">
                            <span class="font-medium text-slate-700">{{ $role['name'] }}</span>
                            <span class="text-sm font-semibold text-indigo-600">{{ $role['total'] }} User</span>
                        </div>
                        <div class="h-3 rounded-full bg-slate-200">
                            <div class="h-3 rounded-full bg-indigo-600" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</x-admin-layout>
