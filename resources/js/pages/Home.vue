<script setup>
import { ref, onMounted } from 'vue';
import { listBoards } from '../lib/boards';
import StatsWidget from '../components/StatsWidget.vue';

const boards = ref([]);
const loading = ref(true);

onMounted(async () => {
    boards.value = await listBoards();
    loading.value = false;
});
</script>

<template>
    <div>
        <div class="relative">
            <div class="pointer-events-none absolute -top-6 right-0 h-56 w-56 overflow-visible sm:h-64 sm:w-64">
                <div class="absolute top-6 right-10 h-40 w-40 rounded-full bg-brand-yellow opacity-90 sm:h-48 sm:w-48" />
                <div class="absolute top-16 right-24 h-36 w-36 rounded-full bg-brand-red opacity-85 sm:h-44 sm:w-44" />
                <div class="absolute top-0 right-32 h-32 w-32 rounded-full bg-brand-blue opacity-85 sm:h-40 sm:w-40" />
            </div>

            <div class="relative max-w-lg">
                <h1 class="-rotate-1 font-display text-4xl leading-[0.98] sm:text-5xl">
                    DRAW BLIND.<br />REVEAL TOGETHER.
                </h1>
                <p class="mt-4 text-sm leading-relaxed font-medium sm:text-base">
                    One canvas. No peeking. Everyone adds a piece — nobody sees the whole thing until
                    it's done. Every drawing earns you points.
                </p>
            </div>
        </div>

        <StatsWidget class="mt-8" />

        <p v-if="loading" class="mt-8 text-sm font-bold">LOADING…</p>
        <ul v-else class="mt-8 flex flex-col gap-5">
            <li
                v-for="board in boards"
                :key="board.id"
                class="flex flex-wrap items-center justify-between gap-4 border-4 border-ink bg-paper px-5 py-4"
                :class="board.status === 'completed' ? 'shadow-[6px_6px_0_#ffcc00]' : 'shadow-[6px_6px_0_#2b6bff]'"
            >
                <div>
                    <h2 class="font-display text-base sm:text-lg">{{ board.name }}</h2>
                    <p
                        class="mt-0.5 text-xs font-bold tracking-wide uppercase"
                        :class="board.status === 'completed' ? 'text-brand-blue' : 'text-ink/70'"
                    >
                        {{ board.status }}
                    </p>
                    <p v-if="board.status === 'open'" class="mt-1 text-xs font-bold">
                        {{ board.contributions_count }} / {{ board.max_contributions }} CONTRIBUTIONS
                    </p>
                </div>
                <router-link
                    v-if="board.status === 'open' && !board.has_contributed"
                    class="border-2 border-ink bg-brand-red px-4 py-2 text-xs font-bold tracking-wide whitespace-nowrap text-paper uppercase transition hover:-translate-y-0.5 active:translate-y-0"
                    :to="{ name: 'draw', params: { id: board.id } }"
                >
                    Add your drawing
                </router-link>
                <router-link
                    v-else
                    class="border-2 border-ink bg-paper px-4 py-2 text-xs font-bold tracking-wide whitespace-nowrap text-ink uppercase transition hover:-translate-y-0.5 active:translate-y-0"
                    :to="{ name: 'board', params: { id: board.id } }"
                >
                    {{ board.status === 'open' ? 'View progress' : 'View artwork' }}
                </router-link>
            </li>
        </ul>
    </div>
</template>
