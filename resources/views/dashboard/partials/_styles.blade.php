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
        outline: none !important;
    }
    .kpi-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        pointer-events: none;
    }

    /* BORDER & GLOW HANYA SAAT HOVER */
    #card-top_risk:hover {
        transform: translateY(-4px);
        border-color: #f43f5e !important; /* Merah Rose Tegas */
        box-shadow: 0 4px 20px rgba(244, 63, 94, 0.15) !important;
    }
    #card-smap:hover {
        transform: translateY(-4px);
        border-color: #a855f7 !important; /* Ungu Tegas */
        box-shadow: 0 4px 20px rgba(168, 85, 247, 0.15) !important;
    }
    #card-dep:hover {
        transform: translateY(-4px);
        border-color: #3b82f6 !important; /* Biru Tegas */
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.15) !important;
    }

    /* DARK MODE HOVER */
    .dark #card-top_risk:hover {
        border-color: #f43f5e !important;
        box-shadow: 0 0 0 1px rgba(244, 63, 94, 0.5), 0 4px 24px rgba(244, 63, 94, 0.35) !important;
    }
    .dark #card-smap:hover {
        border-color: #a855f7 !important;
        box-shadow: 0 0 0 1px rgba(168, 85, 247, 0.5), 0 4px 24px rgba(168, 85, 247, 0.35) !important;
    }
    .dark #card-dep:hover {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 1px rgba(59, 130, 246, 0.5), 0 4px 24px rgba(59, 130, 246, 0.35) !important;
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
