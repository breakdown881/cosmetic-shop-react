import { get } from './apiClient.js';

export const listCategories = () => get('/api/categories');
