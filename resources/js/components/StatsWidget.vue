<script setup>
import { onMounted } from 'vue';
import { useStatsStore } from '../stores/stats';

const statsStore = useStatsStore();

onMounted(() => {
    if (!statsStore.stats) {
        statsStore.fetch();
    }
});
</script>

<template>
    <div v-if="statsStore.stats" class="border-4 border-ink bg-brand-yellow p-5 shadow-[6px_6px_0_#0e0e0e] sm:p-6">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <div class="flex items-baseline gap-3">
                <span class="font-display text-sm sm:text-base">LEVEL {{ statsStore.stats.level }}</span>
                <span class="font-display text-2xl sm:text-3xl">{{ statsStore.stats.points }} PTS</span>
            </div>
            <p class="text-xs font-bold whitespace-nowrap">{{ statsStore.stats.points_to_next_level }} PTS TO LEVEL {{ statsStore.stats.level + 1 }}</p>
        </div>

        <div class="mt-3 h-3.5 w-full border-2 border-ink bg-paper">
            <div class="h-full bg-brand-red transition-all duration-500" :style="{ width: `${statsStore.stats.points_into_level}%` }" />
        </div>

        <div class="mt-4 flex flex-wrap gap-2.5">
            <div
                v-for="badge in statsStore.stats.badges"
                :key="badge.key"
                class="flex items-center gap-1.5 border-2 px-3 py-1.5 text-xs font-bold tracking-wide uppercase"
                :class="badge.achieved ? 'border-ink bg-paper text-ink' : 'border-ink/40 border-dashed text-ink/40'"
                :title="badge.description"
            >
                <svg v-if="badge.achieved" width="12" height="12" viewBox="0 0 24 24" fill="#0e0e0e">
                    <path d="M12 3l2.5 5.5L20 9l-4.5 4 1 6-4.5-3-4.5 3 1-6L3 9l5.5-.5z" />
                </svg>
                {{ badge.label }}
            </div>
        </div>
    </div>
</template>
