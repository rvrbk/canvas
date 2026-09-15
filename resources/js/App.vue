<script setup>
import { onMounted, watch, ref } from 'vue';
import { useAuthStore } from './stores/auth';
import { useStatsStore } from './stores/stats';
import { useRouter, useRoute } from 'vue-router';
import Footer from './components/Footer.vue';
import Logo from './components/Logo.vue';

const auth = useAuthStore();
const statsStore = useStatsStore();
const router = useRouter();
const route = useRoute();

const mobileMenuOpen = ref(false);

onMounted(async () => {
    if (!auth.initialized) {
        await auth.fetchUser();
    }
    if (auth.isAuthenticated) {
        statsStore.fetch();
    }
});

watch(
    () => auth.isAuthenticated,
    (isAuthenticated) => {
        if (isAuthenticated) {
            statsStore.fetch();
        }
    },
);

watch(
    () => route.fullPath,
    () => {
        mobileMenuOpen.value = false;
    },
);

async function handleLogout() {
    mobileMenuOpen.value = false;
    await auth.logout();
    router.push({ name: 'login' });
}
</script>

<template>
    <div class="flex min-h-screen flex-col bg-paper text-ink">
        <header class="relative border-b-4 border-ink bg-paper">
            <div class="mx-auto flex max-w-3xl items-center justify-between px-3 py-2.5 sm:px-4 sm:py-3">
                <router-link
                    :to="{ name: 'home' }"
                    class="flex shrink-0 items-center gap-1.5 whitespace-nowrap font-display text-base tracking-tight sm:gap-2 sm:text-lg"
                >
                    <Logo :size="26" />
                    COLLECTIVE CANVAS
                </router-link>

                <!-- Desktop nav -->
                <nav class="hidden shrink-0 items-center gap-5 text-xs font-bold tracking-wide uppercase sm:flex">
                    <template v-if="auth.isAuthenticated">
                        <router-link :to="{ name: 'leaderboard' }" class="transition hover:text-brand-blue">
                            Leaderboard
                        </router-link>
                        <span
                            v-if="statsStore.stats"
                            class="border-2 border-ink bg-brand-yellow px-2.5 py-1 whitespace-nowrap"
                        >
                            {{ statsStore.stats.points }} pts
                        </span>
                        <span class="whitespace-nowrap">{{ auth.user?.name }}</span>
                        <button type="button" class="whitespace-nowrap transition hover:text-brand-red" @click="handleLogout">
                            Log out
                        </button>
                    </template>
                    <template v-else>
                        <router-link :to="{ name: 'login' }" class="whitespace-nowrap transition hover:text-brand-blue">
                            Log in
                        </router-link>
                        <router-link
                            :to="{ name: 'register' }"
                            class="border-2 border-ink bg-brand-red px-3 py-1.5 whitespace-nowrap text-paper transition hover:-translate-y-0.5 active:translate-y-0"
                        >
                            Sign up
                        </router-link>
                    </template>
                </nav>

                <!-- Mobile hamburger -->
                <button
                    type="button"
                    class="flex h-9 w-9 shrink-0 flex-col items-center justify-center gap-1.5 border-2 border-ink transition active:scale-95 sm:hidden"
                    aria-label="Toggle menu"
                    :aria-expanded="mobileMenuOpen"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                >
                    <span
                        class="block h-0.5 w-4 bg-ink transition"
                        :class="mobileMenuOpen ? 'translate-y-[3px] rotate-45' : ''"
                    />
                    <span
                        class="block h-0.5 w-4 bg-ink transition"
                        :class="mobileMenuOpen ? '-translate-y-[3px] -rotate-45' : ''"
                    />
                </button>
            </div>

            <!-- Mobile dropdown panel -->
            <Transition
                enter-active-class="transition ease-out duration-150"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-100"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
            >
                <div v-if="mobileMenuOpen" class="absolute inset-x-0 top-full z-10 border-b-4 border-ink bg-paper sm:hidden">
                    <nav class="flex flex-col gap-4 px-4 py-4 text-sm font-bold tracking-wide uppercase">
                        <template v-if="auth.isAuthenticated">
                            <router-link :to="{ name: 'home' }" class="transition active:scale-95">Home</router-link>
                            <router-link :to="{ name: 'leaderboard' }" class="transition active:scale-95">
                                Leaderboard
                            </router-link>
                            <span v-if="statsStore.stats" class="normal-case">
                                {{ statsStore.stats.points }} pts · Level {{ statsStore.stats.level }}
                            </span>
                            <span class="normal-case text-ink/60">Signed in as {{ auth.user?.name }}</span>
                            <button type="button" class="text-left transition active:scale-95" @click="handleLogout">
                                Log out
                            </button>
                        </template>
                        <template v-else>
                            <router-link to="/login" class="transition active:scale-95">Log in</router-link>
                            <router-link
                                to="/register"
                                class="w-fit border-2 border-ink bg-brand-red px-3.5 py-1.5 text-paper transition active:scale-95"
                            >
                                Sign up
                            </router-link>
                        </template>
                    </nav>
                </div>
            </Transition>
        </header>

        <main class="mx-auto w-full max-w-3xl flex-1 px-4 py-10">
            <router-view />
        </main>

        <Footer />
    </div>
</template>
