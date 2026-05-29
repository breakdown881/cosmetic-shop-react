import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import CustomerProductShow from './CustomerProductShow.jsx';

const product = {
    id: 7,
    name: 'Detail Serum',
    brand_name: 'Detail Brand',
    category_name: 'Detail Category',
    price: 500000,
    sale_price: 400000,
    discount_percentage: 20,
    inventory_qty: 8,
    description: 'A brightening serum for daily skincare.',
    star: 4.7,
    featured_image: '/adm/images/godakeben450x170.jpg',
    url: '/products/7',
};

describe('CustomerProductShow', () => {
    it('renders customer product detail with buy actions and related products', () => {
        render(
            <CustomerProductShow
                navItems={[{ label: 'Products', href: '/products' }]}
                product={product}
                relatedProducts={[
                    {
                        id: 8,
                        name: 'Related Toner',
                        price: 120000,
                        sale_price: 120000,
                        featured_image: '/adm/images/godakeben450x170.jpg',
                        url: '/products/8',
                    },
                ]}
            />,
        );

        expect(screen.getByRole('banner')).toHaveClass('react-customer-layout__header');
        expect(screen.getByRole('heading', { name: 'Detail Serum' })).toBeInTheDocument();
        expect(screen.getByText('Detail Brand')).toBeInTheDocument();
        expect(screen.getByText('Detail Category')).toBeInTheDocument();
        expect(screen.getByText('A brightening serum for daily skincare.')).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /Add to cart/i })).toBeEnabled();
        expect(screen.getAllByRole('link', { name: 'Related Toner' })[0]).toHaveAttribute('href', '/products/8');
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });


    it('renders approved reviews and login-gated review and wishlist actions', () => {
        render(
            <CustomerProductShow
                auth={{ check: false, loginUrl: '/login' }}
                product={{
                    ...product,
                    reviews: [
                        {
                            id: 1,
                            fullname: 'Approved Reviewer',
                            star: 5,
                            description: 'Visible approved review.',
                        },
                    ],
                    reviewSummary: { average: 5, count: 1 },
                    canReview: false,
                    hasReviewed: false,
                    wishlist: { isWishlisted: false, storeUrl: '/wishlist/items' },
                }}
            />,
        );

        expect(screen.getByRole('heading', { name: 'Customer reviews' })).toBeInTheDocument();
        expect(screen.getByText('Visible approved review.')).toBeInTheDocument();
        expect(screen.getByText('Sign in to review or save this product.')).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Sign in to review' })).toHaveAttribute('href', '/login');
        expect(screen.getByRole('link', { name: 'Sign in to add wishlist' })).toHaveAttribute('href', '/login');
    });

    it('renders review form and wishlist form for logged-in eligible customer', () => {
        render(
            <CustomerProductShow
                auth={{ check: true, user: { name: 'Customer' } }}
                product={{
                    ...product,
                    reviews: [],
                    reviewSummary: { average: 0, count: 0 },
                    canReview: true,
                    hasReviewed: false,
                    wishlist: { isWishlisted: false, storeUrl: '/wishlist/items' },
                }}
            />,
        );

        expect(screen.getByRole('button', { name: 'Add to wishlist' })).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Submit review' })).toBeInTheDocument();
        expect(screen.getByLabelText('Rating')).toHaveAttribute('name', 'star');
        expect(screen.getByLabelText('Review')).toHaveAttribute('name', 'description');
    });

    it('disables add to cart when product is out of stock', () => {
        render(<CustomerProductShow product={{ ...product, inventory_qty: 0 }} />);

        expect(screen.getByRole('button', { name: /Out of stock/i })).toBeDisabled();
    });
});
