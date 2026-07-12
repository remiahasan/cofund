import api from './api'

export const dashboardService = {
    getCreatorDashboard: () => api.get('/dashboard/creator'), // { summary, campaigns }
    getCreatorFundingChart: () => api.get('/dashboard/funding-chart'),
    getBackerDashboard: () => api.get('/dashboard/backer'),
}