<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Tambah User
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Buat akun baru dan tentukan role aksesnya.
        </p>
    </x-slot>

    <form method="POST" action="{{ route('users.store') }}" class="space-y-6">
        @csrf

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-6 lg:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">
                        Nama User
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        autofocus
                        class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('name')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">
                        Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('email')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Password
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('password')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">
                        Konfirmasi Password
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="mt-2 w-full rounded-lg border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Role Akses
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Pilih role yang akan diberikan ke user ini.
                </p>
            </div>

            @error('roles')
                <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
            @enderror

            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($roles as $role)
                    @php
                        $isChecked = in_array($role->name, old('roles', []), true);
                    @endphp

                    <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40">
                        <input
                            type="checkbox"
                            name="roles[]"
                            value="{{ $role->name }}"
                            @checked($isChecked)
                            class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">

                        <span>
                            <span class="block text-sm font-semibold text-slate-900">
                                {{ $role->name }}
                            </span>
                            <span class="mt-1 block text-xs text-slate-500">
                                Guard: {{ $role->guard_name }}
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Simpan User
            </button>
        </div>
    </form>
</x-admin-layout>
