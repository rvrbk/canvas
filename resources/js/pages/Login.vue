<script setup>
import { reactive, ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import GoogleButton from '../components/GoogleButton.vue';

const auth = useAuthStore();
const router = useRouter();
const route = useRoute();

const form = reactive({ email: '', password: '' });
const errors = ref({});
const submitting = ref(false);

const socialErrorMessages = {
    auth_failed: 'Google sign-in failed. Please try again.',
    email_required: 'Your Google account has no email address we can use.',
};
const socialError = route.query.social_error
    ? (socialErrorMessages[route.query.social_error] ?? 'Google sign-in failed. Please try again.')
    : '';

async function submit() {
    submitting.value = true;
    errors.value = {};

    try {
        await auth.login(form);
        router.push({ name: 'home' });
    } catch (error) {
        errors.value = error.response?.data?.errors ?? { email: ['Unable to log in.'] };
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div class="mx-auto max-w-sm">
        <h1 class="mb-6 font-display text-2xl -rotate-1">LOG IN</h1>

        <p v-if="socialError" class="mb-4 border-2 border-brand-red bg-red-50 px-3 py-2 text-sm font-medium text-brand-red">
            {{ socialError }}
        </p>

        <GoogleButton />

        <div class="my-5 flex items-center gap-3 text-xs font-bold tracking-wide text-ink/50">
            <div class="h-px flex-1 bg-ink/20" />
            OR
            <div class="h-px flex-1 bg-ink/20" />
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <label class="block text-xs font-bold tracking-wide uppercase" for="email">Email</label>
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="mt-1.5 w-full border-2 border-ink bg-paper px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue"
                />
                <p v-if="errors.email" class="mt-1 text-sm font-medium text-brand-red">{{ errors.email[0] }}</p>
            </div>

            <div>
                <label class="block text-xs font-bold tracking-wide uppercase" for="password">Password</label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    required
                    class="mt-1.5 w-full border-2 border-ink bg-paper px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue"
                />
            </div>

            <button
                type="submit"
                :disabled="submitting"
                class="w-full border-2 border-ink bg-brand-red py-2.5 text-sm font-bold tracking-wide text-paper uppercase shadow-[4px_4px_0_#0e0e0e] transition hover:-translate-y-0.5 active:translate-y-0 active:shadow-none disabled:opacity-50"
            >
                Log in
            </button>
        </form>

        <p class="mt-5 text-sm font-medium">
            No account?
            <router-link :to="{ name: 'register' }" class="font-bold text-brand-blue hover:underline">Sign up</router-link>
        </p>
    </div>
</template>
