import { defineStore } from 'pinia';
import api from '../lib/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        initialized: false,
    }),

    getters: {
        isAuthenticated: (state) => state.user !== null,
    },

    actions: {
        async fetchUser() {
            try {
                const { data } = await api.get('/api/user');
                this.user = data.data ?? data;
            } catch {
                this.user = null;
            } finally {
                this.initialized = true;
            }
        },

        async login(credentials) {
            await api.get('/sanctum/csrf-cookie');
            await api.post('/login', credentials);
            await this.fetchUser();
        },

        async register(payload) {
            await api.get('/sanctum/csrf-cookie');
            await api.post('/register', payload);
            await this.fetchUser();
        },

        async logout() {
            await api.get('/sanctum/csrf-cookie');
            await api.post('/logout');
            this.user = null;
        },
    },
});
