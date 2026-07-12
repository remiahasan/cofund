import api from './api'

export const dashboardService = {
    getCreatorDashboard: () => api.get('/dashboard/creator'),
    getCreatorFundingChart: () => api.get('/dashboard/funding-chart'),
    getBackerDashboard: () => api.get('/dashboard/backer'),
}