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
        // 1. Sembunyikan semua tab
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.add('hidden');
        });

        // 2. Clear status border aktif dari semua card KPI
        document.querySelectorAll('.kpi-card').forEach(el => {
            el.classList.remove('ring-2', 'ring-red-500', 'ring-indigo-500', 'ring-purple-500');
        });

        // 3. Tampilkan tab yang dipilih
        const selectedContent = document.getElementById('content-' + tabName);
        if (selectedContent) {
            selectedContent.classList.remove('hidden');

            // Beri waktu sebentar agar browser merender elemen HTML (display: block)
            setTimeout(() => {
                // Force resize Chart.js canvas agar tidak terdistorsi/gepeng
                window.dispatchEvent(new Event('resize'));
                resizeTabCharts(selectedContent);

                // Re-render chart SMAP spesifik jika method-nya terdaftar di JS
                if (tabName === 'smap') {
                    if (typeof initSmapCharts === 'function') {
                        initSmapCharts();
                    } else if (typeof renderSmapCharts === 'function') {
                        renderSmapCharts();
                    }
                }
            }, 150);
        }

        // 4. Highlight Card KPI yang sedang aktif
        const selectedCard = document.getElementById('card-' + tabName);
        if (selectedCard) {
            if (tabName === 'top_risk') selectedCard.classList.add('ring-2', 'ring-red-500');
            else if (tabName === 'dep') selectedCard.classList.add('ring-2', 'ring-indigo-500');
            else if (tabName === 'smap') selectedCard.classList.add('ring-2', 'ring-purple-500');
        }
    }

    // Helper universal untuk merefresh Chart.js
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
    // 1. Cek parameter 'tab' dari URL (misal: ?tab=smap&periode=1&tahun=2026)
    const urlParams = new URLSearchParams(window.location.search);
    const activeTabParam = urlParams.get('tab');

    // 2. Jika ada query parameter tab (misal 'smap'), buka tab tersebut.
    // Jika tidak ada, default ke 'top_risk'.
    if (activeTabParam) {
        switchTab(activeTabParam);
    } else {
        switchTab('top_risk');
    }
    });
    </script>
</x-admin-layout>
