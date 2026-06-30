<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Edit User
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Perbarui data user, role akses, atau password.
        </p>
    </x-slot>

    <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="grid gap-6 lg:grid-cols-2">
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700">
                        Nama User
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        autofocus
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

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
                        value="{{ old('email', $user->email) }}"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('email')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">
                        Password Baru
                    </label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Kosongkan jika tidak ingin mengubah password"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    @error('password')
                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">
                        Konfirmasi Password Baru
                    </label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        placeholder="Ulangi password baru"
                        class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
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
                        $isChecked = in_array($role->name, old('roles', $selectedRoles), true);
                    @endphp

                    <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-indigo-300 hover:bg-indigo-50/40">
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

                            @if ($role->name === 'superadmin')
                                <span class="mt-1 block text-xs text-indigo-600">
                                    Memiliki akses penuh ke sistem.
                                </span>
                            @else
                                <span class="mt-1 block text-xs text-slate-500">
                                    Guard: {{ $role->guard_name }}
                                </span>
                            @endif
                        </span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('users.index') }}"
               class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-admin-layout>
