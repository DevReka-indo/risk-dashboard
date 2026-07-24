<style>
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes pulseGlow { 0%, 100% { opacity: 1; } 50% { opacity: 0.6; } }
    .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }

    .kpi-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .kpi-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        pointer-events: none;
    }
    .kpi-card:hover { transform: translateY(-4px); }
    .kpi-card .card-icon { transition: all 0.3s ease; }
    .kpi-card:hover .card-icon { transform: scale(1.1) rotate(-5deg); }

    .card-active-rose {
        border-color: #fda4af !important;
        background: #ffffff !important;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06) !important;
    }
    .card-active-indigo {
        border-color: #a5b4fc !important;
        background: #ffffff !important;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06) !important;
    }
    .card-active-purple {
        border-color: #d8b4fe !important;
        background: #ffffff !important;
        box-shadow: 0 1px 6px rgba(0, 0, 0, 0.06) !important;
    }

    /* Dark mode: gradasi nyala untuk card Top Risk (merah), SMAP (ungu) dan Departemen (biru) */
    .dark #card-top_risk {
        background: linear-gradient(135deg, #3b0a1a 0%, #1f1020 50%, #0f172a 100%) !important;
        border-color: #e11d48 !important;
        box-shadow: 0 0 0 1px rgba(225, 29, 72, 0.3), 0 4px 24px rgba(225, 29, 72, 0.18) !important;
    }
    .dark #card-top_risk.card-active-rose {
        background: linear-gradient(135deg, #5b0f28 0%, #2d0f1e 50%, #0f172a 100%) !important;
        border-color: #f43f5e !important;
        box-shadow: 0 0 0 1px rgba(244, 63, 94, 0.5), 0 4px 32px rgba(244, 63, 94, 0.35) !important;
    }
    .dark .card-active-rose {
        border-color: #f43f5e !important;
        box-shadow: 0 0 0 1px rgba(244, 63, 94, 0.5), 0 4px 32px rgba(244, 63, 94, 0.35) !important;
    }
    .dark #card-smap {
        background: linear-gradient(135deg, #2d1b4e 0%, #1e1b3a 50%, #1a1f3a 100%) !important;
        border-color: #7c3aed !important;
        box-shadow: 0 0 0 1px rgba(124, 58, 237, 0.3), 0 4px 24px rgba(124, 58, 237, 0.18) !important;
    }
    .dark #card-smap.card-active-purple {
        background: linear-gradient(135deg, #3b1f6e 0%, #251b50 50%, #1a1f3a 100%) !important;
        border-color: #a855f7 !important;
        box-shadow: 0 0 0 1px rgba(168, 85, 247, 0.5), 0 4px 32px rgba(168, 85, 247, 0.35) !important;
    }
    .dark #card-dep {
        background: linear-gradient(135deg, #0f2d5e 0%, #111d3a 50%, #0f172a 100%) !important;
        border-color: #2563eb !important;
        box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.3), 0 4px 24px rgba(37, 99, 235, 0.18) !important;
    }
    .dark #card-dep.card-active-indigo {
        background: linear-gradient(135deg, #0f3470 0%, #112148 50%, #0f172a 100%) !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.5), 0 4px 32px rgba(59, 130, 246, 0.35) !important;
    }
    /* Dark mode: active states override */
    .dark .card-active-purple {
        border-color: #a855f7 !important;
        box-shadow: 0 0 0 1px rgba(168, 85, 247, 0.5), 0 4px 32px rgba(168, 85, 247, 0.35) !important;
    }
    .dark .card-active-indigo {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.5), 0 4px 32px rgba(59, 130, 246, 0.35) !important;
    }

    .stat-badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 2px 10px; border-radius: 9999px; font-size: 10px; font-weight: 600;
    }

    .tab-nav-btn { transition: all 0.2s ease; position: relative; cursor: pointer; }
    .tab-nav-btn.active { color: #1e293b; font-weight: 600; }
    .tab-nav-btn.active::after {
        content: ''; position: absolute; bottom: -2px; left: 0; right: 0; height: 2px;
        background: #f43f5e; border-radius: 9999px;
    }
    .tab-nav-btn:not(.active) { color: #94a3b8; }
    .tab-nav-btn:not(.active):hover { color: #64748b; }

    .table-border-custom { border-collapse: collapse; }
    .table-border-custom th, .table-border-custom td { border: 1px solid #1e293b; }
</style>
