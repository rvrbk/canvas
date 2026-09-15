<script setup>
import { ref, onMounted } from 'vue';
import { getLeaderboard } from '../lib/stats';

const entries = ref([]);
const loading = ref(true);

onMounted(async () => {
    entries.value = await getLeaderboard();
    loading.value = false;
});
</script>

<template>
    <div>
        <h1 class="-rotate-1 font-display text-3xl">LEADERBOARD</h1>
        <p class="mt-3 text-sm font-medium">The most prolific collective artists, ranked by contributions.</p>

        <p v-if="loading" class="mt-6 text-sm font-bold">LOADING…</p>
        <p v-else-if="entries.length === 0" class="mt-6 text-sm font-bold">
            No one has contributed yet — be the first!
        </p>
        <ol v-else class="mt-6 flex flex-col gap-3">
            <li
                v-for="(entry, index) in entries"
                :key="entry.id"
                class="flex items-center justify-between gap-3 border-4 border-ink bg-paper px-4 py-3"
                :class="index === 0 ? 'shadow-[6px_6px_0_#ffcc00]' : 'shadow-[4px_4px_0_#0e0e0e]'"
            >
                <div class="flex items-center gap-3">
                    <span
                        class="flex h-8 w-8 items-center justify-center border-2 border-ink font-display text-sm"
                        :class="index === 0 ? 'bg-brand-yellow' : 'bg-paper'"
                    >
                        {{ index + 1 }}
                    </span>
                    <span class="font-bold">{{ entry.name }}</span>
                </div>
                <div class="text-right text-sm font-bold">
                    <span class="text-brand-red">{{ entry.points }}</span> PTS
                    · {{ entry.contributions_count }} DRAWINGS
                </div>
            </li>
        </ol>
    </div>
</template>
