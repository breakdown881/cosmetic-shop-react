import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import CustomerOrderDetailPage from './CustomerOrderDetailPage.jsx';

const order = {
    id: 12,
    status: 'PENDING',
    canCancel: true,
    cancelUrl: '/orders/12/cancel',
    shipping_fullname: 'Detail Shipping Name',
    shipping_mobile: '0900111222',
    shipping_address: '123 Order Street',
    shipping_fee: 20000,
    discount_amount: 10000,
    sub_total: 210000,
    payment_total: 220000,
    created_at: '2026-05-29 09:00:00',
    items: [
        {
            product_id: 7,
            product_name: 'Detail Serum',
            qty: 3,
            unit_price: 70000,
            total_price: 210000,
        },
    ],
};

describe('CustomerOrderDetailPage', () => {
    it('renders order details and cancel form for pending order', () => {
        render(
            <CustomerOrderDetailPage
                csrfToken="csrf-token"
                navItems={[{ label: 'Orders', href: '/orders' }]}
                order={order}
            />,
        );

        expect(screen.getByRole('heading', { name: 'Order #12' })).toBeInTheDocument();
        expect(screen.getByText('Detail Shipping Name')).toBeInTheDocument();
        expect(screen.getByText('123 Order Street')).toBeInTheDocument();
        expect(screen.getByText('Detail Serum')).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Cancel order' })).toBeInTheDocument();
        expect(screen.getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('does not render cancel form for processed order', () => {
        render(<CustomerOrderDetailPage order={{ ...order, status: 'PROCESSING', canCancel: false }} />);

        expect(screen.queryByRole('button', { name: 'Cancel order' })).not.toBeInTheDocument();
        expect(screen.getByText('This order can no longer be cancelled.')).toBeInTheDocument();
    });
});
