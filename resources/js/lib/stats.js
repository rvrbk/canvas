import api from './api';

export async function getStats() {
    const { data } = await api.get('/api/stats');
    return data.data;
}

export async function getLeaderboard() {
    const { data } = await api.get('/api/leaderboard');
    return data.data;
}
