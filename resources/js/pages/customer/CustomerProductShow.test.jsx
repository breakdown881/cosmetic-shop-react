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

    it('disables add to cart when product is out of stock', () => {
        render(<CustomerProductShow product={{ ...product, inventory_qty: 0 }} />);

        expect(screen.getByRole('button', { name: /Out of stock/i })).toBeDisabled();
    });
});
