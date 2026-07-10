document.addEventListener('alpine:init', () => {
    Alpine.data('smapRiskForm', (historyData = {}, currentYear = '') => ({
        // ==========================
        // State
        // ==========================
        quarter: 'TW1',
        year: currentYear,
        inherent: '',
        value: '',
        targetValue: '',

        inherentReadOnly: false,
        history: historyData,

        // ==========================
        // Lifecycle
        // ==========================
        init() {
            this.checkInherent();
        },

        // ==========================
        // Inherent Risk Logic
        // ==========================
        checkInherent() {
            // TW1 boleh diisi manual
            if (this.quarter === 'TW1') {
                this.inherentReadOnly = false;
                return;
            }

            // Selain TW1 otomatis mengambil dari kuartal sebelumnya
            this.inherentReadOnly = true;

            const previousQuarter = {
                TW2: 'TW1',
                TW3: 'TW2',
                TW4: 'TW3',
            };

            const prevQuarter = previousQuarter[this.quarter];

            if (
                this.history?.[this.year]?.[prevQuarter] !== undefined &&
                this.history?.[this.year]?.[prevQuarter] !== null
            ) {
                this.inherent = this.history[this.year][prevQuarter];
            } else {
                this.inherent = '';
            }
        },

        // ==========================
        // Mapping Risk Level
        // ==========================
        getRiskLevelId(score) {
            const value = parseInt(score);

            if (Number.isNaN(value)) return '';

            if (value >= 1 && value <= 5) return 1;
            if (value >= 6 && value <= 11) return 2;
            if (value >= 12 && value <= 15) return 3;
            if (value >= 16 && value <= 19) return 4;
            if (value >= 20 && value <= 25) return 5;

            return '';
        },

        // ==========================
        // Current Risk Level
        // ==========================
        get otomatisLevel() {
            return this.getRiskLevelId(this.value);
        },

        // ==========================
        // Target Risk Level
        // ==========================
        get otomatisTargetLevel() {
            return this.getRiskLevelId(this.targetValue);
        },

        // ==========================
        // Trend
        // ==========================
        get otomatisTrend() {
            const currentRisk = parseInt(this.value);
            const inherentRisk = parseInt(this.inherent);

            if (Number.isNaN(currentRisk) || Number.isNaN(inherentRisk)) {
                return 'Stabil';
            }

            if (currentRisk > inherentRisk) {
                return 'Naik';
            }

            if (currentRisk < inherentRisk) {
                return 'Turun';
            }

            return 'Stabil';
        },
    }));
});
