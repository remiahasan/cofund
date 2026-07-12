import api from './api'

export const tierService = {
    getAll: (params) => api.get('/campaign-tier', { params }),
    store: (campaignId, data) => api.post('/campaign-tier', {
        campaign_id: campaignId,
        name: data.name,
        minimum_amount: data.minimum_amount,
        quota: data.quota,
        reward_description: data.reward_description,
    }),
    update: (tierId, data) => api.put(`/campaign-tier/${tierId}`, data),
    destroy: (tierId) => api.delete(`/campaign-tier/${tierId}`),
}