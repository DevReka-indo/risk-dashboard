document.addEventListener('alpine:init', () => {
    Alpine.data('smapRiskForm', (historyData = {}, currentYear = '', defaultInherent = 0, defaultTarget = 0) => ({
        // ==========================
        // State
        // ==========================
        quarter: 'TW1',
        year: currentYear || new Date().getFullYear().toString(),
        inherent: parseInt(defaultInherent) || 0,
        value: '',
        targetValue: parseInt(defaultTarget) || 0,

        inherentReadOnly: true,
        history: historyData,

        // ==========================
        // Lifecycle
        // ==========================
        init() {
            this.checkInherent();

            // Re-kalkulasi secara realtime saat user mengubah kuartal atau tahun
            this.$watch('quarter', () => this.checkInherent());
            this.$watch('year', () => this.checkInherent());
        },

        // ==========================
        // Dynamic Inherent Risk Logic (Cascading)
        // ==========================
        checkInherent() {
            if (!this.quarter || !this.year) {
                this.inherent = parseInt(defaultInherent) || 0;
                return;
            }

            const yearData = this.history[this.year] || {};
            const quartersOrder = ['TW1', 'TW2', 'TW3', 'TW4'];
            const currentIndex = quartersOrder.indexOf(this.quarter);

            let previousValue = null;

            // 1. Cari riwayat di tahun yang sama (kuartal sebelum kuartal terpilih)
            for (let i = currentIndex - 1; i >= 0; i--) {
                const prevQ = quartersOrder[i];
                if (yearData[prevQ] && yearData[prevQ].value !== undefined) {
                    previousValue = yearData[prevQ].value;
                    break;
                }
            }

            // 2. Jika di tahun ini belum ada kuartal sebelumnya, cari TW4 dari tahun sebelumnya
            if (previousValue === null) {
                const prevYearData = this.history[parseInt(this.year) - 1] || {};
                for (let i = quartersOrder.length - 1; i >= 0; i--) {
                    const prevQ = quartersOrder[i];
                    if (prevYearData[prevQ] && prevYearData[prevQ].value !== undefined) {
                        previousValue = prevYearData[prevQ].value;
                        break;
                    }
                }
            }

            // 3. Pasang nilai inherent yang ditemukan, atau balik ke master jika tidak ada
            if (previousValue !== null && previousValue !== undefined) {
                this.inherent = parseInt(previousValue);
            } else {
                this.inherent = parseInt(defaultInherent) || 0;
            }
        },

        // ==========================
        // Helper Level & Trend
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

        get RiskLevelName() {
            const names = {
                1: 'Low',
                2: 'Low to Moderate',
                3: 'Moderate',
                4: 'Moderate to High',
                5: 'High'
            };
            return names[this.otomatisLevel] || '';
        },

        get otomatisLevel() {
            return this.getRiskLevelId(this.value);
        },

        get otomatisTrend() {
            const currentRisk = parseInt(this.value);
            const inherentRisk = parseInt(this.inherent);

            if (Number.isNaN(currentRisk) || Number.isNaN(inherentRisk) || !this.value) {
                return '';
            }

            if (currentRisk > inherentRisk) return 'Naik';
            if (currentRisk < inherentRisk) return 'Turun';
            return 'Stabil';
        }
    }));
});
