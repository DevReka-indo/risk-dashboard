<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold leading-tight text-slate-900">
                Pengaturan Aplikasi
            </h2>
            <p class="text-xs text-slate-500">Kelola konfigurasi sistem utama dan pantau aktivitas pengguna.</p>
        </div>
    </x-slot>

    <div class="w-full px-2 py-2 md:px-4">

        <!-- Wrapper Tabs (Full Width) -->
        <!-- PERBAIKAN: activeTab sekarang membaca parameter '?tab=' dari URL. Jika kosong, default ke 'sistem' -->
        <div x-data="{
            activeTab: new URLSearchParams(window.location.search).get('tab') || 'sistem'
        }"
        class="w-full overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">

            <!-- Navigasi Tabs -->
            <div class="border-b border-slate-200 bg-slate-50/80 px-4 sm:px-6 pt-2">
                <nav class="-mb-px flex space-x-6 overflow-x-auto">
                    <button @click="activeTab = 'sistem'"
                            :class="activeTab === 'sistem' ? 'border-indigo-600 text-indigo-700 bg-indigo-50/50' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 hover:bg-slate-100/50'"
                            class="whitespace-nowrap border-b-2 py-3 px-4 text-sm font-semibold transition-all duration-200 rounded-t-lg">
                        Konfigurasi Sistem
                    </button>
                    <button @click="activeTab = 'audit'"
                            :class="activeTab === 'audit' ? 'border-indigo-600 text-indigo-700 bg-indigo-50/50' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700 hover:bg-slate-100/50'"
                            class="whitespace-nowrap border-b-2 py-3 px-4 text-sm font-semibold transition-all duration-200 rounded-t-lg">
                        Audit Trail
                    </button>
                </nav>
            </div>

            <!-- Konten Tabs -->
            <div class="p-5 sm:p-6">

                <!-- TAB 1: KONFIGURASI SISTEM -->
                @include('settings.partials.system-configuration')

                <!-- TAB 2: AUDIT TRAIL -->
                @include('settings.partials.audit-trail')

            </div>
        </div>
    </div>
</x-admin-layout>
