import api from './api'

export const adminService = {
    getPendingCampaigns: (params) => api.get('/admin/campaigns', { params: { ...params, status: 'review' } }),
    approveCampaign: (id) => api.post(`/admin/campaigns/${id}/approve`),
    rejectCampaign: (id, reason) => api.post(`/admin/campaigns/${id}/reject`, { reason }),

    getAllCampaigns: (params) => api.get('/admin/campaigns', { params }),
    getCampaignBackings: (id, params) => api.get(`/admin/campaigns/${id}/backings`, { params }),
    forceFailCampaign: (id) => api.post(`/admin/campaigns/${id}/force-fail`),

    getUsers: (params) => api.get('/admin/users', { params }),
    suspendUser: (id) => api.post(`/admin/users/${id}/suspend`),
    activateUser: (id) => api.post(`/admin/users/${id}/activate`),

    getPlatformOverview: () => api.get('/admin/overview'),
}