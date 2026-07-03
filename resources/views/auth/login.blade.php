<x-guest-layout>
    <div class="text-center">
        <h2 class="text-xl font-bold text-slate-900">
            Masuk ke Aplikasi
        </h2>

        <p class="mt-2 text-sm leading-6 text-slate-500">
            Gunakan akun internal perusahaan untuk mengakses Risk Dashboard.
        </p>
    </div>

    <x-auth-session-status class="mt-6" :status="session('status')" />

    @if (session('error'))
        <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    {{--
        ========================================================================
        LOGIN MANUAL DEVELOPER
        ========================================================================

        Saat tahap development, form login manual ini ditampilkan agar bisa login
        menggunakan email atau NIP/employee_id.

        Kalau nanti production dan ingin login hanya SSO:
        comment mulai dari:
            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        sampai:
            </form>

        Setelah itu tombol SSO di bawah tetap aktif.
    --}}
    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
        @csrf

        <div>
            <x-input-label for="email" value="Email atau NIP" />

            <x-text-input
                id="email"
                class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                type="text"
                name="email"
                :value="old('email')"
                placeholder="Masukkan email atau NIP"
                required
                autofocus
                autocomplete="username" />

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input
                id="password"
                class="mt-2 block w-full rounded-2xl border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-slate-300 text-indigo-600 shadow-sm focus:ring-indigo-500">

                <span class="ms-2 text-sm text-slate-600">
                    {{ __('Remember me') }}
                </span>
            </label>

            @if (Route::has('password.request'))
                <a
                    href="{{ route('password.request') }}"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-700">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>

        <button
            type="submit"
            class="inline-flex w-full items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
            Log in
        </button>
    </form>

    <div class="my-6">
        <div class="relative">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-200"></div>
            </div>

            <div class="relative flex justify-center text-sm">
                <span class="bg-white px-3 text-slate-500">
                    atau
                </span>
            </div>
        </div>
    </div>

    <a
        href="{{ route('sso.redirect') }}"
        class="inline-flex w-full items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">

        <span class="flex h-7 w-7 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
            </svg>
        </span>

        Login dengan SSO
    </a>

    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
        <p class="text-center text-xs leading-5 text-slate-500">
            Jika mengalami kendala login, hubungi administrator TI.
        </p>
    </div>
</x-guest-layout>
