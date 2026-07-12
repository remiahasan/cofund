import api from './api'

export const campaignImageService = {
    getAll: (campaignId) => api.get(`/campaign/${campaignId}/images`),
    store: (campaignId, files) => {
        const formData = new FormData()
        files.forEach(file => formData.append('images[]', file))
        return api.post(`/campaign/${campaignId}/images`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
    },
    update: (campaignId, imageId, file) => {
        const formData = new FormData()
        formData.append('image', file)
        return api.put(`/campaign/${campaignId}/images/${imageId}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        })
    },
    destroy: (campaignId, imageId) => api.delete(`/campaign/${campaignId}/images/${imageId}`),
    setPrimary: (imageId) => api.patch(`/campaign-image/${imageId}/set-primary`),
}