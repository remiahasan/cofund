import api from "./api";

export const campaignService = {
    getAll: (params) => api.get('/campaigns', { params }),
    getOne: (id) => api.get(`/campaigns/${id}`),
    getMine: (params) => api.get('/campaigns/mine', { params }),
    store: (formData) => api.post('/campaigns', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }),
    update: (id, formData) => api.post(`/campaigns/${id}?_method=put`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
    }),
    submit: (id) => api.post(`/campaigns/${id}/submit`),
}