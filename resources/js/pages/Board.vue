<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import { getBoard } from '../lib/boards';
import { getEcho } from '../lib/echo';
import { burstConfetti } from '../lib/confetti';
import CompositeCanvas from '../components/CompositeCanvas.vue';

const route = useRoute();

const board = ref(null);
const loading = ref(true);
let channel = null;

onMounted(async () => {
    board.value = await getBoard(route.params.id);
    loading.value = false;

    if (board.value.status === 'completed') {
        burstConfetti();
    }

    channel = getEcho().channel(`board.${route.params.id}`);

    channel.listen('.contribution.submitted', (payload) => {
        if (board.value.status !== 'open') return;
        board.value.contributions_count = payload.contributions_count;
        board.value.max_contributions = payload.max_contributions;
        if (payload.contribution && !board.value.contributions.some((c) => c.id === payload.contribution.id)) {
            board.value.contributions.push(payload.contribution);
        }
    });

    channel.listen('.board.completed', (payload) => {
        board.value = payload.board;
        burstConfetti({ particleCount: 220, duration: 2400 });
    });
});

onUnmounted(() => {
    if (channel) {
        getEcho().leaveChannel(`board.${route.params.id}`);
    }
});
</script>

<template>
    <div v-if="loading" class="text-sm font-bold">LOADING…</div>
    <div v-else class="flex flex-col items-center gap-5">
        <h1 class="-rotate-1 font-display text-2xl">{{ board.name.toUpperCase() }}</h1>

        <template v-if="board.status === 'open'">
            <p class="max-w-md text-center text-sm font-medium">
                This artwork is still being drawn blind, piece by piece. Here's a blurry glimpse —
                come back once it's complete for the big reveal!
            </p>
            <div class="relative">
                <CompositeCanvas
                    :width="board.width"
                    :height="board.height"
                    :background-color="board.background_color"
                    :contributions="board.contributions"
                    class="blur-xl"
                />
                <div class="pointer-events-none absolute inset-0 bg-paper/10" />
            </div>
            <div class="h-3.5 w-full max-w-md border-2 border-ink bg-paper">
                <div
                    class="h-full bg-brand-red transition-all"
                    :style="{ width: `${Math.min(100, (board.contributions_count / board.max_contributions) * 100)}%` }"
                />
            </div>
            <p class="text-sm font-bold">
                {{ board.contributions_count }} / {{ board.max_contributions }} CONTRIBUTIONS
            </p>
        </template>

        <template v-else>
            <p class="max-w-md text-center text-sm font-medium">
                Ta-da! The collective artwork is complete — here's what everyone drew together.
            </p>
            <CompositeCanvas
                :width="board.width"
                :height="board.height"
                :background-color="board.background_color"
                :contributions="board.contributions"
            />
        </template>
    </div>
</template>
