@props([
    'header' => null,
])

<header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white/95 px-4 backdrop-blur-xl sm:px-6 lg:px-8">
    <div class="flex items-center gap-3">
        <button
            type="button"
            class="rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 lg:hidden"
            @click="sidebarOpen = true">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </button>

        <div>
            @if ($header)
                {{ $header }}
            @else
                <h1 class="text-lg font-bold text-slate-900">
                    Dashboard
                </h1>
                <p class="hidden text-sm text-slate-500 sm:block">
                    Enterprise Risk Monitoring System
                </p>
            @endif
        </div>
    </div>

    <div class="flex items-center gap-3">
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button
                type="button"
                class="flex items-center gap-3 rounded-2xl px-2 py-1.5 transition hover:bg-slate-100"
                @click="userMenuOpen = !userMenuOpen"
                @click.outside="userMenuOpen = false"
                @keydown.escape.window="userMenuOpen = false">

                <div class="hidden text-right sm:block">
                    <div class="text-sm font-semibold text-slate-900">
                        {{ auth()->user()->name }}
                    </div>
                    <div class="max-w-40 truncate text-xs text-slate-500">
                        {{ auth()->user()->email }}
                    </div>
                </div>

                {{--
                    Nanti kalau field foto profile sudah ditambahkan ke tabel users,
                    dummy avatar ini bisa diganti menjadi gambar dari asset.

                    Contoh:

                    <img
                        src="{{ asset('assets/img/users/default-avatar.png') }}"
                        alt="{{ auth()->user()->name }}"
                        class="h-9 w-9 rounded-2xl object-cover ring-2 ring-slate-100">

                    Untuk sekarang masih memakai dummy avatar berupa inisial user.
                --}}
                <div class="flex h-9 w-9 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-bold uppercase text-white shadow-lg shadow-indigo-500/20">
                    {{ str(auth()->user()->name)->substr(0, 1) }}
                </div>

                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div
                x-show="userMenuOpen"
                x-transition.origin.top.right
                x-cloak
                class="absolute right-0 mt-3 w-64 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-950/10">

                <div class="border-b border-slate-200 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-600 text-sm font-bold uppercase text-white">
                            {{ str(auth()->user()->name)->substr(0, 1) }}
                        </div>

                        <div class="min-w-0">
                            <div class="truncate text-sm font-semibold text-slate-900">
                                {{ auth()->user()->name }}
                            </div>
                            <div class="truncate text-xs text-slate-500">
                                {{ auth()->user()->email }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-2">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-100">
                        <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975M15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                        Profile
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <button
                            type="submit"
                            class="flex w-full items-center gap-3 rounded-2xl px-3 py-2.5 text-left text-sm font-medium text-rose-600 hover:bg-rose-50">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
