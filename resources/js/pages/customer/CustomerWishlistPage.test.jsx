import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
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

    it('paginates long wishlist product lists', async () => {
        const user = userEvent.setup();
        const items = Array.from({ length: 13 }, (_, index) => ({
            id: index + 1,
            name: `Wishlist Serum ${index + 1}`,
            price: 250000,
            sale_price: 200000,
            featured_image: '/adm/images/godakeben450x170.jpg',
            url: `/products/${index + 1}`,
            removeUrl: `/wishlist/items/${index + 1}`,
        }));

        render(<CustomerWishlistPage items={items} />);

        expect(screen.getByRole('link', { name: 'Wishlist Serum 1' })).toBeInTheDocument();
        expect(screen.queryByRole('link', { name: 'Wishlist Serum 13' })).not.toBeInTheDocument();

        await user.click(screen.getByRole('button', { name: 'Next' }));

        expect(screen.getByRole('link', { name: 'Wishlist Serum 13' })).toBeInTheDocument();
        expect(screen.queryByRole('link', { name: 'Wishlist Serum 1' })).not.toBeInTheDocument();
    });
});
