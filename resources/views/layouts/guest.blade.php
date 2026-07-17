<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Risk Dashboard') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">

    <div
        class="min-h-screen flex flex-col items-center justify-center px-4 py-8"
        style="
            background: linear-gradient(
                180deg,
                #8FAEE8 0%,
                #A8C2EE 30%,
                #BED3F3 65%,
                #DDE8F7 100%
            );
        ">

        {{-- Logo dan Brand --}}
        <div class="mb-8 text-center">

            <a href="{{ url('/') }}" class="inline-flex items-center gap-4">

                {{-- Logo --}}
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl"
                     style="background: transparent; box-shadow: none;">
                    <img src="{{ asset('images/Group 6924.png') }}"
                         alt="Manrisk Logo"
                         class="h-12 w-auto object-contain"
                         style="filter: brightness(0) invert(1);">
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