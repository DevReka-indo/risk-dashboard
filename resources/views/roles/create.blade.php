<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Tambah Role
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Buat role baru dan tentukan permission yang bisa digunakan.
        </p>
    </x-slot>

    <form method="POST" action="{{ route('roles.store') }}" class="space-y-6">
        @csrf

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="max-w-3xl">
                <label for="name" class="block text-sm font-semibold text-slate-700">
                    Nama Role
                </label>

                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Contoh: manager, auditor, risk-owner"
                    autofocus
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                @error('name')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror

                <p class="mt-2 text-sm text-slate-500">
                    Nama role akan disimpan dalam huruf kecil. Khusus role <span class="font-semibold">superadmin</span> akan otomatis mendapatkan permission wildcard.
                </p>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
                <h2 class="text-base font-bold text-slate-900">
                    Daftar Permission
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Centang permission yang boleh digunakan oleh role ini.
                </p>
            </div>

            @error('permissions')
                <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
            @enderror

            @include('roles._permission-checkboxes', [
                'permissions' => $permissions,
                'selectedPermissions' => old('permissions', []),
            ])
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('roles.index') }}"
               class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Batal
            </a>

            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Simpan Role
            </button>
        </div>
    </form>
</x-admin-layout>
