<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getBoard, submitContribution } from '../lib/boards';
import { useStatsStore } from '../stores/stats';
import { burstConfetti } from '../lib/confetti';
import DrawingCanvas from '../components/DrawingCanvas.vue';

const route = useRoute();
const router = useRouter();
const statsStore = useStatsStore();

const board = ref(null);
const canvasRef = ref(null);
const submitting = ref(false);
const error = ref('');
const celebrating = ref(false);
const newBadges = ref([]);

onMounted(async () => {
    board.value = await getBoard(route.params.id);

    if (board.value.status !== 'open' || board.value.has_contributed) {
        router.replace({ name: 'board', params: { id: route.params.id } });
    }

    if (!statsStore.stats) {
        await statsStore.fetch();
    }
});

async function handleSubmit() {
    if (!canvasRef.value.hasStrokes) return;
    submitting.value = true;
    error.value = '';

    const previouslyAchieved = new Set(
        (statsStore.stats?.badges ?? []).filter((b) => b.achieved).map((b) => b.key),
    );

    try {
        await submitContribution(route.params.id, canvasRef.value.strokes);
        await statsStore.fetch();

        newBadges.value = (statsStore.stats?.badges ?? []).filter(
            (b) => b.achieved && !previouslyAchieved.has(b.key),
        );

        celebrating.value = true;
        burstConfetti();

        setTimeout(() => {
            router.push({ name: 'board', params: { id: route.params.id } });
        }, 1100);
    } catch (err) {
        error.value = err.response?.data?.message ?? 'Could not submit your drawing.';
        submitting.value = false;
    }
}
</script>

<template>
    <div v-if="board" class="flex flex-col items-center gap-4">
        <template v-if="celebrating">
            <h1 class="rotate-1 text-center font-display text-3xl text-brand-red sm:text-4xl">
                NICE! YOUR PIECE<br />JUST JOINED THE MIX.
            </h1>
            <p class="text-sm font-bold tracking-wide uppercase">+10 points</p>
            <div v-if="newBadges.length" class="mt-2 flex flex-col items-center gap-2">
                <p class="text-xs font-bold tracking-wide uppercase text-brand-blue">Badge unlocked!</p>
                <div
                    v-for="badge in newBadges"
                    :key="badge.key"
                    class="border-2 border-ink bg-brand-yellow px-4 py-1.5 text-xs font-bold tracking-wide uppercase"
                >
                    {{ badge.label }}
                </div>
            </div>
        </template>

        <template v-else>
            <h1 class="font-display text-2xl">{{ board.name.toUpperCase() }}</h1>
            <p class="max-w-md text-center text-sm font-medium">
                You're drawing blind — you won't see anyone else's contribution until the artwork
                is complete. Draw whatever you like, then submit for 10 points!
            </p>
            <DrawingCanvas
                ref="canvasRef"
                :width="board.width"
                :height="board.height"
                :background-color="board.background_color"
            />
            <p v-if="error" class="text-sm font-medium text-brand-red">{{ error }}</p>
            <button
                type="button"
                :disabled="submitting"
                class="border-2 border-ink bg-brand-red px-6 py-2.5 text-sm font-bold tracking-wide text-paper uppercase shadow-[4px_4px_0_#0e0e0e] transition hover:-translate-y-0.5 active:translate-y-0 active:shadow-none disabled:opacity-50"
                @click="handleSubmit"
            >
                {{ submitting ? 'Submitting…' : 'Submit my drawing' }}
            </button>
        </template>
    </div>
</template>
