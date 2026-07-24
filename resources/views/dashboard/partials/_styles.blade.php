<style>
    /* Keyframe Animations */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulseGlow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    .animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }

    /* KPI Card Base */
    .kpi-card, .kpi-card:focus, .kpi-card:active {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        outline: none !important;
        box-shadow: none !important;
        -webkit-tap-highlight-color: transparent;
        z-index: 1;
        /* Ditebalkan sedikit dari 1px ke 1.5px */
        border-width: 1.5px !important;
        border-style: solid !important;
    }

    /* Konten teks/elemen di dalam card berada di atas layer gradasi */
    .kpi-card > * {
        position: relative;
        z-index: 3;
    }

    /* Layer Gradasi Overlay Pseudo-element */
    .kpi-card::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        pointer-events: none;
        z-index: 2;
        transition: all 0.3s ease;
    }

    /* =========================================================
       1. KONDISI DEFAULT / DIAM (BORDER DITEBALKAN & DIKLARIKAN)
       ========================================================= */

    /* Top Risk (Merah Lebiih Jelas) */
    #card-top_risk {
        border-color: rgba(244, 63, 94, 0.65) !important;
    }
    #card-top_risk::after {
        background: linear-gradient(135deg, rgba(244, 63, 94, 0.12) 0%, transparent 70%);
    }

    /* SMAP (Ungu Lebih Jelas) */
    #card-smap {
        border-color: rgba(168, 85, 247, 0.65) !important;
    }
    #card-smap::after {
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.12) 0%, transparent 70%);
    }

    /* Departemen (Biru Lebih Jelas) */
    #card-dep {
        border-color: rgba(59, 130, 246, 0.65) !important;
    }
    #card-dep::after {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
    }

    /* =========================================================
       2. KONDISI HOVER (BORDER MENYALA FULL & GLOW DITINGKATKAN)
       ========================================================= */

    #card-top_risk:hover {
        transform: translateY(-3px);
        border-color: #f43f5e !important;
        box-shadow: 0 0 0 1px #f43f5e, 0 4px 22px rgba(244, 63, 94, 0.35) !important;
    }
    #card-top_risk:hover::after {
        background: linear-gradient(135deg, rgba(244, 63, 94, 0.25) 0%, transparent 75%);
    }

    #card-smap:hover {
        transform: translateY(-3px);
        border-color: #a855f7 !important;
        box-shadow: 0 0 0 1px #a855f7, 0 4px 22px rgba(168, 85, 247, 0.35) !important;
    }
    #card-smap:hover::after {
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.25) 0%, transparent 75%);
    }

    #card-dep:hover {
        transform: translateY(-3px);
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 1px #3b82f6, 0 4px 22px rgba(59, 130, 246, 0.35) !important;
    }
    #card-dep:hover::after {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.25) 0%, transparent 75%);
    }

    /* Badges & Navigation Buttons */
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
