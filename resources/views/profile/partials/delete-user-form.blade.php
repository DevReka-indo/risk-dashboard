<section class="space-y-5">
    <div>
        <h2 class="text-base font-bold text-slate-900">
            Hapus Akun
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Setelah akun Anda dihapus, semua data dan sumber daya akan dihapus secara permanen. Sebelum menghapus akun, pastikan Anda telah mengunduh data yang ingin disimpan.
        </p>
    </div>

    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-rose-800">
                    Tindakan ini tidak dapat dibatalkan
                </p>
                <p class="text-sm text-rose-600">
                    Semua data yang terkait dengan akun Anda akan dihapus secara permanen.
                </p>
            </div>
        </div>
    </div>

    <button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="inline-flex items-center justify-center rounded-2xl border border-rose-200 bg-white px-6 py-2.5 text-sm font-semibold text-rose-600 shadow-sm hover:bg-rose-50 transition"
    >
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-start gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-rose-100">
                    <svg class="h-6 w-6 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">
                        Konfirmasi Hapus Akun
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Apakah Anda yakin ingin menghapus akun? Tindakan ini tidak dapat dibatalkan. Silakan masukkan password Anda untuk konfirmasi.
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <label for="password" class="block text-sm font-semibold text-slate-700">
                    Password
                </label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    placeholder="Masukkan password Anda..."
                >
                @error('password', 'userDeletion')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close')"
                    class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-6 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-2xl border border-rose-200 bg-rose-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-rose-500/20 hover:bg-rose-700 transition"
                >
                    Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>