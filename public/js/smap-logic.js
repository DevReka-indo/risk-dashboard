document.addEventListener('alpine:init', () => {
    Alpine.data('smapRiskForm', (historyData = {}, currentYear = '', defaultInherent = 0, defaultTarget = 0) => ({
        // ==========================
        // State
        // ==========================
        quarter: '',
        year: currentYear,
        inherent: parseInt(defaultInherent) || 0, // Mengambil nilai default bawaan dari database
        value: '',
        targetValue: parseInt(defaultTarget) || 0, // Mengambil nilai target bawaan dari database

        inherentReadOnly: true,
        history: historyData,

        // ==========================
        // Lifecycle
        // ==========================
        init() {
            this.checkInherent();

            // Re-kalkulasi jika kuartal diubah oleh user
            this.$watch('quarter', () => {
                this.checkInherent();
            });
        },

        // ==========================
        // Inherent Risk Logic
        // ==========================
        checkInherent() {
            // Kunci nilai inherent agar SELALU menggunakan nilai default dari database untuk semua kuartal (TW1 - TW4)
            this.inherent = parseInt(defaultInherent) || 0;
        },

        // ==========================
        // Mapping Risk Level (1-5)
        // ==========================
        getRiskLevelId(score) {
            const val = parseInt(score);
            if (Number.isNaN(val) || val <= 0) return '';

            if (val >= 1 && val <= 5) return 1;
            if (val >= 6 && val <= 11) return 2;
            if (val >= 12 && val <= 15) return 3;
            if (val >= 16 && val <= 19) return 4;
            if (val >= 20 && val <= 25) return 5;

            return '';
        },

        // ==========================
        // Helper Translate Level ID ke Teks
        // ==========================
        getRiskLevelName(levelId) {
            const names = {
                1: 'Low',
                2: 'Low Moderate',
                3: 'Moderate',
                4: 'Moderate to High',
                5: 'High'
            };
            return names[levelId] || 'Menunggu input...';
        },

        // ==========================
        // Inherent Risk Level
        // ==========================
        get inherentLevel() {
            return this.getRiskLevelId(this.inherent);
        },

        get inherentLevelName() {
            return this.getRiskLevelName(this.inherentLevel);
        },

        // ==========================
        // Current Risk Level
        // ==========================
        get otomatisLevel() {
            return this.getRiskLevelId(this.value);
        },

        get otomatisLevelName() {
            return this.getRiskLevelName(this.otomatisLevel);
        },

        // ==========================
        // Target Risk Level
        // ==========================
        get otomatisTargetLevel() {
            return this.getRiskLevelId(this.targetValue);
        },

        get otomatisTargetLevelName() {
            return this.getRiskLevelName(this.otomatisTargetLevel);
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
    }));
});
