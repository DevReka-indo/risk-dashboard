/**
 * Fungsi Inisialisasi Chart SMAP
 * Dibuat global agar bisa dipanggil dari switchTab() di index.blade.php
 */
function initSmapCharts() {
    // 1. GUARD CLAUSE: Cek Tab Aktif di URL
    const urlParams = new URLSearchParams(window.location.search);
    const activeTab = urlParams.get('tab');

    // Jika parameter tab ada dan BUKAN 'smap', batalkan render!
    if (activeTab && activeTab !== 'smap') {
        return;
    }

    // ----------------------------------------------------
    // 1. Data Kategori (Bar Chart)
    // ----------------------------------------------------
    const categoryChartEl = document.getElementById('categoryChart');
    if (categoryChartEl) {
        // Hancurkan chart lama jika sudah terlanjur dirender
        const existingCatChart = Chart.getChart(categoryChartEl);
        if (existingCatChart) {
            existingCatChart.destroy();
        }

        const rawDataAttr = categoryChartEl.getAttribute('data-categories');
        const catRawData = rawDataAttr ? JSON.parse(rawDataAttr) : [];
        const catLabels = catRawData.map(item => item.name.length > 15 ? item.name.substring(0, 15) + '...' : item.name);
        const catTotals = catRawData.map(item => item.total);

        if (catLabels.length > 0) {
            new Chart(categoryChartEl, {
                type: 'bar',
                data: {
                    labels: catLabels,
                    datasets: [{
                        label: 'Jumlah Risiko',
                        data: catTotals,
                        backgroundColor: '#6366f1',
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { precision: 0, color: '#64748b' } },
                        x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 11 } } }
                    }
                }
            });
        }
    }

    // ----------------------------------------------------
    // 2. Data Level (Donut Chart)
    // ----------------------------------------------------
    const levelChartEl = document.getElementById('levelChart');
    if (levelChartEl) {
        // Hancurkan chart lama jika sudah terlanjur dirender
        const existingLevelChart = Chart.getChart(levelChartEl);
        if (existingLevelChart) {
            existingLevelChart.destroy();
        }

        const rawDataAttr = levelChartEl.getAttribute('data-level-distribution');
        const levelRawData = rawDataAttr ? JSON.parse(rawDataAttr) : {};
        const levelLabels = Object.keys(levelRawData);
        const levelTotals = Object.values(levelRawData);

        const colorMap = {
            'High': '#be123c', 'Tinggi': '#be123c',
            'Moderate to High': '#f59e0b',
            'Moderate': '#eab308',
            'Low to Moderate': '#4ade80', // hijau muda
            'Low': '#166534', 'Rendah': '#166534' // hijau tua
        };
        const levelColors = levelLabels.map(label => colorMap[label] || '#94a3b8');

        if (levelLabels.length > 0) {
            new Chart(levelChartEl, {
                type: 'doughnut',
                data: {
                    labels: levelLabels,
                    datasets: [{
                        data: levelTotals,
                        backgroundColor: levelColors,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8, font: { size: 12 } } }
                    }
                }
            });
        }
    }
}

// Jalankan saat DOM Ready (Fungsi initSmapCharts sendiri sudah aman karena punya Guard Clause di atas)
document.addEventListener("DOMContentLoaded", function () {
    initSmapCharts();
});
