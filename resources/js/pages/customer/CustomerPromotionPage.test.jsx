import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import CustomerPromotionPage from './CustomerPromotionPage.jsx';

describe('CustomerPromotionPage', () => {
    it('renders active vouchers for customer site', () => {
        render(
            <CustomerPromotionPage
                promotions={[
                    {
                        code: 'BEAUTY50',
                        description: 'Giam 50.000 cho don skincare',
                        label: '50.000 VND',
                        expires_at: '2026-05-30 10:00:00',
                    },
                ]}
                navItems={[{ label: 'Products', href: '/products' }]}
            />,
        );

        expect(screen.getByRole('heading', { name: 'Khuyen mai' })).toBeInTheDocument();
        expect(screen.getByText('BEAUTY50')).toBeInTheDocument();
        expect(screen.getByText('Giam 50.000 cho don skincare')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Shop now' })).toHaveAttribute('href', '/products');
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('renders empty promotion state', () => {
        render(<CustomerPromotionPage promotions={[]} />);

        expect(screen.getByText('No active promotions right now.')).toBeInTheDocument();
    });
});
