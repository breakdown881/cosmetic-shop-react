import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import PublicFooter from './PublicFooter.jsx';

describe('PublicFooter', () => {
    it('renders footer links, newsletter form and back-to-top element', () => {
        render(
            <PublicFooter
                categoryLinks={[{ href: '/category/kem', label: 'Kem Chống Nắng' }]}
                policyLinks={[{ href: '/products', label: 'Sản phẩm' }]}
                socialLinks={[{ href: 'https://facebook.test', icon: 'fab fa-facebook-f' }]}
            />,
        );

        expect(screen.getByRole('link', { name: 'Kem Chống Nắng' })).toHaveAttribute('href', '/category/kem');
        expect(screen.getByRole('link', { name: 'Sản phẩm' })).toHaveAttribute('href', '/products');
        expect(screen.getByPlaceholderText('Nhập email của bạn..')).toHaveAttribute('name', 'email');
        expect(document.querySelector('.back-to-top')).toHaveTextContent('▲');
    });
});
