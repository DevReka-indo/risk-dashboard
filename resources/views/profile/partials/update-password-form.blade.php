<section>
    <div class="mb-5">
        <h2 class="text-base font-bold text-slate-900">
            Ubah Password
        </h2>
        <p class="mt-1 text-sm text-slate-500">
            Pastikan akun Anda menggunakan password yang panjang dan aman.
        </p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div>
            <label for="update_password_current_password" class="block text-sm font-semibold text-slate-700">
                Password Saat Ini
            </label>
            <input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="current-password"
                placeholder="Masukkan password saat ini..."
            >
            @error('current_password', 'updatePassword')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- New Password --}}
        <div>
            <label for="update_password_password" class="block text-sm font-semibold text-slate-700">
                Password Baru
            </label>
            <input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="new-password"
                placeholder="Masukkan password baru..."
            >
            @error('password', 'updatePassword')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-semibold text-slate-700">
                Konfirmasi Password Baru
            </label>
            <input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-2 w-full rounded-2xl border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="new-password"
                placeholder="Konfirmasi password baru..."
            >
            @error('password_confirmation', 'updatePassword')
                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="flex items-center gap-4 pt-2">
            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 transition"
            >
                Simpan Password
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-medium text-emerald-600"
                >
                    ✓ Password berhasil diperbarui.
                </p>
            @endif
        </div>
    </form>
</section>