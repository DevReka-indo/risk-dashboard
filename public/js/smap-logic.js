document.addEventListener('alpine:init', () => {
    Alpine.data('smapRiskForm', (historyData = {}, currentYear = '') => ({
        // ==========================
        // State
        // ==========================
        quarter: '', // Sengaja dikosongkan awal agar trigger saat dipilih
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
            if (!this.quarter) {
                this.inherentReadOnly = false;
                return;
            }

            // TW1 selalu manual (awal tahun)
            if (this.quarter === 'TW1') {
                this.inherentReadOnly = false;
                this.inherent = '';
                return;
            }

            // Selain TW1, otomatis mengambil VALUE dari kuartal sebelumnya
            this.inherentReadOnly = true;

            const previousQuarter = {
                TW2: 'TW1',
                TW3: 'TW2',
                TW4: 'TW3',
            };

            const prevQuarter = previousQuarter[this.quarter];

            // 🔥 Membaca properti .value dari kuartal sebelumnya sesuai aturan barumu
            if (
                this.history &&
                this.history[this.year] &&
                this.history[this.year][prevQuarter] !== undefined &&
                this.history[this.year][prevQuarter] !== null
            ) {
                this.inherent = this.history[this.year][prevQuarter].value;
            } else {
                this.inherent = 0; // Set 0 jika data kuartal sebelumnya belum ada di DB
            }
        },

        // ==========================
        // Mapping Risk Level (1-5)
        // ==========================
        getRiskLevelId(score) {
            const val = parseInt(score);
            if (Number.isNaN(val)) return '';

            if (val >= 1 && val <= 5) return 1;
            if (val >= 6 && val <= 11) return 2;
            if (val >= 12 && val <= 15) return 3;
            if (val >= 16 && val <= 19) return 4;
            if (val >= 20 && val <= 25) return 5;

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

            if (currentRisk > inherentRisk) return 'Naik';
            if (currentRisk < inherentRisk) return 'Turun';
            return 'Stabil';
        },
    }));
});

