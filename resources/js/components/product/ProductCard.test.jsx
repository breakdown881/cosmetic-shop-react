import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import ProductCard from './ProductCard.jsx';

describe('ProductCard', () => {
    it('renders sale badge when product has discount percentage', () => {
        render(
            <ProductCard
                product={{
                    id: 1,
                    name: 'Sale Serum',
                    price: 300000,
                    sale_price: 240000,
                    discount_percentage: 20,
                    url: '/products/1',
                }}
            />,
        );

        expect(screen.getByText('-20%')).toHaveClass('react-product-card__sale-badge');
        expect(screen.getByText(/300\.000/)).toBeInTheDocument();
        expect(screen.getByText(/240\.000/)).toBeInTheDocument();
    });
});
