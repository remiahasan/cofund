import api from './api'

export const adminService = {
    getAllCampaigns: (status) => api.get('/admin/campaign', { params: { status } }),
    getPendingCampaigns: () => api.get('/admin/campaign/review'),
    getCampaignDetail: (id) => api.get(`/admin/campaign/${id}`),
    approveCampaign: (id) => api.patch(`/admin/campaign/${id}/approve`),
    rejectCampaign: (id, reason) => api.patch(`/admin/campaign/${id}/reject`, { reason }),
    forceFailCampaign: (id) => api.patch(`/admin/campaign/${id}/force-fail`),

    getUsers: () => api.get('/admin/user'),
    getUserDetail: (id) => api.get(`/admin/user/${id}`),

    getOverview: () => api.get('/admin/dashboard'),
    getOverviewChart: () => api.get('/admin/dashboard/funding-chart'),
}