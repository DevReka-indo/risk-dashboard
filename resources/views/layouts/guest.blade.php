<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Risk Dashboard') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-slate-900 dark:text-slate-100 bg-slate-900">

    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-8 bg-gradient-to-b from-[#8FAEE8] via-[#BED3F3] to-[#DDE8F7] dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">

        {{-- Logo dan Brand --}}
        <div class="mb-8 text-center">

            <a href="{{ url('/') }}" class="inline-flex items-center gap-4">

                {{-- Frameless Glossy Logo Container --}}
                <div class="relative flex items-center justify-center">
                    {{-- Light Mode Logo Icon (Glossy Dark Chrome Emblem) --}}
                    <img src="{{ asset('images/Group 6924.png') }}"
                         alt="Manrisk Logo"
                         class="h-12 w-auto object-contain filter brightness-0 drop-shadow-[0_4px_8px_rgba(0,0,0,0.3)] dark:hidden">

                    {{-- Dark Mode Logo Icon (Glossy Glowing White Emblem) --}}
                    <img src="{{ asset('images/Group 6924.png') }}"
                         alt="Manrisk Logo"
                         class="h-12 w-auto object-contain hidden dark:block filter brightness-0 invert drop-shadow-[0_0_15px_rgba(255,255,255,0.8)]">
                </div>

                {{-- Brand Text --}}
                <div class="text-left">
                    <div class="text-2xl font-bold tracking-wide text-white drop-shadow-md">
                        Manrisk
                    </div>
                    <div class="text-xs font-medium text-white/80 drop-shadow-sm">
                        Monitoring Risiko Perusahaan
                    </div>
                </div>

            </a>

        </div>

        {{-- CARD LOGIN --}}
        <div
            class="w-full max-w-md
            rounded-[30px]
            bg-white/95 backdrop-blur-sm
            border border-white/50
            p-8
            shadow-[0_35px_90px_rgba(15,23,42,0.28)]
            ring-1 ring-white/30">

            {{ $slot }}

        </div>

        {{-- Footer --}}
        <p class="mt-6 text-center text-sm text-white/80 drop-shadow-sm">
            © {{ date('Y') }} PT Rekaindo Global Jasa. All rights reserved.
        </p>

    </div>

</body>

</html>