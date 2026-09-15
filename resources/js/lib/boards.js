import api from './api';

export async function listBoards() {
    const { data } = await api.get('/api/boards');
    return data.data;
}

export async function getBoard(id) {
    const { data } = await api.get(`/api/boards/${id}`);
    return data.data;
}

export async function submitContribution(id, strokes) {
    const { data } = await api.post(`/api/boards/${id}/contributions`, { strokes });
    return data.data;
}
