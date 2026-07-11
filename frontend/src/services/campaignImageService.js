import api from './api'

export const campaignImageService = {
    destroy: (campaignId, imageId) => api.delete(`/campaigns/${campaignId}/images/${imageId}`),
}