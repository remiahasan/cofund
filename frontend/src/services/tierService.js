import api from './api'

export const tierService = {
    getAll: (campaignId) => api.get(`/campaigns/${campaignId}/tiers`),
    store: (campaignId, data) => api.post(`/campaigns/${campaignId}/tiers`, data),
    update: (campaignId, tierId, data) => api.put(`/campaigns/${campaignId}/tiers/${tierId}`, data),
    destroy: (campaignId, tierId) => api.delete(`/campaigns/${campaignId}/tiers/${tierId}`),
}