document.addEventListener('alpine:init', () => {
    Alpine.data('smapRiskForm', (historyData = {}, defaultYear = new Date().getFullYear(), defaultInherent = '') => ({
        // ==========================
        // State
        // ==========================
        quarter: 'TW1',
        year: defaultYear,
        // 2. SET nilai awal inherent dari parameter yang dikirim oleh Blade
        inherent: defaultInherent,
        value: '',
        targetValue: '',

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
        // Current Risk Level (SEBELUMNYA HILANG)
        // ==========================
        get otomatisLevel() {
            return this.getRiskLevelId(this.value);
        },

        // ==========================
        // Target Risk Level (SEBELUMNYA HILANG)
        // ==========================
        get otomatisTargetLevel() {
            return this.getRiskLevelId(this.targetValue);
        },

        // ==========================
        // Inherent Risk Level
        // ==========================
        get inherentLevel() {
            return this.getRiskLevelId(this.inherent);
        },

        get inherentLevelName() {
            const levels = {
                1: 'Low',
                2: 'Low to Moderate',
                3: 'Moderate',
                4: 'Moderate to High',
                5: 'High'
            };
            return levels[this.inherentLevel] || '-';
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

            if (currentRisk > inherentRisk) return 'Naik';
            if (currentRisk < inherentRisk) return 'Turun';
            return 'Stabil';
        },

        // ==========================
        // Level Names
        // ==========================
        get otomatisLevelName() {
            const levels = {
                1: 'Low',
                2: 'Low to Moderate',
                3: 'Moderate',
                4: 'Moderate to High',
                5: 'High'
            };
            return levels[this.otomatisLevel] || '-';
        },

        get otomatisTargetLevelName() {
            const levels = {
                1: 'Low',
                2: 'Low to Moderate',
                3: 'Moderate',
                4: 'Moderate to High',
                5: 'High'
            };
            return levels[this.otomatisTargetLevel] || '-';
        }
    }));
});
