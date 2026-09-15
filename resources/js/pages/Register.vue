<script setup>
import { reactive, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import GoogleButton from '../components/GoogleButton.vue';

const auth = useAuthStore();
const router = useRouter();

const form = reactive({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
const errors = ref({});
const submitting = ref(false);

async function submit() {
    submitting.value = true;
    errors.value = {};

    try {
        await auth.register(form);
        router.push({ name: 'home' });
    } catch (error) {
        errors.value = error.response?.data?.errors ?? {};
    } finally {
        submitting.value = false;
    }
}
</script>

<template>
    <div class="mx-auto max-w-sm">
        <h1 class="mb-6 font-display text-2xl -rotate-1">CREATE YOUR ACCOUNT</h1>

        <GoogleButton label="Sign up with Google" />

        <div class="my-5 flex items-center gap-3 text-xs font-bold tracking-wide text-ink/50">
            <div class="h-px flex-1 bg-ink/20" />
            OR
            <div class="h-px flex-1 bg-ink/20" />
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <label class="block text-xs font-bold tracking-wide uppercase" for="name">Name</label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1.5 w-full border-2 border-ink bg-paper px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue"
                />
                <p v-if="errors.name" class="mt-1 text-sm font-medium text-brand-red">{{ errors.name[0] }}</p>
            </div>

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
                <p v-if="errors.password" class="mt-1 text-sm font-medium text-brand-red">{{ errors.password[0] }}</p>
            </div>

            <div>
                <label class="block text-xs font-bold tracking-wide uppercase" for="password_confirmation">
                    Confirm password
                </label>
                <input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
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
                Sign up
            </button>
        </form>

        <p class="mt-5 text-sm font-medium">
            Already have an account?
            <router-link :to="{ name: 'login' }" class="font-bold text-brand-blue hover:underline">Log in</router-link>
        </p>
    </div>
</template>
