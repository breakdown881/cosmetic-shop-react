import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import CustomerCartPage from './CustomerCartPage.jsx';

const cart = {
    items: [
        {
            product_id: 5,
            name: 'Cart Serum',
            quantity: 2,
            sale_price: 200000,
            subtotal: 400000,
            image: '/adm/images/godakeben450x170.jpg',
            url: '/products/5',
        },
    ],
    total: 400000,
};

describe('CustomerCartPage', () => {
    it('renders cart items, total and checkout link in customer layout', () => {
        render(<CustomerCartPage cart={cart} navItems={[{ label: 'Products', href: '/products' }]} />);

        expect(screen.getByRole('banner')).toHaveClass('react-customer-layout__header');
        expect(screen.getByRole('heading', { name: 'Giỏ hàng' })).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Cart Serum' })).toHaveAttribute('href', '/products/5');
        expect(screen.getByDisplayValue('2')).toHaveAttribute('name', 'quantity');
        expect(screen.getAllByText(/400\.000/)[0]).toBeInTheDocument();
        expect(screen.getByLabelText('Voucher code')).toHaveAttribute('name', 'discount_code');
        expect(screen.getByRole('form', { name: 'Validate voucher' })).toHaveAttribute('action', '/cart/vouchers/validate');
        expect(screen.getByRole('link', { name: 'Checkout' })).toHaveAttribute('href', '/checkout');
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('renders an empty cart state', () => {
        render(<CustomerCartPage cart={{ items: [], total: 0 }} />);

        expect(screen.getByText('Your cart is empty.')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Continue shopping' })).toHaveAttribute('href', '/products');
    });
});
