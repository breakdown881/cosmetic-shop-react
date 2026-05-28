import { render, screen } from '@testing-library/react';
import { describe, expect, it } from 'vitest';
import PublicSidebar from './PublicSidebar.jsx';

describe('PublicSidebar', () => {
    it('renders categories and active price range', () => {
        render(
            <PublicSidebar
                allProductsUrl="/products"
                activeCategoryId={2}
                currentPriceRange="100000-200000"
                categories={[
                    { id: 1, name: 'Kem dưỡng da', url: '/categories/kem-duong-da-1' },
                    { id: 2, name: 'Sữa rửa mặt', url: '/categories/sua-rua-mat-2' },
                ]}
            />,
        );

        expect(screen.getByRole('link', { name: 'Tất cả sản phẩm' })).toHaveAttribute('href', '/products');
        expect(screen.getByRole('link', { name: 'Sữa rửa mặt' })).toHaveAttribute(
            'href',
            '/categories/sua-rua-mat-2',
        );
        expect(screen.getByLabelText('100.000đ - 200.000đ')).toBeChecked();
    });
});
