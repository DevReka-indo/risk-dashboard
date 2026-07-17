<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Role Management
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Kelola role dan akses permission untuk setiap pengguna.
        </p>
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <form method="GET" action="{{ route('roles.index') }}" class="flex w-full flex-col gap-3 sm:flex-row lg:max-w-xl">
                    <div class="relative flex-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197M15.803 15.803A7.5 7.5 0 1 0 5.197 5.197a7.5 7.5 0 0 0 10.606 10.606Z" />
                            </svg>
                        </div>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari role..."
                            class="w-full rounded-2xl border-slate-200 bg-white py-2.5 pl-11 pr-4 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <!-- Filter button -->
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M5.25 12h13.5M8.25 17.25h7.5" />
                        </svg>

                    </button>

                    @if ($search)
                        <a href="{{ route('roles.index') }}"
                           class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Reset
                        </a>
                    @endif
                </form>

                @can('role.create')
                    <a href="{{ route('roles.create') }}"
                       class="inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Role
                    </a>
                @endcan
            </div>
        </div>

        <div class="overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="bg-indigo-600 text-white">
                            <th class="rounded-tl-[28px] px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide border-r border-slate-300">
                                Role
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide border-r border-slate-300">
                                Guard
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide border-r border-slate-300">
                                Permission
                            </th>
                            <th class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wide border-r border-slate-300">
                                User
                            </th>
                            <th class="rounded-tr-[28px] px-6 py-4 text-center text-sm font-semibold uppercase tracking-wide">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr class="hover:bg-slate-50 transition border-b border-slate-300">
                                <td class="whitespace-nowrap px-6 py-4 border-r border-slate-300">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $role->name === 'superadmin' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-600' }}">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <div class="font-semibold text-slate-900">
                                                {{ $role->name }}
                                            </div>

                                            @if ($role->name === 'superadmin')
                                                <div class="text-xs text-indigo-600">
                                                    Akses penuh sistem
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-600 border-r border-slate-300">
                                    {{ $role->guard_name }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 border-r border-slate-300">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ $role->permissions_count }} permission
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 border-r border-slate-300">
                                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">
                                        {{ $role->users_count }} user
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @can('role.edit')
                                            <a href="{{ route('roles.edit', $role) }}"
                                               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 shadow-sm transition-all duration-200 hover:border-amber-200 hover:bg-amber-50 hover:text-amber-600">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Edit
                                            </a>
                                        @endcan

                                        @can('role.delete')
                                            @if ($role->name !== 'superadmin')
                                                <form method="POST" action="{{ route('roles.destroy', $role) }}" 
                                                      onsubmit="return confirm('Yakin ingin menghapus role ini?')"
                                                      class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center gap-1.5 rounded-lg border border-rose-100 bg-white px-3 py-1.5 text-xs font-semibold text-rose-500 shadow-sm transition-all duration-200 hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center border-b border-slate-300">
                                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-slate-100 text-slate-400">
                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-sm font-semibold text-slate-900">
                                        Role tidak ditemukan
                                    </div>
                                    <p class="mt-1 text-sm text-slate-500">
                                        Tambahkan role baru atau ubah filter pencarian.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($roles->hasPages())
                <div class="border-t border-slate-200 px-6 py-4">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>