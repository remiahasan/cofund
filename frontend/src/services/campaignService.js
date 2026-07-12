import api from './api'

export const campaignService = {
    getAll: (params) => api.get('/campaign', { params }),
    getOne: (id) => api.get(`/campaign/${id}`),
    store: (payload) => api.post('/campaign', payload),
    update: (id, payload) => api.put(`/campaign/${id}`, payload),
    destroy: (id) => api.delete(`/campaign/${id}`),
    submitToReview: (id) => api.patch(`/campaign/${id}/to-review`),
}