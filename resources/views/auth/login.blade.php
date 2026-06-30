<x-guest-layout>
    <div class="text-center">
        <h2 class="text-xl font-bold text-slate-900">
            Masuk ke Aplikasi
        </h2>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            Gunakan akun SSO internal perusahaan untuk mengakses Risk Dashboard.
        </p>
    </div>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    @if (session('error'))
        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="mt-6">
        <a
            href="{{ route('sso.redirect') }}"
            class="inline-flex w-full items-center justify-center gap-3 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-indigo-500/25 transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">

            <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-white/15">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </span>

            Login dengan SSO
        </a>
    </div>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
        <p class="text-center text-xs leading-5 text-slate-500">
            Jika mengalami kendala login, hubungi administrator TI.
        </p>
    </div>
</x-guest-layout>
