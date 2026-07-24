<x-admin-layout>
    <x-slot name="header">
        <h1 class="text-lg font-bold text-slate-900">
            Risk Monitoring Dashboard
        </h1>
        <p class="hidden text-sm text-slate-500 sm:block">
            Pusat pantauan metrik risiko, tindakan penanganan, dan prioritas perusahaan.
        </p>
    </x-slot>

    @include('dashboard.partials._styles')

    <div class="space-y-6 pb-10">
        @include('dashboard.partials._kpi-cards')

        @include('dashboard.partials._tab-top-risk')

        @include('dashboard.partials._tab-smap')

        @include('dashboard.partials._tab-dep')

    </div>

    @include('dashboard.partials._ai-chat')

    {{-- PEMANGGILAN LIBRARY SCRIPT --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
    <script src="{{ asset('js/dashboard-chart.js') }}"></script>
    <script src="{{ asset('js/ai-chat.js') }}"></script>

    <script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });

        document.querySelectorAll('.kpi-card').forEach(el => {
            el.classList.remove('ring-2', 'ring-rose-500', 'ring-purple-500', 'ring-blue-500', 'card-active-rose', 'card-active-purple', 'card-active-indigo');
            el.blur();
        });

        const selectedContent = document.getElementById('content-' + tabName);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');

            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
                if (typeof resizeTabCharts === 'function') {
                    resizeTabCharts(selectedContent);
                }

                if (tabName === 'smap') {
                    if (typeof initSmapCharts === 'function') {
                        initSmapCharts();
                    } else if (typeof renderSmapCharts === 'function') {
                        renderSmapCharts();
                    }
                }
            }, 150);
        }

        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', currentUrl);
    }

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

    function switchTopRiskTab(tabName) {
        document.querySelectorAll('.toprisk-tab-content').forEach(el => {
            el.classList.add('hidden');
        });

        const selectedContent = document.getElementById('toprisk-' + tabName);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
        }

        document.querySelectorAll('.tab-nav-btn').forEach(btn => {
            btn.classList.remove('active', 'text-slate-800');
            btn.classList.add('text-slate-400');
        });

        const selectedBtn = document.getElementById('tab-' + tabName);
        if (selectedBtn) {
            selectedBtn.classList.add('active', 'text-slate-800');
            selectedBtn.classList.remove('text-slate-400');
        }
    }

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
