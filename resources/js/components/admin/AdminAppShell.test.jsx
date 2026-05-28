import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import AdminAppShell from './AdminAppShell.jsx';

describe('AdminAppShell', () => {
    it('renders admin chrome and selected React page component', () => {
        render(
            <AdminAppShell
                topNav={{ brandUrl: '/admin', userName: 'Admin', labels: { brand: 'Goda', hello: 'Chao', logout: 'Logout' } }}
                sidebar={{
                    items: [
                        { active: true, href: '/admin', icon: 'fas fa-fw fa-tachometer-alt', label: 'Overview' },
                    ],
                }}
                footer={{ csrfToken: 'csrf-token', logoutUrl: '/admin/logout', labels: { exit: 'Exit' } }}
                page={{
                    component: 'AdminDashboard',
                    props: {
                        labels: { emptyOrders: 'No orders.' },
                        metrics: [{ key: 'orders', label: 'Orders', value: 2 }],
                        orders: [],
                    },
                }}
            />,
        );

        expect(screen.getByRole('link', { name: 'Goda' })).toHaveAttribute('href', '/admin');
        expect(screen.getByRole('link', { name: /Overview/ })).toHaveAttribute('href', '/admin');
        expect(screen.getByText('Orders 2')).toBeInTheDocument();
        expect(screen.getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
    });
});
