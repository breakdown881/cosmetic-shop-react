import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import PublicWelcomePage from './PublicWelcomePage.jsx';

describe('PublicWelcomePage', () => {
    it('renders auth links, cards and version', () => {
        render(
            <PublicWelcomePage
                title="Goda Shop"
                description="React storefront"
                version="Laravel v11"
                authLinks={[{ href: '/login', label: 'Login' }]}
                cards={[{ href: '/products', title: 'Sản phẩm', description: 'Xem sản phẩm' }]}
            />,
        );

        expect(screen.getByRole('heading', { name: 'Goda Shop' })).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Login' })).toHaveAttribute('href', '/login');
        expect(screen.getByRole('link', { name: /Sản phẩm/ })).toHaveAttribute('href', '/products');
        expect(screen.getByText('Laravel v11')).toBeInTheDocument();
    });
});
