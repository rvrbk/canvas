import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../pages/Home.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/boards/:id/draw',
        name: 'draw',
        component: () => import('../pages/Draw.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/boards/:id',
        name: 'board',
        component: () => import('../pages/Board.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/leaderboard',
        name: 'leaderboard',
        component: () => import('../pages/Leaderboard.vue'),
        meta: { requiresAuth: true },
    },
    {
        path: '/login',
        name: 'login',
        component: () => import('../pages/Login.vue'),
        meta: { guestOnly: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('../pages/Register.vue'),
        meta: { guestOnly: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const auth = useAuthStore();

    if (!auth.initialized) {
        await auth.fetchUser();
    }

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return { name: 'login' };
    }

    if (to.meta.guestOnly && auth.isAuthenticated) {
        return { name: 'home' };
    }

    return true;
});

// Track SPA page views in Google Analytics 4.
// The gtag snippet in app.blade.php sets send_page_view: false so only
// this hook fires page_view events, preventing a duplicate on the initial load.
router.afterEach((to) => {
    if (typeof window.gtag !== 'function') return;
    window.gtag('event', 'page_view', {
        page_path: to.fullPath,
        page_title: document.title,
    });
});

export default router;
