document.addEventListener('alpine:init', () => {
    Alpine.data('smapRiskForm', (initialValue = '', initialInherent = '', initialTrend = 'Stabil') => ({
        value: initialValue,
        inherent: initialInherent,

        get otomatisLevel() {
            const v = parseInt(this.value);
            if (!v) return '';

            if (v >= 1 && v <= 5) return 1;
            if (v >= 6 && v <= 11) return 2;
            if (v >= 12 && v <= 15) return 3;
            if (v >= 16 && v <= 19) return 4;
            if (v >= 20 && v <= 25) return 5;

            return '';
        },

        get otomatisTrend() {
            const v = parseInt(this.value);
            const i = parseInt(this.inherent);

            if (isNaN(v) || isNaN(i)) return initialTrend;

            if (v > i) return 'Naik';
            if (v < i) return 'Turun';
            return 'Stabil';
        }
    }));
});
