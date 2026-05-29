import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import AdminOrderManager from './AdminOrderPage.jsx';

describe('AdminOrderManager', () => {
    beforeEach(() => {
        window.axios = {
            get: vi.fn(),
            post: vi.fn(),
            patch: vi.fn(),
            delete: vi.fn(),
        };
    });

    afterEach(() => {
        vi.restoreAllMocks();
        delete window.axios;
    });

    it('paginates long admin order tables', async () => {
        const user = userEvent.setup();
        window.axios.get.mockResolvedValueOnce({
            data: {
                data: Array.from({ length: 11 }, (_, index) => ({
                    id: index + 1,
                    customer_name: `Customer ${index + 1}`,
                    status: 'PENDING',
                    payment_method_label: 'Cash',
                    payment_total: 100000,
                    note: `Order note ${index + 1}`,
                    created_at: '2026-05-29 10:00:00',
                    items: [],
                })),
            },
        });

        render(
            <AdminOrderManager
                apiUrl="/admin/api/orders"
                canCreate={false}
                canDelete={false}
                canEdit={false}
                paymentMethods={{ 0: 'Cash' }}
                statusOptions={['PENDING']}
            />,
        );

        expect(await screen.findByText('Order note 1')).toBeInTheDocument();
        expect(screen.queryByText('Order note 11')).not.toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Next' }));

        expect(screen.getByText('Order note 11')).toBeInTheDocument();
        expect(screen.queryByText('Order note 1')).not.toBeInTheDocument();
    });
});
