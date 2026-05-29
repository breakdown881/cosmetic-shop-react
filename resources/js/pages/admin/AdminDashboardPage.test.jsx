import { render, screen } from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { describe, expect, it } from 'vitest';
import AdminDashboard from './AdminDashboardPage.jsx';

const dashboardProps = {
    periods: [
        { key: 'today', label: 'Hôm nay' },
        { key: 'month', label: 'Tháng này' },
    ],
    metrics: [
        { key: 'orders', label: 'Đơn hàng', value: 3 },
        { key: 'revenue', label: 'Doanh thu', value: 1200000, type: 'currency' },
        { key: 'cancelledOrders', label: 'Đơn hủy', value: 1 },
    ],
    orders: [
        {
            id: 10,
            customerName: 'Nguyễn Văn A',
            customerPhone: '0900000000',
            deliveryAddress: 'Hà Nội',
            orderDate: '2026-05-28',
            paymentMethod: 'COD',
            status: 'Mới',
            total: 250000,
        },
    ],
    labels: {
        delete: 'Xóa',
        detail: 'Chi tiết',
        edit: 'Sửa',
        emptyOrders: 'Chưa có đơn hàng.',
        find: 'Tìm',
        fromDate: 'Từ ngày',
        orders: 'Đơn hàng',
        toDate: 'Đến ngày',
    },
};

describe('AdminDashboard', () => {
    it('renders periods, metrics and orders', () => {
        render(<AdminDashboard {...dashboardProps} />);

        expect(screen.getByRole('button', { name: 'Hôm nay' })).toHaveClass('active');
        expect(screen.getByText(/Đơn hàng 3/)).toBeInTheDocument();
        expect(screen.getByText(/Doanh thu/)).toHaveTextContent('1.200.000');
        expect(screen.getByText('#10')).toBeInTheDocument();
        expect(screen.getByText('Nguyễn Văn A')).toBeInTheDocument();
        expect(screen.getByRole('button', { name: 'Tìm' })).toBeInTheDocument();
    });

    it('changes active period and renders empty state', async () => {
        const user = userEvent.setup();

        render(<AdminDashboard {...dashboardProps} orders={[]} />);

        await user.click(screen.getByRole('button', { name: 'Tháng này' }));

        expect(screen.getByRole('button', { name: 'Tháng này' })).toHaveClass('active');
        expect(screen.getByText('Chưa có đơn hàng.')).toBeInTheDocument();
    });
});
