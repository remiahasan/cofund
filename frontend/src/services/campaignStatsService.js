import api from './api'

export const campaignStatsService = {
    getFundingStats: (campaignId) => api.get(`/campaigns/${campaignId}/funding-stats`),
}