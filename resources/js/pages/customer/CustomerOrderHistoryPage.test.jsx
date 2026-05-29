import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it } from 'vitest';
import CustomerOrderHistoryPage from './CustomerOrderHistoryPage.jsx';

const orders = [
    {
        id: 12,
        status: 'PENDING',
        shipping_fullname: 'Owned Order Name',
        payment_total: 180000,
        created_at: '2026-05-29 09:00:00',
        items: [
            {
                product_id: 7,
                product_name: 'Owned Serum',
                qty: 2,
                unit_price: 90000,
                total_price: 180000,
            },
        ],
    },
];

describe('CustomerOrderHistoryPage', () => {
    it('renders authenticated customer order history', () => {
        render(
            <CustomerOrderHistoryPage
                navItems={[{ label: 'Products', href: '/products' }]}
                orders={orders}
            />,
        );

        expect(screen.getByRole('banner')).toHaveClass('react-customer-layout__header');
        expect(screen.getByRole('heading', { name: 'Đơn hàng của tôi' })).toBeInTheDocument();
        expect(screen.getByText('Owned Order Name')).toBeInTheDocument();
        expect(screen.getByText('PENDING')).toBeInTheDocument();
        expect(screen.getByText('Owned Serum')).toBeInTheDocument();
        expect(screen.getByText(/180\.000/)).toBeInTheDocument();
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('renders login prompt for guests', () => {
        render(<CustomerOrderHistoryPage requiresAuth orders={[]} />);

        expect(screen.getByText('Please sign in to view your orders.')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Continue shopping' })).toHaveAttribute('href', '/products');
    });

    it('renders empty order state', () => {
        render(<CustomerOrderHistoryPage orders={[]} />);

        expect(screen.getByText('You have no orders yet.')).toBeInTheDocument();
    });

    it('paginates long customer order history lists', async () => {
        const user = userEvent.setup();
        const manyOrders = Array.from({ length: 11 }, (_, index) => ({
            ...orders[0],
            id: index + 1,
            shipping_fullname: `Order ${index + 1}`,
        }));

        render(<CustomerOrderHistoryPage orders={manyOrders} />);

        expect(screen.getByText('Order 1')).toBeInTheDocument();
        expect(screen.queryByText('Order 11')).not.toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Next' }));

        expect(screen.getByText('Order 11')).toBeInTheDocument();
        expect(screen.queryByText('Order 1')).not.toBeInTheDocument();
    });
});
