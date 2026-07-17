<aside
    class="fixed inset-y-0 left-0 z-50 flex w-72 transform flex-col
           border-r border-indigo-100
           bg-gradient-to-b from-slate-50 via-white to-indigo-50/40
           shadow-2xl
           transition-transform duration-300
           lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

    <div class="flex h-16 items-center gap-4 border-b border-indigo-100 px-6 bg-white/70 backdrop-blur">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <div
    class="flex h-12 w-12 items-center justify-center
           rounded-2xl
           bg-gradient-to-br
           from-white-500
           via-white
           to-white-500
           text-white
           shadow-xl shadow-indigo-300/40">
               <img src="{{ asset('images/Group 6924.png') }}"
                     alt="Manrisk Logo"
                     class="h-7 w-7 object-contain brightness-0 invert text-white">
            </div>

            <div>
               <div class="text-base font-bold tracking-wide text-slate-800">
                    Manrisk
                </div>
              <div class="text-xs text-indigo-400 font-medium">
                    Monitoring Risiko Perusahaan
                </div>
            </div>
        </a>
    </div>

   <nav class="flex-1 overflow-y-auto no-scrollbar px-4 pt-8 pb-5 space-y-3">
        @can('dashboard.view')
            <a href="{{ route('dashboard') }}"
                class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
               {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 13.125C3 12.504 3.504 12 4.125 12h3.75C8.496 12 9 12.504 9 13.125v6.75C9 20.496 8.496 21 7.875 21h-3.75A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 4.125C9.75 3.504 10.254 3 10.875 3h2.25c.621 0 1.125.504 1.125 1.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125ZM15 8.625C15 8.004 15.504 7.5 16.125 7.5h3.75c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-3.75A1.125 1.125 0 0 1 15 19.875V8.625Z" />
                    </svg>
                </span>

                Dashboard
            </a>
        @endcan

        {{-- MENU UTAMA --}}
        @canany(['toprisk.view', 'risk.view'])
            <div class="pt-4">
                <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-300">
                    MENU UTAMA
                </div>

                @can('toprisk.view')
                    <a href="{{ route('top-risk.index') }}"
                        class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('top-risk.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 15l3-3 3 2 5-7" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 7h1.5V8.5" />
                            </svg>
                        </span>
                        Top Risk
                    </a>
                @endcan

                @can('risk.view')
                    <a href="{{ route('department-risk.index') }}"
                        class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
                       {{ request()->routeIs('department-risk.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5A3.375 3.375 0 0 0 10.125 2.25H4.125A1.125 1.125 0 0 0 3 3.375v17.25c0 .621.504 1.125 1.125 1.125h15.75c.621 0 1.125-.504 1.125-1.125v-6.375Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 11.25h6m-6 4h6m-6-8h2.25" />
                            </svg>
                        </span>
                        Risk Department
                    </a>

                    <a href="{{ route('smap-risk.index') }}"
                        class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
                       {{ request()->routeIs('smap-risk.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751A11.956 11.956 0 0 1 12 2.714Z" />
                            </svg>
                        </span>
                        Risk SMAP
                    </a>
                @endcan
            </div>
        @endcanany

        {{-- MASTER DATA --}}
        @canany(['risk.view', 'unit-kerja.view'])
            <div class="pt-4">
               <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-300">
                    Menu Lainnya
                </div>

                @can('risk.view')
                    <a href="{{ route('kategori-risiko.index') }}"
                        class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
                    {{ request()->routeIs('kategori-risiko.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                            </svg>
                        </span>
                        Kategori Risiko
                    </a>
                @endcan

                @can('unit-kerja.view')
                    <a href="{{ route('unit-kerja.index') }}"
                        class="group flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
                       {{ request()->routeIs('unit-kerja.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M8.25 7.5h1.5m-1.5 3h1.5m-1.5 3h1.5m4.5-6h1.5m-1.5 3h1.5m-1.5 3h1.5" />
                            </svg>
                        </span>
                        Unit Kerja
                    </a>
                @endcan
            </div>
        @endcanany

        @canany(['user.view', 'role.view', 'permission.view'])
            <div class="pt-4">
               <div class="px-3 mb-2 text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-300">
                    User Management
                </div>

                @can('user.view')
                    <a href="{{ route('users.index') }}"
                        class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
                       {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.25a8.25 8.25 0 0 1 15 0" />
                            </svg>
                        </span>
                        Users
                    </a>
                @endcan

                @can('role.view')
                    <a href="{{ route('roles.index') }}"
                        class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
                       {{ request()->routeIs('roles.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </span>
                        Roles
                    </a>
                @endcan

                @can('permission.view')
                    <a href="{{ route('permissions.index') }}"
                        class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
                       {{ request()->routeIs('permissions.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5A2.25 2.25 0 0 0 19.5 19.5v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </span>
                        Permissions
                    </a>
                @endcan
            </div>
        @endcanany

        @can('setting.view')
            <div class="pt-4">
                <div class="px-3 pb-2 text-xs font-semibold uppercase tracking-wider text-slate-400">
                    System
                </div>

                <a href="{{ route('settings.index') }}"
                    class="flex items-center gap-3 rounded-2xl px-3 py-2.5 text-sm font-medium transition
                   {{ request()->routeIs('settings.*') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/25' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' }}">
                    <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/10">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.66.84.084.037.168.076.25.118.334.17.73.154 1.04-.058l1.063-.723a1.125 1.125 0 0 1 1.45.12l1.833 1.833c.39.39.44 1.003.12 1.45l-.723 1.063c-.212.31-.228.706-.058 1.04.042.082.081.166.118.25.154.347.466.597.84.66l1.281.213c.542.09.94.56.94 1.11v2.593c0 .55-.398 1.02-.94 1.11l-1.281.213c-.374.063-.686.313-.84.66a6.83 6.83 0 0 1-.118.25c-.17.334-.154.73.058 1.04l.723 1.063c.32.447.27 1.06-.12 1.45l-1.833 1.833a1.125 1.125 0 0 1-1.45.12l-1.063-.723c-.31-.212-.706-.228-1.04-.058a6.83 6.83 0 0 1-.25.118c-.347.154-.597.466-.66.84l-.213 1.281c-.09.542-.56.94-1.11.94h-2.593c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.063-.374-.313-.686-.66-.84a6.83 6.83 0 0 1-.25-.118c-.334-.17-.73-.154-1.04.058l-1.063.723a1.125 1.125 0 0 1-1.45-.12L2.98 18.65a1.125 1.125 0 0 1-.12-1.45l.723-1.063c.212-.31.228-.706.058-1.04a6.83 6.83 0 0 1-.118-.25c-.154-.347-.466-.597-.84-.66l-1.281-.213a1.125 1.125 0 0 1-.94-1.11v-2.593c0-.55.398-1.02.94-1.11l1.281-.213c.374-.063.686-.313.84-.66.037-.084.076-.168.118-.25.17-.334.154-.73-.058-1.04L2.86 6.94a1.125 1.125 0 0 1 .12-1.45l1.833-1.833a1.125 1.125 0 0 1 1.45-.12l1.063.723c.31.212.706.228 1.04.058.082-.042.166-.081.25-.118.347-.154.597-.466.66-.84l.213-1.281Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </span>
                    Settings
                </a>
            </div>
        @endcan
    </nav>
</aside>
