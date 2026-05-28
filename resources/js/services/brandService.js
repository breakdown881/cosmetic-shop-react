import { get } from './apiClient.js';

export const listBrands = () => get('/api/brands');
