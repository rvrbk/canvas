import { defineStore } from 'pinia';
import { getStats } from '../lib/stats';

export const useStatsStore = defineStore('stats', {
    state: () => ({
        stats: null,
    }),

    actions: {
        async fetch() {
            try {
                this.stats = await getStats();
            } catch {
                this.stats = null;
            }
        },
    },
});
