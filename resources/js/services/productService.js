import { get } from './apiClient.js';

export const listProducts = (params = {}) =>
    get('/api/products', {
        params,
    });

export const searchProducts = (params = {}) =>
    get('/api/products/search', {
        params,
    });
