import api from './api'

export const transactionService = {
    getMine: (params) => api.get('/wallet/transactions', { params }),
}