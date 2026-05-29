import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import CustomerWishlistPage from './CustomerWishlistPage.jsx';

describe('CustomerWishlistPage', () => {
    it('renders wishlist products with remove actions', () => {
        render(
            <CustomerWishlistPage
                navItems={[{ label: 'Products', href: '/products' }]}
                items={[
                    {
                        id: 10,
                        name: 'Wishlist Serum',
                        price: 250000,
                        sale_price: 200000,
                        featured_image: '/adm/images/godakeben450x170.jpg',
                        url: '/products/10',
                        removeUrl: '/wishlist/items/10',
                    },
                ]}
            />,
        );

        expect(screen.getByRole('heading', { name: 'Wishlist' })).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Wishlist Serum' })).toHaveAttribute('href', '/products/10');
        expect(screen.getByRole('button', { name: 'Remove from wishlist' })).toBeInTheDocument();
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('renders empty state', () => {
        render(<CustomerWishlistPage items={[]} />);

        expect(screen.getByText('Your wishlist is empty.')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Browse products' })).toHaveAttribute('href', '/products');
    });
});
