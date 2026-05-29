import { render, screen, waitFor } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import AdminSpaApp from './AdminSpaApp.jsx';

const dashboardPayload = {
    metrics: [{ key: 'orders', label: 'Orders', value: 2 }],
    orders: [],
    periods: [],
    labels: { emptyOrders: 'No orders.' },
};

describe('AdminSpaApp', () => {
    beforeEach(() => {
        window.history.pushState({}, '', '/admin');
        window.axios = {
            delete: vi.fn().mockResolvedValue({ data: null }),
            get: vi.fn((url) => Promise.resolve({
                data: url === '/admin/api/dashboard' ? dashboardPayload : { data: [] },
            })),
            patch: vi.fn().mockResolvedValue({ data: {} }),
            post: vi.fn().mockResolvedValue({ data: {} }),
        };
    });

    afterEach(() => {
        vi.restoreAllMocks();
        delete window.axios;
    });

    it('renders admin chrome and loads dashboard data from the Laravel API', async () => {
        render(<AdminSpaApp csrfToken="csrf-token" logoutUrl="/admin/logout" role="MANAGER" userName="Admin" />);

        expect(screen.getByRole('link', { name: 'Goda' })).toHaveAttribute('href', '/admin');
        expect(await screen.findByText('Orders 2')).toBeInTheDocument();
        expect(window.axios.get).toHaveBeenCalledWith('/admin/api/dashboard', {});
        expect(screen.getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
    });

    it('uses client-side navigation and loads the selected resource API', async () => {
        const user = userEvent.setup();

        render(<AdminSpaApp csrfToken="csrf-token" logoutUrl="/admin/logout" role="MANAGER" userName="Admin" />);
        await screen.findByText('Orders 2');

        await user.click(document.querySelector('a[href="/admin/brands"]'));

        expect(window.location.pathname).toBe('/admin/brands');
        await waitFor(() => expect(window.axios.get).toHaveBeenCalledWith('/admin/api/brands', {}));
    });
});
