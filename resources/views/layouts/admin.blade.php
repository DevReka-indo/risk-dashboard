<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ sidebarOpen: false }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? config('app.name', 'Risk Dashboard') }}</title>

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="font-sans text-slate-900 dark:text-slate-100 antialiased bg-slate-50 dark:bg-slate-950 transition-colors">

<div class="min-h-screen bg-slate-100 dark:bg-slate-950 text-slate-900 dark:text-slate-100 transition-colors">


    {{-- Overlay Mobile --}}
    <div
        x-show="sidebarOpen"
        x-transition.opacity
        x-cloak
        class="fixed inset-0 z-40 bg-black/30 lg:hidden"
        @click="sidebarOpen = false">
    </div>

    {{-- Sidebar --}}
    <x-admin.sidebar />

    {{-- Content --}}
    <div class="min-h-screen lg:pl-72">

        {{-- Topbar --}}
        <x-admin.topbar :header="$header ?? null" />

        <main class="px-6 py-6">

            {{-- Success --}}
            @if(session('success'))
                <div
                    x-data="{show:true}"
                    x-show="show"
                    x-init="setTimeout(()=>show=false,4000)"
                    x-transition
                    class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-5 py-3 text-emerald-700 shadow-sm">

                    {{ session('success') }}

                </div>
            @endif

            {{-- Error --}}
            @if(session('error'))
                <div
                    x-data="{show:true}"
                    x-show="show"
                    x-init="setTimeout(()=>show=false,4000)"
                    x-transition
                    class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-5 py-3 text-rose-700 shadow-sm">

                    {{ session('error') }}

                </div>
            @endif

            {{-- Content --}}
            {{ $slot }}

        </main>

    </div>

</div>

</body>

</html>