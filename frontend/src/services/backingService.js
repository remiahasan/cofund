import api from './api'

export const backingService = {
    getForCampaign: (campaignId, params) => api.get(`/campaign/${campaignId}/backing`, { params }),
    store: (campaignId, payload) => api.post('/backing', {
        campaign_id: campaignId,
        campaign_tier_id: payload.tier_id ?? null,
        amount: payload.amount ?? null,
    }),
    show: (id) => api.get(`/backing/${id}`),
}