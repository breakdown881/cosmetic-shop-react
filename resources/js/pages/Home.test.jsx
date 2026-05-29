import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import Home from './Home.jsx';

const categories = [
    { id: 1, name: 'Chăm sóc da', productsCount: 2 },
    { id: 2, name: 'Trang điểm', productsCount: 0 },
];

const categorySections = [
    {
        id: 1,
        name: 'Chăm sóc da',
        products: [
            {
                id: 10,
                name: 'Serum dưỡng sáng',
                category_id: 1,
                price: 250000,
                sale_price: 199000,
                featured_image: '/adm/images/godakeben450x170.jpg',
                url: '#product-10',
            },
        ],
    },
    {
        id: 2,
        name: 'Trang điểm',
        products: [],
    },
];

describe('Home', () => {
    it('renders promotion slider, sidebar categories and category product sections', () => {
        render(
            <Home
                categories={categories}
                categorySections={categorySections}
                slides={[
                    {
                        title: 'Sale mỹ phẩm',
                        description: 'Ưu đãi hôm nay',
                        imageUrl: '/adm/images/slider1.jpg',
                        ctaUrl: '#categories',
                        ctaLabel: 'Mua ngay',
                    },
                ]}
            />,
        );

        expect(screen.getByRole('heading', { name: 'Sale mỹ phẩm' })).toBeInTheDocument();
        expect(screen.getByRole('navigation', { name: 'Menu chính' })).toBeInTheDocument();
        expect(screen.getByRole('complementary', { name: 'Điều hướng danh mục' })).toBeInTheDocument();
        expect(screen.getByRole('link', { name: /Chăm sóc da/ })).toHaveAttribute('href', '#category-1');
        expect(screen.getByRole('heading', { name: 'Sản phẩm nổi bật' })).toBeInTheDocument();
        expect(screen.getAllByRole('link', { name: 'Serum dưỡng sáng' })[0]).toHaveAttribute('href', '#product-10');
        expect(screen.getByText('Danh mục này chưa có sản phẩm nổi bật.')).toBeInTheDocument();
    });

    it('surfaces page errors to the user', () => {
        render(<Home errorMessage="Không thể tải dữ liệu." />);

        expect(screen.getByRole('alert')).toHaveTextContent('Không thể tải dữ liệu.');
    });
});
