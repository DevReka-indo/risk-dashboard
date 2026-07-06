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

        {{-- Cards --}}
        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-5">

            {{-- Total Risk --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Total Risk</p>

                <h2 class="mt-2 text-3xl font-bold text-slate-900">
                    {{ $stats['total_risks'] }}
                </h2>
            </div>

            {{-- Total Users --}}
<div class="rounded-2xl border border-blue-200 bg-blue-50 p-6 shadow-sm">
    <p class="text-sm text-blue-600">Total Users</p>

    <h2 class="mt-2 text-3xl font-bold text-blue-700">
        {{ $stats['total_users'] }}
    </h2>
</div>

            {{-- High Risk --}}
            <div class="rounded-2xl border border-red-200 bg-red-50 p-6 shadow-sm">
                <p class="text-sm text-red-600">High Risk</p>

                <h2 class="mt-2 text-3xl font-bold text-red-700">
                    {{ $stats['high_risks'] }}
                </h2>
            </div>

            {{-- Critical Risk --}}
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6 shadow-sm">
                <p class="text-sm text-rose-600">Critical Risk</p>

                <h2 class="mt-2 text-3xl font-bold text-rose-700">
                    {{ $stats['critical_risks'] }}
                </h2>
            </div>

            {{-- Open Risk --}}
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-6 shadow-sm">
                <p class="text-sm text-amber-600">Open Risk</p>

                <h2 class="mt-2 text-3xl font-bold text-amber-700">
                    {{ $stats['open_risks'] }}
                </h2>
            </div>

        </div>

        {{-- Risk Category --}}
        
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <h2 class="mb-4 text-lg font-semibold text-slate-900">
                Risk by Category
            </h2>

            <div class="space-y-3">

                @foreach($riskCategories as $category)

                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">

                        <span class="font-medium text-slate-700">
                            {{ $category['name'] }}
                        </span>

                        <span class="rounded-full bg-indigo-100 px-3 py-1 text-sm font-semibold text-indigo-700">
                            {{ $category['total'] }}
                        </span>

                    </div>

                @endforeach

            </div>

        </div>

    </div>
    {{-- Role Statistics --}}
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

    <h2 class="mb-4 text-lg font-semibold text-slate-900">
        Users by Role
    </h2>

    <div class="space-y-4">

        @foreach($roleStatistics as $role)

            @php
                $max = max($roleStatistics->pluck('total')->toArray()) ?: 1;
                $percent = ($role['total'] / $max) * 100;
            @endphp

            <div>

                <div class="mb-2 flex justify-between">

                    <span class="font-medium text-slate-700">
                        {{ $role['name'] }}
                    </span>

                    <span class="text-sm font-semibold text-indigo-600">
                        {{ $role['total'] }} User
                    </span>

                </div>

                <div class="h-3 rounded-full bg-slate-200">

                    <div
                        class="h-3 rounded-full bg-indigo-600"
                        style="width: {{ $percent }}%">
                    </div>

                </div>

            </div>

        @endforeach

    </div>

</div>


</x-admin-layout>