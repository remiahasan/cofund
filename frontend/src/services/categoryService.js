import api from "./api";

export const categoryService = {
    getAll: (params) => api.get('/categories', { params })
}