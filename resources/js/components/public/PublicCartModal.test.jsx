import { render, screen, within } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import PublicCartModal from './PublicCartModal.jsx';

describe('PublicCartModal', () => {
    it('renders bootstrap cart modal with legacy selectors and Laravel links', () => {
        render(
            <PublicCartModal
                cartUrl="/products"
                checkoutUrl="/payment/create"
                subtotal="120.000₫"
                items={[
                    {
                        id: 'row-1',
                        image: '/images/a.jpg',
                        name: 'Kem dưỡng',
                        price: 120000,
                        qty: 1,
                    },
                ]}
            />,
        );

        const modal = document.getElementById('modal-cart-detail');

        expect(modal).toHaveClass('modal', 'fade');
        expect(modal.querySelector('.cart-product')).not.toBeNull();
        expect(modal.querySelector('.price-total')).toHaveTextContent('120.000₫');
        expect(within(modal).getByText('Kem dưỡng')).toBeInTheDocument();
        expect(within(modal).getByRole('link', { name: 'Tiếp tục mua sắm' })).toHaveAttribute('href', '/products');
        expect(within(modal).getByRole('link', { name: 'Đặt hàng' })).toHaveAttribute('href', '/payment/create');
    });

    it('renders empty cart state', () => {
        render(<PublicCartModal items={[]} subtotal="0₫" />);

        expect(screen.getByText('Giỏ hàng đang trống.')).toBeInTheDocument();
    });
});
