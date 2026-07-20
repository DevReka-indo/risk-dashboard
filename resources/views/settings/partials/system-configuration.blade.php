<div x-show="activeTab === 'sistem'" style="display: none;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0">

    <form action="{{ route('settings.update-system') }}" method="POST" class="space-y-6">
        @csrf

        <div class="max-w-lg mb-6">
            <label for="default_year" class="block text-sm font-semibold text-slate-700">Tahun Monitoring Default</label>
            <div class="mt-2">
                <input type="number" name="default_year" id="default_year" value="{{ date('Y') }}"
                    class="block w-full rounded-md border-slate-300 px-4 py-2.5 text-slate-900 shadow-sm transition-colors focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <p class="mt-2 text-xs text-slate-500">Tahun yang otomatis terbuka saat pengguna masuk ke dashboard utama.</p>
        </div>

        <hr class="border-slate-200">

        <div class="flex justify-start pt-2">
            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-900 focus:ring-offset-2">
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>
