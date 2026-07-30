<x-admin-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-slate-100 transition-colors duration-300">
                Pengaturan Aplikasi
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors duration-300">Kelola konfigurasi sistem utama dan pantau aktivitas pengguna.</p>
        </div>
    </x-slot>

    <div class="w-full px-2 py-2 md:px-4">

        <!-- Wrapper Tabs -->
        <div x-data="{
            activeTab: new URLSearchParams(window.location.search).get('tab') || 'audit'
        }"
        class="w-full overflow-hidden rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl transition-colors duration-300">

            <!-- Navigasi Tabs -->
            <div class="border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800 px-4 sm:px-6 py-3.5 transition-colors duration-300">
                <nav class="flex space-x-6 overflow-x-auto">
                    <button @click="activeTab = 'audit'"
                            :class="activeTab === 'audit' ? 'border-indigo-600/30 dark:border-slate-700 text-indigo-700 dark:text-slate-200 bg-indigo-50/50 dark:bg-slate-900' : 'border-transparent text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-700 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100/50 dark:hover:bg-slate-800/40'"
                            class="whitespace-nowrap border py-2 px-4.5 text-sm font-semibold transition-all duration-200 rounded-lg">
                        Audit Trail
                    </button>
                </nav>
            </div>

            <div class="p-0 dark:bg-[#0B1220]">
                <!-- TAB 2: AUDIT TRAIL -->
                @include('settings.partials.audit-trail')
            </div>

        </div>
    </div> 
</x-admin-layout>
