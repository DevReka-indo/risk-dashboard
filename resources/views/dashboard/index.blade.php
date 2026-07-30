<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900 dark:text-white">
            Risk Monitoring Dashboard
        </h1>
        <p class="hidden text-sm text-slate-500 dark:text-slate-400 sm:block">
            Pusat pantauan metrik risiko, tindakan penanganan, dan prioritas perusahaan.
        </p>
    </x-slot>

    {{-- STYLES CUSTOM & ANIMASI --}}
    @includeIf('dashboard.partials._styles')

    <div class="space-y-6 pb-10">
        {{-- KPI CARDS HEADER (TAB NAVIGATOR) --}}
        @includeIf('dashboard.partials._kpi-cards')

        {{-- TAB CONTENT 1: TOP RISK --}}
        @includeIf('dashboard.partials._tab-top-risk')

        {{-- TAB CONTENT 2: SMAP --}}
        @includeIf('dashboard.partials._tab-smap')

        {{-- TAB CONTENT 3: DEPARTEMEN --}}
        @includeIf('dashboard.partials._tab-dep')
    </div>

    {{-- ASISTEN VIRTUAL / AI CHAT --}}
    @includeIf('dashboard.partials._ai-chat')

    {{-- PEMANGGILAN LIBRARY SCRIPT EXTERNAL & LOCAL --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <script src="{{ asset('js/dashboard-chart.js') }}"></script>
    <script src="{{ asset('js/ai-chat.js') }}"></script>

    {{-- MANAGEMENT INIALISASI SCRIPT DASHBOARD --}}
    <script>
        // Data Payload untuk Chart dari Controller
        window.dashboardData = {
            topRisk: @json($nilaiTopRisk ?? []),
            unitLevel: @json($unitLevelDistribution ?? []),
            smap: @json($smapData ?? [])
        };

        /**
         * Pindah Tab Utama Dashboard (top_risk, smap, dep)
         */
        function switchTab(tabName) {
            // 1. Sembunyikan seluruh kontainer tab
            document.querySelectorAll('.tab-content').forEach(el => {
                el.classList.add('hidden');
            });

            // 2. Lepas efek fokus dari kartu KPI
            document.querySelectorAll('.kpi-card').forEach(el => {
                el.classList.remove('ring-2', 'ring-slate-900', 'dark:ring-white', 'border-slate-900');
                if (document.activeElement === el) {
                    el.blur();
                }
            });

            // 3. Tampilkan kontainer tab yang dipilih
            const selectedContent = document.getElementById('content-' + tabName);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');

                // Berikan highlight pada kartu KPI yang aktif
                const activeCard = document.getElementById('kpi-card-' + tabName);
                if (activeCard) {
                    activeCard.classList.add('ring-2', 'ring-slate-900', 'dark:ring-white');
                }

                // Trigger penyesuaian ukuran Chart setelah DOM ter-render
                setTimeout(() => {
                    window.dispatchEvent(new Event('resize'));
                    if (typeof resizeTabCharts === 'function') {
                        resizeTabCharts(selectedContent);
                    }

                    // Inisialisasi spesifik berdasarkan tab
                    if (tabName === 'top_risk' && typeof initTopRiskCharts === 'function') {
                        initTopRiskCharts();
                    } else if (tabName === 'smap') {
                        if (typeof initSmapCharts === 'function') {
                            initSmapCharts();
                        } else if (typeof renderSmapCharts === 'function') {
                            renderSmapCharts();
                        }
                    }
                }, 150);
            }

            // 4. Update query string di URL tanpa meload ulang halaman (History State)
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('tab', tabName);
            window.history.replaceState({}, '', currentUrl);
        }

        /**
         * Redraw / Resize Chart saat kontainer tab berubah dari hidden -> visible
         */
        function resizeTabCharts(container) {
            if (!container) return;

            container.querySelectorAll('canvas').forEach(canvas => {
                const chartInstance = Chart.getChart(canvas);
                if (chartInstance) {
                    chartInstance.resize();
                    chartInstance.update();
                }
            });
        }

        /**
         * Pindah Sub-Tab internal untuk Top Risk (jika ada)
         */
        function switchTopRiskTab(tabName) {
            document.querySelectorAll('.toprisk-tab-content').forEach(el => {
                el.classList.add('hidden');
            });

            const selectedContent = document.getElementById('toprisk-' + tabName);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }

            document.querySelectorAll('.tab-nav-btn').forEach(btn => {
                btn.classList.remove('active', 'text-slate-900', 'dark:text-white', 'border-b-2', 'border-slate-900', 'dark:border-white');
                btn.classList.add('text-slate-400');
            });

            const selectedBtn = document.getElementById('tab-' + tabName);
            if (selectedBtn) {
                selectedBtn.classList.add('active', 'text-slate-900', 'dark:text-white', 'border-b-2', 'border-slate-900', 'dark:border-white');
                selectedBtn.classList.remove('text-slate-400');
            }
        }

        // Listener saat DOM siap dieksekusi
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTabParam = urlParams.get('tab');

            if (activeTabParam) {
                switchTab(activeTabParam);
            } else {
                switchTab('top_risk');
            }
        });
    </script>
</x-admin-layout>