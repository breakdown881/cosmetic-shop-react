import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import PublicHeader from './PublicHeader.jsx';

const baseProps = {
    activeRoute: 'product.index',
    cartCount: 3,
    csrfToken: 'csrf-token',
    searchValue: 'serum',
    socialLinks: [{ href: 'https://facebook.test', icon: 'fab fa-facebook-f' }],
    urls: {
        bannerImage: '/images/banner.jpg',
        contact: '/contact',
        customerAddress: '/customer/address',
        customerOrders: '/customer/orders',
        customerShow: '/customer',
        home: '/',
        logoImage: '/images/logo.jpg',
        logout: '/logout',
        products: '/products',
    },
};

describe('PublicHeader', () => {
    it('renders guest header with nav, search and cart selectors', () => {
        render(<PublicHeader {...baseProps} />);

        expect(screen.getAllByRole('link', { name: 'Sản phẩm' })[0]).toHaveAttribute('href', '/products');
        expect(screen.getByRole('searchbox')).toHaveValue('serum');
        expect(screen.getByRole('searchbox')).toHaveAttribute('name', 'search');
        expect(document.querySelector('.btn-register')).not.toBeNull();
        expect(document.querySelector('.btn-login')).not.toBeNull();
        expect(document.querySelector('.btn-cart-detail .number-total-product')).toHaveTextContent('3');
    });

    it('renders authenticated account menu and logout form', () => {
        render(<PublicHeader {...baseProps} isAuthenticated userName="Alice" />);

        expect(screen.getByText('Alice')).toBeInTheDocument();
        expect(screen.getAllByText('Đơn hàng của tôi')[0]).toHaveAttribute('href', '/customer/orders');
        expect(document.getElementById('logout-form')).toHaveAttribute('action', '/logout');
        expect(screen.getByDisplayValue('csrf-token')).toHaveAttribute('name', '_token');
    });
});
