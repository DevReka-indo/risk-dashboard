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

<body class="bg-white font-sans text-slate-900 antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center bg-white px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 text-center">
            <a href="{{ url('/') }}" class="inline-flex items-center justify-center">
                <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-indigo-600 text-white shadow-lg shadow-indigo-500/25">
                    {{--
                        Jika logo aplikasi sudah tersedia, uncomment kode img di bawah ini,
                        lalu ganti path asset sesuai lokasi logo.

                        Contoh:
                        <img
                            src="{{ asset('assets/img/logo-risk-dashboard.png') }}"
                            alt="{{ config('app.name') }}"
                            class="h-14 w-14 object-contain"
                        >

                        Jika pakai logo, hapus SVG icon fallback di bawah.
                    --}}

                    <svg class="h-10 w-10" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" />
                    </svg>
                </div>
            </a>

            <h1 class="mt-5 text-2xl font-bold tracking-tight text-slate-900">
                Risk Dashboard
            </h1>

            <p class="mt-2 text-sm text-slate-500">
                Enterprise Risk Monitoring System
            </p>
        </div>

        <div class="w-full max-w-md overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-950/10 sm:p-8">
            {{ $slot }}
        </div>

        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} PT Rekaindo Global Jasa. All rights reserved.
        </p>
    </div>
</body>
</html>
