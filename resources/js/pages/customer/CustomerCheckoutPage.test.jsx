import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import CustomerCheckoutPage from './CustomerCheckoutPage.jsx';

const checkout = {
    cart: {
        items: [
            {
                product_id: 3,
                name: 'Checkout Serum',
                quantity: 2,
                sale_price: 270000,
                subtotal: 540000,
                image: '/adm/images/godakeben450x170.jpg',
                url: '/products/3',
            },
        ],
        total: 540000,
    },
    feeShips: [{ id: 9, label: 'City HCM', price: 25000 }],
    paymentMethods: { 0: 'Cash', 1: 'Bank transfer' },
};

describe('CustomerCheckoutPage', () => {
    it('renders checkout form, cart summary and payment options', () => {
        render(
            <CustomerCheckoutPage
                checkout={checkout}
                navItems={[{ label: 'Cart', href: '/cart' }]}
            />,
        );

        expect(screen.getByRole('banner')).toHaveClass('react-customer-layout__header');
        expect(screen.getByRole('heading', { name: 'Thanh toán' })).toBeInTheDocument();
        expect(screen.getByLabelText('Full name')).toHaveAttribute('name', 'shipping_fullname');
        expect(screen.getByLabelText('Mobile')).toHaveAttribute('name', 'shipping_mobile');
        expect(screen.getByLabelText('Address')).toHaveAttribute('name', 'shipping_housenumber_street');
        expect(screen.getByLabelText('Shipping fee')).toHaveValue('9');
        expect(screen.getByLabelText('Payment method')).toHaveValue('0');
        expect(screen.getByText('Checkout Serum')).toBeInTheDocument();
        expect(screen.getByText(/540\.000/)).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Place order' })).toBeEnabled();
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('renders empty cart checkout state', () => {
        render(<CustomerCheckoutPage checkout={{ ...checkout, cart: { items: [], total: 0 } }} />);

        expect(screen.getByText('Your cart is empty.')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Back to products' })).toHaveAttribute('href', '/products');
    });
});
