import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Cross-page "so sánh" (compare) selection. Backed by localStorage so it
 * survives navigation without a server round trip — product cards toggle
 * membership, the floating compare bar (layouts/shop.blade.php) reads it.
 * Capped at 3, matching CompareController::MAX_COMPARE.
 */
Alpine.store('compare', {
    items: [],
    max: 3,

    init() {
        try {
            this.items = JSON.parse(localStorage.getItem('compareList') || '[]');
        } catch (e) {
            this.items = [];
        }
    },

    persist() {
        try {
            localStorage.setItem('compareList', JSON.stringify(this.items));
        } catch (e) {}
    },

    has(slug) {
        return this.items.some((item) => item.slug === slug);
    },

    toggle(product) {
        if (this.has(product.slug)) {
            this.items = this.items.filter((item) => item.slug !== product.slug);
        } else {
            if (this.items.length >= this.max) return;
            this.items = [...this.items, product];
        }
        this.persist();
    },

    remove(slug) {
        this.items = this.items.filter((item) => item.slug !== slug);
        this.persist();
    },

    clear() {
        this.items = [];
        this.persist();
    },

    get slugsQuery() {
        return this.items.map((item) => item.slug).join(',');
    },
});

Alpine.start();
