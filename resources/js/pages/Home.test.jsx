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
                url: '/products/10',
            },
        ],
        url: '/categories/1',
    },
    {
        id: 2,
        name: 'Trang điểm',
        products: [],
        url: '/categories/2',
    },
];

describe('Home', () => {
    it('renders promotion slider, sidebar categories and category product sections', () => {
        render(
            <Home
                categories={categories}
                categorySections={categorySections}
                promotions={[
                    {
                        code: 'BEAUTY50',
                        description: 'Giam 50.000 cho don skincare',
                        label: '50.000 VND',
                        expires_at: '2026-05-30 10:00:00',
                    },
                ]}
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
        expect(screen.getByRole('banner')).toHaveClass('react-customer-layout__header');
        expect(screen.getByRole('navigation', { name: 'Customer navigation' })).toBeInTheDocument();
        expect(screen.getAllByRole('link', { name: 'Tất cả sản phẩm' })[0]).toHaveAttribute('href', '/products');
        expect(screen.getByRole('complementary', { name: 'Điều hướng danh mục' })).toBeInTheDocument();
        expect(screen.getByRole('link', { name: /Chăm sóc da/ })).toHaveAttribute('href', '#category-1');
        expect(screen.getByRole('heading', { name: 'Sản phẩm nổi bật' })).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Voucher khuyến mãi' })).toBeInTheDocument();
        expect(screen.getByText('Sản phẩm có sẵn')).toBeInTheDocument();
        expect(screen.getByText('BEAUTY50')).toBeInTheDocument();
        expect(screen.getByText(/Hết hạn:/)).toBeInTheDocument();
        expect(screen.getAllByRole('link', { name: 'Serum dưỡng sáng' })[0]).toHaveAttribute('href', '/products/10');
        expect(screen.getByText('Danh mục này chưa có sản phẩm nổi bật.')).toBeInTheDocument();
        expect(screen.getByRole('heading', { name: 'Nhận ưu đãi chăm sóc da mỗi tuần' })).toBeInTheDocument();
        expect(screen.getByLabelText('Email của bạn')).toHaveAttribute('placeholder', 'email@example.com');
        expect(screen.getByRole('button', { name: 'Đăng ký' })).toBeInTheDocument();
        expect(screen.getByRole('form', { name: 'Newsletter signup' })).toHaveAttribute('action', '/newsletter/subscribe');
    });

    it('surfaces page errors to the user', () => {
        render(<Home errorMessage="Không thể tải dữ liệu." />);

        expect(screen.getByRole('alert')).toHaveTextContent('Không thể tải dữ liệu.');
    });

    it('renders a compact promotion empty state', () => {
        render(<Home categories={categories} categorySections={categorySections} promotions={[]} />);

        expect(screen.getByText('Đang cập nhật ưu đãi')).toBeInTheDocument();
        expect(screen.getByText(/Voucher mới sẽ được mở lại sớm/)).toBeInTheDocument();
    });
});
