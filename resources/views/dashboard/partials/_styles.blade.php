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
        border-color: #f43f5e !important;
        background: linear-gradient(135deg, #fff1f2, #ffffff) !important;
        box-shadow: 0 4px 20px rgba(244, 63, 94, 0.15) !important;
    }
    .card-active-indigo {
        border-color: #6366f1 !important;
        background: linear-gradient(135deg, #eef2ff, #ffffff) !important;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.15) !important;
    }
    .card-active-purple {
        border-color: #9333ea !important;
        background: linear-gradient(135deg, #fbf6ffff, #ffffff) !important;
        box-shadow: 0 4px 20px rgba(134, 66, 198, 0.15) !important;
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
