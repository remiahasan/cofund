import api from './api'

export const backingService = {
    store: (campaignId, data) => api.post(`/campaigns/${campaignId}/back`, data),
    getMine: (params) => api.get('/backings/mine', { params }),
}