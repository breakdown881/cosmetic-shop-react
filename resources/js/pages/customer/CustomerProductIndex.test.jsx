import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import CustomerProductIndex from './CustomerProductIndex.jsx';

const baseProps = {
    filters: { q: 'serum', category_id: 1, brand_id: 2, sort: 'price_desc' },
    filterOptions: {
        categories: [{ id: 1, name: 'Skin Care' }],
        brands: [{ id: 2, name: 'Acme Beauty' }],
    },
    navItems: [
        { label: 'Home', href: '/' },
        { label: 'Products', href: '/products' },
    ],
    products: {
        data: [
            {
                id: 10,
                name: 'Ruby Serum',
                price: 300000,
                sale_price: 270000,
                featured_image: '/adm/images/godakeben450x170.jpg',
                url: '/products/10',
            },
        ],
        meta: { currentPage: 1, lastPage: 1, total: 1 },
    },
    title: 'All products',
};

describe('CustomerProductIndex', () => {
    it('renders customer layout, filters, sorting and product list', () => {
        render(<CustomerProductIndex {...baseProps} />);

        expect(screen.getByRole('banner')).toHaveClass('react-customer-layout__header');
        expect(screen.getByRole('navigation', { name: 'Customer navigation' })).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'All products' })).toBeInTheDocument();
        expect(screen.getByDisplayValue('serum')).toHaveAttribute('name', 'q');
        expect(screen.getByRole('combobox', { name: 'Category' })).toHaveValue('1');
        expect(screen.getByRole('combobox', { name: 'Brand' })).toHaveValue('2');
        expect(screen.getByRole('combobox', { name: 'Sort' })).toHaveValue('price_desc');
        expect(screen.getAllByRole('link', { name: 'Ruby Serum' })[0]).toHaveAttribute('href', '/products/10');
        expect(screen.queryByText(/Admin/i)).not.toBeInTheDocument();
    });

    it('renders an empty state when no products match filters', () => {
        render(
            <CustomerProductIndex
                {...baseProps}
                products={{ data: [], meta: { currentPage: 1, lastPage: 1, total: 0 } }}
            />,
        );

        expect(screen.getByText('No products match your filters.')).toBeInTheDocument();
    });

    it('renders pagination links for paginated product lists', () => {
        render(
            <CustomerProductIndex
                {...baseProps}
                products={{
                    ...baseProps.products,
                    meta: { currentPage: 2, lastPage: 3, total: 25 },
                    links: { prev: '/products?page=1', next: '/products?page=3' },
                }}
            />,
        );

        expect(screen.getByRole('navigation', { name: 'Pagination' })).toBeInTheDocument();
        expect(screen.getByRole('link', { name: 'Previous' })).toHaveAttribute('href', '/products?page=1');
        expect(screen.getByRole('link', { name: '2' })).toHaveAttribute('aria-current', 'page');
        expect(screen.getByRole('link', { name: 'Next' })).toHaveAttribute('href', '/products?page=3');
    });
});
