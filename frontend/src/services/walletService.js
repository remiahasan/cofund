import api from './api'

export const walletService = {
    getBalance: () => api.get('/wallet'),
    topup: (amount) => api.post('/wallet/topup', { amount }),
    withdraw: (amount) => api.post('/wallet/withdraw', { amount }),
}

export const transactionService = {
    getAll: () => api.get('/transaction'),
}